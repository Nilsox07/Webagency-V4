<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Schlanker Mollie-Client (cURL). Der gehostete Checkout bietet automatisch alle
 * im Mollie-Konto aktivierten Methoden an — PayPal, Kreditkarte, SEPA, Sofort usw.
 */
function mollie_request(string $method, string $path, array $data = []): array
{
    $key = (string) (app_config()['mollie']['api_key'] ?? '');
    if ($key === '') {
        throw new RuntimeException('Mollie ist noch nicht eingerichtet.');
    }
    $url = 'https://api.mollie.com/v2/' . ltrim($path, '/');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key, 'Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_TIMEOUT => 20,
    ]);
    if ($data && strtoupper($method) !== 'GET') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    $resp = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($resp === false) {
        throw new RuntimeException('Verbindung zu Mollie fehlgeschlagen: ' . $err);
    }
    $json = json_decode((string) $resp, true) ?: [];
    if ($code >= 400) {
        throw new RuntimeException('Mollie-Fehler: ' . (string) ($json['detail'] ?? ('HTTP ' . $code)));
    }
    return $json;
}

/** Zahlung anlegen. Betrag in Cent. Gibt die Mollie-Payment zurück (inkl. Checkout-URL). */
function mollie_create_payment(int $cent, string $description, string $redirectUrl, string $webhookUrl, array $metadata = []): array
{
    return mollie_request('POST', 'payments', [
        'amount' => ['currency' => 'EUR', 'value' => number_format($cent / 100, 2, '.', '')],
        'description' => $description,
        'redirectUrl' => $redirectUrl,
        'webhookUrl' => $webhookUrl,
        'metadata' => $metadata,
    ]);
}

function mollie_get_payment(string $id): array
{
    return mollie_request('GET', 'payments/' . rawurlencode($id));
}

/** Checkout-URL aus einer Mollie-Payment-Antwort ziehen. */
function mollie_checkout_url(array $payment): ?string
{
    return $payment['_links']['checkout']['href'] ?? null;
}
