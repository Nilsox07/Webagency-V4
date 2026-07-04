<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

$profile = require_profile();

$stmt = db()->prepare(
    'select id, created_at, customer_id, titel, paket, care_stufe, phase, notiz_kunde, liefertermin
     from projects
     where customer_id = ?
     order by created_at asc'
);
$stmt->execute([$profile['id']]);

json_response([
    'ok' => true,
    'profile' => public_profile($profile),
    'projects' => $stmt->fetchAll(),
    'csrf' => csrf_token(),
]);
