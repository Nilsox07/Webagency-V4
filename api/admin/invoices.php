<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/billing.php';

require_admin();
$pdo = db();
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    $stmt = $pdo->query(
        'select i.id, i.nummer, i.status, i.ausgestellt_am, i.faellig_am, i.brutto_cent, p.name, p.email, p.firma
         from invoices i join profiles p on p.id = i.customer_id
         order by i.created_at desc'
    );
    $out = [];
    foreach ($stmt->fetchAll() as $r) {
        $out[] = [
            'id' => $r['id'], 'nummer' => $r['nummer'], 'status' => $r['status'],
            'ausgestellt_am' => $r['ausgestellt_am'], 'faellig_am' => $r['faellig_am'],
            'betrag' => money_de((int) $r['brutto_cent']),
            'kunde' => $r['firma'] ?: ($r['name'] ?: $r['email']),
        ];
    }
    json_response(['ok' => true, 'invoices' => $out, 'payments_enabled' => billing_payments_enabled(), 'csrf' => csrf_token()]);
}

require_csrf_token();
$input = json_input();
$action = (string) ($input['action'] ?? '');

if ($method === 'POST' && $action === 'create_offer') {
    $offerId = trim((string) ($input['offer_id'] ?? ''));
    $stmt = $pdo->prepare('select * from angebote where id = ? limit 1');
    $stmt->execute([$offerId]);
    $offer = $stmt->fetch();
    if (!$offer) {
        json_response(['ok' => false, 'error' => 'Angebot nicht gefunden.'], 404);
    }
    $cust = $pdo->prepare('select * from profiles where id = ? limit 1');
    $cust->execute([$offer['customer_id']]);
    $customer = $cust->fetch();
    $invId = billing_create_invoice_for_offer($pdo, $offer, $customer);
    billing_issue_invoice($pdo, $invId);
    json_response(['ok' => true, 'invoice_id' => $invId, 'csrf' => csrf_token()]);
}

if ($method === 'PATCH') {
    $id = trim((string) ($input['id'] ?? ''));
    $act = (string) ($input['action'] ?? '');
    $map = ['mark_paid' => 'bezahlt', 'storniert' => 'storniert', 'offen' => 'offen'];
    if (!isset($map[$act])) {
        json_response(['ok' => false, 'error' => 'Unbekannte Aktion.'], 400);
    }
    $pdo->prepare('update invoices set status = ? where id = ?')->execute([$map[$act], $id]);
    json_response(['ok' => true, 'csrf' => csrf_token()]);
}

json_response(['ok' => false, 'error' => 'Methode nicht erlaubt.'], 405);
