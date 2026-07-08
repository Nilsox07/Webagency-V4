<?php require __DIR__ . '/includes/bootstrap.php'; ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Ihr Angebot · Sartu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="portal.css?v=9" />
</head>
<body>
  <div class="pt-wrap pt-wrap-app">
    <div class="pt-top">
      <a class="pt-brand" href="portal.php"><span class="dot"></span>Sartu</a>
      <div class="pt-top-actions"><button class="btn btn-ghost btn-sm" id="logoutBtn">Abmelden</button></div>
    </div>

    <div id="gate" class="card"><span class="spinner"></span> Lädt Ihr Angebot …</div>

    <main id="app" class="hidden">
      <div class="pt-hello">
        <div><p class="eyebrow">Ihr Angebot</p><h1>Ihr Website-Paket</h1><p class="muted">Bitte prüfen Sie das Angebot und beauftragen Sie uns verbindlich.</p></div>
      </div>
      <div id="offerBox"></div>
    </main>

    <div id="accepted" class="card hidden" style="text-align:center;">
      <h2>Vielen Dank für Ihren Auftrag! 🎉</h2>
      <p class="muted">Ihr Auftrag ist bestätigt. Als Nächstes brauchen wir ein paar Infos zu Ihrem Projekt — das dauert nur wenige Minuten.</p>
      <p style="margin-top:16px;"><a class="btn btn-primary" href="onboarding.php">Jetzt Briefing ausfüllen</a></p>
    </div>

    <p class="notice notice-err hidden" id="err"></p>
  </div>

  <script src="angebot.js?v=3"></script>
</body>
</html>
