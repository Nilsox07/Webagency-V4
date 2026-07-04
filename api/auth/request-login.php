<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_method('POST');
rate_limit('auth-request-login', 5, 900);

$input = json_input(8192);
$email = clean_email((string) ($input['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['ok' => false, 'error' => 'Bitte eine gültige E-Mail eingeben.'], 400);
}

$stmt = db()->prepare('select * from profiles where email = ? and is_active = 1 limit 1');
$stmt->execute([$email]);
$profile = $stmt->fetch();
if (!$profile) {
    json_response(['ok' => false, 'error' => 'Für diese E-Mail gibt es keinen Zugang. Bitte melden Sie sich bei Sartu.'], 404);
}

$token = create_login_token($profile);
$sent = send_login_mail($profile, $token['code'], $token['link_token']);
if (!$sent) {
    $response = ['ok' => false, 'error' => 'Die Login-E-Mail konnte nicht versendet werden. Bitte Mailversand auf dem Server konfigurieren.'];
    if (app_config()['app']['debug']) {
        $response['debug_code'] = $token['code'];
        $response['debug_link'] = public_base_url() . '/auth-callback.php?token=' . rawurlencode($token['link_token']);
    }
    json_response($response, 500);
}

json_response(['ok' => true]);
