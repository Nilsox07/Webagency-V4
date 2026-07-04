<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

$profile = current_profile();
if (!$profile) {
    json_response(['ok' => true, 'authenticated' => false]);
}

json_response([
    'ok' => true,
    'authenticated' => true,
    'profile' => public_profile($profile),
    'csrf' => csrf_token(),
]);
