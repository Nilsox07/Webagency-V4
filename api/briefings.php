<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/http.php';

require_method('POST');
rate_limit('briefing-submit', 6, 3600);

$input = json_input(262144);
$payload = $input['payload'] ?? $input;
if (!is_array($payload)) {
    json_response(['ok' => false, 'error' => 'Payload fehlt.'], 400);
}

$kontakt = is_array($payload['kontakt'] ?? null) ? $payload['kontakt'] : [];
$email = isset($kontakt['email']) ? clean_email((string) $kontakt['email']) : null;
$name = isset($kontakt['name']) ? trim((string) $kontakt['name']) : null;

$id = uuidv4();
$stmt = db()->prepare(
    'insert into briefings (id, payload, kontakt_email, kontakt_name, status)
     values (?, ?, ?, ?, ?)'
);
$stmt->execute([
    $id,
    json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    $email ?: null,
    $name ?: null,
    'neu',
]);

json_response(['ok' => true, 'id' => $id], 201);
