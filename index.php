<?php
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/copy-briefs.php';
$brief = sartu_copy_brief('index');
$pageTitle = $brief['title'];
$pageDesc  = $brief['description'];
?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script>document.documentElement.classList.add('js');</script>
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="robots" content="noindex,nofollow" />
  <link rel="canonical" href="https://[DOMAIN]/" />
  <link rel="stylesheet" href="assets/fonts-v2/sartu-fonts.css" />
  <link rel="stylesheet" href="styles-v2.css?v=1" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Sartu" />
  <meta property="og:locale" content="de_DE" />
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      { "@type": "Organization", "@id": "https://[DOMAIN]/#organization", "name": "Sartu", "url": "https://[DOMAIN]/", "email": "hallo@sartu.de", "description": "Webdesign-Agentur für Firmenwebsites zum Festpreis: Webdesign, Texte, SEO, Wartung und Hosting in Deutschland.", "areaServed": "DE", "sameAs": [] },
      { "@type": "WebSite", "@id": "https://[DOMAIN]/#website", "url": "https://[DOMAIN]/", "name": "Sartu", "inLanguage": "de-DE", "publisher": { "@id": "https://[DOMAIN]/#organization" } },
      { "@type": "FAQPage", "mainEntity": [
        { "@type": "Question", "name": "Was kostet eine Firmenwebsite bei Sartu?", "acceptedAnswer": { "@type": "Answer", "text": "Sartu baut Firmenwebsites zum Festpreis: Start 1.290 €, Wachstum 3.290 €, Platzhirsch 6.490 €. Dazu kommt der monatliche Rundum-Schutz für den sicheren Betrieb ab 49 €. Den genauen Festpreis erhalten Sie vorab schriftlich." } },
        { "@type": "Question", "name": "Wie schnell ist meine Website online?", "acceptedAnswer": { "@type": "Answer", "text": "Nach vollständigen Inhalten geht eine kleine Website oft in 7 bis 14 Werktagen online. Mit der Express-Option liefern wir in etwa halber Zeit, gegen einen Aufschlag von 50 Prozent." } },
        { "@type": "Question", "name": "Welche Seiten brauche ich? Ich weiß es selbst nicht genau.", "acceptedAnswer": { "@type": "Answer", "text": "Das müssen Sie nicht wissen. Sie sagen uns, was Ihre Firma macht und was die Website erreichen soll. Sartu empfiehlt daraus das passende Paket und schlägt die konkrete Seitenstruktur im Angebot vor." } },
        { "@type": "Question", "name": "Bin ich an einen Vertrag gebunden?", "acceptedAnswer": { "@type": "Answer", "text": "Die Anfrage ist unverbindlich und kostenlos. Sie erhalten einen Festpreis schriftlich und entscheiden dann in Ruhe. Es entsteht kein Abo-Zwang für die Website selbst." } }
      ] }
    ]
  }
  </script>
