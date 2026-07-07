<?php
declare(strict_types=1);

/**
 * Inhalts-Modell + Renderer für den Selbst-Editor (Stufe 1).
 *
 * Datenmodell (siehe database/mysql-schema.sql):
 *   site_pages          – eine Kundenseite (project_id, slug, vorlage, is_published)
 *   site_blocks         – ein Feld je (page, section, field) mit Entwurf+Live-Wert
 *   site_page_versions  – Schnappschuss vor jeder Veröffentlichung (für Rückgängig)
 *
 * Der Renderer gibt die Kundenseite aus Vorlage (site-content-schema.php) + Inhalten
 * aus. Bilder werden über eine Media-Map (upload-id => ['url','alt']) aufgelöst.
 */

require_once __DIR__ . '/site-content-schema.php';
require_once __DIR__ . '/legal-generator.php';

function sc_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Seite per project_id + slug holen; legt sie an, wenn sie fehlt. */
function sc_get_or_create_page(PDO $pdo, string $projectId, string $slug = 'home', string $vorlage = 'standard'): array
{
    $stmt = $pdo->prepare('select * from site_pages where project_id = ? and slug = ? limit 1');
    $stmt->execute([$projectId, $slug]);
    $page = $stmt->fetch();
    if ($page) {
        return $page;
    }
    $id = uuidv4();
    $ins = $pdo->prepare('insert into site_pages (id, project_id, slug, vorlage) values (?, ?, ?, ?)');
    $ins->execute([$id, $projectId, $slug, $vorlage]);
    $stmt->execute([$projectId, $slug]);
    return $stmt->fetch();
}

/** Standard-Seiten (Start + Pflichtseiten) für ein Projekt anlegen, falls fehlend. */
function sc_ensure_pages(PDO $pdo, string $projectId): void
{
    // Kundenprofil für die Vorbelegung der Rechtsseiten.
    $prof = $pdo->prepare('select p.* from profiles p join projects pr on pr.customer_id = p.id where pr.id = ? limit 1');
    $prof->execute([$projectId]);
    $profile = $prof->fetch() ?: [];
    $impData = sartu_legal_prefill_impressum($profile);

    foreach (sartu_site_page_types() as $slug => $def) {
        $stmt = $pdo->prepare('select id from site_pages where project_id = ? and slug = ? limit 1');
        $stmt->execute([$projectId, $slug]);
        if ($stmt->fetch()) {
            continue;
        }
        $pageId = uuidv4();
        $ins = $pdo->prepare('insert into site_pages (id, project_id, slug, vorlage, titel, nav_label, typ, position) values (?, ?, ?, ?, ?, ?, ?, ?)');
        $ins->execute([$pageId, $projectId, $slug, $def['vorlage'], $def['titel'], $def['nav_label'], $def['typ'], $def['position']]);

        if ($slug === 'impressum') {
            foreach ($impData as $k => $v) {
                if ($v !== '') {
                    sc_save_field($pdo, $pageId, 'impressum', $k, $v);
                }
            }
        } elseif ($slug === 'datenschutz') {
            sc_save_field($pdo, $pageId, 'datenschutz', 'titel', 'Datenschutzerklärung');
            sc_save_field($pdo, $pageId, 'datenschutz', 'text', sartu_generate_datenschutz($impData, []));
        }
    }
}

/** Alle Seiten eines Projekts (für Seitenliste/Navigation). */
function sc_project_pages(PDO $pdo, string $projectId): array
{
    $stmt = $pdo->prepare('select id, slug, vorlage, titel, nav_label, typ, aktiv, is_published, position from site_pages where project_id = ? order by position asc, created_at asc');
    $stmt->execute([$projectId]);
    return $stmt->fetchAll();
}

/** Eine Seite per Slug (oder null). */
function sc_page_by_slug(PDO $pdo, string $projectId, string $slug): ?array
{
    $stmt = $pdo->prepare('select * from site_pages where project_id = ? and slug = ? limit 1');
    $stmt->execute([$projectId, $slug]);
    return $stmt->fetch() ?: null;
}

