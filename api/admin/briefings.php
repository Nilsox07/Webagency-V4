<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_admin();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
    require_csrf_token();
}

if ($method === 'GET') {
    $rows = db()->query(
        'select id, created_at, updated_at, payload, status, kontakt_email, kontakt_name
         from briefings
         order by created_at desc'
    )->fetchAll();
    foreach ($rows as &$row) {
        $row['payload'] = $row['payload'] ? json_decode((string) $row['payload'], true) : null;
    }
    json_response(['ok' => true, 'briefings' => $rows]);
}

if ($method === 'PATCH') {
    $input = json_input();
    $id = (string) ($input['id'] ?? '');
    $status = (string) ($input['status'] ?? '');
    $allowed = ['neu', 'in_bearbeitung', 'umgewandelt', 'abgelehnt'];
    if ($id === '' || !in_array($status, $allowed, true)) {
        json_response(['ok' => false, 'error' => 'Ungültige Anfrage.'], 400);
    }

    $stmt = db()->prepare('update briefings set status = ? where id = ?');
    $stmt->execute([$status, $id]);
    json_response(['ok' => true]);
}

json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
