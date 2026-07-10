<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
$token = trim((string) ($_GET['token'] ?? ''));

if ($token !== '') {
    $profile = consume_login_link($token);
    if ($profile) {
        $default = ($profile['role'] ?? '') === 'admin' ? 'admin.php' : 'portal.php';
        // Nur whitelisted interne Zielseiten zulassen (kein Open-Redirect).
        $allowed = ['angebot.php', 'onboarding.php', 'portal.php', 'admin.php'];
        $next = (string) ($_GET['next'] ?? '');
        header('Location: ' . (in_array($next, $allowed, true) && ($profile['role'] ?? '') !== 'admin' ? $next : $default));
        exit;
    }
    $error = 'Der Login-Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen Link an.';
} else {
    $error = 'Es wurde kein gültiger Login-Token übergeben.';
}
?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Anmeldung · Sartu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="portal.css?v=13" />
  <script>(function(){try{var t=localStorage.getItem('sartu-theme');if(t==='light'||t==='dark')document.documentElement.setAttribute('data-theme',t);}catch(e){}})();</script>
</head>
<body>
  <div class="pt-wrap">
    <div class="auth-box">
      <div class="card" style="text-align:center;">
        <p class="eyebrow">Kundenportal</p>
        <h1>Anmeldung fehlgeschlagen</h1>
        <p class="notice notice-err" style="margin-top:16px;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <p style="margin-top:16px;"><a href="login.php">Zurück zum Login</a></p>
      </div>
    </div>
  </div>
</body>
</html>
