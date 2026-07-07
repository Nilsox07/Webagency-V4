<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Datei-Upload-Helfer (Stufe 1, F1).
 * Dateien landen in einem nicht öffentlichen Ordner und werden nur über
 * api/file.php (besitz-geprüft) ausgeliefert — nie direkt per URL.
 */

/** Basis-Speicherordner (überschreibbar via SARTU_STORAGE_PATH). */
function upload_storage_dir(): string
{
    $base = getenv('SARTU_STORAGE_PATH');
    if (!$base) {
        $base = dirname(__DIR__) . '/storage';
    }
    $dir = rtrim($base, '/\\') . '/uploads';
    if (!is_dir($dir)) {
        @mkdir($dir, 0770, true);
    }
    return $dir;
}

/** Erlaubte MIME-Typen → Dateiendung. */
function upload_allowed_types(): array
{
    return [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
    ];
}

function upload_max_bytes(): int
{
    return 8 * 1024 * 1024; // 8 MB
}

/** MIME echt aus dem Dateiinhalt bestimmen (nicht dem Client vertrauen). */
function upload_detect_mime(string $tmpPath): string
{
    if (function_exists('finfo_open')) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        if ($fi) {
            $mime = (string) finfo_file($fi, $tmpPath);
            finfo_close($fi);
            return $mime;
        }
    }
    $img = @getimagesize($tmpPath);
    return $img['mime'] ?? 'application/octet-stream';
}

/** Automatischer Alt-Text als Fallback aus dem Originalnamen. */
function upload_auto_alt(string $originalName): string
{
    $name = pathinfo($originalName, PATHINFO_FILENAME);
    $name = preg_replace('~[_\-]+~', ' ', $name);
    $name = preg_replace('~\s+~', ' ', trim((string) $name));
    if ($name === '') {
        return '';
    }
    return ucfirst($name);
}

/**
 * Bild verkleinern/neu kodieren (Ladezeit), wenn GD verfügbar ist.
 * Gibt true zurück, wenn die Zieldatei geschrieben wurde.
 */
function upload_process_image(string $srcPath, string $destPath, string $mime, int $maxDim = 1800): bool
{
    if (!function_exists('imagecreatetruecolor')) {
        return false;
    }
    switch ($mime) {
        case 'image/jpeg': $img = @imagecreatefromjpeg($srcPath); break;
        case 'image/png':  $img = @imagecreatefrompng($srcPath); break;
        case 'image/webp': $img = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : false; break;
        case 'image/gif':  $img = @imagecreatefromgif($srcPath); break;
        default: return false;
    }
    if (!$img) {
        return false;
    }
    $w = imagesx($img);
    $h = imagesy($img);
    $scale = min(1.0, $maxDim / max($w, $h));
    if ($scale < 1.0) {
        $nw = (int) round($w * $scale);
        $nh = (int) round($h * $scale);
        $resized = imagecreatetruecolor($nw, $nh);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($img);
        $img = $resized;
    }
    $ok = false;
    if ($mime === 'image/png') {
        $ok = imagepng($img, $destPath, 6);
    } elseif ($mime === 'image/gif') {
        $ok = imagegif($img, $destPath);
    } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
        $ok = imagewebp($img, $destPath, 82);
    } else {
        $ok = imagejpeg($img, $destPath, 82);
    }
    imagedestroy($img);
    return (bool) $ok;
}

/** Zugriffs-URL für einen Upload. */
function upload_public_url(string $id): string
{
    return 'api/file.php?id=' . rawurlencode($id);
}

/**
 * Prüft, ob $profile auf $projectId zugreifen darf (eigener Kunde oder Admin).
 * Gibt die Projektzeile zurück oder null.
 */
function upload_project_for(PDO $pdo, array $profile, string $projectId): ?array
{
    $stmt = $pdo->prepare('select id, customer_id from projects where id = ? limit 1');
    $stmt->execute([$projectId]);
    $project = $stmt->fetch();
    if (!$project) {
        return null;
    }
    if (($profile['role'] ?? '') === 'admin') {
        return $project;
    }
    return ($project['customer_id'] === $profile['id']) ? $project : null;
}
