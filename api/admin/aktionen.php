<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/aktionen.php';

require_admin();

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$pdo = db();

if ($method === 'GET') {
    json_response([
        'ok' => true,
        'aktionen' => aktionen_all($pdo),
        'ziele' => aktion_ziele(),
        'typen' => aktion_typen(),
        'csrf' => csrf_token(),
    ]);
}

require_csrf_token();
$in = json_input();

if ($method === 'DELETE') {
    $id = trim((string) ($in['id'] ?? ''));
    if ($id === '') { json_response(['ok' => false, 'error' => 'ID fehlt.'], 400); }
    $pdo->prepare('delete from aktionen where id = ?')->execute([$id]);
    json_response(['ok' => true]);
}

if ($method === 'PATCH') {
    // Nur aktiv/inaktiv umschalten.
    $id = trim((string) ($in['id'] ?? ''));
    if ($id === '') { json_response(['ok' => false, 'error' => 'ID fehlt.'], 400); }
    $aktiv = !empty($in['aktiv']) ? 1 : 0;
    $pdo->prepare('update aktionen set aktiv = ? where id = ?')->execute([$aktiv, $id]);
    json_response(['ok' => true]);
}

if ($method !== 'POST') {
    json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
}

// POST = anlegen oder aktualisieren (Upsert per id).
$id = trim((string) ($in['id'] ?? ''));
$name = trim((string) ($in['name'] ?? ''));
$typ = trim((string) ($in['typ'] ?? 'prozent'));
$ziel = trim((string) ($in['ziel'] ?? 'alle'));
$wert = (int) ($in['wert'] ?? 0);
$badge = trim((string) ($in['badge'] ?? ''));
$hinweis = trim((string) ($in['hinweis'] ?? ''));
$start = trim((string) ($in['start_am'] ?? ''));
$end = trim((string) ($in['end_am'] ?? ''));
$aktiv = !empty($in['aktiv']) ? 1 : 0;

if ($name === '') {
    json_response(['ok' => false, 'error' => 'Bitte einen Namen für die Aktion angeben.'], 400);
}
if (!array_key_exists($typ, aktion_typen())) { $typ = 'prozent'; }
if (!array_key_exists($ziel, aktion_ziele())) { $ziel = 'alle'; }
$wert = max(0, $wert);
if ($typ === 'prozent') { $wert = min(100, $wert); }

$vd = static function (string $d): ?string {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? $d : null;
};
$startVal = $vd($start);
$endVal = $vd($end);

if ($id !== '') {
    $pdo->prepare('update aktionen set name = ?, typ = ?, ziel = ?, wert = ?, badge = ?, hinweis = ?, start_am = ?, end_am = ?, aktiv = ? where id = ?')
        ->execute([$name, $typ, $ziel, $wert, $badge ?: null, $hinweis ?: null, $startVal, $endVal, $aktiv, $id]);
} else {
    $id = uuidv4();
    $pdo->prepare('insert into aktionen (id, name, typ, ziel, wert, badge, hinweis, start_am, end_am, aktiv) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([$id, $name, $typ, $ziel, $wert, $badge ?: null, $hinweis ?: null, $startVal, $endVal, $aktiv]);
}

json_response(['ok' => true, 'id' => $id]);
