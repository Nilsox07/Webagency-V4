<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/http.php';
require_once __DIR__ . '/../includes/mollie.php';

// Mollie ruft diesen Endpunkt mit der Payment-ID auf. Wir holen den Status direkt
// von Mollie (Vertrauensanker) und aktualisieren Zahlung + Rechnung.
require_method('POST');

$paymentId = trim((string) ($_POST['id'] ?? ''));
if ($paymentId === '') {
    http_response_code(400);
    echo 'missing id';
    exit;
}

$pdo = db();

// Nur bekannte Zahlungen verarbeiten.
$stmt = $pdo->prepare('select * from payments where provider_id = ? limit 1');
$stmt->execute([$paymentId]);
$row = $stmt->fetch();
if (!$row) {
    http_response_code(200);
    echo 'unknown';
    exit;
}

try {
    $payment = mollie_get_payment($paymentId);
} catch (Throwable $e) {
    http_response_code(200); // Mollie später erneut versuchen lassen
    echo 'retry';
    exit;
}

$status = (string) ($payment['status'] ?? 'open');
$method = (string) ($payment['method'] ?? '');
$paid = $status === 'paid';

$pdo->prepare('update payments set status = ?, methode = ?, bezahlt_am = ' . ($paid ? 'now()' : 'bezahlt_am') . ' where id = ?')
    ->execute([$status, $method, $row['id']]);

if ($paid) {
    $pdo->prepare('update invoices set status = ? where id = ? and status = ?')->execute(['bezahlt', $row['invoice_id'], 'offen']);
}

http_response_code(200);
echo 'ok';
