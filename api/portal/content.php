<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/site-content.php';
require_once __DIR__ . '/../../includes/uploads.php';

$profile = require_profile();
$pdo = db();

/** Erstes Projekt des Kunden + zugehörige Editor-Seite holen (legt sie an). */
function content_page_for(PDO $pdo, array $profile): ?array
{
    $stmt = $pdo->prepare('select id, titel, paket from projects where customer_id = ? order by created_at asc limit 1');
    $stmt->execute([$profile['id']]);
    $project = $stmt->fetch();
    if (!$project) {
        return null;
    }
    $page = sc_get_or_create_page($pdo, $project['id'], 'home', 'standard');
    $page['_project'] = $project;
    return $page;
}

/** Media-Liste (Bilder/PDFs) eines Projekts. */
function content_media(PDO $pdo, string $projectId): array
{
    $stmt = $pdo->prepare('select id, original_name, alt_text, mime, bytes from uploads where project_id = ? order by created_at desc');
    $stmt->execute([$projectId]);
    $out = [];
    foreach ($stmt->fetchAll() as $u) {
        $out[] = [
            'id' => $u['id'],
            'original_name' => $u['original_name'],
            'alt_text' => $u['alt_text'],
            'mime' => $u['mime'],
            'bytes' => (int) $u['bytes'],
            'url' => upload_public_url($u['id']),
        ];
    }
    return $out;
}

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    $page = content_page_for($pdo, $profile);
    if (!$page) {
        json_response(['ok' => true, 'has_project' => false, 'csrf' => csrf_token()]);
    }
    $vorlage = (string) $page['vorlage'];
    $verStmt = $pdo->prepare('select id, created_at, anlass from site_page_versions where page_id = ? order by created_at desc limit 20');
    $verStmt->execute([$page['id']]);

    $diff = $pdo->prepare('select count(*) as c from site_blocks where page_id = ? and not (wert_draft <=> wert_published)');
    $diff->execute([$page['id']]);
    $unpublished = (int) ($diff->fetch()['c'] ?? 0) > 0;

    json_response([
        'ok' => true,
        'has_project' => true,
        'unpublished' => $unpublished,
        'page' => [
            'id' => $page['id'],
            'slug' => $page['slug'],
            'vorlage' => $vorlage,
            'is_published' => (int) $page['is_published'] === 1,
            'project_id' => $page['_project']['id'],
        ],
        'template' => sartu_site_template($vorlage),
        'palette' => sartu_site_palette(),
        'content' => sc_load_content($pdo, $page, 'draft'),
        'media' => content_media($pdo, (string) $page['_project']['id']),
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

$page = content_page_for($pdo, $profile);
if (!$page) {
    json_response(['ok' => false, 'error' => 'Kein Projekt vorhanden.'], 400);
}
$pageId = (string) $page['id'];
$vorlage = (string) $page['vorlage'];
$fields = sartu_site_fields($vorlage);

if ($action === 'publish') {
    sc_publish_page($pdo, $pageId, (string) $profile['id']);
    json_response(['ok' => true, 'published' => true, 'csrf' => csrf_token()]);
}

if ($action === 'restore') {
    $vid = trim((string) ($input['version_id'] ?? ''));
    $done = $vid !== '' && sc_restore_version($pdo, $pageId, $vid);
    json_response(['ok' => $done, 'restored' => $done, 'content' => sc_load_content($pdo, $page, 'draft'), 'csrf' => csrf_token()]);
}

if ($action === 'alt') {
    $uid = trim((string) ($input['upload_id'] ?? ''));
    $alt = mb_substr(trim((string) ($input['alt_text'] ?? '')), 0, 500);
    $stmt = $pdo->prepare('update uploads set alt_text = ? where id = ? and project_id = ?');
    $stmt->execute([$alt, $uid, (string) $page['_project']['id']]);
    json_response(['ok' => true, 'csrf' => csrf_token()]);
}

if ($action === 'delete_media') {
    $uid = trim((string) ($input['upload_id'] ?? ''));
    $sel = $pdo->prepare('select storage_path from uploads where id = ? and project_id = ? limit 1');
    $sel->execute([$uid, (string) $page['_project']['id']]);
    $u = $sel->fetch();
    if ($u) {
        if (!empty($u['storage_path']) && is_file($u['storage_path'])) {
            @unlink($u['storage_path']);
        }
        $pdo->prepare('delete from uploads where id = ? and project_id = ?')->execute([$uid, (string) $page['_project']['id']]);
    }
    json_response(['ok' => true, 'csrf' => csrf_token()]);
}

// action === 'save' : Entwurfs-Felder speichern.
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
    if (!isset($fields[$key])) {
        continue; // nur bekannte Felder aus dem Schema
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