/** Seite ein-/ausblenden (nur für nicht-pflichtige Inhaltsseiten sinnvoll). */
function sc_set_page_active(PDO $pdo, string $pageId, bool $aktiv): void
{
    $pdo->prepare('update site_pages set aktiv = ?, updated_at = current_timestamp where id = ?')->execute([$aktiv ? 1 : 0, $pageId]);
}

/** Alle Seiten eines Projekts veröffentlichen (Entwurf → Live). */
function sc_publish_project(PDO $pdo, string $projectId, ?string $userId = null): void
{
    foreach (sc_project_pages($pdo, $projectId) as $p) {
        sc_publish_page($pdo, (string) $p['id'], $userId);
    }
}

/**
 * Inhalte einer Seite als [section][field] => wert laden.
 * $variant: 'draft' (Editor) oder 'published' (Live). List/hours-Felder werden
 * anhand des Schemas per json_decode zu Arrays aufgelöst.
 */
function sc_load_content(PDO $pdo, array $page, string $variant = 'published'): array
{
    $col = $variant === 'draft' ? 'wert_draft' : 'wert_published';
    $stmt = $pdo->prepare('select section_key, field_key, ' . $col . ' as wert from site_blocks where page_id = ?');
    $stmt->execute([$page['id']]);

    $fields = sartu_site_fields((string) ($page['vorlage'] ?? 'standard'));
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $key = $row['section_key'] . '.' . $row['field_key'];
        $type = $fields[$key]['type'] ?? 'text';
        $val = $row['wert'];
        if ($val !== null && ($type === 'list' || $type === 'hours')) {
            $decoded = json_decode((string) $val, true);
            $val = is_array($decoded) ? $decoded : [];
        }
        $out[$row['section_key']][$row['field_key']] = $val;
    }
    return $out;
}

