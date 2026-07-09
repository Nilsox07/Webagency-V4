<?php
declare(strict_types=1);

/**
 * Sartu — Web-Installer (einmalig, ohne phpMyAdmin/Kommandozeile).
 * Ablauf: Dateien per FTP hochladen → includes/config.local.php mit DB-Zugang anlegen
 * → diese Seite im Browser aufrufen → Datenbank einrichten + ersten Admin anlegen.
 * >>> NACH DER EINRICHTUNG DIESE DATEI LÖSCHEN (install.php). <<<
 */

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function guid(): string
{
    $d = random_bytes(16);
    $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
    $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
}

$steps = [];
$fatal = null;
$adminDone = null;
$pdo = null;

// --- Config + DB-Verbindung ---
$configLocal = __DIR__ . '/includes/config.local.php';
if (!is_file($configLocal)) {
    $fatal = 'Es fehlt noch <code>includes/config.local.php</code>. Bitte aus <code>includes/config.local.example.php</code> '
        . 'kopieren, deine Datenbank-Zugangsdaten eintragen und per FTP hochladen — dann diese Seite neu laden.';
} else {
    try {
        $cfg = require __DIR__ . '/includes/config.php';
        $db = $cfg['db'];
        $pdo = new PDO((string) $db['dsn'], (string) $db['user'], (string) $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    } catch (Throwable $ex) {
        $fatal = 'Keine Verbindung zur Datenbank. Prüfe Host, Datenbankname, Benutzer und Passwort in '
            . '<code>includes/config.local.php</code>. (Diese Zugangsdaten bekommst du im Panel deines Hosters.)';
    }
}

$action = $_SERVER['REQUEST_METHOD'] === 'POST' ? (string) ($_POST['action'] ?? '') : '';

// --- Schema einspielen ---
if ($pdo && $action === 'schema') {
    try {
        $sql = (string) file_get_contents(__DIR__ . '/database/mysql-schema.sql');
        $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;      // Kommentarzeilen raus
        $count = 0;
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
            $pdo->exec($stmt);
            $count++;
        }
        $steps[] = ['ok', 'Datenbank eingerichtet — ' . $count . ' Anweisungen ausgeführt (Tabellen angelegt).'];
    } catch (Throwable $ex) {
        $steps[] = ['fail', 'Fehler beim Einrichten: ' . e($ex->getMessage())];
    }
}

// --- Ersten Admin anlegen ---
if ($pdo && $action === 'admin') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $name = trim((string) ($_POST['name'] ?? ''));
    try {
        $adminExists = (int) $pdo->query("SELECT COUNT(*) FROM profiles WHERE role = 'admin'")->fetchColumn();
        if ($adminExists > 0) {
            $steps[] = ['warn', 'Es gibt bereits einen Admin — aus Sicherheitsgründen wird kein weiterer über den Installer angelegt.'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $steps[] = ['fail', 'Bitte eine gültige E-Mail-Adresse angeben.'];
        } else {
            $pdo->prepare('INSERT INTO profiles (id, email, name, role, is_active) VALUES (?, ?, ?, ?, 1)')
                ->execute([guid(), $email, $name ?: null, 'admin']);
            $adminDone = $email;
            $steps[] = ['ok', 'Admin-Konto angelegt für ' . e($email) . '. Login über <code>/login</code>.'];
        }
    } catch (Throwable $ex) {
        $steps[] = ['fail', 'Konnte Admin nicht anlegen (ist das Schema schon eingespielt?): ' . e($ex->getMessage())];
    }
}

