<?php require __DIR__ . '/includes/bootstrap.php'; ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Impressum von Sartu — Anbieterkennzeichnung gemäß § 5 DDG." />
  <meta name="robots" content="noindex,nofollow" />
  <title>Impressum | Sartu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css?v=43" />
  <link rel="canonical" href="https://[DOMAIN]/impressum" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Sartu" />
  <meta property="og:locale" content="de_DE" />
  <meta property="og:url" content="https://[DOMAIN]/impressum" />
  <meta property="og:title" content="Impressum | Sartu" />
  <meta property="og:description" content="Anbieterkennzeichnung gemäß § 5 DDG." />
  <meta name="twitter:card" content="summary" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": "https://[DOMAIN]/#organization",
        "name": "Sartu",
        "url": "https://[DOMAIN]/",
        "logo": "https://[DOMAIN]/assets/logo-teal.png",
        "email": "hallo@sartu.de",
        "description": "Webdesign-Agentur für Festpreis-Websites: Webdesign, SEO, lokales SEO, Wartung & Hosting in Deutschland — kontaktlos, transparent, DSGVO-konform.",
        "areaServed": "DE",
        "sameAs": []
      },
      {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "name": "Start",
            "item": "https://[DOMAIN]/"
          },
          {
            "@type": "ListItem",
            "position": 2,
            "name": "Impressum",
            "item": "https://[DOMAIN]/impressum"
          }
        ]
      }
    ]
  }
  </script>
</head>
<body>
  <?php require_once __DIR__ . '/includes/site-partials.php'; render_site_header('./'); ?>

  <main>
    <section class="page-hero compact">
      <div class="container">
        <p class="eyebrow eyebrow-green">Rechtliches</p>
        <h1>Impressum</h1>
        <p class="page-lead">Anbieterkennzeichnung gemäß § 5 Digitale-Dienste-Gesetz (DDG).</p>
      </div>
    </section>

    <section class="legal">
      <div class="container">
        <div class="legal-content">
          <div class="legal-note">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            <p><strong>Platzhalter:</strong> Die folgenden Angaben sind Beispieltexte und müssen vor dem Livegang durch die echten Daten ersetzt und rechtlich geprüft werden. Dies ist keine Rechtsberatung.</p>
          </div>

          <h2>Angaben gemäß § 5 DDG</h2>
          <p>
            [Vor- und Nachname / Firmenname]<br />
            [Straße und Hausnummer]<br />
            [PLZ und Ort]<br />
            Deutschland
          </p>

          <h2>Vertreten durch</h2>
          <p>[Name der vertretungsberechtigten Person]</p>

          <h2>Kontakt</h2>
          <p>
            Telefon: [Telefonnummer]<br />
            E-Mail: <a href="mailto:hallo@sartu.de">hallo@sartu.de</a>
          </p>

          <h2>Umsatzsteuer-ID</h2>
          <p>Umsatzsteuer-Identifikationsnummer gemäß § 27a Umsatzsteuergesetz:<br />[USt-IdNr. – sofern vorhanden]</p>

          <h2>Kleinunternehmerregelung</h2>
          <p>[Falls zutreffend:] Gemäß § 19 UStG wird keine Umsatzsteuer berechnet und ausgewiesen.</p>

          <h2>Redaktionell verantwortlich</h2>
          <p>
            [Name]<br />
            [Anschrift wie oben]
          </p>

          <h2>EU-Streitschlichtung</h2>
          <p>Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener">https://ec.europa.eu/consumers/odr/</a>. Unsere E-Mail-Adresse finden Sie oben im Impressum.</p>

          <h2>Verbraucherstreitbeilegung / Universalschlichtungsstelle</h2>
          <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>

          <h2>Haftung für Inhalte</h2>
          <p>Als Diensteanbieter sind wir gemäß § 7 Abs. 1 DDG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 DDG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen.</p>

          <h2>Haftung für Links</h2>
          <p>Unser Angebot enthält ggf. Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Für diese fremden Inhalte können wir keine Gewähr übernehmen. Verantwortlich ist stets der jeweilige Anbieter oder Betreiber der Seiten.</p>

          <h2>Urheberrecht</h2>
          <p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Beiträge Dritter sind als solche gekennzeichnet.</p>
        </div>
      </div>
    </section>
  </main>

  <?php render_site_footer('./'); ?>

  <script src="script.js"></script>
  <script src="cookies.js?v=2"></script>
  <script src="fab.js?v=3"></script>
</body>
</html>