/** Einen Entwurfs-Wert setzen (Upsert). $wert ist String oder Array (wird JSON). */
function sc_save_field(PDO $pdo, string $pageId, string $section, string $field, $wert): void
{
    $stored = is_array($wert) ? json_encode($wert, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ($wert === null ? null : (string) $wert);
    $stmt = $pdo->prepare(
        'insert into site_blocks (id, page_id, section_key, field_key, wert_draft)
         values (?, ?, ?, ?, ?)
         on duplicate key update wert_draft = values(wert_draft), updated_at = current_timestamp'
    );
    $stmt->execute([uuidv4(), $pageId, $section, $field, $stored]);
}

/** Schnappschuss aller Entwurfs- und Live-Werte einer Seite ablegen. */
function sc_snapshot(PDO $pdo, string $pageId, string $anlass, ?string $userId = null): void
{
    $stmt = $pdo->prepare('select section_key, field_key, wert_draft, wert_published from site_blocks where page_id = ?');
    $stmt->execute([$pageId]);
    $snap = json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $ins = $pdo->prepare('insert into site_page_versions (id, page_id, anlass, snapshot, erstellt_von) values (?, ?, ?, ?, ?)');
    $ins->execute([uuidv4(), $pageId, $anlass, $snap, $userId]);
}

/** Veröffentlichen: Schnappschuss + Entwurf → Live kopieren. */
function sc_publish_page(PDO $pdo, string $pageId, ?string $userId = null): void
{
    $pdo->beginTransaction();
    try {
        sc_snapshot($pdo, $pageId, 'vor_veroeffentlichung', $userId);
        $pdo->prepare('update site_blocks set wert_published = wert_draft, updated_at = current_timestamp where page_id = ?')->execute([$pageId]);
        $pdo->prepare('update site_pages set is_published = 1, updated_at = current_timestamp where id = ?')->execute([$pageId]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/** Rückgängig: einen Schnappschuss zurück in die Entwurfs-Werte spielen. */
function sc_restore_version(PDO $pdo, string $pageId, string $versionId): bool
{
    $stmt = $pdo->prepare('select snapshot from site_page_versions where id = ? and page_id = ? limit 1');
    $stmt->execute([$versionId, $pageId]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }
    $blocks = json_decode((string) $row['snapshot'], true);
    if (!is_array($blocks)) {
        return false;
    }
    $pdo->beginTransaction();
    try {
        sc_snapshot($pdo, $pageId, 'vor_rueckgaengig', null);
        foreach ($blocks as $b) {
            sc_save_field($pdo, $pageId, (string) $b['section_key'], (string) $b['field_key'], $b['wert_draft'] ?? null);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    return true;
}

/* =========================================================================
 * Renderer — gibt die Kundenseite aus. $media: [upload_id => ['url','alt']].
 * ========================================================================= */

/** Ist eine Sektion sichtbar? (Kunde kann sie auf __aktiv=0 stellen) */
function sc_visible(array $content, string $section): bool
{
    return (string) ($content[$section]['__aktiv'] ?? '1') !== '0';
}

/** Nur aktive Listeneinträge (Kunde kann einzelne auf _aktiv=false stellen). */
function sc_active_items($items): array
{
    if (!is_array($items)) {
        return [];
    }
    $out = [];
    foreach ($items as $it) {
        if (is_array($it) && array_key_exists('_aktiv', $it) && $it['_aktiv'] === false) {
            continue;
        }
        $out[] = $it;
    }
    return $out;
}

function sc_media(array $media, ?string $ref): array
{
    if ($ref === null || $ref === '') {
        return ['url' => '', 'alt' => ''];
    }
    // Im Demo-/Vorschaumodus kann direkt eine URL stehen.
    if (preg_match('~^(https?:|/|data:|api/)~', $ref)) {
        return ['url' => $ref, 'alt' => ''];
    }
    return $media[$ref] ?? ['url' => '', 'alt' => ''];
}

function sc_img(array $media, ?string $ref, string $class = ''): string
{
    $m = sc_media($media, $ref);
    if ($m['url'] === '') {
        return '';
    }
    $cls = $class !== '' ? ' class="' . sc_e($class) . '"' : '';
    return '<img' . $cls . ' src="' . sc_e($m['url']) . '" alt="' . sc_e($m['alt']) . '" loading="lazy" />';
}

/**
 * Einfache, sichere Formatierung: **fett**, *kursiv*, Links (URL/E-Mail) und
 * Absätze. Zuerst escapen (kein HTML vom Kunden), dann die Marker anwenden —
 * so ist XSS ausgeschlossen, der Kunde bekommt aber echte Formatierung.
 */
function sc_richtext(?string $text): string
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }
    $parts = preg_split('~\n{2,}~', $text) ?: [$text];
    $html = '';
    foreach ($parts as $p) {
        $safe = sc_e(trim($p));
        $safe = preg_replace('~\*\*(.+?)\*\*~s', '<strong>$1</strong>', $safe);
        $safe = preg_replace('~(?<![\*\w])\*(?!\s)(.+?)(?<!\s)\*(?![\*\w])~s', '<em>$1</em>', $safe);
        $safe = preg_replace('~\b(https?://[^\s<]+)~i', '<a href="$1" target="_blank" rel="noopener">$1</a>', $safe);
        $safe = preg_replace('~([\w.+-]+@[\w-]+\.[\w.-]+)~', '<a href="mailto:$1">$1</a>', $safe);
        $html .= '<p>' . nl2br($safe) . '</p>';
    }
    return $html;
}

/** Alias für reinen Absatz-Text (ohne Marker) — z. B. Adressen. */
function sc_paragraphs(?string $text): string
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }
    $parts = preg_split('~\n{2,}~', $text) ?: [$text];
    $html = '';
    foreach ($parts as $p) {
        $html .= '<p>' . nl2br(sc_e(trim($p))) . '</p>';
    }
    return $html;
}

/**
 * Farb-Rollen aus dem Design-Inhalt auflösen: [css-var => validierter Hex].
 * Ungültige/leere Werte fallen auf den Standard zurück (Injection-Schutz).
 */
function sc_resolve_theme(array $design): array
{
    $out = [];
    foreach (sartu_site_theme() as $role) {
        $val = $design[$role['key']] ?? '';
        if (!is_string($val) || !(sartu_site_valid_hex($val) || sartu_site_palette_has($val))) {
            $val = $role['default'];
        }
        $out[$role['var']] = strtolower($val);
    }
    return $out;
}

/**
 * Die komplette Kundenseite rendern (Mehrseiten-fähig).
 * $page: site_pages-Row · $content: sc_load_content() · $media: Map ·
 * $nav: [['slug','label','url','current'=>bool], …] · $theme: Site-Theme (optional).
 */
function render_customer_site(array $page, array $content, array $media = [], array $nav = [], ?array $theme = null, string $siteName = ''): void
{
    $vorlage = (string) ($page['vorlage'] ?? 'standard');
    if ($theme === null) {
        $theme = sc_resolve_theme($content['design'] ?? []);
    }
    $hero = $content['hero'] ?? [];
    if ($siteName === '') {
        $siteName = (string) ($hero['headline'] ?? ($page['titel'] ?? ''));
    }
    $seo = $content['seo'] ?? [];
    $title = ($seo['titel'] ?? '') !== '' ? $seo['titel'] : ($hero['headline'] ?? ($page['titel'] ?? 'Website'));
    $metaDesc = trim((string) ($seo['description'] ?? ''));
    ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= sc_e((string) $title) ?></title>
  <?php if ($metaDesc !== ''): ?><meta name="description" content="<?= sc_e($metaDesc) ?>" /><?php endif; ?>
  <style>
    :root {
      --accent: <?= sc_e($theme['accent']) ?>; --accent-text: <?= sc_e($theme['accent-text']) ?>;
      --text: <?= sc_e($theme['text']) ?>; --heading: <?= sc_e($theme['heading']) ?>; --muted: <?= sc_e($theme['muted']) ?>;
      --bg: <?= sc_e($theme['bg']) ?>; --soft: <?= sc_e($theme['soft']) ?>; --card: <?= sc_e($theme['card']) ?>;
      --line: <?= sc_e($theme['line']) ?>; --nav: <?= sc_e($theme['nav']) ?>;
    }
    * { box-sizing:border-box; }
    body { margin:0; font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,sans-serif; color:var(--text); background:var(--bg); line-height:1.6; }
    img { max-width:100%; height:auto; display:block; }
    h1,h2,h3 { color:var(--heading); }
    .cs-wrap { max-width:1080px; margin:0 auto; padding:0 20px; }
    .cs-hero { background:var(--soft); padding:72px 0; }
    .cs-hero h1 { font-size:clamp(28px,5vw,48px); margin:0 0 12px; }
    .cs-hero p { font-size:18px; color:var(--muted); max-width:640px; }
    .cs-btn { display:inline-block; margin-top:20px; background:var(--accent); color:var(--accent-text); text-decoration:none; padding:12px 22px; border-radius:10px; font-weight:600; }
    section.cs-sec { padding:56px 0; }
    section.cs-sec:nth-child(even) { background:var(--soft); }
    .cs-sec h2 { font-size:clamp(22px,3.5vw,32px); margin:0 0 8px; }
    section.cs-sec a { color:var(--accent); }
    .cs-ey { color:var(--accent); font-weight:700; letter-spacing:.04em; text-transform:uppercase; font-size:13px; margin:0 0 6px; }
    .cs-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-top:24px; }
    .cs-card { background:var(--card); border:1px solid var(--line); border-radius:14px; padding:20px; }
    .cs-card h3 { margin:0 0 6px; }
    .cs-hours { list-style:none; padding:0; margin:16px 0 0; max-width:420px; }
    .cs-hours li { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--line); }
    .cs-foot { padding:32px 0; color:var(--muted); font-size:14px; text-align:center; }
    .cs-foot a { color:var(--muted); }
    .cs-media { border-radius:14px; overflow:hidden; margin-top:24px; }
    .cs-nav { background:var(--nav); border-bottom:1px solid var(--line); position:sticky; top:0; z-index:5; }
    .cs-nav .cs-wrap { display:flex; gap:18px; align-items:center; flex-wrap:wrap; padding-top:14px; padding-bottom:14px; }
    .cs-nav a { color:var(--text); text-decoration:none; font-weight:600; font-size:15px; }
    .cs-nav a.is-current { color:var(--accent); }
    .cs-legal p { margin:0 0 10px; }
  </style>
</head>
<body>
  <?php sc_render_nav($nav); ?>
  <?php
    if ($vorlage === 'impressum') {
        sc_body_impressum($content);
    } elseif ($vorlage === 'datenschutz') {
        sc_body_datenschutz($content);
    } elseif ($vorlage === 'inhalt') {
        sc_body_inhalt($content);
    } else {
        sc_body_standard($content, $media);
    }
  ?>
  <?php sc_render_footer($nav, $siteName); ?>
</body>
</html>
<?php
}

