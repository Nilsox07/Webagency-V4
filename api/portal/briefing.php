<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/briefing2-schema.php';

$profile = require_profile();
$pdo = db();

function briefing_project(PDO $pdo, array $profile): ?array
{
    $stmt = $pdo->prepare('select id from projects where customer_id = ? order by created_at asc limit 1');
    $stmt->execute([$profile['id']]);
    return $stmt->fetch() ?: null;
}

function briefing_row(PDO $pdo, string $projectId): array
{
    $stmt = $pdo->prepare('select answers, status from project_briefings where project_id = ? limit 1');
    $stmt->execute([$projectId]);
    $row = $stmt->fetch();
    if (!$row) {
        return ['answers' => [], 'status' => 'offen'];
    }
    $answers = $row['answers'] ? json_decode((string) $row['answers'], true) : [];
    return ['answers' => is_array($answers) ? $answers : [], 'status' => $row['status'] ?: 'offen'];
}

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$project = briefing_project($pdo, $profile);

if ($method === 'GET') {
    if (!$project) {
        json_response(['ok' => true, 'has_project' => false, 'csrf' => csrf_token()]);
    }
    $data = briefing_row($pdo, (string) $project['id']);
    json_response([
        'ok' => true,
        'has_project' => true,
        'project_id' => $project['id'],
        'answers' => $data['answers'],
        'status' => $data['status'],
        'csrf' => csrf_token(),
    ]);
}

if ($method !== 'POST') {
    json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
}

require_csrf_token();
if (!$project) {
    json_response(['ok' => false, 'error' => 'Kein Projekt vorhanden.'], 400);
}
$projectId = (string) $project['id'];
$input = json_input();
$action = (string) ($input['action'] ?? 'save');

// Eingehende Antworten gegen das Schema säubern.
$fields = sartu_briefing2_fields();
$incoming = is_array($input['answers'] ?? null) ? $input['answers'] : [];
$clean = [];
foreach ($incoming as $key => $val) {
    if (!isset($fields[$key])) {
        continue;
    }
    $spec = $fields[$key];
    $type = $spec['type'];
    if ($type === 'multi') {
        $opts = array_column($spec['options'] ?? [], 'value');
        $clean[$key] = is_array($val) ? array_values(array_intersect(array_map('strval', $val), $opts)) : [];
    } elseif ($type === 'choice') {
        $opts = array_column($spec['options'] ?? [], 'value');
        $clean[$key] = (is_string($val) && in_array($val, $opts, true)) ? $val : '';
    } elseif ($type === 'files') {
        $clean[$key] = is_array($val) ? array_slice(array_map('strval', array_values($val)), 0, 40) : [];
    } elseif ($type === 'file') {
        $clean[$key] = is_string($val) ? $val : '';
    } else {
        $s = (string) $val;
        if (isset($spec['max'])) {
            $s = mb_substr($s, 0, (int) $spec['max']);
        }
        $clean[$key] = $s;
    }
}

// Mit vorhandenen Antworten mischen (Teil-Speichern je Schritt möglich).
$existing = briefing_row($pdo, $projectId)['answers'];
$merged = array_merge($existing, $clean);

$status = ($action === 'submit') ? 'abgeschlossen' : 'offen';

$stmt = $pdo->prepare(
    'insert into project_briefings (id, project_id, answers, status, submitted_at)
     values (?, ?, ?, ?, ' . ($action === 'submit' ? 'now()' : 'null') . ')
     on duplicate key update answers = values(answers), status = values(status), updated_at = current_timestamp'
    . ($action === 'submit' ? ', submitted_at = now()' : '')
);
$stmt->execute([uuidv4(), $projectId, json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $status]);

json_response(['ok' => true, 'status' => $status, 'submitted' => $action === 'submit', 'csrf' => csrf_token()]);
