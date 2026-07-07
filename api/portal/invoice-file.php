<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/billing.php';

require_method('GET');
$profile = require_profile();
$pdo = db();

$id = trim((string) ($_GET['id'] ?? ''));
$typ = ($_GET['typ'] ?? 'pdf') === 'xml' ? 'xml' : 'pdf';
if ($id === '') {
    json_response(['ok' => false, 'error' => 'Rechnung fehlt.'], 400);
}

$stmt = $pdo->prepare('select * from invoices where id = ? limit 1');
$stmt->execute([$id]);
$inv = $stmt->fetch();
if (!$inv) {
    json_response(['ok' => false, 'error' => 'Nicht gefunden.'], 404);
}
if (($profile['role'] ?? '') !== 'admin' && $inv['customer_id'] !== $profile['id']) {
    json_response(['ok' => false, 'error' => 'Kein Zugriff.'], 403);
}

$path = $typ === 'xml' ? (string) $inv['xml_path'] : (string) $inv['pdf_path'];
if ($path === '' || !is_file($path)) {
    // Falls Datei fehlt: on-the-fly neu erzeugen (PDF), sonst 404.
    if ($typ === 'pdf') {
        $full = billing_load_invoice($pdo, $id);
        $pdf = billing_render_invoice_pdf($full);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="Rechnung-' . preg_replace('~[^A-Za-z0-9-]~', '', (string) $inv['nummer']) . '.pdf"');
        echo $pdf;
        exit;
    }
    json_response(['ok' => false, 'error' => 'Datei fehlt.'], 404);
}

$nr = preg_replace('~[^A-Za-z0-9-]~', '', (string) $inv['nummer']) ?: 'rechnung';
if ($typ === 'xml') {
    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="Rechnung-' . $nr . '.xml"');
} else {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="Rechnung-' . $nr . '.pdf"');
}
header('Content-Length: ' . (string) filesize($path));
header('Cache-Control: private, no-store');
readfile($path);