/** Obere Navigation über die aktiven Seiten. */
function sc_render_nav(array $nav): void
{
    if (count($nav) < 2) {
        return;
    }
    ?>
  <nav class="cs-nav"><div class="cs-wrap">
    <?php foreach ($nav as $n): ?><a href="<?= sc_e((string) $n['url']) ?>"<?= !empty($n['current']) ? ' class="is-current"' : '' ?>><?= sc_e((string) $n['label']) ?></a><?php endforeach; ?>
  </div></nav>
    <?php
}

/** Fußzeile mit Links zu den Pflichtseiten. */
function sc_render_footer(array $nav, string $siteName): void
{
    $legal = array_filter($nav, static function ($n) {
        return in_array($n['slug'] ?? '', ['impressum', 'datenschutz'], true);
    });
    ?>
  <footer class="cs-foot"><div class="cs-wrap"><?= sc_e($siteName) ?> · Website von Sartu<?php foreach ($legal as $n): ?> · <a href="<?= sc_e((string) $n['url']) ?>"><?= sc_e((string) $n['label']) ?></a><?php endforeach; ?></div></footer>
    <?php
}

/** Startseite / Onepager. */
function sc_body_standard(array $content, array $media): void
{
    $get = static function (string $section, string $field, $default = '') use ($content) {
        return $content[$section][$field] ?? $default;
    };
    $hero = $content['hero'] ?? [];
    ?>
  <header class="cs-hero">
    <div class="cs-wrap">
      <h1><?= sc_e((string) ($hero['headline'] ?? '')) ?></h1>
      <?php if (!empty($hero['subline'])): ?><p><?= nl2br(sc_e((string) $hero['subline'])) ?></p><?php endif; ?>
      <?php if (!empty($hero['cta_text'])): ?><a class="cs-btn" href="<?= sc_e((string) ($hero['cta_ziel'] ?? '#')) ?>"><?= sc_e((string) $hero['cta_text']) ?></a><?php endif; ?>
      <?php $img = sc_img($media, $hero['bild'] ?? null, 'cs-media-img'); if ($img !== ''): ?><div class="cs-media"><?= $img ?></div><?php endif; ?>
    </div>
  </header>

  <?php if (sc_visible($content, 'ueber') && (trim((string) $get('ueber', 'titel')) !== '' || trim((string) $get('ueber', 'text')) !== '')): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('ueber', 'titel')): ?><h2><?= sc_e((string) $get('ueber', 'titel')) ?></h2><?php endif; ?>
    <?= sc_richtext((string) $get('ueber', 'text')) ?>
    <?php $img = sc_img($media, $get('ueber', 'bild', null), ''); if ($img !== ''): ?><div class="cs-media"><?= $img ?></div><?php endif; ?>
  </div></section>
  <?php endif; ?>

  <?php $items = sc_active_items($get('leistungen', 'items', [])); if (sc_visible($content, 'leistungen') && $items): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('leistungen', 'titel')): ?><h2><?= sc_e((string) $get('leistungen', 'titel')) ?></h2><?php endif; ?>
    <?php if ($get('leistungen', 'einleitung')): ?><p><?= nl2br(sc_e((string) $get('leistungen', 'einleitung'))) ?></p><?php endif; ?>
    <div class="cs-grid">
      <?php foreach ($items as $it): if (!is_array($it)) continue; ?>
      <div class="cs-card"><h3><?= sc_e((string) ($it['titel'] ?? '')) ?></h3><?= sc_richtext((string) ($it['text'] ?? '')) ?></div>
      <?php endforeach; ?>
    </div>
  </div></section>
  <?php endif; ?>

  <?php $members = sc_active_items($get('team', 'members', [])); if (sc_visible($content, 'team') && $members): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('team', 'titel')): ?><h2><?= sc_e((string) $get('team', 'titel')) ?></h2><?php endif; ?>
    <div class="cs-grid">
      <?php foreach ($members as $m): if (!is_array($m)) continue; ?>
      <div class="cs-card"><?= sc_img($media, $m['bild'] ?? null, '') ?><h3><?= sc_e((string) ($m['name'] ?? '')) ?></h3><p><?= sc_e((string) ($m['rolle'] ?? '')) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div></section>
  <?php endif; ?>

  <?php $zeiten = $get('oeffnungszeiten', 'zeiten', []); if (sc_visible($content, 'oeffnungszeiten') && is_array($zeiten) && $zeiten): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('oeffnungszeiten', 'titel')): ?><h2><?= sc_e((string) $get('oeffnungszeiten', 'titel')) ?></h2><?php else: ?><h2>Öffnungszeiten</h2><?php endif; ?>
    <ul class="cs-hours">
      <?php foreach ($zeiten as $tag => $wert): if (trim((string) $wert) === '') continue; ?><li><span><?= sc_e((string) $tag) ?></span><span><?= sc_e((string) $wert) ?></span></li><?php endforeach; ?>
    </ul>
    <?php if ($get('oeffnungszeiten', 'hinweis')): ?><p><?= sc_e((string) $get('oeffnungszeiten', 'hinweis')) ?></p><?php endif; ?>
  </div></section>
  <?php endif; ?>

  <?php $posts = sc_active_items($get('beitraege', 'posts', [])); if (sc_visible($content, 'beitraege') && $posts): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('beitraege', 'titel')): ?><h2><?= sc_e((string) $get('beitraege', 'titel')) ?></h2><?php endif; ?>
    <div class="cs-grid">
      <?php foreach ($posts as $p): if (!is_array($p)) continue; ?>
      <div class="cs-card"><?= sc_img($media, $p['bild'] ?? null, '') ?><p class="cs-ey"><?= sc_e((string) ($p['datum'] ?? '')) ?></p><h3><?= sc_e((string) ($p['titel'] ?? '')) ?></h3><?= sc_richtext((string) ($p['text'] ?? '')) ?></div>
      <?php endforeach; ?>
    </div>
  </div></section>
  <?php endif; ?>

  <?php if (trim((string) $get('kontakt', 'titel')) !== '' || trim((string) $get('kontakt', 'adresse')) !== ''): ?>
  <section class="cs-sec" id="kontakt"><div class="cs-wrap">
    <h2><?= sc_e((string) ($get('kontakt', 'titel') ?: 'Kontakt')) ?></h2>
    <?= sc_paragraphs((string) $get('kontakt', 'adresse')) ?>
    <?php if ($get('kontakt', 'telefon')): ?><p><a href="tel:<?= sc_e((string) $get('kontakt', 'telefon')) ?>"><?= sc_e((string) $get('kontakt', 'telefon')) ?></a></p><?php endif; ?>
    <?php if ($get('kontakt', 'email')): ?><p><a href="mailto:<?= sc_e((string) $get('kontakt', 'email')) ?>"><?= sc_e((string) $get('kontakt', 'email')) ?></a></p><?php endif; ?>
    <?php if ($get('kontakt', 'maps')): ?><p><a href="<?= sc_e((string) $get('kontakt', 'maps')) ?>" target="_blank" rel="noopener">Auf der Karte ansehen &rarr;</a></p><?php endif; ?>
  </div></section>
  <?php endif; ?>
    <?php
}

