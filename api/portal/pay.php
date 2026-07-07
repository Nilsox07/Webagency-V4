<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/billing.php';
require_once __DIR__ . '/../../includes/mollie.php';

require_method('POST');
$profile = require_profile();
require_csrf_token();

if (!mollie_enabled()) {
    json_response(['ok' => false, 'error' => 'Online-Zahlung ist noch nicht aktiviert.'], 400);
}

$pdo = db();
$input = json_input();
$invoiceId = trim((string) ($input['invoice_id'] ?? ''));

$stmt = $pdo->prepare('select * from invoices where id = ? and customer_id = ? limit 1');
$stmt->execute([$invoiceId, $profile['id']]);
$inv = $stmt->fetch();
if (!$inv) {
    json_response(['ok' => false, 'error' => 'Rechnung nicht gefunden.'], 404);
}
if ($inv['status'] !== 'offen') {
    json_response(['ok' => false, 'error' => 'Diese Rechnung ist nicht (mehr) offen.'], 400);
}

$base = public_base_url();
try {
    $payment = mollie_create_payment(
        (int) $inv['brutto_cent'],
        'Rechnung ' . (string) $inv['nummer'] . ' · Sartu',
        $base . '/portal.php?bezahlt=1',
        $base . '/api/mollie-webhook.php',
        ['invoice_id' => $inv['id']]
    );
} catch (Throwable $e) {
    json_response(['ok' => false, 'error' => $e->getMessage()], 502);
}

$pdo->prepare('insert into payments (id, invoice_id, provider, provider_id, betrag_cent, status) values (?, ?, ?, ?, ?, ?)')
    ->execute([uuidv4(), $inv['id'], 'mollie', $payment['id'] ?? null, (int) $inv['brutto_cent'], $payment['status'] ?? 'open']);

$url = mollie_checkout_url($payment);
if (!$url) {
    json_response(['ok' => false, 'error' => 'Checkout konnte nicht gestartet werden.'], 502);
}
json_response(['ok' => true, 'checkout_url' => $url, 'csrf' => csrf_token()]);
