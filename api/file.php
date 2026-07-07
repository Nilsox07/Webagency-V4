<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/uploads.php';

require_method('GET');
$profile = require_profile();

$id = trim((string) ($_GET['id'] ?? ''));
if ($id === '') {
    json_response(['ok' => false, 'error' => 'Datei fehlt.'], 400);
}

$pdo = db();
$stmt = $pdo->prepare(
    'select u.id, u.project_id, u.storage_path, u.original_name, u.mime, p.customer_id
     from uploads u join projects p on p.id = u.project_id
     where u.id = ? limit 1'
);
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    json_response(['ok' => false, 'error' => 'Nicht gefunden.'], 404);
}
if (($profile['role'] ?? '') !== 'admin' && $row['customer_id'] !== $profile['id']) {
    json_response(['ok' => false, 'error' => 'Kein Zugriff.'], 403);
}

$path = (string) $row['storage_path'];
if ($path === '' || !is_file($path)) {
    json_response(['ok' => false, 'error' => 'Datei fehlt auf dem Server.'], 404);
}

$mime = (string) ($row['mime'] ?: 'application/octet-stream');
header('Content-Type: ' . $mime);
header('Content-Length: ' . (string) filesize($path));
header('Cache-Control: private, max-age=86400');
header('X-Content-Type-Options: nosniff');
if ($mime === 'application/pdf') {
    $name = preg_replace('~[^A-Za-z0-9._-]~', '_', (string) ($row['original_name'] ?: 'datei.pdf'));
    header('Content-Disposition: inline; filename="' . $name . '"');
}
readfile($path);
