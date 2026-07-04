<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_method('POST');
rate_limit('auth-verify-login', 10, 900);

$input = json_input(8192);
$email = clean_email((string) ($input['email'] ?? ''));
$code = preg_replace('/\D+/', '', (string) ($input['code'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($code) !== 6) {
    json_response(['ok' => false, 'error' => 'Code ungültig oder abgelaufen. Fordern Sie einen neuen an.'], 400);
}

$profile = consume_login_code($email, $code);
if (!$profile) {
    json_response(['ok' => false, 'error' => 'Code ungültig oder abgelaufen. Fordern Sie einen neuen an.'], 401);
}

json_response([
    'ok' => true,
    'profile' => public_profile($profile),
    'csrf' => csrf_token(),
]);
