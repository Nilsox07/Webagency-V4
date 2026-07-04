<?php require __DIR__ . '/includes/bootstrap.php'; ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Mein Projekt · Sartu Kundenportal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="portal.css?v=2" />
</head>
<body>
  <div class="pt-wrap">
    <div class="pt-top">
      <a class="pt-brand" href="./"><span class="dot"></span>Sartu</a>
      <div class="pt-top-actions">
        <select id="projSwitch" class="hidden" style="min-width:160px; padding:9px 12px; border-radius:999px; background:rgba(255,255,255,.05); border:1.5px solid var(--border); color:#fff;"></select>
        <button class="btn btn-ghost btn-sm" id="logoutBtn">Abmelden</button>
      </div>
    </div>

    <div id="loading" class="card"><span class="spinner"></span> Lädt Ihr Projekt …</div>
    <div id="empty" class="card hidden">
      <h2>Noch kein Projekt hinterlegt</h2>
      <p class="muted">Sobald Ihr Angebot bestätigt ist, erscheint Ihr Projekt hier. Fragen? <a href="kontakt.php">Schreiben Sie uns</a>.</p>
    </div>

    <main id="app" class="hidden">
      <div class="pt-hello card">
        <p class="eyebrow">Ihr Projekt</p>
        <h1 id="hello">Hallo</h1>
        <p id="projTitle"></p>
        <div class="row" style="margin-top:10px;">
          <span class="badge" id="paketBadge"></span>
          <span class="badge" id="careBadge"></span>
          <span class="badge" id="terminBadge"></span>
        </div>
      </div>

      <div class="card">
        <h2>Status Ihres Projekts</h2>
        <div class="timeline" id="timeline"></div>
      </div>

      <div class="card now-card" id="nowCard">
        <p class="eyebrow">Jetzt dran</p>
        <p id="nowText"></p>
      </div>

      <div class="card hidden" id="noteCard">
        <h2>Notiz von Sartu</h2>
        <p id="noteText" style="color:var(--text-light);"></p>
      </div>

      <!-- Platzhalter-Karten: zeigen, was kommt, ohne klickbare kaputte Funktionen -->
      <div class="spread" style="gap:16px; align-items:stretch;">
        <div class="card is-soon" style="flex:1; min-width:220px;">
          <div class="spread"><h3>Inhalte hochladen</h3><span class="badge badge-soon">Bald</span></div>
          <p class="muted" style="margin-top:8px;">Texte, Bilder &amp; Logo bequem hochladen.</p>
        </div>
        <div class="card is-soon" style="flex:1; min-width:220px;">
          <div class="spread"><h3>Care-Minuten</h3><span class="badge badge-soon">Bald</span></div>
          <p class="muted" style="margin-top:8px;">Verbrauchte &amp; offene Wartungsminuten im Blick.</p>
        </div>
        <div class="card is-soon" style="flex:1; min-width:220px;">
          <div class="spread"><h3>Dokumente</h3><span class="badge badge-soon">Bald</span></div>
          <p class="muted" style="margin-top:8px;">Angebote, Rechnungen &amp; Verträge zum Download.</p>
        </div>
      </div>

      <div class="pt-footer">
        <a href="datenschutz.php">Datenschutz</a>
        <a id="kuendigung" href="mailto:?subject=Kündigung%20Sartu%20Care">Kündigung / Care beenden</a>
        <a href="kontakt.php">Kontakt</a>
      </div>
    </main>

    <p class="notice notice-err hidden" id="err"></p>
  </div>

  <script src="portal-local.js?v=4"></script>
</body>
</html>
