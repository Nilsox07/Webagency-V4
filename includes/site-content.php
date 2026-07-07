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

/** p-Absätze aus Zeilenumbrüchen (escaped). */
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
 * Die komplette Kundenseite rendern.
 * $page: site_pages-Row · $content: sc_load_content()-Ergebnis · $media: Map.
 */
function render_customer_site(array $page, array $content, array $media = []): void
{
    $tpl = sartu_site_template((string) ($page['vorlage'] ?? 'standard'));
    $accent = $content['design']['akzentfarbe'] ?? '#0f766e';
    if (!is_string($accent) || !sartu_site_palette_has($accent)) {
        $accent = '#0f766e';
    }
    $hero = $content['hero'] ?? [];
    $title = $hero['headline'] ?? ($page['titel'] ?? 'Website');

    $get = static function (string $section, string $field, $default = '') use ($content) {
        return $content[$section][$field] ?? $default;
    };
    ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= sc_e((string) $title) ?></title>
  <style>
    :root { --accent: <?= sc_e($accent) ?>; --ink:#1c2530; --muted:#5c6672; --bg:#ffffff; --soft:#f5f7f8; }
    * { box-sizing:border-box; }
    body { margin:0; font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,sans-serif; color:var(--ink); background:var(--bg); line-height:1.6; }
    img { max-width:100%; height:auto; display:block; }
    .cs-wrap { max-width:1080px; margin:0 auto; padding:0 20px; }
    .cs-hero { background:var(--soft); padding:72px 0; }
    .cs-hero h1 { font-size:clamp(28px,5vw,48px); margin:0 0 12px; }
    .cs-hero p { font-size:18px; color:var(--muted); max-width:640px; }
    .cs-btn { display:inline-block; margin-top:20px; background:var(--accent); color:#fff; text-decoration:none; padding:12px 22px; border-radius:10px; font-weight:600; }
    section.cs-sec { padding:56px 0; }
    section.cs-sec:nth-child(even) { background:var(--soft); }
    .cs-sec h2 { font-size:clamp(22px,3.5vw,32px); margin:0 0 8px; }
    .cs-ey { color:var(--accent); font-weight:700; letter-spacing:.04em; text-transform:uppercase; font-size:13px; margin:0 0 6px; }
    .cs-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-top:24px; }
    .cs-card { background:var(--bg); border:1px solid #e6eaed; border-radius:14px; padding:20px; }
    section.cs-sec:nth-child(even) .cs-card { background:#fff; }
    .cs-card h3 { margin:0 0 6px; }
    .cs-hours { list-style:none; padding:0; margin:16px 0 0; max-width:420px; }
    .cs-hours li { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e6eaed; }
    .cs-foot { padding:32px 0; color:var(--muted); font-size:14px; text-align:center; }
    .cs-media { border-radius:14px; overflow:hidden; margin-top:24px; }
  </style>
</head>
<body>
  <header class="cs-hero">
    <div class="cs-wrap">
      <h1><?= sc_e((string) ($hero['headline'] ?? '')) ?></h1>
      <?php if (!empty($hero['subline'])): ?><p><?= nl2br(sc_e((string) $hero['subline'])) ?></p><?php endif; ?>
      <?php if (!empty($hero['cta_text'])): ?><a class="cs-btn" href="<?= sc_e((string) ($hero['cta_ziel'] ?? '#')) ?>"><?= sc_e((string) $hero['cta_text']) ?></a><?php endif; ?>
      <?php $img = sc_img($media, $hero['bild'] ?? null, 'cs-media-img'); if ($img !== ''): ?><div class="cs-media"><?= $img ?></div><?php endif; ?>
    </div>
  </header>

  <?php if (trim((string) $get('ueber', 'titel')) !== '' || trim((string) $get('ueber', 'text')) !== ''): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('ueber', 'titel')): ?><h2><?= sc_e((string) $get('ueber', 'titel')) ?></h2><?php endif; ?>
    <?= sc_paragraphs((string) $get('ueber', 'text')) ?>
    <?php $img = sc_img($media, $get('ueber', 'bild', null), ''); if ($img !== ''): ?><div class="cs-media"><?= $img ?></div><?php endif; ?>
  </div></section>
  <?php endif; ?>

  <?php $items = $get('leistungen', 'items', []); if (is_array($items) && $items): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('leistungen', 'titel')): ?><h2><?= sc_e((string) $get('leistungen', 'titel')) ?></h2><?php endif; ?>
    <?php if ($get('leistungen', 'einleitung')): ?><p><?= nl2br(sc_e((string) $get('leistungen', 'einleitung'))) ?></p><?php endif; ?>
    <div class="cs-grid">
      <?php foreach ($items as $it): if (!is_array($it)) continue; ?>
      <div class="cs-card"><h3><?= sc_e((string) ($it['titel'] ?? '')) ?></h3><p><?= nl2br(sc_e((string) ($it['text'] ?? ''))) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div></section>
  <?php endif; ?>

  <?php $members = $get('team', 'members', []); if (is_array($members) && $members): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('team', 'titel')): ?><h2><?= sc_e((string) $get('team', 'titel')) ?></h2><?php endif; ?>
    <div class="cs-grid">
      <?php foreach ($members as $m): if (!is_array($m)) continue; ?>
      <div class="cs-card"><?= sc_img($media, $m['bild'] ?? null, '') ?><h3><?= sc_e((string) ($m['name'] ?? '')) ?></h3><p><?= sc_e((string) ($m['rolle'] ?? '')) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div></section>
  <?php endif; ?>

  <?php $zeiten = $get('oeffnungszeiten', 'zeiten', []); if (is_array($zeiten) && $zeiten): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('oeffnungszeiten', 'titel')): ?><h2><?= sc_e((string) $get('oeffnungszeiten', 'titel')) ?></h2><?php endif; ?>
    <ul class="cs-hours">
      <?php foreach ($zeiten as $tag => $wert): ?><li><span><?= sc_e((string) $tag) ?></span><span><?= sc_e((string) $wert) ?></span></li><?php endforeach; ?>
    </ul>
    <?php if ($get('oeffnungszeiten', 'hinweis')): ?><p><?= sc_e((string) $get('oeffnungszeiten', 'hinweis')) ?></p><?php endif; ?>
  </div></section>
  <?php endif; ?>

  <?php $posts = $get('beitraege', 'posts', []); if (is_array($posts) && $posts): ?>
  <section class="cs-sec"><div class="cs-wrap">
    <?php if ($get('beitraege', 'titel')): ?><h2><?= sc_e((string) $get('beitraege', 'titel')) ?></h2><?php endif; ?>
    <div class="cs-grid">
      <?php foreach ($posts as $p): if (!is_array($p)) continue; ?>
      <div class="cs-card"><?= sc_img($media, $p['bild'] ?? null, '') ?><p class="cs-ey"><?= sc_e((string) ($p['datum'] ?? '')) ?></p><h3><?= sc_e((string) ($p['titel'] ?? '')) ?></h3><p><?= nl2br(sc_e((string) ($p['text'] ?? ''))) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div></section>
  <?php endif; ?>

  <?php if (trim((string) $get('kontakt', 'titel')) !== '' || trim((string) $get('kontakt', 'adresse')) !== ''): ?>
  <section class="cs-sec" id="kontakt"><div class="cs-wrap">
    <?php if ($get('kontakt', 'titel')): ?><h2><?= sc_e((string) $get('kontakt', 'titel')) ?></h2><?php endif; ?>
    <?= sc_paragraphs((string) $get('kontakt', 'adresse')) ?>
    <?php if ($get('kontakt', 'telefon')): ?><p><a href="tel:<?= sc_e((string) $get('kontakt', 'telefon')) ?>"><?= sc_e((string) $get('kontakt', 'telefon')) ?></a></p><?php endif; ?>
    <?php if ($get('kontakt', 'email')): ?><p><a href="mailto:<?= sc_e((string) $get('kontakt', 'email')) ?>"><?= sc_e((string) $get('kontakt', 'email')) ?></a></p><?php endif; ?>
    <?php if ($get('kontakt', 'maps')): ?><p><a href="<?= sc_e((string) $get('kontakt', 'maps')) ?>" target="_blank" rel="noopener">Auf der Karte ansehen &rarr;</a></p><?php endif; ?>
  </div></section>
  <?php endif; ?>

  <footer class="cs-foot"><div class="cs-wrap"><?= sc_e((string) ($hero['headline'] ?? '')) ?> · Website von Sartu</div></footer>
</body>
</html>
<?php
}
