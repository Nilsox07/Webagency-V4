<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/uploads.php';

require_method('POST');
$profile = require_profile();
require_csrf_token();
rate_limit('upload', 60, 3600);

$projectId = trim((string) ($_POST['project_id'] ?? ''));
$typ = trim((string) ($_POST['typ'] ?? 'media')) ?: 'media';

if ($projectId === '') {
    json_response(['ok' => false, 'error' => 'Projekt fehlt.'], 400);
}

$pdo = db();
$project = upload_project_for($pdo, $profile, $projectId);
if (!$project) {
    json_response(['ok' => false, 'error' => 'Kein Zugriff auf dieses Projekt.'], 403);
}

if (!isset($_FILES['file']) || !is_array($_FILES['file']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    json_response(['ok' => false, 'error' => 'Keine Datei empfangen.'], 400);
}

$file = $_FILES['file'];
if (!is_uploaded_file($file['tmp_name'])) {
    json_response(['ok' => false, 'error' => 'Ungültiger Upload.'], 400);
}
if ((int) $file['size'] > upload_max_bytes()) {
    json_response(['ok' => false, 'error' => 'Datei zu groß (max. 8 MB).'], 413);
}

$mime = upload_detect_mime($file['tmp_name']);
$allowed = upload_allowed_types();
if (!isset($allowed[$mime])) {
    json_response(['ok' => false, 'error' => 'Dateityp nicht erlaubt (Bilder oder PDF).'], 415);
}
$ext = $allowed[$mime];

$id = uuidv4();
$dir = upload_storage_dir();
$destPath = $dir . '/' . $id . '.' . $ext;

$isImage = strpos($mime, 'image/') === 0;
$processed = false;
if ($isImage) {
    $processed = upload_process_image($file['tmp_name'], $destPath, $mime);
}
if (!$processed) {
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        json_response(['ok' => false, 'error' => 'Datei konnte nicht gespeichert werden.'], 500);
    }
}

$originalName = (string) ($file['name'] ?? '');
$altText = $isImage ? upload_auto_alt($originalName) : '';
$bytes = is_file($destPath) ? (int) filesize($destPath) : (int) $file['size'];

$stmt = $pdo->prepare(
    'insert into uploads (id, project_id, typ, storage_path, original_name, alt_text, mime, bytes, hochgeladen_von)
     values (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$id, $projectId, $typ, $destPath, $originalName, $altText ?: null, $mime, $bytes, $profile['id']]);

json_response([
    'ok' => true,
    'upload' => [
        'id' => $id,
        'typ' => $typ,
        'original_name' => $originalName,
        'alt_text' => $altText,
        'mime' => $mime,
        'bytes' => $bytes,
        'url' => upload_public_url($id),
    ],
    'csrf' => csrf_token(),
], 201);
