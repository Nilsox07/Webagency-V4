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
  <link rel="stylesheet" href="styles-v2.css?v=2" />
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
        { "@type": "Question", "name": "Bin ich an einen Vertrag gebunden?", "acceptedAnswer": { "@type": "Answer", "text": "Die Anfrage ist kostenlos und unverbindlich. Die Website zahlen Sie einmalig zum Festpreis und sie gehört Ihnen, keine Miet-Website. Für den sicheren Betrieb kommt der monatliche Rundum-Schutz dazu (Pflicht, mindestens 12 Monate). Hosting, Backups und Updates braucht jede Website ohnehin." } }
      ] }
    ]
  }
  </script>
</head>
<body>

  <!-- ===================== HEADER ===================== -->
  <header class="hdr">
    <div class="container hdr-in">
      <a href="./" class="brand" aria-label="Sartu Startseite">Sartu<span class="dot">.</span></a>
      <nav class="hdr-nav" aria-label="Hauptnavigation">
        <a href="#arbeiten">Arbeiten</a>
        <a href="leistungen.php">Leistungen</a>
        <a href="preise.php">Preise</a>
        <a href="ablauf.php">Ablauf</a>
        <a href="ueber-uns.php">Über uns</a>
      </nav>
      <div class="hdr-right">
        <a href="anfrage.php" class="btn btn-primary hdr-cta">Kostenlos starten</a>
        <button class="hdr-burger" aria-label="Menü"><span></span><span></span><span></span></button>
      </div>
    </div>
  </header>

  <main>

    <!-- ===================== HERO ===================== -->
    <section class="hero">
      <div class="hero-fx" aria-hidden="true"><div class="fx-parallax"><span class="fx-blobs"><span class="fx-blob fx-b1"></span><span class="fx-blob fx-b2"></span><span class="fx-blob fx-b3"></span></span><span class="fx-dots"></span></div></div>
      <div class="container">
        <div class="hero-grid">
          <div class="hero-copy">
            <span class="label">Webdesign-Agentur · Festpreis</span>
            <h1>Webdesign zum <em>Festpreis</em> für kleine Unternehmen.</h1>
            <p class="hero-sub">Sie müssen nicht wissen, welche Seiten Sie brauchen. Laden Sie hoch, was Sie haben, wir machen daraus Ihre professionelle Firmenwebsite.</p>
            <div class="hero-act">
              <a href="anfrage.php" class="btn btn-primary btn-lg">Kostenlos starten <span class="arr" aria-hidden="true">→</span></a>
              <a href="#arbeiten" class="btn btn-ghost btn-lg">Beispiele ansehen</a>
            </div>
          </div>
          <aside class="hero-card" aria-label="So läuft es ab">
            <p class="hc-label">So läuft's · digital, ohne Termine</p>
            <ol class="hc-steps">
              <li><span class="hc-n">1</span><div>
                <b>Anfrage mit Lumi</b>
                <p>In wenigen Klicks sagen Sie, was Ihre Firma macht, und laden hoch, was Sie haben.</p>
                <div class="hc-chips"><span>Logo</span><span>Fotos</span><span>Texte</span><span>Speisekarte</span><span>alte Website</span></div>
              </div></li>
              <li><span class="hc-n">2</span><div>
                <b>Festpreis-Angebot</b>
                <p>Sie bekommen Paket-Empfehlung und Ihren Festpreis schriftlich. Unverbindlich.</p>
              </div></li>
              <li><span class="hc-n">3</span><div>
                <b>Alles im Portal, kontaktlos</b>
                <p>Nach Ihrem OK: Feedback per Klick, Freigabe, Onlinegang. Ohne Meetings.</p>
              </div></li>
            </ol>
          </aside>
        </div>
        <div class="hero-meta">
          <span><b>Festpreis</b> ab 1.290 €</span>
          <span><b>7–14</b> Werktage</span>
          <span><b>Texte</b> inklusive</span>
          <span><b>Hosting</b> in Deutschland</span>
        </div>
      </div>
    </section>

    <!-- ===================== MARQUEE ===================== -->
    <div class="marq" aria-hidden="true">
      <div class="marq-track">
        <span>Festpreis</span><span>Kein Fachchinesisch</span><span>Alle Texte inklusive</span><span>Hosting in Deutschland</span><span>In 14 Tagen online</span>
        <span>Festpreis</span><span>Kein Fachchinesisch</span><span>Alle Texte inklusive</span><span>Hosting in Deutschland</span><span>In 14 Tagen online</span>
      </div>
    </div>

    <!-- ===================== ARBEITEN (der Star) ===================== -->
    <section id="arbeiten">
      <div class="container">
        <div class="sec-head">
          <div>
            <span class="label">Arbeiten</span>
            <h2>Drei Branchen. Drei eigene Handschriften.</h2>
          </div>
          <p class="sub">Kein Vorlagen-Look, kein Baukasten. Jede Seite eine eigene Marke. Sehen Sie live, wie unterschiedlich eine Sartu-Seite wirkt.</p>
        </div>

        <div class="works">
          <article class="work reveal">
            <div class="work-media">
              <figure class="work-frame">
                <div class="work-bar"><i></i><i></i><i></i><span class="u">klarfeld-zahnaerzte.de</span></div>
                <img src="assets/ref/praxis-shot.jpg" alt="Beispielprojekt: Website einer Zahnarztpraxis, von Sartu gebaut" loading="lazy" />
              </figure>
            </div>
            <div class="work-body">
              <p class="work-idx">Projekt 01</p>
              <h3>Klarfeld Zahnärzte</h3>
              <p class="meta">Zahnarztpraxis · Paket Wachstum</p>
              <p>Ruhige, vertrauensbildende Praxis-Website mit sanfter Bildsprache und Online-Terminen. Nimmt ängstlichen Patienten die Scheu.</p>
              <a class="work-link" href="musterseiten/praxis.html" target="_blank" rel="noopener">Live ansehen <span class="ext" aria-hidden="true">↗</span></a>
            </div>
          </article>

          <article class="work reveal">
            <div class="work-media">
              <figure class="work-frame">
                <div class="work-bar"><i></i><i></i><i></i><span class="u">steinweg-rechtsanwaelte.de</span></div>
                <img src="assets/ref/kanzlei-shot.jpg" alt="Beispielprojekt: Website einer Rechtsanwaltskanzlei, von Sartu gebaut" loading="lazy" />
              </figure>
            </div>
            <div class="work-body">
              <p class="work-idx">Projekt 02</p>
              <h3>Steinweg Rechtsanwälte</h3>
              <p class="meta">Kanzlei · Paket Platzhirsch</p>
              <p>Seriöser, umfangreicher Auftritt mit klarer Struktur, Navy und Gold. Wirkt so etabliert, wie die Kanzlei arbeitet.</p>
              <a class="work-link" href="musterseiten/kanzlei.html" target="_blank" rel="noopener">Live ansehen <span class="ext" aria-hidden="true">↗</span></a>
            </div>
          </article>

          <article class="work reveal">
            <div class="work-media">
              <figure class="work-frame">
                <div class="work-bar"><i></i><i></i><i></i><span class="u">holzwerk-nordkamp.de</span></div>
                <img src="assets/ref/handwerk-shot.jpg" alt="Beispielprojekt: Website eines Handwerksbetriebs, von Sartu gebaut" loading="lazy" />
              </figure>
            </div>
            <div class="work-body">
              <p class="work-idx">Projekt 03</p>
              <h3>Holzwerk Nordkamp</h3>
              <p class="meta">Zimmerei &amp; Dach · Paket Start</p>
              <p>Handfeste Handwerks-Website mit kräftiger Typo und echten Baustellen-Bildern. Führt direkt zur Anfrage.</p>
              <a class="work-link" href="musterseiten/handwerk.html" target="_blank" rel="noopener">Live ansehen <span class="ext" aria-hidden="true">↗</span></a>
            </div>
          </article>
        </div>
        <p class="works-note">Beispielprojekte · fiktive Marken · keine echten Kundendaten</p>
      </div>
    </section>

    <!-- ===================== LEISTUNGEN (editoriale Liste) ===================== -->
    <section class="tint">
      <div class="container">
        <div class="sec-head">
          <div>
            <span class="label">Leistungen</span>
            <h2>Alles aus einer Hand.</h2>
          </div>
          <p class="sub">Design, Texte, Technik und Betrieb. Ein Ansprechpartner, ein Festpreis, keine Schnittstellen, an denen etwas verloren geht.</p>
        </div>
        <div class="svc">
          <a class="svc-row" href="leistung-webdesign.php"><span class="n">01</span><h3>Webdesign</h3><p>Neue Firmenwebsite, mobil-optimiert und auf Anfragen ausgelegt.</p><span class="go" aria-hidden="true">→</span></a>
          <a class="svc-row" href="leistung-redesign.php"><span class="n">02</span><h3>Redesign</h3><p>Aus einer veralteten Seite wird eine, die wieder Kunden bringt.</p><span class="go" aria-hidden="true">→</span></a>
          <a class="svc-row" href="leistung-seo.php"><span class="n">03</span><h3>SEO &amp; KI-Suche</h3><p>Laufend bei Google und in der KI-Suche gefunden werden.</p><span class="go" aria-hidden="true">→</span></a>
          <a class="svc-row" href="leistung-texte.php"><span class="n">04</span><h3>Texte</h3><p>Texte, die verkaufen statt nur zu beschreiben. Inklusive.</p><span class="go" aria-hidden="true">→</span></a>
          <a class="svc-row" href="leistung-logo.php"><span class="n">05</span><h3>Logo &amp; Branding</h3><p>Ein Erscheinungsbild, das seriös wirkt und überall passt.</p><span class="go" aria-hidden="true">→</span></a>
          <a class="svc-row" href="leistung-wartung.php"><span class="n">06</span><h3>Wartung &amp; Hosting</h3><p>Sicherer Betrieb in Deutschland. Backups, Updates, Monitoring.</p><span class="go" aria-hidden="true">→</span></a>
        </div>
      </div>
    </section>

    <!-- ===================== PAKETE ===================== -->
    <section id="preise">
      <div class="container">
        <div class="sec-head">
          <div>
            <span class="label">Pakete</span>
            <h2>Klare Festpreise, ohne Agentur-Nebel.</h2>
          </div>
          <p class="sub">Sie sagen uns, was Ihre Firma macht. Wir empfehlen das Paket und den Festpreis. Texte, Design und Technik sind immer inklusive.</p>
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
            <a href="anfrage.php?paket=wachstum" class="btn">Wachstum anfragen <span class="arr" aria-hidden="true">→</span></a>
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
        <p class="plans-note">Größeres Vorhaben, mehr als 20 Seiten oder mehrere Standorte? Individueller Festpreis. <a href="preise.php">Alle Pakete &amp; Details →</a></p>
      </div>
    </section>

    <!-- ===================== ABLAUF + GARANTIE ===================== -->
    <section class="tint">
      <div class="container">
        <div class="sec-head">
          <div>
            <span class="label">So läuft's</span>
            <h2>In drei Schritten zur Website.</h2>
          </div>
          <p class="sub">Alles digital, ohne Termine, ohne Verkaufsdruck. Sie entscheiden, wann Sie dran sind.</p>
        </div>
        <div class="steps reveal">
          <div class="step">
            <span class="n">01</span>
            <h3>Ein paar Klick-Fragen</h3>
            <p>Sie beschreiben in einer Minute, was Ihre Firma macht und was die Website bringen soll. Ohne Termin, unverbindlich.</p>
          </div>
          <div class="step">
            <span class="n">02</span>
            <h3>Empfehlung &amp; Festpreis</h3>
            <p>Sie bekommen eine klare Paket-Empfehlung und Ihren Festpreis schwarz auf weiß. Alles unverbindlich, bis Sie zusagen.</p>
          </div>
          <div class="step">
            <span class="n">03</span>
            <h3>Wir bauen, Sie geben frei</h3>
            <p>Online in 7 bis 14 Werktagen, sicher gehostet in Deutschland. Auf Wunsch per Express in etwa halber Zeit.</p>
          </div>
        </div>
        <div class="guar reveal">
          <span class="guar-ic"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg></span>
          <div>
            <h3>Geld-zurück auf den ersten Entwurf</h3>
            <p>Überzeugt Sie der erste Design-Entwurf nicht, bekommen Sie Ihre Anzahlung zurück. Fair und ohne Kleingedrucktes.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================== FAQ ===================== -->
    <section>
      <div class="container">
        <div class="sec-head">
          <div>
            <span class="label">Häufige Fragen</span>
            <h2>Kurz und ehrlich beantwortet.</h2>
          </div>
        </div>
        <div class="faq-list reveal">
          <details class="faq-item"><summary>Was kostet eine Firmenwebsite bei Sartu?</summary><div class="faq-a">Wir arbeiten zum Festpreis: Start 1.290 €, Wachstum 3.290 €, Platzhirsch 6.490 €. Dazu kommt der monatliche Rundum-Schutz für den sicheren Betrieb (ab 49 €). Den genauen Festpreis bekommen Sie vorab schriftlich. Mehr auf der <a href="preise.php">Preisseite</a>.</div></details>
          <details class="faq-item"><summary>Ich weiß selbst nicht, welche Seiten ich brauche.</summary><div class="faq-a">Das müssen Sie auch nicht. Sie sagen uns, was Ihre Firma macht und was die Website erreichen soll. Wir empfehlen daraus das passende Paket und schlagen die konkrete Seitenstruktur im Angebot vor. Sie entscheiden dann.</div></details>
          <details class="faq-item"><summary>Wie schnell ist meine Website online?</summary><div class="faq-a">Nach vollständigen Inhalten geht eine kleine Website oft in 7 bis 14 Werktagen online. Mit der Express-Option liefern wir in etwa halber Zeit, gegen einen Aufschlag von 50 Prozent.</div></details>
          <details class="faq-item"><summary>Bin ich an einen Vertrag gebunden?</summary><div class="faq-a">Die Anfrage ist kostenlos und unverbindlich. Die Website zahlen Sie einmalig zum Festpreis, sie gehört Ihnen (keine Miet-Website). Für den sicheren Betrieb kommt der monatliche Rundum-Schutz dazu (Pflicht, mind. 12 Monate). Hosting, Backups und Updates braucht jede Website ohnehin.</div></details>
          <details class="faq-item"><summary>Werde ich bei Google gefunden?</summary><div class="faq-a">Jede Website bekommt eine saubere Struktur, gute Texte und die technischen Grundlagen für Google und die KI-Suche. Für laufende Sichtbarkeit gibt es die <a href="leistung-seo.php">SEO-Betreuung</a>, monatlich und nach 3 Monaten kündbar.</div></details>
        </div>
      </div>
    </section>

    <!-- ===================== KONTAKT (dunkel) ===================== -->
    <section class="final">
      <div class="container reveal">
        <span class="label">Nächster Schritt</span>
        <h2>Bereit anzufangen?</h2>
        <p>Beschreiben Sie in einer Minute Ihr Vorhaben. Sie bekommen eine ehrliche Empfehlung und Ihren Festpreis, unverbindlich.</p>
        <div class="hero-act">
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
          <span class="brand">Sartu<span style="color:var(--petrol)">.</span></span>
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
        <span>© <?= date('Y') ?> Sartu</span>
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
        entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('vis'); io.unobserve(en.target); } });
      }, { threshold: 0.1, rootMargin: '0px 0px -6% 0px' });
      els.forEach(function (e) { io.observe(e); });
      var burger = document.querySelector('.hdr-burger'), nav = document.querySelector('.hdr-nav');
      if (burger && nav) burger.addEventListener('click', function () { nav.style.display = nav.style.display === 'flex' ? '' : 'flex'; });

      // Hero-Muster: leichte Scroll-Parallax (nur wenn Bewegung erlaubt)
      var fx = document.querySelector('.hero .fx-parallax');
      var allowMotion = !window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (fx && allowMotion) {
        var ticking = false;
        window.addEventListener('scroll', function () {
          if (ticking) return; ticking = true;
          requestAnimationFrame(function () {
            var y = window.pageYOffset || 0;
            fx.style.transform = 'translateY(' + (y * 0.14) + 'px)';
            ticking = false;
          });
        }, { passive: true });
      }
    })();
  </script>
</body>
</html>
