<?php require __DIR__ . '/includes/bootstrap.php'; require __DIR__ . '/includes/briefing2-schema.php'; ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Admin · Sartu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="portal.css?v=3" />
</head>
<body>
  <div class="pt-wrap pt-wrap-app pt-wrap-wide">
    <div class="pt-top">
      <a class="pt-brand" href="./"><span class="dot"></span>Sartu · Admin</a>
      <div class="pt-top-actions"><button class="btn btn-ghost btn-sm" id="logoutBtn">Abmelden</button></div>
    </div>

    <div id="gate" class="card"><span class="spinner"></span> Prüfe Zugang …</div>
    <div id="denied" class="card hidden">
      <h2>Kein Admin-Zugang</h2>
      <p class="muted">Dieser Bereich ist nur für Sartu-Admins. Sie werden abgemeldet …</p>
    </div>

    <main id="app" class="hidden">
      <div class="pt-hello">
        <div><p class="eyebrow">Admin</p><h1>Übersicht</h1></div>
      </div>

      <div class="pt-tiles">
        <div class="pt-tile"><span class="pt-tile-k">Neue Anfragen</span><strong id="ovAnfragen">—</strong><span class="pt-tile-s">unbearbeitet</span></div>
        <div class="pt-tile"><span class="pt-tile-k">Offene Angebote</span><strong id="ovAngebote">—</strong><span class="pt-tile-s">gesendet, wartet</span></div>
        <div class="pt-tile"><span class="pt-tile-k">Aktive Projekte</span><strong id="ovProjekte">—</strong><span class="pt-tile-s">in Arbeit / live</span></div>
        <div class="pt-tile"><span class="pt-tile-k">Kunden</span><strong id="ovKunden">—</strong><span class="pt-tile-s">gesamt</span></div>
      </div>

      <nav class="tabs pt-tabs">
        <button class="tab is-on" data-tab="anfragen">Anfragen</button>
        <button class="tab" data-tab="angebote">Angebote</button>
        <button class="tab" data-tab="rechnungen">Rechnungen</button>
        <button class="tab" data-tab="projekte">Projekte</button>
        <button class="tab" data-tab="kunden">Kunden</button>
        <button class="tab" data-tab="aktionen">Aktionen</button>
      </nav>

      <section id="tab-anfragen">
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Datum</th><th>Name</th><th>E-Mail</th><th>Status</th></tr></thead>
            <tbody id="anfragenBody"><tr><td colspan="4"><span class="spinner"></span> Lädt …</td></tr></tbody>
          </table>
        </div>
      </section>

      <section id="tab-angebote" class="hidden">
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Datum</th><th>Kunde</th><th>Paket</th><th>Preis</th><th>Status</th></tr></thead>
            <tbody id="angebotBody"><tr><td colspan="5"><span class="spinner"></span> Lädt …</td></tr></tbody>
          </table>
        </div>
      </section>

      <section id="tab-rechnungen" class="hidden">
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Nummer</th><th>Kunde</th><th>Datum</th><th>Betrag</th><th>Status</th></tr></thead>
            <tbody id="rechnungBody"><tr><td colspan="5"><span class="spinner"></span> Lädt …</td></tr></tbody>
          </table>
        </div>
      </section>

      <section id="tab-projekte" class="hidden">
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Projekt</th><th>Kunde</th><th>Paket</th><th>Phase</th></tr></thead>
            <tbody id="projekteBody"><tr><td colspan="4"><span class="spinner"></span> Lädt …</td></tr></tbody>
          </table>
        </div>
      </section>

      <section id="tab-kunden" class="hidden">
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Name</th><th>E-Mail</th><th>Firma</th><th>Rolle</th><th>Projekte</th></tr></thead>
            <tbody id="kundenBody"><tr><td colspan="5"><span class="spinner"></span> Lädt …</td></tr></tbody>
          </table>
        </div>
      </section>

      <section id="tab-aktionen" class="hidden">
        <div class="spread" style="margin-bottom:12px;">
          <p class="muted" style="max-width:640px;">Zeitlich begrenzte Rabatte. Aktive Aktionen erscheinen als Banner auf der passenden Seite. Sie können mehrere gleichzeitig anlegen — die exakt zum Produkt passende schlägt eine „Alle Pakete"-Aktion.</p>
          <button class="btn btn-primary btn-sm" id="aktNew">+ Neue Aktion</button>
        </div>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Name</th><th>Rabatt</th><th>Gilt für</th><th>Zeitraum</th><th>Status</th></tr></thead>
            <tbody id="aktionenBody"><tr><td colspan="5"><span class="spinner"></span> Lädt …</td></tr></tbody>
          </table>
        </div>
      </section>
    </main>

    <p class="notice notice-err hidden" id="err"></p>
  </div>

  <div class="modal-bg" id="modalBg"><div class="modal" id="modalBox"></div></div>

  <script id="briefing2Schema" type="application/json"><?= json_encode(sartu_briefing2_schema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
  <script src="briefing-schema.js?v=5"></script>
  <script src="admin-local.js?v=8"></script>
</body>
</html>
