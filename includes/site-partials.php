<?php
declare(strict_types=1);

require_once __DIR__ . '/aktionen.php';

function render_site_header(string $logoHref = './'): void
{
    echo render_promo_bar();
    ?>
  <!-- ============ HEADER ============ -->
  <header class="site-header" id="top">
    <div class="container header-inner">
      <a href="<?= htmlspecialchars($logoHref, ENT_QUOTES, 'UTF-8') ?>" class="logo" aria-label="Sartu Startseite">
        <img src="assets/logo-wordmark-teal.svg" alt="Sartu — Webdesign-Agentur, Logo" class="logo-img" width="360" height="86" />
      </a>

      <nav class="main-nav" id="mainNav" aria-label="Hauptnavigation">
        <div class="nav-item has-dropdown">
          <a href="leistungen.php">Leistungen<span class="nav-caret" aria-hidden="true">▾</span></a>
          <div class="nav-dropdown">
            <a href="leistungen.php" class="dd-all">Alle Leistungen im Überblick</a>
            <a href="leistung-webdesign.php">Webdesign &amp; Website-Erstellung</a>
            <a href="leistung-redesign.php">Website-Redesign</a>
            <a href="leistung-seo.php">Suchmaschinenoptimierung (SEO)</a>
            <a href="leistung-lokales-seo.php">Lokales SEO &amp; Google-Profil</a>
            <a href="leistung-wartung.php">Wartung, Hosting &amp; Betrieb</a>
            <a href="leistung-texte.php">Texterstellung &amp; Inhalte</a>
            <a href="leistung-logo.php">Logo &amp; Branding</a>
            <a href="vergleiche.php">Website-Vergleiche</a>
            <a href="qualitaet.php">Qualität &amp; Abnahme</a>
          </div>
        </div>
        <a href="ablauf.php">Ablauf</a>
        <a href="ratgeber.php">Ratgeber</a>
        <a href="preise.php">Preise</a>
        <a href="ueber-uns.php">Über uns</a>
      </nav>

      <a href="anfrage.php" class="btn btn-primary header-cta">Projekt einschätzen lassen</a>

      <button class="nav-toggle" id="navToggle" aria-label="Menü öffnen" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>
    <?php
}

function render_site_footer(string $logoHref = './'): void
{
    ?>
  <!-- ============ FOOTER ============ -->
  <footer class="site-footer footer-rich">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="<?= htmlspecialchars($logoHref, ENT_QUOTES, 'UTF-8') ?>" class="logo footer-logo" aria-label="Sartu Startseite">
            <img src="assets/logo-wordmark-teal-light.svg" alt="Sartu — Webdesign-Agentur, Logo" class="logo-img" loading="lazy" width="360" height="86" />
          </a>
          <p>Moderne Websites zum Festpreis — kontaktlos, transparent und DSGVO-konform aus Deutschland.</p>
          <address>
            [FIRMENNAME / INHABER]<br />
            [STRASSE UND HAUSNUMMER]<br />
            [PLZ] [ORT]<br />
            Tel.: [TELEFON]<br />
            E-Mail: [E-MAIL]
          </address>
        </div>

        <div class="footer-col">
          <p class="footer-h">Leistungen</p>
          <a href="leistung-webdesign.php">Webdesign</a>
          <a href="leistung-wartung.php">Hosting &amp; Wartung</a>
          <a href="leistung-seo.php">SEO-Optimierung</a>
          <a href="leistung-lokales-seo.php">Lokales SEO</a>
          <a href="leistungen.php">Alle Leistungen</a>
        </div>

        <div class="footer-col">
          <p class="footer-h">Agentur</p>
          <a href="ueber-uns.php">Über uns</a>
          <a href="ablauf.php">Ablauf</a>
          <a href="qualitaet.php">Qualität &amp; Abnahme</a>
          <a href="vergleiche.php">Vergleiche</a>
          <a href="ratgeber.php">Ratgeber</a>
          <a href="kontakt.php">Kontakt</a>
        </div>

        <div class="footer-col">
          <p class="footer-h">Rechtliches</p>
          <a href="impressum.php">Impressum</a>
          <a href="datenschutz.php">Datenschutz</a>
          <a href="agb.php">AGB</a>
        </div>
      </div>

      <div class="footer-bottom">
        <span class="copy">© <?= date('Y') ?> Sartu · Alle Rechte vorbehalten.</span>
        <div class="footer-social">
          <!-- GO-LIVE: [INSTAGRAM-URL] als <a>-Tag einsetzen --><span class="footer-social-soon" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></span>
          <!-- GO-LIVE: [FACEBOOK-URL] als <a>-Tag einsetzen --><span class="footer-social-soon" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H7v3h3v6h3v-6h3l1-3h-4v-2c0-.6.4-1 1-1z"/></svg></span>
          <!-- GO-LIVE: [LINKEDIN-URL] als <a>-Tag einsetzen --><span class="footer-social-soon" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M4.5 3.5a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM3 9h3v12H3V9zm6 0h3v1.7c.5-.9 1.6-1.9 3.4-1.9 3 0 3.6 2 3.6 4.5V21h-3v-6.1c0-1.4 0-3.2-2-3.2s-2.2 1.5-2.2 3.1V21H9V9z"/></svg></span>
        </div>
      </div>
    </div>
  </footer>
    <?php
}
