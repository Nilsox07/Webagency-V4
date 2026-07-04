<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/http.php';

function start_app_session(): void
{
    $name = (string) (app_config()['app']['session_name'] ?? 'sartu_session');
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name($name);
        ini_set('session.use_strict_mode', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function public_base_url(): string
{
    $configured = app_config()['mail']['base_url'] ?? '';
    if ($configured !== '') {
        return rtrim((string) $configured, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    $dir = preg_replace('~/api(?:/.*)?$~', '', $dir) ?: '';
    return rtrim($scheme . '://' . $host . $dir, '/');
}

function public_profile(array $profile): array
{
    return [
        'id' => $profile['id'] ?? null,
        'email' => $profile['email'] ?? null,
        'name' => $profile['name'] ?? null,
        'firma' => $profile['firma'] ?? null,
        'telefon' => $profile['telefon'] ?? null,
        'role' => $profile['role'] ?? 'customer',
    ];
}

function current_profile(): ?array
{
    start_app_session();
    $id = $_SESSION['profile_id'] ?? null;
    if (!$id) {
        return null;
    }
    $stmt = db()->prepare('select * from profiles where id = ? and is_active = 1 limit 1');
    $stmt->execute([(string) $id]);
    $profile = $stmt->fetch();
    return $profile ?: null;
}

function require_profile(): array
{
    $profile = current_profile();
    if (!$profile) {
        json_response(['ok' => false, 'error' => 'Bitte melden Sie sich an.'], 401);
    }
    return $profile;
}

function require_admin(): array
{
    $profile = require_profile();
    if (($profile['role'] ?? '') !== 'admin') {
        json_response(['ok' => false, 'error' => 'Kein Admin-Zugang.'], 403);
    }
    return $profile;
}

function csrf_token(): string
{
    start_app_session();
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function require_csrf_token(): void
{
    start_app_session();
    $sent = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $stored = (string) ($_SESSION['csrf_token'] ?? '');
    if ($sent === '' || $stored === '' || !hash_equals($stored, $sent)) {
        json_response(['ok' => false, 'error' => 'Sicherheitsprüfung fehlgeschlagen. Bitte laden Sie die Seite neu.'], 419);
    }
}

function logout_profile(): void
{
    start_app_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'] ?: '/',
            'domain' => $params['domain'] ?? '',
            'secure' => (bool) ($params['secure'] ?? false),
            'httponly' => (bool) ($params['httponly'] ?? true),
            'samesite' => 'Lax',
        ]);
    }
    session_destroy();
}

function create_login_token(array $profile): array
{
    $code = (string) random_int(100000, 999999);
    $linkToken = bin2hex(random_bytes(32));
    $id = uuidv4();
    $expires = (new DateTimeImmutable('+20 minutes'))->format('Y-m-d H:i:s');

    $stmt = db()->prepare(
        'insert into login_tokens (id, profile_id, email, code_hash, link_hash, expires_at)
         values (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $id,
        $profile['id'],
        clean_email((string) $profile['email']),
        hash('sha256', $code),
        hash('sha256', $linkToken),
        $expires,
    ]);

    return ['code' => $code, 'link_token' => $linkToken];
}

function send_login_mail(array $profile, string $code, string $linkToken): bool
{
    $mail = app_config()['mail'];
    $base = public_base_url();
    $link = $base . '/auth-callback.php?token=' . rawurlencode($linkToken);
    $name = trim((string) ($profile['name'] ?: $profile['email']));

    $subject = 'Ihr Sartu Login-Code';
    $body = "Hallo {$name},\n\n"
        . "Ihr Login-Code lautet: {$code}\n\n"
        . "Alternativ können Sie diesen Link öffnen:\n{$link}\n\n"
        . "Der Code ist 20 Minuten gültig.\n\n"
        . "Viele Grüße\nSartu";

    $headers = [
        'From: Sartu <' . $mail['from'] . '>',
        'Reply-To: ' . $mail['reply_to'],
        'Content-Type: text/plain; charset=UTF-8',
    ];

    return mail((string) $profile['email'], $subject, $body, implode("\r\n", $headers));
}

function consume_login_code(string $email, string $code): ?array
{
    $stmt = db()->prepare(
        'select t.*, p.email, p.name, p.firma, p.telefon, p.role, p.is_active
         from login_tokens t
         join profiles p on p.id = t.profile_id
         where t.email = ? and t.code_hash = ? and t.consumed_at is null and t.expires_at > now() and p.is_active = 1
         order by t.created_at desc
         limit 1'
    );
    $stmt->execute([clean_email($email), hash('sha256', $code)]);
    $row = $stmt->fetch();
    return $row ? consume_token_row($row) : null;
}

function consume_login_link(string $linkToken): ?array
{
    $stmt = db()->prepare(
        'select t.*, p.email, p.name, p.firma, p.telefon, p.role, p.is_active
         from login_tokens t
         join profiles p on p.id = t.profile_id
         where t.link_hash = ? and t.consumed_at is null and t.expires_at > now() and p.is_active = 1
         limit 1'
    );
    $stmt->execute([hash('sha256', $linkToken)]);
    $row = $stmt->fetch();
    return $row ? consume_token_row($row) : null;
}

function consume_token_row(array $row): array
{
    $stmt = db()->prepare('update login_tokens set consumed_at = now() where id = ?');
    $stmt->execute([$row['id']]);

    start_app_session();
    session_regenerate_id(true);
    $_SESSION['profile_id'] = $row['profile_id'];
    csrf_token();

    return [
        'id' => $row['profile_id'],
        'email' => $row['email'],
        'name' => $row['name'],
        'firma' => $row['firma'],
        'telefon' => $row['telefon'],
        'role' => $row['role'],
        'is_active' => $row['is_active'],
    ];
}
