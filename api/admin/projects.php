<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_admin();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
    require_csrf_token();
}

if ($method === 'GET') {
    $projects = db()->query(
        'select id, created_at, customer_id, titel, paket, care_stufe, phase, notiz_kunde, notiz_intern, liefertermin
         from projects
         order by created_at desc'
    )->fetchAll();
    $profiles = db()->query('select id, created_at, email, name, firma, telefon, role, is_active from profiles order by created_at desc')->fetchAll();
    json_response(['ok' => true, 'projects' => $projects, 'profiles' => $profiles]);
}

if ($method === 'PATCH') {
    $input = json_input();
    $id = trim((string) ($input['id'] ?? ''));
    if ($id === '') {
        json_response(['ok' => false, 'error' => 'Projekt fehlt.'], 400);
    }

    $allowedPhases = ['angebot_bestaetigt','inhalte_liefern','design_laeuft','korrektur_1','korrektur_2','korrektur_3','korrektur_4','finalisierung','live'];
    $phase = (string) ($input['phase'] ?? '');
    if (!in_array($phase, $allowedPhases, true)) {
        json_response(['ok' => false, 'error' => 'Ungültige Phase.'], 400);
    }

    $liefertermin = trim((string) ($input['liefertermin'] ?? '')) ?: null;
    $stmt = db()->prepare(
        'update projects
         set phase = ?, liefertermin = ?, notiz_kunde = ?, notiz_intern = ?
         where id = ?'
    );
    $stmt->execute([
        $phase,
        $liefertermin,
        ($input['notiz_kunde'] ?? '') !== '' ? (string) $input['notiz_kunde'] : null,
        ($input['notiz_intern'] ?? '') !== '' ? (string) $input['notiz_intern'] : null,
        $id,
    ]);
    json_response(['ok' => true]);
}

json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
