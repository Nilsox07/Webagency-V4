<?php require __DIR__ . '/includes/bootstrap.php'; require __DIR__ . '/includes/briefing2-schema.php'; ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Projekt-Briefing · Sartu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="portal.css?v=9" />
</head>
<body>
  <div class="pt-wrap pt-wrap-app">
    <div class="pt-top">
      <a class="pt-brand" href="portal.php"><span class="dot"></span>Sartu</a>
      <div class="pt-top-actions">
        <a class="btn btn-ghost btn-sm" href="portal.php">Zum Portal</a>
        <button class="btn btn-ghost btn-sm" id="logoutBtn">Abmelden</button>
      </div>
    </div>

    <div id="gate" class="card"><span class="spinner"></span> Lädt Ihr Briefing …</div>

    <main id="app" class="hidden">
      <div class="pt-hello">
        <div>
          <p class="eyebrow">Projekt-Briefing</p>
          <h1>Erzählen Sie uns von Ihrem Projekt</h1>
          <p class="muted">Stichpunkte reichen — daraus baut Sartu Ihre Website. Alles speichert sich automatisch, Sie können jederzeit pausieren.</p>
        </div>
      </div>

      <div class="ob-progress"><div class="ob-bar" id="obBar"></div></div>
      <p class="muted ob-steplabel" id="obStepLabel"></p>

      <div id="obStepBody"></div>

      <div class="ob-nav">
        <button class="btn btn-ghost" id="obBack">Zurück</button>
        <span class="muted" id="obSaveState"></span>
        <button class="btn btn-primary" id="obNext">Weiter</button>
      </div>
    </main>

    <div id="done" class="card hidden" style="text-align:center;">
      <h2>Vielen Dank! 🎉</h2>
      <p class="muted">Ihr Briefing liegt jetzt bei Sartu. Wir melden uns mit dem ersten Entwurf. Sie können Ihre Angaben jederzeit ergänzen.</p>
      <p style="margin-top:16px;"><a class="btn btn-primary" href="portal.php">Zurück zum Portal</a> <button class="btn btn-ghost" id="editAgain">Noch etwas ergänzen</button></p>
    </div>

    <p class="notice notice-err hidden" id="err"></p>
  </div>

  <script id="briefingSchema" type="application/json"><?= json_encode(sartu_briefing2_schema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
  <script src="onboarding.js?v=1"></script>
</body>
</html>
