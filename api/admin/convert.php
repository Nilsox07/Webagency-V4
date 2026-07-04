<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_method('POST');
require_admin();
require_csrf_token();

$input = json_input();
$briefingId = trim((string) ($input['briefing_id'] ?? ''));
$email = clean_email((string) ($input['email'] ?? ''));
$name = trim((string) ($input['name'] ?? ''));
$titel = trim((string) ($input['titel'] ?? ''));
$paket = trim((string) ($input['paket'] ?? ''));
$care = trim((string) ($input['care_stufe'] ?? ''));

if ($briefingId === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $titel === '') {
    json_response(['ok' => false, 'error' => 'Bitte E-Mail, Titel und Anfrage angeben.'], 400);
}

$pdo = db();
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('select * from profiles where email = ? limit 1');
    $stmt->execute([$email]);
    $profile = $stmt->fetch();

    if (!$profile) {
        $profile = [
            'id' => uuidv4(),
            'email' => $email,
            'name' => $name ?: null,
            'firma' => null,
            'telefon' => null,
            'role' => 'customer',
            'is_active' => 1,
        ];
        $ins = $pdo->prepare('insert into profiles (id, email, name, role, is_active) values (?, ?, ?, ?, 1)');
        $ins->execute([$profile['id'], $profile['email'], $profile['name'], 'customer']);
    } elseif (($profile['role'] ?? '') !== 'admin') {
        $upd = $pdo->prepare('update profiles set name = coalesce(nullif(?, \'\'), name), is_active = 1 where id = ?');
        $upd->execute([$name, $profile['id']]);
        $profile['name'] = $name ?: $profile['name'];
    }

    $projectId = uuidv4();
    $project = $pdo->prepare(
        'insert into projects (id, customer_id, titel, paket, care_stufe, phase)
         values (?, ?, ?, ?, ?, ?)'
    );
    $project->execute([$projectId, $profile['id'], $titel, $paket ?: null, $care ?: null, 'angebot_bestaetigt']);

    $briefing = $pdo->prepare('update briefings set status = ? where id = ?');
    $briefing->execute(['umgewandelt', $briefingId]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

$token = create_login_token($profile);
$inviteSent = send_login_mail($profile, $token['code'], $token['link_token']);

json_response([
    'ok' => true,
    'project_id' => $projectId,
    'customer_id' => $profile['id'],
    'invite_sent' => $inviteSent,
]);
