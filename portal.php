<?php require __DIR__ . '/includes/bootstrap.php'; ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Mein Website-Konto · Sartu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="portal.css?v=5" />
</head>
<body>
  <div class="pt-wrap pt-wrap-app">
    <div class="pt-top">
      <a class="pt-brand" href="./"><span class="dot"></span>Sartu</a>
      <div class="pt-top-actions">
        <span class="pt-user" id="ptUser"></span>
        <button class="btn btn-ghost btn-sm" id="logoutBtn">Abmelden</button>
      </div>
    </div>

    <div id="gate" class="card"><span class="spinner"></span> Lädt Ihr Konto …</div>

    <main id="app" class="hidden">
      <div class="pt-hello">
        <div>
          <p class="eyebrow">Mein Website-Konto</p>
          <h1 id="hello">Hallo</h1>
          <p id="projTitle" class="muted"></p>
        </div>
        <div class="pt-hello-badges">
          <span class="badge" id="paketBadge"></span>
          <span class="badge badge-ok" id="statusBadge"></span>
        </div>
      </div>

      <nav class="tabs pt-tabs" id="ptTabs" aria-label="Portal-Bereiche">
        <button class="tab is-on" data-tab="cockpit">Cockpit</button>
        <button class="tab" data-tab="bearbeiten">Website bearbeiten</button>
        <button class="tab" data-tab="statistik">Statistik</button>
        <button class="tab" data-tab="wachsen">Wachsen</button>
        <button class="tab" data-tab="paket">Paket &amp; Rechnungen</button>
        <button class="tab" data-tab="hilfe">Hilfe</button>
      </nav>

      <!-- ===== COCKPIT ===== -->
      <section id="pane-cockpit" class="pt-pane">
        <div class="pt-tiles">
          <div class="pt-tile pt-tile-ok"><span class="pt-tile-k">Website</span><strong id="tOnline">Online</strong><span class="pt-tile-s">rund um die Uhr erreichbar</span></div>
          <div class="pt-tile pt-tile-ok"><span class="pt-tile-k">Ladezeit</span><strong id="tSpeed">Schnell</strong><span class="pt-tile-s">läuft im Cockpit mit</span></div>
          <div class="pt-tile pt-tile-ok"><span class="pt-tile-k">Sicherheit</span><strong id="tSec">Aktuell</strong><span class="pt-tile-s">Updates automatisch</span></div>
          <div class="pt-tile"><span class="pt-tile-k">Letztes Backup</span><strong id="tBackup">gestern</strong><span class="pt-tile-s">täglich, extern gesichert</span></div>
          <div class="pt-tile"><span class="pt-tile-k">Besucher (30 Tage)</span><strong id="tVisits">—</strong><span class="pt-tile-s">datenschutzkonform</span></div>
          <div class="pt-tile"><span class="pt-tile-k">Rundum-Schutz</span><strong id="tCare">aktiv</strong><span class="pt-tile-s" id="tCareSub">gehört zu Ihrem Paket</span></div>
        </div>

        <div class="card now-card" id="nowCard">
          <p class="eyebrow">Status Ihres Projekts</p>
          <p id="nowText">Ihre Website läuft. Ab jetzt kümmert sich Sartu um Betrieb, Updates und Sicherheit.</p>
        </div>
      </section>

      <!-- ===== WEBSITE BEARBEITEN ===== -->
      <section id="pane-bearbeiten" class="pt-pane hidden">
        <div class="pt-editbar">
          <div>
            <h2>Website bearbeiten</h2>
            <p class="muted" id="edStatus">Ihre Änderungen speichern sich automatisch. Ihr Design bleibt geschützt.</p>
          </div>
          <div class="pt-editbar-actions">
            <a class="btn btn-ghost btn-sm" id="edPreview" href="vorschau.php" target="_blank" rel="noopener">Vorschau</a>
            <button class="btn btn-primary btn-sm" id="edPublish">Veröffentlichen</button>
          </div>
        </div>
        <div id="editorRoot"><p class="muted"><span class="spinner"></span> Lädt Editor …</p></div>
      </section>

      <!-- ===== STATISTIK ===== -->
      <section id="pane-statistik" class="pt-pane hidden">
        <div class="card">
          <h2>Besucher &amp; Anfragen</h2>
          <p class="muted">Datenschutzkonform gemessen — ohne Cookies-Zwang, ohne Weitergabe.</p>
          <div class="pt-tiles pt-tiles-3">
            <div class="pt-tile"><span class="pt-tile-k">Besucher (30 Tage)</span><strong>412</strong><span class="pt-tile-s">Demo-Wert</span></div>
            <div class="pt-tile"><span class="pt-tile-k">über Google</span><strong>61 %</strong><span class="pt-tile-s">Demo-Wert</span></div>
            <div class="pt-tile"><span class="pt-tile-k">Anfragen</span><strong>9</strong><span class="pt-tile-s">Demo-Wert</span></div>
          </div>
        </div>
      </section>

      <!-- ===== WACHSEN ===== -->
      <section id="pane-wachsen" class="pt-pane hidden">
        <div class="pt-grow">
          <div class="card pt-grow-card"><h3>Weitere Seite</h3><p class="pt-grow-price">199 € <span>inkl. Text</span></p><p class="muted">Eine neue Leistungs- oder Themenseite, damit Sie auch dafür gefunden werden.</p><a class="btn btn-primary btn-sm" href="kontakt.php">Seite anfragen</a></div>
          <div class="card pt-grow-card"><h3>Größeres Paket</h3><p class="pt-grow-price">nur die Differenz</p><p class="muted">Reingewachsen? Wechseln Sie hoch und zahlen nur den Unterschied.</p><a class="btn btn-dark btn-sm" href="kontakt.php">Upgrade anfragen</a></div>
          <div class="card pt-grow-card"><h3>SEO-Betreuung</h3><p class="pt-grow-price">490 €<span>/Monat</span></p><p class="muted">Damit Ihre Website bei Google und in der KI-Suche weiter nach oben kommt. Begrenzte Plätze.</p><a class="btn btn-dark btn-sm" href="leistung-seo.php">Ansehen</a></div>
          <div class="card pt-grow-card"><h3>KI-Chat-Assistent</h3><p class="pt-grow-price">990 € <span>+ 79 €/Mon.</span></p><p class="muted">Beantwortet Besucherfragen rund um die Uhr — trainiert auf Ihre Inhalte.</p><a class="btn btn-dark btn-sm" href="kontakt.php">Anfragen</a></div>
        </div>
      </section>

      <!-- ===== PAKET & RECHNUNGEN ===== -->
      <section id="pane-paket" class="pt-pane hidden">
        <div class="card">
          <h2>Ihr Paket</h2>
          <div class="pt-kv"><span>Website-Paket</span><strong id="kvPaket">—</strong></div>
          <div class="pt-kv"><span>Rundum-Schutz</span><strong id="kvCare">—</strong></div>
          <div class="pt-kv"><span>Läuft auf</span><strong>Sartu-Servern in Deutschland</strong></div>
        </div>
        <div class="card">
          <h2>Rechnungen</h2>
          <p class="muted" id="invEmpty">Ihre Rechnungen erscheinen hier zum Download, sobald sie erstellt sind.</p>
        </div>
      </section>

      <!-- ===== HILFE ===== -->
      <section id="pane-hilfe" class="pt-pane hidden">
        <div class="card">
          <h2>Hilfe &amp; Anfragen</h2>
          <p class="muted">Kurze Anleitungen und ein direkter Draht zu uns — schriftlich, ohne Termin.</p>
          <div class="pt-edit-list">
            <a class="pt-edit-row" href="kontakt.php"><strong>Nachricht an Sartu</strong><span>schreiben →</span></a>
            <a class="pt-edit-row" href="preise.php"><strong>Alle Pakete &amp; Preise</strong><span>ansehen →</span></a>
            <a class="pt-edit-row" href="datenschutz.php"><strong>Datenschutz &amp; AVV</strong><span>lesen →</span></a>
          </div>
        </div>
      </section>

      <div class="pt-footer">
        <a href="datenschutz.php">Datenschutz</a>
        <a href="kontakt.php">Kontakt</a>
      </div>
    </main>

    <p class="notice notice-err hidden" id="err"></p>
  </div>

  <script src="portal-local.js?v=6"></script>
  <script src="portal-editor.js?v=2"></script>
</body>
</html>
