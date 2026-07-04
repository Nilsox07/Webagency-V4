<?php
declare(strict_types=1);

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$scriptDir = rtrim($scriptDir, '/');
$path = $uriPath;

if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($path, $scriptDir . '/')) {
    $path = substr($path, strlen($scriptDir));
}

$path = trim(rawurldecode($path), '/');
if ($path === '') {
    require __DIR__ . '/index.php';
    return true;
}

$blockedFile = basename($path);
if (
    preg_match('/\.(md|sql|log)$/i', $blockedFile)
    || preg_match('/^(\.env|.*\.env.*|config\.local(\.example)?\.php)$/i', $blockedFile)
    || str_starts_with($path, '.git/')
) {
    http_response_code(404);
    return true;
}

if ($path === 'briefing') {
    header('Location: anfrage.php', true, 308);
    return true;
}

$file = __DIR__ . '/' . $path;
if (is_file($file)) {
    return false;
}

if (str_ends_with($path, '.html')) {
    $phpFile = __DIR__ . '/' . substr($path, 0, -5) . '.php';
    if (is_file($phpFile)) {
        require $phpFile;
        return true;
    }
}

if (is_file($file . '.php')) {
    require $file . '.php';
    return true;
}

http_response_code(404);
return false;
