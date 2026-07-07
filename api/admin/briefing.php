<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_method('GET');
require_admin();

$projectId = trim((string) ($_GET['project_id'] ?? ''));
if ($projectId === '') {
    json_response(['ok' => false, 'error' => 'Projekt fehlt.'], 400);
}

$pdo = db();
// Briefing hängt am Kunden des Projekts (project_briefings.project_id ist die Projekt-ID,
// aber das Onboarding speichert je Kundenprojekt — hier über das Projekt auflösen).
$stmt = $pdo->prepare(
    'select pb.answers, pb.status, pb.submitted_at
     from project_briefings pb
     where pb.project_id = ? limit 1'
);
$stmt->execute([$projectId]);
$row = $stmt->fetch();

if (!$row) {
    // Onboarding speichert das Briefing am (ersten) Projekt des Kunden — Fallback über den Kunden.
    $cust = $pdo->prepare('select customer_id from projects where id = ? limit 1');
    $cust->execute([$projectId]);
    $c = $cust->fetch();
    if ($c) {
        $alt = $pdo->prepare(
            'select pb.answers, pb.status, pb.submitted_at
             from project_briefings pb
             join projects p on p.id = pb.project_id
             where p.customer_id = ? order by pb.updated_at desc limit 1'
        );
        $alt->execute([$c['customer_id']]);
        $row = $alt->fetch();
    }
}

if (!$row) {
    json_response(['ok' => true, 'has_briefing' => false, 'csrf' => csrf_token()]);
}

$answers = $row['answers'] ? json_decode((string) $row['answers'], true) : [];

json_response([
    'ok' => true,
    'has_briefing' => true,
    'answers' => is_array($answers) ? $answers : [],
    'status' => $row['status'] ?: 'offen',
    'submitted_at' => $row['submitted_at'],
    'csrf' => csrf_token(),
]);
