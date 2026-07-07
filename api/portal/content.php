<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/site-content.php';
require_once __DIR__ . '/../../includes/uploads.php';

$profile = require_profile();
$pdo = db();

/** Erstes Projekt des Kunden (oder null). */
function content_project(PDO $pdo, array $profile): ?array
{
    $stmt = $pdo->prepare('select id, titel, paket from projects where customer_id = ? order by created_at asc limit 1');
    $stmt->execute([$profile['id']]);
    return $stmt->fetch() ?: null;
}

/** Media-Liste (Bilder/PDFs) eines Projekts. */
function content_media(PDO $pdo, string $projectId): array
{
    $stmt = $pdo->prepare('select id, original_name, alt_text, mime, bytes from uploads where project_id = ? order by created_at desc');
    $stmt->execute([$projectId]);
    $out = [];
    foreach ($stmt->fetchAll() as $u) {
        $out[] = [
            'id' => $u['id'], 'original_name' => $u['original_name'], 'alt_text' => $u['alt_text'],
            'mime' => $u['mime'], 'bytes' => (int) $u['bytes'], 'url' => upload_public_url($u['id']),
        ];
    }
    return $out;
}

/** Kompakte Seitenliste fürs Portal. */
function content_pages(PDO $pdo, string $projectId): array
{
    $out = [];
    foreach (sc_project_pages($pdo, $projectId) as $p) {
        $out[] = [
            'slug' => $p['slug'], 'titel' => $p['titel'], 'nav_label' => $p['nav_label'],
            'typ' => $p['typ'], 'aktiv' => (int) $p['aktiv'] === 1,
        ];
    }
    return $out;
}

/** Gibt es projektweit unveröffentlichte Änderungen? */
function content_unpublished(PDO $pdo, string $projectId): bool
{
    $stmt = $pdo->prepare(
        'select count(*) as c from site_blocks b
         join site_pages p on p.id = b.page_id
         where p.project_id = ? and not (b.wert_draft <=> b.wert_published)'
    );
    $stmt->execute([$projectId]);
    return (int) ($stmt->fetch()['c'] ?? 0) > 0;
}

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    $project = content_project($pdo, $profile);
    if (!$project) {
        json_response(['ok' => true, 'has_project' => false, 'csrf' => csrf_token()]);
    }
    $projectId = (string) $project['id'];
    sc_ensure_pages($pdo, $projectId);

    $slug = preg_replace('~[^a-z0-9\-]~', '', strtolower((string) ($_GET['page'] ?? 'home'))) ?: 'home';
    $page = sc_page_by_slug($pdo, $projectId, $slug) ?: sc_page_by_slug($pdo, $projectId, 'home');
    if (!$page) {
        json_response(['ok' => false, 'error' => 'Keine Seite gefunden.'], 404);
    }

    $verStmt = $pdo->prepare('select id, created_at, anlass from site_page_versions where page_id = ? order by created_at desc limit 20');
    $verStmt->execute([$page['id']]);

    json_response([
        'ok' => true,
        'has_project' => true,
        'unpublished' => content_unpublished($pdo, $projectId),
        'pages' => content_pages($pdo, $projectId),
        'page' => [
            'id' => $page['id'], 'slug' => $page['slug'], 'titel' => $page['titel'],
            'vorlage' => $page['vorlage'], 'typ' => $page['typ'], 'aktiv' => (int) $page['aktiv'] === 1,
            'is_published' => (int) $page['is_published'] === 1, 'project_id' => $projectId,
        ],
        'template' => sartu_site_template((string) $page['vorlage']),
        'palette' => sartu_site_palette(),
        'content' => sc_load_content($pdo, $page, 'draft'),
        'media' => content_media($pdo, $projectId),
        'versions' => $verStmt->fetchAll(),
        'csrf' => csrf_token(),
    ]);
}

if ($method !== 'POST') {
    json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
}

require_csrf_token();
$input = json_input();
$action = (string) ($input['action'] ?? 'save');

$project = content_project($pdo, $profile);
if (!$project) {
    json_response(['ok' => false, 'error' => 'Kein Projekt vorhanden.'], 400);
}
$projectId = (string) $project['id'];
sc_ensure_pages($pdo, $projectId);

// Projektweite Aktionen zuerst.
if ($action === 'publish') {
    sc_publish_project($pdo, $projectId, (string) $profile['id']);
    json_response(['ok' => true, 'published' => true, 'csrf' => csrf_token()]);
}

if ($action === 'toggle_page') {
    $slug = preg_replace('~[^a-z0-9\-]~', '', strtolower((string) ($input['slug'] ?? ''))) ?: '';
    $pg = sc_page_by_slug($pdo, $projectId, $slug);
    if (!$pg) {
        json_response(['ok' => false, 'error' => 'Seite nicht gefunden.'], 404);
    }
    if (($pg['typ'] ?? '') !== 'inhalt') {
        json_response(['ok' => false, 'error' => 'Startseite und Pflichtseiten können nicht deaktiviert werden.'], 403);
    }
    sc_set_page_active($pdo, (string) $pg['id'], !empty($input['aktiv']));
    json_response(['ok' => true, 'csrf' => csrf_token()]);
}

