<?php
declare(strict_types=1);

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function require_method(string $method): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== strtoupper($method)) {
        json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
    }
}

function json_input(int $maxBytes = 1048576): array
{
    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($contentLength > $maxBytes) {
        json_response(['ok' => false, 'error' => 'Die Anfrage ist zu groß.'], 413);
    }

    $raw = file_get_contents('php://input') ?: '';
    if ($raw === '') {
        return [];
    }
    if (strlen($raw) > $maxBytes) {
        json_response(['ok' => false, 'error' => 'Die Anfrage ist zu groß.'], 413);
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function client_ip(): string
{
    $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($forwarded !== '') {
        $parts = explode(',', (string) $forwarded);
        return trim($parts[0]);
    }
    return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

function rate_limit(string $bucket, int $limit, int $windowSeconds): void
{
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sartu-rate-limits';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }

    $key = hash('sha256', $bucket . '|' . client_ip());
    $file = $dir . DIRECTORY_SEPARATOR . $key . '.json';
    $now = time();
    $state = ['reset' => $now + $windowSeconds, 'count' => 0];

    $handle = @fopen($file, 'c+');
    if (!$handle) {
        return;
    }

    try {
        flock($handle, LOCK_EX);
        $raw = stream_get_contents($handle);
        $stored = $raw ? json_decode($raw, true) : null;
        if (is_array($stored) && (int) ($stored['reset'] ?? 0) > $now) {
            $state = [
                'reset' => (int) $stored['reset'],
                'count' => (int) ($stored['count'] ?? 0),
            ];
        }

        $state['count']++;
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode($state));
        fflush($handle);
        flock($handle, LOCK_UN);
    } finally {
        fclose($handle);
    }

    if ($state['count'] > $limit) {
        header('Retry-After: ' . max(1, $state['reset'] - $now));
        json_response(['ok' => false, 'error' => 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.'], 429);
    }
}

function clean_email(string $email): string
{
    return strtolower(trim($email));
}

function uuidv4(): string
{
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
