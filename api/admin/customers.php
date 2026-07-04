<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_admin();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
    require_csrf_token();
}

if ($method === 'GET') {
    $profiles = db()->query('select id, created_at, email, name, firma, telefon, role, is_active from profiles order by created_at desc')->fetchAll();
    $projects = db()->query('select id, customer_id from projects')->fetchAll();
    json_response(['ok' => true, 'profiles' => $profiles, 'projects' => $projects]);
}

if ($method === 'PATCH') {
    $input = json_input();
    $id = trim((string) ($input['id'] ?? ''));
    if ($id === '') {
        json_response(['ok' => false, 'error' => 'Kunde fehlt.'], 400);
    }

    $stmt = db()->prepare('update profiles set name = ?, firma = ?, telefon = ? where id = ?');
    $stmt->execute([
        trim((string) ($input['name'] ?? '')) ?: null,
        trim((string) ($input['firma'] ?? '')) ?: null,
        trim((string) ($input['telefon'] ?? '')) ?: null,
        $id,
    ]);
    json_response(['ok' => true]);
}

json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