if ($action === 'alt') {
    $uid = trim((string) ($input['upload_id'] ?? ''));
    $alt = mb_substr(trim((string) ($input['alt_text'] ?? '')), 0, 500);
    $pdo->prepare('update uploads set alt_text = ? where id = ? and project_id = ?')->execute([$alt, $uid, $projectId]);
    json_response(['ok' => true, 'csrf' => csrf_token()]);
}

if ($action === 'delete_media') {
    $uid = trim((string) ($input['upload_id'] ?? ''));
    $sel = $pdo->prepare('select storage_path from uploads where id = ? and project_id = ? limit 1');
    $sel->execute([$uid, $projectId]);
    $u = $sel->fetch();
    if ($u) {
        if (!empty($u['storage_path']) && is_file($u['storage_path'])) {
            @unlink($u['storage_path']);
        }
        $pdo->prepare('delete from uploads where id = ? and project_id = ?')->execute([$uid, $projectId]);
    }
    json_response(['ok' => true, 'csrf' => csrf_token()]);
}

// Ab hier seiten-bezogene Aktionen — Zielseite auflösen.
$slug = preg_replace('~[^a-z0-9\-]~', '', strtolower((string) ($input['page'] ?? 'home'))) ?: 'home';
$page = sc_page_by_slug($pdo, $projectId, $slug) ?: sc_page_by_slug($pdo, $projectId, 'home');
if (!$page) {
    json_response(['ok' => false, 'error' => 'Seite nicht gefunden.'], 404);
}
$pageId = (string) $page['id'];
$vorlage = (string) $page['vorlage'];
$fields = sartu_site_fields($vorlage);

if ($action === 'restore') {
    $vid = trim((string) ($input['version_id'] ?? ''));
    $done = $vid !== '' && sc_restore_version($pdo, $pageId, $vid);
    json_response(['ok' => $done, 'restored' => $done, 'content' => sc_load_content($pdo, $page, 'draft'), 'csrf' => csrf_token()]);
}

if ($action === 'toggle_section') {
    $section = (string) ($input['section'] ?? '');
    $sec = sartu_site_section($vorlage, $section);
    $mode = sartu_site_customer_mode($vorlage, $section);
    if (!$sec || !in_array($mode, ['edit', 'toggle'], true) || empty($sec['hideable'])) {
        json_response(['ok' => false, 'error' => 'Nicht erlaubt.'], 403);
    }
    sc_save_field($pdo, $pageId, $section, '__aktiv', !empty($input['aktiv']) ? '1' : '0');
    json_response(['ok' => true, 'csrf' => csrf_token()]);
}

if ($action === 'toggle_item') {
    $section = (string) ($input['section'] ?? '');
    $field = (string) ($input['field'] ?? '');
    $index = (int) ($input['index'] ?? -1);
    $key = $section . '.' . $field;
    if (!isset($fields[$key]) || ($fields[$key]['customer'] ?? null) !== 'toggle' || empty($fields[$key]['spec']['toggle_items'])) {
        json_response(['ok' => false, 'error' => 'Nicht erlaubt.'], 403);
    }
    $draft = sc_load_content($pdo, $page, 'draft');
    $list = $draft[$section][$field] ?? [];
    if (!is_array($list) || !isset($list[$index]) || !is_array($list[$index])) {
        json_response(['ok' => false, 'error' => 'Eintrag nicht gefunden.'], 400);
    }
    $list[$index]['_aktiv'] = !empty($input['aktiv']);
    sc_save_field($pdo, $pageId, $section, $field, array_values($list));
    json_response(['ok' => true, 'csrf' => csrf_token()]);
}

// action === 'save' : Entwurfs-Felder speichern (nur betriebliche 'edit'-Felder).
$incoming = $input['fields'] ?? [];
if (!is_array($incoming)) {
    json_response(['ok' => false, 'error' => 'Ungültige Daten.'], 400);
}

$saved = 0;
foreach ($incoming as $f) {
    if (!is_array($f)) {
        continue;
    }
    $section = (string) ($f['section'] ?? '');
    $field = (string) ($f['field'] ?? '');
    $key = $section . '.' . $field;
    if (!isset($fields[$key]) || ($fields[$key]['customer'] ?? null) !== 'edit') {
        continue;
    }
    $spec = $fields[$key]['spec'];
    $type = $fields[$key]['type'];
    $wert = $f['wert'] ?? null;

    if ($type === 'color') {
        $wert = is_string($wert) ? strtolower(trim($wert)) : '';
        $wert = ($wert !== '' && (sartu_site_palette_has($wert) || sartu_site_valid_hex($wert))) ? $wert : null;
    } elseif ($type === 'list') {
        $wert = is_array($wert) ? array_slice(array_values($wert), 0, (int) ($spec['max_items'] ?? 50)) : [];
    } elseif ($type === 'hours') {
        $wert = is_array($wert) ? $wert : [];
    } else {
        $wert = $wert === null ? null : (string) $wert;
        if ($wert !== null && isset($spec['max'])) {
            $wert = mb_substr($wert, 0, (int) $spec['max']);
        }
    }
    sc_save_field($pdo, $pageId, $section, $field, $wert);
    $saved++;
}

json_response(['ok' => true, 'saved' => $saved, 'csrf' => csrf_token()]);
