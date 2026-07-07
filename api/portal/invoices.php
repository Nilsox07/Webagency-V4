<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/billing.php';

require_method('GET');
$profile = require_profile();
$pdo = db();

$rows = billing_customer_invoices($pdo, (string) $profile['id']);
$invoices = [];
foreach ($rows as $r) {
    $invoices[] = [
        'id' => $r['id'],
        'nummer' => $r['nummer'],
        'status' => $r['status'],
        'ausgestellt_am' => $r['ausgestellt_am'],
        'faellig_am' => $r['faellig_am'],
        'betrag' => money_de((int) $r['brutto_cent']),
        'bezahlbar' => $r['status'] === 'offen',
    ];
}

json_response([
    'ok' => true,
    'invoices' => $invoices,
    'payments_enabled' => billing_payments_enabled(),
    'csrf' => csrf_token(),
]);
