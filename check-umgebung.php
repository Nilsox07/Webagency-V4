<?php
declare(strict_types=1);

/**
 * Sartu — Umgebungs-Selbstcheck.
 * Nach dem Hochladen im Browser aufrufen: https://deine-domain.de/check-umgebung.php
 * Zeigt, ob dein Webspace alles kann. KEINE Passwörter/Secrets werden angezeigt.
 * >>> NACH DEM CHECK DIESE DATEI LÖSCHEN. <<<
 */

$rows = [];
/** @param string $status ok|warn|fail */
function check(array &$rows, string $name, string $status, string $detail): void
{
    $rows[] = ['name' => $name, 'status' => $status, 'detail' => $detail];
}

// --- PHP-Version ---
$php = PHP_VERSION;
check($rows, 'PHP-Version', version_compare($php, '8.1.0', '>=') ? 'ok' : 'fail',
    $php . (version_compare($php, '8.1.0', '>=') ? ' — passt (≥ 8.1)' : ' — zu alt, mind. 8.1 nötig'));

// --- Extensions ---
$needed = [
    'pdo_mysql' => 'Datenbank (Portal, Rechnungen)',
    'mbstring'  => 'Umlaute/Textlängen',
    'gd'        => 'Bild-Komprimierung bei Uploads',
    'fileinfo'  => 'Echte Datei-Typ-Prüfung bei Uploads',
    'iconv'     => 'Umlaute in PDF-Rechnungen',
    'curl'      => 'Mollie-Zahlungen',
    'json'      => 'Datenverarbeitung',
    'openssl'   => 'HTTPS / sichere Mails',
];
foreach ($needed as $ext => $wofuer) {
    $has = extension_loaded($ext);
    check($rows, 'Erweiterung: ' . $ext, $has ? 'ok' : 'fail',
        $has ? 'vorhanden — ' . $wofuer : 'FEHLT — nötig für: ' . $wofuer);
}

// --- Konfiguration ---
$configLocal = __DIR__ . '/includes/config.local.php';
check($rows, 'config.local.php', is_file($configLocal) ? 'ok' : 'warn',
    is_file($configLocal) ? 'angelegt' : 'noch nicht angelegt (aus config.local.example.php kopieren)');

// --- Datenbank + Schema ---
$dbOk = false;
try {
    $cfg = require __DIR__ . '/includes/config.php';
    $db = $cfg['db'];
    $pdo = new PDO((string) $db['dsn'], (string) $db['user'], (string) $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $dbOk = true;
    check($rows, 'Datenbank-Verbindung', 'ok', 'Verbindung steht');
    // Schema importiert? Schlüssel-Tabellen prüfen.
    $tables = ['profiles', 'briefings', 'projects', 'angebote', 'invoices', 'site_pages', 'aktionen'];
    $missing = [];
    foreach ($tables as $t) {
        try { $pdo->query('SELECT 1 FROM `' . $t . '` LIMIT 1'); }
        catch (Throwable $e) { $missing[] = $t; }
    }
    check($rows, 'DB-Schema (Tabellen)', $missing ? 'fail' : 'ok',
        $missing ? 'Fehlende Tabellen: ' . implode(', ', $missing) . ' → mysql-schema.sql importieren'
                 : 'alle ' . count($tables) . ' Schlüssel-Tabellen vorhanden');
} catch (Throwable $e) {
    check($rows, 'Datenbank-Verbindung', 'fail',
        'Keine Verbindung — DB-Zugang in config.local.php prüfen (Host/Name/Benutzer/Passwort)');
}

// --- Speicher (Uploads/Rechnungen) ---
$storage = getenv('SARTU_STORAGE_PATH') ?: (__DIR__ . '/storage');
if (!is_dir($storage)) { @mkdir($storage, 0770, true); }
$writable = is_dir($storage) && is_writable($storage);
$outsideRoot = strpos(realpath($storage) ?: $storage, realpath(__DIR__) ?: __DIR__) !== 0;
check($rows, 'Speicher-Ordner beschreibbar', $writable ? 'ok' : 'fail',
    ($writable ? 'ja' : 'NEIN — Schreibrechte fehlen') . ($outsideRoot ? ' · liegt außerhalb des Web-Ordners (ideal)' : ' · Tipp: SARTU_STORAGE_PATH außerhalb des Web-Ordners setzen'));

// --- Mailversand ---
check($rows, 'Mailversand', function_exists('mail') ? 'warn' : 'fail',
    function_exists('mail') ? 'mail() vorhanden — Login-/Angebots-Mails testen; bei Spam-Problemen SMTP nutzen' : 'mail() nicht verfügbar — SMTP einrichten');

// --- HTTPS ---
$https = (($_SERVER['HTTPS'] ?? '') === 'on')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443);
check($rows, 'HTTPS aktiv', $https ? 'ok' : 'warn',
    $https ? 'Seite läuft über HTTPS' : 'gerade nicht über HTTPS aufgerufen — vor Livegang SSL erzwingen');

// --- Ausgabe ---
$fails = count(array_filter($rows, static fn($r) => $r['status'] === 'fail'));
$warns = count(array_filter($rows, static fn($r) => $r['status'] === 'warn'));
$col = ['ok' => '#22c55e', 'warn' => '#eab308', 'fail' => '#ef4444'];
$ico = ['ok' => '✓', 'warn' => '!', 'fail' => '✗'];
header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Umgebungs-Check · Sartu</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#0b1f1a;color:#eaf7f0;margin:0;padding:40px 16px;}
  .wrap{max-width:760px;margin:0 auto;}
  h1{font-size:1.6rem;margin:0 0 4px;}
  .sum{margin:0 0 24px;color:#9fb2ab;}
  .row{display:flex;gap:14px;align-items:flex-start;padding:12px 14px;border-radius:10px;background:rgba(255,255,255,.04);margin-bottom:8px;}
  .badge{flex:none;width:26px;height:26px;border-radius:999px;display:grid;place-items:center;font-weight:800;color:#0b1f1a;}
  .name{font-weight:700;}
  .detail{color:#b7c7c0;font-size:.92rem;}
  .warn-del{margin-top:26px;padding:14px 16px;border:1px solid rgba(239,68,68,.5);border-radius:10px;background:rgba(239,68,68,.08);color:#fecaca;font-weight:600;}
</style></head><body><div class="wrap">
<h1>Umgebungs-Check</h1>
<p class="sum"><?= $fails ? "<strong style='color:#ef4444'>$fails Punkt(e) blockieren den Start.</strong> " : "<strong style='color:#22c55e'>Alles Pflicht-Nötige erfüllt.</strong> " ?><?= $warns ? "$warns Hinweis(e) beachten." : "" ?></p>
<?php foreach ($rows as $r): ?>
  <div class="row">
    <span class="badge" style="background:<?= $col[$r['status']] ?>"><?= $ico[$r['status']] ?></span>
    <div><div class="name"><?= htmlspecialchars($r['name'], ENT_QUOTES) ?></div><div class="detail"><?= htmlspecialchars($r['detail'], ENT_QUOTES) ?></div></div>
  </div>
<?php endforeach; ?>
<p class="warn-del">⚠️ Diese Datei nach dem Check bitte wieder <strong>löschen</strong> (<code>check-umgebung.php</code>) — sie soll nicht dauerhaft online sein.</p>
</div></body></html>
