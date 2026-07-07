<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_admin();

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$pdo = db();

if ($method === 'GET') {
    $stmt = $pdo->query(
        'select a.*, p.email, p.name, p.firma
         from angebote a join profiles p on p.id = a.customer_id
         order by a.created_at desc'
    );
    json_response(['ok' => true, 'offers' => $stmt->fetchAll(), 'csrf' => csrf_token()]);
}

if ($method !== 'POST') {
    json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
}

require_csrf_token();
$in = json_input();

$briefingId = trim((string) ($in['briefing_id'] ?? '')) ?: null;
$email = clean_email((string) ($in['email'] ?? ''));
$name = trim((string) ($in['name'] ?? ''));
$titel = trim((string) ($in['titel'] ?? ''));
$paket = trim((string) ($in['paket'] ?? ''));
$preis = isset($in['preis_einmalig']) && $in['preis_einmalig'] !== '' ? (int) $in['preis_einmalig'] : null;
$care = trim((string) ($in['care_stufe'] ?? ''));
$carePreis = isset($in['care_preis']) && $in['care_preis'] !== '' ? (int) $in['care_preis'] : null;
$korr = isset($in['korrekturrunden']) && $in['korrekturrunden'] !== '' ? (int) $in['korrekturrunden'] : null;
$umfang = trim((string) ($in['umfang'] ?? ''));
$liefertext = trim((string) ($in['liefertext'] ?? ''));
$hinweis = trim((string) ($in['hinweis'] ?? ''));
$gueltig = trim((string) ($in['gueltig_bis'] ?? '')) ?: null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $titel === '' || $paket === '') {
    json_response(['ok' => false, 'error' => 'Bitte E-Mail, Titel und Paket angeben.'], 400);
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('select * from profiles where email = ? limit 1');
    $stmt->execute([$email]);
    $profile = $stmt->fetch();

    if (!$profile) {
        $profile = ['id' => uuidv4(), 'email' => $email, 'name' => $name ?: null];
        $pdo->prepare('insert into profiles (id, email, name, role, is_active) values (?, ?, ?, ?, 1)')
            ->execute([$profile['id'], $email, $profile['name'], 'customer']);
    } elseif (($profile['role'] ?? '') !== 'admin') {
        $pdo->prepare('update profiles set name = coalesce(nullif(?, \'\'), name), is_active = 1 where id = ?')
            ->execute([$name, $profile['id']]);
    }

    $offerId = uuidv4();
    $pdo->prepare(
        'insert into angebote (id, briefing_id, customer_id, titel, paket, preis_einmalig, care_stufe, care_preis, korrekturrunden, umfang, liefertext, hinweis, gueltig_bis, status)
         values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute([$offerId, $briefingId, $profile['id'], $titel, $paket, $preis, $care ?: null, $carePreis, $korr, $umfang ?: null, $liefertext ?: null, $hinweis ?: null, $gueltig, 'gesendet']);

    if ($briefingId) {
        $pdo->prepare('update briefings set status = ? where id = ? and status = ?')->execute(['in_bearbeitung', $briefingId, 'neu']);
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

$token = create_login_token($profile);
$sent = send_offer_mail($profile, $token['code'], $token['link_token']);

json_response(['ok' => true, 'offer_id' => $offerId, 'customer_id' => $profile['id'], 'mail_sent' => $sent]);