</head>
<body>

  <!-- ===================== HEADER ===================== -->
  <header class="hdr">
    <div class="container hdr-in">
      <a href="./" class="brand" aria-label="Sartu Startseite"><span class="bdot" aria-hidden="true"></span>Sartu</a>
      <nav class="hdr-nav" aria-label="Hauptnavigation">
        <a href="leistungen.php">Leistungen</a>
        <a href="preise.php">Preise</a>
        <a href="ablauf.php">Ablauf</a>
        <a href="ratgeber.php">Ratgeber</a>
        <a href="ueber-uns.php">Über uns</a>
      </nav>
      <div class="hdr-right">
        <a href="anfrage.php" class="btn btn-primary hdr-cta">Projekt starten</a>
        <button class="hdr-burger" aria-label="Menü"><span></span><span></span><span></span></button>
      </div>
    </div>
  </header>

  <main>

    <!-- ===================== HERO ===================== -->
    <section class="hero">
      <div class="container hero-grid">
        <div class="hero-copy">
          <p class="eyebrow">Webdesign-Agentur · Festpreis</p>
          <h1>Eine Website, die neue Kunden bringt. Zum <span class="hi">Festpreis</span>.</h1>
          <p class="hero-sub">Für Handwerk, Praxen, Kanzleien und Dienstleister: klare Firmenwebsites, fertig in 7 bis 14 Werktagen. Feste Ansprechperson, kein Abo-Zwang, Hosting in Deutschland.</p>
          <div class="hero-cta">
            <a href="anfrage.php" class="btn btn-primary btn-lg">Projekt starten <span class="arr" aria-hidden="true">→</span></a>
            <a href="#beispiele" class="btn btn-ghost btn-lg">Beispiele ansehen</a>
          </div>
          <div class="hero-trust">
            <span>Festpreis ab 1.290 €</span>
            <span>In 7–14 Werktagen online</span>
            <span>Alle Texte inklusive</span>
            <span>Hosting in Deutschland</span>
          </div>
        </div>
        <div class="mock" aria-hidden="false">
          <figure class="mock-frame">
            <div class="mock-bar"><i></i><i></i><i></i><span class="url">steinweg-rechtsanwaelte.de</span></div>
            <img src="assets/ref/kanzlei-shot.jpg" alt="Beispielprojekt: Website einer Rechtsanwaltskanzlei, von Sartu gebaut" width="1440" height="900" loading="eager" />
          </figure>
          <span class="mock-tag">Beispielprojekt</span>
        </div>
      </div>
    </section>

    <!-- ===================== PROBLEM / STATEMENT ===================== -->
    <section class="statement">
      <div class="container">
        <div class="reveal">
          <p class="eyebrow">Das Problem</p>
          <h2>Die meisten Firmenwebsites sehen okay aus. Aber sie sagen in drei Sekunden nicht, warum man genau hier anfragen soll.</h2>
        </div>
        <div class="reveal">
          <p>Genau da setzen wir an. Erst sortieren wir Ihr Angebot, Ihre Zielgruppe und die Fragen Ihrer Kunden. Dann bauen wir eine Seite, die Besucher ruhig zum Anruf führt und die Google und die KI-Suche klar verstehen.</p>
          <a href="ablauf.php" class="text-link">So läuft ein Projekt ab <span aria-hidden="true">→</span></a>
        </div>
      </div>
    </section>

    <!-- ===================== PAKETE ===================== -->
    <section id="preise">
      <div class="container">
        <div class="sec-head">
          <span class="eyebrow">Pakete</span>
          <h2 class="section-title">Klare Festpreise, ohne Agentur-Nebel.</h2>
          <p>Sie sagen uns, was Ihre Firma macht. Wir empfehlen das passende Paket und den Festpreis. Texte, Design und Technik sind immer inklusive.</p>
        </div>
        <div class="plans reveal">
          <article class="pcard">
            <p class="pname">Start</p>
            <p class="psit">Wenn Kunden Sie googeln und sofort einen guten Eindruck bekommen sollen.</p>
            <p class="pamount">1.290 <span class="u">€</span></p>
            <p class="pcare"><span class="req">Pflicht</span>+ 49 €/Monat Rundum-Schutz für den sicheren Betrieb</p>
            <ul class="pfeat">
              <li>Alles Wichtige auf einer Seite</li>
              <li>Alle Texte inklusive</li>
              <li>Gefunden bei der Suche nach Ihrem Namen</li>
              <li>In 7 Werktagen online</li>
            </ul>
            <a href="anfrage.php?paket=start" class="btn btn-ghost">Start anfragen</a>
          </article>

          <article class="pcard feat">
            <span class="pcard-badge">Meistgewählt</span>
            <p class="pname">Wachstum</p>
            <p class="psit">Wenn Ihre Website aktiv neue Anfragen bringen soll.</p>
            <p class="pamount">3.290 <span class="u">€</span></p>
            <p class="pcare"><span class="req">Pflicht</span>+ 99 €/Monat Rundum-Schutz für den sicheren Betrieb</p>
            <ul class="pfeat">
              <li>Bis zu 8 Seiten, jede Leistung einzeln gefunden</li>
              <li>Alle Texte inklusive</li>
              <li>Inhalte selbst ändern, Anfragen messen</li>
              <li>In 7–14 Werktagen online</li>
            </ul>
            <a href="anfrage.php?paket=wachstum" class="btn btn-primary">Wachstum anfragen <span class="arr" aria-hidden="true">→</span></a>
          </article>

          <article class="pcard">
            <p class="pname">Platzhirsch</p>
            <p class="psit">Wenn Sie in Ihrer Region die Nummer 1 sein möchten.</p>
            <p class="pamount">6.490 <span class="u">€</span></p>
            <p class="pcare"><span class="req">Pflicht</span>+ 249 €/Monat Rundum-Schutz für den sicheren Betrieb</p>
            <ul class="pfeat">
              <li>Bis zu 20 Seiten inkl. Team &amp; Jobs</li>
              <li>Logo &amp; Online-Terminbuchung inklusive</li>
              <li>Alle Texte inklusive, News- &amp; Blog-Bereich</li>
              <li>In 7–14 Werktagen online</li>
            </ul>
            <a href="anfrage.php?paket=platzhirsch" class="btn btn-ghost">Platzhirsch anfragen</a>
          </article>
        </div>
        <p class="plans-note">Größeres Vorhaben, mehr als 20 Seiten oder mehrere Standorte? Individueller Festpreis. <a href="preise.php">Alle Pakete &amp; Details ansehen →</a></p>
      </div>
    </section>

    <!-- ===================== BEISPIELPROJEKTE ===================== -->
    <section id="beispiele" class="tint">
      <div class="container">
        <div class="sec-head">
          <span class="eyebrow">Beispielprojekte</span>
          <h2 class="section-title">So sehen Sartu-Websites aus.</h2>
          <p>Drei Branchen, drei eigene Handschriften. Kein Vorlagen-Look, kein Baukasten. Sehen Sie selbst, wie eigenständig eine Sartu-Seite wirkt.</p>
        </div>
        <div class="refs reveal">
          <article class="refcard">
            <div class="refthumb"><img src="assets/ref/praxis-shot.jpg" alt="Beispielprojekt: Website einer Zahnarztpraxis" loading="lazy" /></div>
            <div class="refbody">
              <p class="refbadge">Zahnarztpraxis · Wachstum</p>
              <h3>Klarfeld Zahnärzte</h3>
              <p>Ruhige, vertrauensbildende Praxis-Website mit Online-Terminen.</p>
              <a class="reflink" href="musterseiten/praxis.html" target="_blank" rel="noopener">Live ansehen <span class="ext" aria-hidden="true">↗</span></a>
            </div>
          </article>
          <article class="refcard">
            <div class="refthumb"><img src="assets/ref/kanzlei-shot.jpg" alt="Beispielprojekt: Website einer Rechtsanwaltskanzlei" loading="lazy" /></div>
            <div class="refbody">
              <p class="refbadge">Rechtsanwälte · Platzhirsch</p>
              <h3>Steinweg Rechtsanwälte</h3>
              <p>Seriöser, umfangreicher Auftritt für eine etablierte Kanzlei.</p>
              <a class="reflink" href="musterseiten/kanzlei.html" target="_blank" rel="noopener">Live ansehen <span class="ext" aria-hidden="true">↗</span></a>
            </div>
          </article>
          <article class="refcard">
            <div class="refthumb"><img src="assets/ref/handwerk-shot.jpg" alt="Beispielprojekt: Website eines Handwerksbetriebs" loading="lazy" /></div>
            <div class="refbody">
              <p class="refbadge">Zimmerei &amp; Dach · Start</p>
              <h3>Holzwerk Nordkamp</h3>
              <p>Handfeste Handwerks-Website, die Anfragen bringt.</p>
              <a class="reflink" href="musterseiten/handwerk.html" target="_blank" rel="noopener">Live ansehen <span class="ext" aria-hidden="true">↗</span></a>
            </div>
          </article>
        </div>
        <p class="refnote">Beispielprojekte mit fiktiven Marken zur Veranschaulichung. Keine echten Kundendaten.</p>
      </div>
    </section>

    <!-- ===================== ABLAUF + GARANTIE ===================== -->
    <section>
      <div class="container">
        <div class="sec-head">
          <span class="eyebrow">So läuft's</span>
          <h2 class="section-title">In drei Schritten zur fertigen Website.</h2>
          <p>Alles digital, ohne Termine, ohne Verkaufsdruck. Sie entscheiden, wann Sie dran sind.</p>
        </div>
        <div class="steps reveal">
          <div class="step">
            <span class="n">Schritt 1</span>
            <h3>Ein paar Klick-Fragen</h3>
            <p>Sie beschreiben in einer Minute, was Ihre Firma macht und was die Website bringen soll. Ohne Termin, unverbindlich.</p>
          </div>
          <div class="step">
            <span class="n">Schritt 2</span>
            <h3>Empfehlung &amp; Festpreis</h3>
            <p>Sie bekommen eine klare Paket-Empfehlung und Ihren Festpreis schwarz auf weiß. Kein Vertrag, kein Abo-Zwang.</p>
          </div>
          <div class="step">
            <span class="n">Schritt 3</span>
            <h3>Wir bauen, Sie geben frei</h3>
            <p>Online in 7 bis 14 Werktagen, sicher gehostet in Deutschland. Auf Wunsch per Express in etwa halber Zeit.</p>
          </div>
        </div>
        <div class="guar reveal" style="margin-top:34px">
          <span class="guar-ic"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg></span>
          <div>
            <h3>Geld-zurück auf den ersten Entwurf</h3>
            <p>Überzeugt Sie der erste Design-Entwurf nicht, bekommen Sie Ihre Anzahlung zurück. Fair und ohne Kleingedrucktes.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================== FAQ ===================== -->
    <section class="tint">
      <div class="container">
        <div class="sec-head">
          <span class="eyebrow">Häufige Fragen</span>
          <h2 class="section-title">Kurz und ehrlich beantwortet.</h2>
        </div>
        <div class="faq-list reveal">
          <details class="faq-item"><summary>Was kostet eine Firmenwebsite bei Sartu?</summary><div class="faq-a">Wir arbeiten zum Festpreis: Start 1.290 €, Wachstum 3.290 €, Platzhirsch 6.490 €. Dazu kommt der monatliche Rundum-Schutz für den sicheren Betrieb (ab 49 €). Den genauen Festpreis bekommen Sie vorab schriftlich. Mehr dazu auf der <a href="preise.php">Preisseite</a>.</div></details>
          <details class="faq-item"><summary>Ich weiß selbst nicht, welche Seiten ich brauche.</summary><div class="faq-a">Das müssen Sie auch nicht. Sie sagen uns, was Ihre Firma macht und was die Website erreichen soll. Wir empfehlen daraus das passende Paket und schlagen die konkrete Seitenstruktur im Angebot vor. Sie entscheiden dann.</div></details>
          <details class="faq-item"><summary>Wie schnell ist meine Website online?</summary><div class="faq-a">Nach vollständigen Inhalten geht eine kleine Website oft in 7 bis 14 Werktagen online. Mit der Express-Option liefern wir in etwa halber Zeit, gegen einen Aufschlag von 50 Prozent.</div></details>
          <details class="faq-item"><summary>Bin ich an einen Vertrag gebunden?</summary><div class="faq-a">Die Anfrage ist kostenlos und unverbindlich. Sie erhalten einen Festpreis schriftlich und entscheiden in Ruhe. Kein Abo-Zwang für die Website selbst.</div></details>
          <details class="faq-item"><summary>Werde ich bei Google gefunden?</summary><div class="faq-a">Jede Website bekommt eine saubere Struktur, gute Texte und die technischen Grundlagen für Google und die KI-Suche. Für laufende Sichtbarkeit gibt es die <a href="leistung-seo.php">SEO-Betreuung</a>, monatlich und nach 3 Monaten kündbar.</div></details>
        </div>
      </div>
    </section>

    <!-- ===================== FINALE CTA (dunkel, tiefpetrol) ===================== -->
    <section class="final">
      <div class="container reveal">
        <p class="eyebrow">Nächster Schritt</p>
        <h2>Bereit für eine Website, die arbeitet?</h2>
        <p>Beschreiben Sie in einer Minute Ihr Vorhaben. Sie bekommen eine ehrliche Empfehlung und Ihren Festpreis, unverbindlich.</p>
        <div class="hero-cta">
          <a href="anfrage.php" class="btn btn-light btn-lg">Projekt starten <span class="arr" aria-hidden="true">→</span></a>
          <a href="preise.php" class="btn btn-outline-l btn-lg">Preise ansehen</a>
        </div>
      </div>
    </section>

  </main>

  <!-- ===================== FOOTER ===================== -->
  <footer class="ft">
    <div class="container">
      <div class="ft-grid">
        <div class="ft-brand">
          <span class="brand"><span class="bdot" aria-hidden="true"></span>Sartu</span>
          <p>Moderne Firmenwebsites zum Festpreis. Transparent, persönlich und DSGVO-konform aus Deutschland.</p>
        </div>
        <div class="ft-col">
          <h4>Leistungen</h4>
          <a href="leistung-webdesign.php">Webdesign</a>
          <a href="leistung-seo.php">SEO-Optimierung</a>
          <a href="leistung-wartung.php">Hosting &amp; Wartung</a>
          <a href="leistungen.php">Alle Leistungen</a>
        </div>
        <div class="ft-col">
          <h4>Agentur</h4>
          <a href="ueber-uns.php">Über uns</a>
          <a href="ablauf.php">Ablauf</a>
          <a href="ratgeber.php">Ratgeber</a>
          <a href="kontakt.php">Kontakt</a>
        </div>
        <div class="ft-col">
          <h4>Rechtliches</h4>
          <a href="impressum.php">Impressum</a>
          <a href="datenschutz.php">Datenschutz</a>
          <a href="agb.php">AGB</a>
        </div>
      </div>
      <div class="ft-bot">
        <span>© <?= date('Y') ?> Sartu. Alle Rechte vorbehalten.</span>
        <span>Festpreis · 7–14 Werktage · Hosting in Deutschland</span>
      </div>
    </div>
  </footer>

  <script>
    (function () {
      var els = document.querySelectorAll('.reveal');
      if (!('IntersectionObserver' in window) || !document.documentElement.classList.contains('js')) {
        els.forEach(function (e) { e.classList.add('vis'); }); return;
      }
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting) { en.target.classList.add('vis'); io.unobserve(en.target); }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
      els.forEach(function (e) { io.observe(e); });

      var burger = document.querySelector('.hdr-burger');
      var nav = document.querySelector('.hdr-nav');
      if (burger && nav) burger.addEventListener('click', function () {
        var open = nav.style.display === 'flex';
        nav.style.display = open ? '' : 'flex';
      });
    })();
  </script>
</body>
</html>