// Status ermitteln
$schemaOk = false;
$hasAdmin = false;
if ($pdo) {
    try { $pdo->query('SELECT 1 FROM profiles LIMIT 1'); $schemaOk = true; } catch (Throwable $ex) {}
    if ($schemaOk) {
        try { $hasAdmin = (int) $pdo->query("SELECT COUNT(*) FROM profiles WHERE role='admin'")->fetchColumn() > 0; } catch (Throwable $ex) {}
    }
}
$col = ['ok' => '#22c55e', 'warn' => '#eab308', 'fail' => '#ef4444'];
header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Installer · Sartu</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#0b1f1a;color:#eaf7f0;margin:0;padding:40px 16px;}
  .wrap{max-width:640px;margin:0 auto;}
  h1{font-size:1.6rem;margin:0 0 6px;} h2{font-size:1.15rem;margin:26px 0 10px;}
  p{color:#b7c7c0;line-height:1.5;} code{background:rgba(255,255,255,.08);padding:1px 6px;border-radius:5px;}
  .card{background:rgba(255,255,255,.04);border-radius:12px;padding:18px 20px;margin:14px 0;}
  .msg{display:flex;gap:10px;align-items:flex-start;padding:10px 12px;border-radius:8px;background:rgba(255,255,255,.04);margin:8px 0;}
  .dot{flex:none;width:22px;height:22px;border-radius:999px;display:grid;place-items:center;font-weight:800;color:#0b1f1a;}
  label{display:block;font-weight:600;margin:12px 0 4px;} input{width:100%;box-sizing:border-box;padding:10px 12px;border-radius:8px;border:1px solid rgba(255,255,255,.18);background:#0e2822;color:#eaf7f0;font-size:1rem;}
  button{margin-top:14px;background:#bef264;color:#0b1f1a;font-weight:800;border:none;padding:11px 18px;border-radius:999px;font-size:1rem;cursor:pointer;}
  .step-num{display:inline-grid;place-items:center;width:24px;height:24px;border-radius:999px;background:#bef264;color:#0b1f1a;font-weight:800;margin-right:8px;}
  .done{color:#22c55e;font-weight:700;} .warn-del{margin-top:24px;padding:14px 16px;border:1px solid rgba(239,68,68,.5);border-radius:10px;background:rgba(239,68,68,.08);color:#fecaca;font-weight:600;}
  a{color:#bef264;}
</style></head><body><div class="wrap">
<h1>Sartu — Einrichtung</h1>
<p>Einmalige Installation. Danach diese Datei löschen.</p>

<?php if ($fatal): ?>
  <div class="card"><div class="msg"><span class="dot" style="background:#ef4444">✗</span><div><?= $fatal ?></div></div></div>
<?php else: ?>

  <?php foreach ($steps as [$st, $txt]): ?>
    <div class="msg"><span class="dot" style="background:<?= $col[$st] ?>"><?= $st === 'ok' ? '✓' : ($st === 'warn' ? '!' : '✗') ?></span><div><?= $txt ?></div></div>
  <?php endforeach; ?>

  <div class="card">
    <h2><span class="step-num">1</span>Datenbank einrichten</h2>
    <?php if ($schemaOk): ?>
      <p class="done">✓ Erledigt — alle Tabellen sind vorhanden.</p>
    <?php else: ?>
      <p>Legt alle benötigten Tabellen an (aus <code>database/mysql-schema.sql</code>). Kann gefahrlos wiederholt werden.</p>
      <form method="post"><input type="hidden" name="action" value="schema"><button type="submit">Datenbank jetzt einrichten</button></form>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2><span class="step-num">2</span>Ersten Admin anlegen</h2>
    <?php if (!$schemaOk): ?>
      <p>Zuerst Schritt 1 ausführen.</p>
    <?php elseif ($hasAdmin): ?>
      <p class="done">✓ Ein Admin ist angelegt. Login über <a href="login.php">/login</a>.</p>
    <?php else: ?>
      <p>Dein Zugang zum Admin-Bereich. Login läuft passwortlos per Code an diese E-Mail.</p>
      <form method="post">
        <input type="hidden" name="action" value="admin">
        <label>Deine E-Mail</label><input type="email" name="email" required placeholder="du@deine-domain.de">
        <label>Name (optional)</label><input type="text" name="name" placeholder="Nils">
        <button type="submit">Admin anlegen</button>
      </form>
    <?php endif; ?>
  </div>

  <?php if ($schemaOk && $hasAdmin): ?>
    <div class="card"><h2>Fertig 🎉</h2><p>Melde dich jetzt über <a href="login.php">/login</a> an.</p></div>
  <?php endif; ?>

<?php endif; ?>

<p class="warn-del">⚠️ Nach der Einrichtung bitte <strong>diese Datei löschen</strong> (<code>install.php</code>) sowie <code>check-umgebung.php</code> — sie dürfen nicht online bleiben.</p>
</div></body></html>
