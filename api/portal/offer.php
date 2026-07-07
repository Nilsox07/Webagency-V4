<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/site-content.php';

const SARTU_AGB_VERSION = 'AGB-2026-07';

$profile = require_profile();
$pdo = db();

function offer_public(array $o): array
{
    return [
        'id' => $o['id'],
        'titel' => $o['titel'],
        'paket' => $o['paket'],
        'preis_einmalig' => $o['preis_einmalig'] !== null ? (int) $o['preis_einmalig'] : null,
        'care_stufe' => $o['care_stufe'],
        'care_preis' => $o['care_preis'] !== null ? (int) $o['care_preis'] : null,
        'korrekturrunden' => $o['korrekturrunden'] !== null ? (int) $o['korrekturrunden'] : null,
        'umfang' => $o['umfang'],
        'liefertext' => $o['liefertext'],
        'hinweis' => $o['hinweis'],
        'gueltig_bis' => $o['gueltig_bis'],
        'status' => $o['status'],
    ];
}

/** Neuestes relevantes Angebot des Kunden (offen bevorzugt). */
function current_offer(PDO $pdo, string $customerId): ?array
{
    $stmt = $pdo->prepare('select * from angebote where customer_id = ? order by field(status, "gesendet", "angenommen") desc, created_at desc limit 1');
    $stmt->execute([$customerId]);
    return $stmt->fetch() ?: null;
}

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    $offer = current_offer($pdo, (string) $profile['id']);
    json_response([
        'ok' => true,
        'has_offer' => (bool) $offer,
        'offer' => $offer ? offer_public($offer) : null,
        'agb_version' => SARTU_AGB_VERSION,
        'csrf' => csrf_token(),
    ]);
}

if ($method !== 'POST') {
    json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
}

require_csrf_token();
$in = json_input();
$action = (string) ($in['action'] ?? 'accept');

$stmt = $pdo->prepare('select * from angebote where customer_id = ? and status = ? order by created_at desc limit 1');
$stmt->execute([(string) $profile['id'], 'gesendet']);
$offer = $stmt->fetch();
if (!$offer) {
    json_response(['ok' => false, 'error' => 'Kein offenes Angebot vorhanden.'], 400);
}

if ($action === 'decline') {
    $pdo->prepare('update angebote set status = ? where id = ?')->execute(['abgelehnt', $offer['id']]);
    json_response(['ok' => true, 'declined' => true, 'csrf' => csrf_token()]);
}

// Verbindliche Annahme.
if (empty($in['agb'])) {
    json_response(['ok' => false, 'error' => 'Bitte bestätigen Sie AGB und Widerrufsbelehrung.'], 400);
}

$snapshot = json_encode(offer_public($offer) + ['angenommen_von' => $profile['email']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$projectId = uuidv4();

$pdo->beginTransaction();
try {
    $pdo->prepare(
        'insert into projects (id, customer_id, titel, paket, care_stufe, phase) values (?, ?, ?, ?, ?, ?)'
    )->execute([$projectId, $profile['id'], $offer['titel'], $offer['paket'], $offer['care_stufe'], 'angebot_bestaetigt']);

    $pdo->prepare(
        'update angebote set status = ?, angenommen_am = now(), angenommen_ip = ?, agb_version = ?, snapshot = ?, project_id = ? where id = ?'
    )->execute(['angenommen', client_ip(), SARTU_AGB_VERSION, $snapshot, $projectId, $offer['id']]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

// Pflichtseiten anlegen + Rechtsseiten vorbelegen (schaltet Briefing/Editor frei).
sc_ensure_pages($pdo, $projectId);

json_response(['ok' => true, 'accepted' => true, 'project_id' => $projectId, 'csrf' => csrf_token()]);
