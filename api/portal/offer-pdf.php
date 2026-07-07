<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/billing.php';

require_method('GET');
$profile = require_profile();
$pdo = db();

// Neuestes Angebot des Kunden (offen bevorzugt).
$stmt = $pdo->prepare('select * from angebote where customer_id = ? order by field(status, "gesendet", "angenommen") desc, created_at desc limit 1');
$stmt->execute([$profile['id']]);
$offer = $stmt->fetch();
if (!$offer) {
    json_response(['ok' => false, 'error' => 'Kein Angebot vorhanden.'], 404);
}

$pdf = billing_render_offer_pdf($offer, $profile);
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Angebot-Sartu.pdf"');
header('Content-Length: ' . strlen($pdf));
header('Cache-Control: private, no-store');
echo $pdf;
