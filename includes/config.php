<?php
declare(strict_types=1);

$config = [
    'db' => [
        'dsn' => getenv('SARTU_DB_DSN') ?: 'mysql:host=localhost;dbname=sartu;charset=utf8mb4',
        'user' => getenv('SARTU_DB_USER') ?: 'sartu_user',
        'password' => getenv('SARTU_DB_PASSWORD') ?: '',
    ],
    'mail' => [
        'from' => getenv('SARTU_MAIL_FROM') ?: 'noreply@sartu.de',
        'reply_to' => getenv('SARTU_MAIL_REPLY_TO') ?: 'hallo@sartu.de',
        'base_url' => rtrim((string) (getenv('SARTU_BASE_URL') ?: ''), '/'),
    ],
    'app' => [
        'debug' => getenv('SARTU_DEBUG') === '1',
        'session_name' => 'sartu_session',
    ],
    // Zahlungen (Mollie). Solange api_key leer ist, bleibt die Bezahlfunktion
    // ausgeblendet — erst nach Eintragen des Keys (hier oder in config.local.php)
    // ist alles aktiv. Test: 'test_xxx', Live: 'live_xxx'.
    'mollie' => [
        'api_key' => getenv('SARTU_MOLLIE_KEY') ?: '',
    ],
    // Rechnungsangaben von Sartu (Verkäufer) — für PDF und E-Rechnung.
    'billing' => [
        'kleinunternehmer' => getenv('SARTU_KLEINUNTERNEHMER') === '1',
        'ust_satz' => (int) (getenv('SARTU_UST') ?: 19),
        'verkaeufer' => [
            'name'    => getenv('SARTU_FIRMA_NAME') ?: 'Sartu',
            'inhaber' => getenv('SARTU_FIRMA_INHABER') ?: '',
            'strasse' => getenv('SARTU_FIRMA_STRASSE') ?: '',
            'plz_ort' => getenv('SARTU_FIRMA_PLZ_ORT') ?: '',
            'land'    => getenv('SARTU_FIRMA_LAND') ?: 'Deutschland',
            'email'   => getenv('SARTU_MAIL_REPLY_TO') ?: 'hallo@sartu.de',
            'telefon' => getenv('SARTU_FIRMA_TELEFON') ?: '',
            'ust_id'  => getenv('SARTU_FIRMA_USTID') ?: '',
            'steuernr' => getenv('SARTU_FIRMA_STEUERNR') ?: '',
            'iban'    => getenv('SARTU_FIRMA_IBAN') ?: '',
            'bic'     => getenv('SARTU_FIRMA_BIC') ?: '',
            'bank'    => getenv('SARTU_FIRMA_BANK') ?: '',
        ],
    ],
];

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $local = require $localConfig;
    if (is_array($local)) {
        $config = array_replace_recursive($config, $local);
    }
}

return $config;
