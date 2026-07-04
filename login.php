<?php require __DIR__ . '/includes/bootstrap.php'; ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Login · Sartu Kundenportal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="portal.css?v=2" />
</head>
<body>
  <div class="pt-wrap">
    <div class="pt-top">
      <a class="pt-brand" href="./"><span class="dot"></span>Sartu</a>
    </div>

    <div class="auth-box">
      <div class="card">
        <p class="eyebrow">Kundenportal</p>
        <h1>Anmelden</h1>
        <p class="muted" style="margin-bottom:18px;">Gib Ihre E-Mail ein. Wir schicken Ihnen einen Login-Link <strong>und</strong> einen 6-stelligen Code.</p>

        <!-- Schritt 1: E-Mail -->
        <form id="emailForm">
          <div class="field">
            <label for="email">E-Mail-Adresse</label>
            <input type="email" id="email" name="email" autocomplete="email" placeholder="name@firma.de" required />
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;" id="sendBtn">Login-Link anfordern</button>
        </form>

        <!-- Schritt 2: Code (erscheint nach dem Absenden) -->
        <form id="codeForm" class="hidden" style="margin-top:6px;">
          <div class="notice notice-ok" id="sentNote"></div>
          <div class="field">
            <label for="code">6-stelliger Code aus der E-Mail</label>
            <input type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code" placeholder="123456" maxlength="6" />
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;" id="verifyBtn">Code bestätigen &amp; einloggen</button>
          <p class="muted" style="margin-top:12px;">Oder klick einfach den Link in der E-Mail — beides funktioniert.</p>
          <button type="button" class="btn btn-ghost btn-sm" style="margin-top:10px;" id="backBtn">Andere E-Mail</button>
        </form>

        <p class="notice notice-err hidden" id="err"></p>
        <p class="muted" id="configWarn" style="margin-top:16px;"></p>
      </div>
      <p class="muted" style="text-align:center; margin-top:14px;">
        Noch kein Zugang? Konten legt Sartu an — <a href="kontakt.php">melde Sie bei uns</a>.
      </p>
    </div>
  </div>

  <script src="login-local.js?v=1"></script>
</body>
</html>