/** Pflichtseite: Impressum — aus den Feldern zu einem vollständigen Impressum zusammengesetzt. */
function sc_body_impressum(array $content): void
{
    $imp = $content['impressum'] ?? [];
    $klein = !empty($imp['kleinunternehmer']) && (string) $imp['kleinunternehmer'] !== '0';
    ?>
  <section class="cs-sec cs-legal" id="impressum"><div class="cs-wrap">
    <h2>Impressum</h2>
    <h3>Angaben gemäß § 5 DDG</h3>
    <?php if (!empty($imp['firmenname'])): ?><p><strong><?= sc_e((string) $imp['firmenname']) ?></strong></p><?php endif; ?>
    <?= sc_paragraphs((string) ($imp['adresse'] ?? '')) ?>
    <?php if (!empty($imp['inhaber'])): ?><h3>Vertreten durch</h3><p><?= sc_e((string) $imp['inhaber']) ?></p><?php endif; ?>
    <?php if (!empty($imp['telefon']) || !empty($imp['email'])): ?>
      <h3>Kontakt</h3>
      <?php if (!empty($imp['telefon'])): ?><p>Telefon: <?= sc_e((string) $imp['telefon']) ?></p><?php endif; ?>
      <?php if (!empty($imp['email'])): ?><p>E-Mail: <?= sc_e((string) $imp['email']) ?></p><?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($imp['ust_id'])): ?><h3>Umsatzsteuer-ID</h3><p>Umsatzsteuer-Identifikationsnummer gemäß § 27a Umsatzsteuergesetz:<br><?= sc_e((string) $imp['ust_id']) ?></p><?php endif; ?>
    <?php if ($klein): ?><h3>Kleinunternehmerregelung</h3><p>Gemäß § 19 UStG wird keine Umsatzsteuer berechnet und ausgewiesen.</p><?php endif; ?>
    <?php if (!empty($imp['register'])): ?><h3>Registereintrag</h3><p><?= sc_e((string) $imp['register']) ?></p><?php endif; ?>
    <?php if (!empty($imp['verantwortlich'])): ?><h3>Redaktionell verantwortlich</h3><p><?= sc_e((string) $imp['verantwortlich']) ?></p><?php endif; ?>
    <h3>EU-Streitschlichtung</h3>
    <p>Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener">https://ec.europa.eu/consumers/odr/</a></p>
    <h3>Verbraucherstreitbeilegung</h3>
    <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
    <h3>Haftung für Inhalte</h3>
    <p>Als Diensteanbieter sind wir gemäß § 7 Abs. 1 DDG für eigene Inhalte auf diesen Seiten verantwortlich. Nach §§ 8 bis 10 DDG sind wir jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen.</p>
    <h3>Haftung für Links</h3>
    <p>Unser Angebot enthält ggf. Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Für diese fremden Inhalte ist stets der jeweilige Anbieter verantwortlich.</p>
    <h3>Urheberrecht</h3>
    <p>Die durch die Seitenbetreiber erstellten Inhalte und Werke unterliegen dem deutschen Urheberrecht.</p>
  </div></section>
    <?php
}

/** Generische Inhaltsseite (von Sartu gepflegter Text). */
function sc_body_inhalt(array $content): void
{
    $in = $content['inhalt'] ?? [];
    ?>
  <section class="cs-sec cs-legal"><div class="cs-wrap">
    <h2><?= sc_e((string) ($in['titel'] ?? 'Seite')) ?></h2>
    <?= sc_richtext((string) ($in['text'] ?? '')) ?>
  </div></section>
    <?php
}

/** Pflichtseite: Datenschutz (von Sartu gepflegter Text). */
function sc_body_datenschutz(array $content): void
{
    $ds = $content['datenschutz'] ?? [];
    ?>
  <section class="cs-sec cs-legal" id="datenschutz"><div class="cs-wrap">
    <h2><?= sc_e((string) ($ds['titel'] ?? 'Datenschutzerklärung')) ?></h2>
    <?php if (trim((string) ($ds['text'] ?? '')) !== ''): ?>
      <?= sc_richtext((string) $ds['text']) ?>
    <?php else: ?>
      <p>Die Datenschutzerklärung wird von Sartu erstellt und hier eingesetzt.</p>
    <?php endif; ?>
  </div></section>
    <?php
}
