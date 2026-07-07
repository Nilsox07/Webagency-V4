<?php
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/content-index.php';

$ratgeberCards = sartu_ratgeber_hub_cards();
$decisionCards = sartu_contextual_seo_hub_cards();
?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Sartu Ratgeber — 50 Fachbegriffe rund um Webdesign, SEO, Technik, Recht und Marketing verständlich erklärt. Plus Antworten auf die häufigsten Fragen." />
  <title>Webdesign-Lexikon: 50 Begriffe einfach erklärt | Sartu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css?v=41" />
  <link rel="canonical" href="https://[DOMAIN]/ratgeber" />
  <meta name="robots" content="noindex,nofollow" />
  <meta property="og:type" content="article" />
  <meta property="og:site_name" content="Sartu" />
  <meta property="og:locale" content="de_DE" />
  <meta property="og:url" content="https://[DOMAIN]/ratgeber" />
  <meta property="og:title" content="Ratgeber — 50 Webdesign-Begriffe erklärt | Sartu" />
  <meta property="og:description" content="50 Fachbegriffe rund um Webdesign, SEO, Technik, Recht und Marketing verständlich erklärt — durchsuchbar und nach Themen sortiert." />
  <meta property="og:image" content="https://[DOMAIN]/[OG-IMAGE]" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Ratgeber — 50 Webdesign-Begriffe erklärt | Sartu" />
  <meta name="twitter:description" content="50 Fachbegriffe rund um Webdesign, SEO, Technik, Recht und Marketing verständlich erklärt — durchsuchbar und nach Themen sortiert." />
  <meta name="twitter:image" content="https://[DOMAIN]/[OG-IMAGE]" />
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
            "name": "Ratgeber",
            "item": "https://[DOMAIN]/ratgeber"
          }
        ]
      },
      {
        "@type": "FAQPage",
        "mainEntity": [
          {
            "@type": "Question",
            "name": "Was bedeutet Webdesign?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Webdesign beschreibt die Gestaltung und Struktur einer Website: Layout, Farben, Schriften, Nutzerführung, mobile Darstellung und die Frage, wie schnell Besucher verstehen, was ein Unternehmen anbietet."
            }
          },
          {
            "@type": "Question",
            "name": "Was ist SEO einfach erklärt?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "SEO bedeutet Suchmaschinenoptimierung. Ziel ist, dass eine Website bei passenden Suchanfragen in Google besser gefunden wird, weil Inhalte, Struktur, Technik und Relevanz stimmen."
            }
          },
          {
            "@type": "Question",
            "name": "Was ist ein CMS?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Ein CMS ist ein Content-Management-System, mit dem Inhalte einer Website ohne Programmierkenntnisse gepflegt werden können. Es lohnt sich vor allem, wenn regelmäßig Texte, Bilder oder Beiträge geändert werden."
            }
          },
          {
            "@type": "Question",
            "name": "Was ist responsive Design?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Responsive Design bedeutet, dass sich eine Website automatisch an Smartphone, Tablet und Desktop anpasst. Das ist wichtig für Nutzererlebnis, Google-Bewertung und mobile Anfragen."
            }
          },
          {
            "@type": "Question",
            "name": "Was ist der Unterschied zwischen Domain, Hosting und Website?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Die Domain ist die Adresse, Hosting ist der Speicherplatz auf einem Server und die Website sind die sichtbaren Inhalte, Funktionen und Dateien, die Besucher aufrufen."
            }
          },
          {
            "@type": "Question",
            "name": "Wofür braucht eine Website HTTPS?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Ein SSL-Zertifikat verschlüsselt die Verbindung zwischen Website und Besucher. Es sorgt für HTTPS, Vertrauen, Datenschutz und eine sichere Übertragung bei Formularen."
            }
          },
          {
            "@type": "Question",
            "name": "Was ist eine Conversion-Rate?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Die Conversion-Rate zeigt, welcher Anteil der Besucher eine gewünschte Aktion ausführt, zum Beispiel Anfrage senden, anrufen, Termin buchen oder Angebot herunterladen."
            }
          },
          {
            "@type": "Question",
            "name": "Was bedeutet lokale Sichtbarkeit bei Google?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Lokales SEO optimiert eine Website und das Google-Unternehmensprofil für Suchanfragen mit Ortsbezug, etwa „in der Nähe“, Stadtname oder regionales Einzugsgebiet."
            }
          },
          {
            "@type": "Question",
            "name": "Was bedeutet DSGVO-konform bei einer Website?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "DSGVO-konform bedeutet, dass personenbezogene Daten rechtmäßig verarbeitet werden. Dazu gehören SSL, Datenschutzerklärung, Cookie-/Tracking-Regeln, sichere Formulare und passende Verträge mit Dienstleistern."
            }
          },
          {
            "@type": "Question",
            "name": "Was ist Page Speed?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Page Speed beschreibt, wie schnell eine Website lädt und nutzbar wird. Schnelle Seiten verbessern Nutzererlebnis, mobile Nutzung, Anfragen und oft auch SEO-Signale."
            }
          },
          {
            "@type": "Question",
            "name": "Was ist der Unterschied zwischen Website und Landingpage?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Eine Website umfasst mehrere Seiten und Informationen. Eine Landingpage ist eine fokussierte Einzelseite mit einem klaren Ziel, zum Beispiel Anfrage, Buchung oder Download."
            }
          },
          {
            "@type": "Question",
            "name": "Wie viele Keywords sollte eine Seite haben?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Eine gute Seite konzentriert sich auf ein Hauptthema und wenige eng verwandte Suchbegriffe. Zu viele Keywords verwässern die Suchintention und machen Texte oft schlechter lesbar."
            }
          },
          {
            "@type": "Question",
            "name": "Braucht jede Website ein Cookie-Banner?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Ein Cookie-Banner ist nötig, wenn nicht notwendige Cookies oder Tracking eingesetzt werden. Rein technisch notwendige Cookies können anders behandelt werden; im Zweifel sollte rechtlich geprüft werden."
            }
          },
          {
            "@type": "Question",
            "name": "Wie oft sollte man eine Website aktualisieren?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Technisch sollte eine Website laufend aktuell bleiben. Inhalte sollten immer dann angepasst werden, wenn sich Leistungen, Preise, Team, Öffnungszeiten, rechtliche Angaben oder wichtige Suchanfragen ändern."
            }
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
    <!-- ============ PAGE HERO ============ -->
    <section class="page-hero">
      <div class="container page-hero-grid">
          <div class="page-hero-copy">
            <p class="eyebrow eyebrow-green">Ratgeber &amp; Wissen</p>
                    <h1>Webdesign-Lexikon: 50 Fachbegriffe —<br /><span class="accent">endlich verständlich.</span></h1>
        <p class="page-lead">Webdesign, SEO, Technik, Recht und Marketing stecken voller Fachsprache. Hier erklären wir die 50 wichtigsten Begriffe in einfachen Worten — durchsuchbar und nach Themen sortiert.</p>
          </div>
          <figure class="hero-photo page-hero-media">
              <picture><source srcset="assets/hero-knowledge-petrol.webp" type="image/webp" /><img src="assets/hero-knowledge-petrol.png" alt="Arbeitsplatz mit aufgeschlagenen Notizen zum Website-Wissen" loading="eager" width="1792" height="1024" /></picture>
            </figure>
        </div>
    </section>

    <section class="hero-answer-strip">
      <div class="container">
        <p class="answer-first on-light">Dieser Ratgeber erklärt die <strong>50 wichtigsten Fachbegriffe</strong> rund um Webdesign, SEO, Technik, Recht und Marketing — verständlich, durchsuchbar und nach Themen sortiert.</p>
        <div class="decision-grid">
          <article class="decision-card">
            <span class="label">Angebote verstehen</span>
            <h3>Fachbegriffe einordnen</h3>
            <p>Sie erkennen schneller, welche Leistungen wichtig sind und welche Begriffe nur kompliziert klingen.</p>
          </article>
          <article class="decision-card is-blue">
            <span class="label">SEO &amp; Technik</span>
            <h3>Suchfragen direkt beantworten</h3>
            <p>Kosten, Ablauf, SEO, Wartung, BFSG und Onepager werden als eigene Ratgeber ausführlich erklärt.</p>
          </article>
          <article class="decision-card">
            <span class="label">Nächster Schritt</span>
            <h3>Besser vorbereitet anfragen</h3>
            <p>Wenn etwas offen bleibt, übersetzen wir es im Website-Vorschlag in klare Entscheidungen.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- ============ GLOSSAR ============ -->
    <section class="glossary">
      <div class="container">
        <!-- ============ RATGEBER-ARTIKEL ============ -->
        <h2 class="section-title" style="margin-bottom:18px;">Ratgeber-Artikel</h2>
        <div class="service-cards" style="margin-bottom:42px;">
          <?php foreach ($ratgeberCards as $card): ?>
            <?php $cardClass = 'service-card' . (($card['variant'] ?? '') === 'blue' ? ' is-blue' : ''); ?>
            <article class="<?= htmlspecialchars($cardClass, ENT_QUOTES, 'UTF-8') ?>">
              <h3><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p><?= htmlspecialchars($card['text'], ENT_QUOTES, 'UTF-8') ?></p>
              <a href="<?= htmlspecialchars($card['href'], ENT_QUOTES, 'UTF-8') ?>" class="svc-link"><?= htmlspecialchars($card['linkText'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">→</span></a>
            </article>
          <?php endforeach; ?>
        </div>

        <h2 class="section-title" style="margin-bottom:18px;">Entscheidungshilfen</h2>
        <div class="service-cards" style="margin-bottom:42px;">
          <?php foreach ($decisionCards as $card): ?>
            <?php $cardClass = 'service-card' . (($card['variant'] ?? '') === 'blue' ? ' is-blue' : ''); ?>
            <article class="<?= htmlspecialchars($cardClass, ENT_QUOTES, 'UTF-8') ?>">
              <h3><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p><?= htmlspecialchars($card['text'], ENT_QUOTES, 'UTF-8') ?></p>
              <a href="<?= htmlspecialchars($card['href'], ENT_QUOTES, 'UTF-8') ?>" class="svc-link"><?= htmlspecialchars($card['linkText'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">→</span></a>
            </article>
          <?php endforeach; ?>
        </div>

        <h2 class="sr-only">Webdesign-Lexikon von A bis Z</h2>
        <div class="glossary-tools">
          <div class="glossary-search">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input type="text" id="glossarySearch" placeholder="Begriff suchen … z. B. SEO, Domain, DSGVO" autocomplete="off" aria-label="Begriff suchen" />
          </div>
          <div class="glossary-cats" role="tablist" aria-label="Kategorien filtern">
            <button class="gcat active" data-cat="all">Alle</button>
            <button class="gcat" data-cat="design">Design &amp; UX</button>
            <button class="gcat" data-cat="seo">SEO &amp; Sichtbarkeit</button>
            <button class="gcat" data-cat="technik">Technik &amp; Hosting</button>
            <button class="gcat" data-cat="recht">Recht &amp; DSGVO</button>
            <button class="gcat" data-cat="marketing">Marketing &amp; Anfragen</button>
          </div>
        </div>

        <p class="glossary-count" id="glossaryCount">50 Begriffe</p>

        <div class="glossary-grid" id="glossaryGrid">
          <!-- Design & UX -->
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>Responsive Design</h3></summary><p>Gestaltungsansatz, bei dem sich das Layout automatisch an jede Bildschirmgröße anpasst — vom Smartphone bis zum großen Monitor.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>UX – User Experience</h3></summary><p>Das gesamte Erlebnis, das ein Besucher auf Ihrer Website hat: wie einfach, angenehm und zielführend sich die Bedienung anfühlt.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>UI – User Interface</h3></summary><p>Die sichtbare Benutzeroberfläche: Buttons, Menüs, Farben und alle Elemente, mit denen Besucher interagieren.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>Wireframe</h3></summary><p>Ein schlichtes Gerüst einer Seite ohne Farben und Bilder, das nur Struktur und Anordnung der Inhalte zeigt.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>Mockup</h3></summary><p>Ein visueller Entwurf, der schon nah am späteren Design ist — inklusive Farben, Schriften und Bildern.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>Above the Fold</h3></summary><p>Der Bereich, der ohne Scrollen sofort sichtbar ist. Hier entscheidet sich oft, ob Besucher bleiben oder abspringen.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>Handlungsaufforderung</h3></summary><p>Ein klarer nächster Schritt wie „Jetzt anfragen“ oder „Termin buchen“, der Besucher zur gewünschten Aktion führt.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>Hero-Bereich</h3></summary><p>Der große, aufmerksamkeitsstarke Kopfbereich ganz oben — meist mit Überschrift, Kernbotschaft und Handlungsaufforderung.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>White Space</h3></summary><p>Bewusst eingesetzte Leerflächen („Negativraum“), die ein Design ruhig, hochwertig und gut lesbar machen.</p></details>
          <details class="term-card" data-cat="design"><summary><span class="term-cat">Design &amp; UX</span><h3>Favicon</h3></summary><p>Das kleine Symbol, das im Browser-Tab und in Lesezeichen neben dem Seitentitel erscheint.</p></details>

          <!-- SEO & Sichtbarkeit -->
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>SEO – Suchmaschinenoptimierung</h3></summary><p>Alle Maßnahmen, damit Ihre Website bei Google &amp; Co. besser gefunden wird und mehr passende Besucher erhält.</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>Keyword</h3></summary><p>Ein Suchbegriff, den potenzielle Kunden bei Google eingeben. Auf relevante Keywords wird eine Seite gezielt optimiert.</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>Meta-Description</h3></summary><p>Der kurze Beschreibungstext, der in den Google-Ergebnissen unter dem Titel steht und zum Klicken anregen soll.</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>Title-Tag</h3></summary><p>Der Seitentitel, der als blaue, klickbare Überschrift in den Suchergebnissen und im Browser-Tab erscheint.</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>Backlink</h3></summary><p>Ein Link von einer anderen Website auf Ihre. Hochwertige Backlinks stärken das Vertrauen bei Suchmaschinen.</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>Sitemap</h3></summary><p>Eine strukturierte Übersicht aller Seiten, die Suchmaschinen hilft, Ihre Website vollständig zu erfassen.</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>Alt-Text</h3></summary><p>Ein beschreibender Text für Bilder, der Suchmaschinen und Screenreadern erklärt, was auf dem Bild zu sehen ist.</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>Lokales SEO</h3></summary><p>Optimierung für Suchanfragen mit Ortsbezug, inklusive Google-Profil und einheitlicher Adressdaten (NAP-Konsistenz).</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>SERP</h3></summary><p>Search Engine Results Page: die Seite mit den Suchergebnissen, die Google nach einer Suchanfrage anzeigt.</p></details>
          <details class="term-card" data-cat="seo"><summary><span class="term-cat">SEO &amp; Sichtbarkeit</span><h3>Ranking</h3></summary><p>Die Position, auf der Ihre Website für ein bestimmtes Keyword in den Suchergebnissen erscheint.</p></details>

          <!-- Technik & Hosting -->
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>Domain</h3></summary><p>Die Internetadresse Ihrer Website, z. B. Ihre-firma.de — Ihre digitale Hausnummer.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>Hosting</h3></summary><p>Der Speicherplatz auf einem Server, auf dem Ihre Website liegt und rund um die Uhr abrufbar gehalten wird.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>SSL / HTTPS</h3></summary><p>Eine Verschlüsselung, die Daten zwischen Besucher und Website schützt. Erkennbar am Schloss-Symbol und „https://“.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>CMS</h3></summary><p>Ein System, mit dem Texte, Bilder und Inhalte einer Website auch ohne Programmierkenntnisse gepflegt werden können.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>Cache</h3></summary><p>Ein Zwischenspeicher, der Inhalte vorhält, damit Seiten beim erneuten Aufruf deutlich schneller laden.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>CDN</h3></summary><p>Ein weltweites Servernetz, das Website-Dateien vom nächstgelegenen Standort ausliefert und so die Ladezeit verkürzt.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>Google-Ladezeitwerte</h3></summary><p>Googles Kennzahlen für Ladezeit, Reaktionsfähigkeit und visuelle Stabilität — wichtig für Ranking und Nutzererlebnis.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>DNS</h3></summary><p>Domain Name System: das „Telefonbuch“ des Internets, das Domainnamen in die zugehörigen Server-Adressen übersetzt.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>Backup</h3></summary><p>Eine Sicherheitskopie Ihrer Website, mit der sich der letzte Stand im Notfall schnell wiederherstellen lässt.</p></details>
          <details class="term-card" data-cat="technik"><summary><span class="term-cat">Technik &amp; Hosting</span><h3>Ladezeit (Page Speed)</h3></summary><p>Die Zeit, bis eine Seite vollständig geladen ist. Kurze Ladezeiten verbessern Nutzererlebnis und Ranking.</p></details>

          <!-- Recht & DSGVO -->
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>DSGVO</h3></summary><p>Die Datenschutz-Grundverordnung der EU regelt, wie personenbezogene Daten erhoben und verarbeitet werden dürfen.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>Impressumspflicht</h3></summary><p>Gesetzliche Pflicht in Deutschland, auf geschäftlichen Websites ein vollständiges Impressum mit Anbieterangaben zu führen.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>Datenschutzerklärung</h3></summary><p>Pflichttext, der verständlich erklärt, welche Daten auf der Website wie und warum verarbeitet werden.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>Cookie-Consent</h3></summary><p>Die Einwilligung, die Besucher per Banner geben, bevor nicht notwendige Cookies gesetzt werden dürfen.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>BFSG</h3></summary><p>Barrierefreiheitsstärkungsgesetz: verpflichtet seit Juni 2025 bestimmte Anbieter zu barrierefreien digitalen Angeboten. Kleinstunternehmer-Dienstleister sind ausgenommen.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>AV-Vertrag</h3></summary><p>Vertrag zur Auftragsverarbeitung: regelt datenschutzkonform, wenn ein Dienstleister personenbezogene Daten in Ihrem Auftrag verarbeitet.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>Widerrufsrecht</h3></summary><p>Verbrauchern steht bei online geschlossenen Verträgen grundsätzlich ein 14-tägiges Widerrufsrecht zu.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>Barrierefreiheit</h3></summary><p>Gestaltung, die Websites für möglichst alle Menschen nutzbar macht — etwa für Menschen mit Seh- oder Bewegungseinschränkungen.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>Tracking</h3></summary><p>Das Erfassen von Besucherverhalten (z. B. mit Analytics-Tools), um eine Website zu verbessern — nur datenschutzkonform erlaubt.</p></details>
          <details class="term-card" data-cat="recht"><summary><span class="term-cat">Recht &amp; DSGVO</span><h3>Drittanbieter-Cookies</h3></summary><p>Cookies, die nicht von der besuchten Website selbst, sondern von externen Diensten gesetzt werden — datenschutzrechtlich besonders sensibel.</p></details>

          <!-- Marketing & Anfragen -->
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>Conversion-Rate</h3></summary><p>Der Anteil der Besucher, die eine gewünschte Aktion ausführen (z. B. eine Anfrage) — eine zentrale Erfolgskennzahl.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>Lead</h3></summary><p>Ein qualifizierter Interessent, der z. B. über ein Formular Kontaktdaten hinterlässt und zum Kunden werden kann.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>Anfrageweg</h3></summary><p>Der „Verkaufstrichter“: der Weg vom ersten Besuch bis zur Anfrage, in mehreren aufeinander folgenden Stufen gedacht.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>Landingpage</h3></summary><p>Eine fokussierte Einzelseite mit genau einem Ziel — etwa eine Kampagnenseite, die gezielt zur Anfrage führt.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>A/B-Test</h3></summary><p>Vergleich zweier Varianten einer Seite, um datenbasiert herauszufinden, welche besser funktioniert.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>Google Business Profile</h3></summary><p>Der kostenlose Google-Unternehmenseintrag, der bei lokaler Suche und in Maps erscheint — wichtig für lokale Sichtbarkeit.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>E-Mail-Marketing</h3></summary><p>Direkte Kommunikation mit Interessenten und Kunden per Newsletter, um Beziehungen aufzubauen und Angebote zu platzieren.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>KPI</h3></summary><p>Key Performance Indicator: eine messbare Kennzahl, an der sich der Erfolg einer Maßnahme ablesen lässt.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>Retargeting</h3></summary><p>Werbung, die gezielt Personen erneut anspricht, die Ihre Website bereits besucht, aber noch nicht angefragt haben.</p></details>
          <details class="term-card" data-cat="marketing"><summary><span class="term-cat">Marketing &amp; Anfragen</span><h3>Bounce-Rate</h3></summary><p>Absprungrate: der Anteil der Besucher, die die Website nach nur einer Seite wieder verlassen.</p></details>
        </div>

        <div class="glossary-empty" id="glossaryEmpty">Kein Begriff gefunden. Versuchen Sie es mit einem anderen Suchwort oder einer anderen Kategorie.</div>
      </div>
    </section>

    <!-- ============ FAQ ============ -->
    <section class="faq alt" id="faq">
      <div class="container">
        <div class="faq-head">
          <p class="eyebrow eyebrow-green center">Häufige Fragen</p>
          <h2 class="section-title">14 Fragen aus der Praxis.</h2>
          <p>Sie müssen kein Profi sein, um eine gute Website zu bekommen. Hier beantworten wir die Fragen, die uns am häufigsten begegnen.</p>
        </div>

        <div class="faq-list">
          <details class="faq-item"><summary>Was bedeutet Webdesign?</summary><div class="faq-a">Webdesign beschreibt die Gestaltung und Struktur einer Website: Layout, Farben, Schriften, Nutzerführung, mobile Darstellung und die Frage, wie schnell Besucher verstehen, was ein Unternehmen anbietet.</div></details>
          <details class="faq-item"><summary>Was ist SEO einfach erklärt?</summary><div class="faq-a">SEO bedeutet Suchmaschinenoptimierung. Ziel ist, dass eine Website bei passenden Suchanfragen in Google besser gefunden wird, weil Inhalte, Struktur, Technik und Relevanz stimmen.</div></details>
          <details class="faq-item"><summary>Was ist ein CMS?</summary><div class="faq-a">Ein CMS ist ein Content-Management-System, mit dem Inhalte einer Website ohne Programmierkenntnisse gepflegt werden können. Es lohnt sich vor allem, wenn regelmäßig Texte, Bilder oder Beiträge geändert werden.</div></details>
          <details class="faq-item"><summary>Was ist responsive Design?</summary><div class="faq-a">Responsive Design bedeutet, dass sich eine Website automatisch an Smartphone, Tablet und Desktop anpasst. Das ist wichtig für Nutzererlebnis, Google-Bewertung und mobile Anfragen.</div></details>
          <details class="faq-item"><summary>Was ist der Unterschied zwischen Domain, Hosting und Website?</summary><div class="faq-a">Die Domain ist die Adresse, Hosting ist der Speicherplatz auf einem Server und die Website sind die sichtbaren Inhalte, Funktionen und Dateien, die Besucher aufrufen.</div></details>
          <details class="faq-item"><summary>Wofür braucht eine Website HTTPS?</summary><div class="faq-a">Ein SSL-Zertifikat verschlüsselt die Verbindung zwischen Website und Besucher. Es sorgt für HTTPS, Vertrauen, Datenschutz und eine sichere Übertragung bei Formularen.</div></details>
          <details class="faq-item"><summary>Was ist eine Conversion-Rate?</summary><div class="faq-a">Die Conversion-Rate zeigt, welcher Anteil der Besucher eine gewünschte Aktion ausführt, zum Beispiel Anfrage senden, anrufen, Termin buchen oder Angebot herunterladen.</div></details>
          <details class="faq-item"><summary>Was bedeutet lokale Sichtbarkeit bei Google?</summary><div class="faq-a">Lokales SEO optimiert eine Website und das Google-Unternehmensprofil für Suchanfragen mit Ortsbezug, etwa „in der Nähe“, Stadtname oder regionales Einzugsgebiet.</div></details>
          <details class="faq-item"><summary>Was bedeutet DSGVO-konform bei einer Website?</summary><div class="faq-a">DSGVO-konform bedeutet, dass personenbezogene Daten rechtmäßig verarbeitet werden. Dazu gehören SSL, Datenschutzerklärung, Cookie-/Tracking-Regeln, sichere Formulare und passende Verträge mit Dienstleistern.</div></details>
          <details class="faq-item"><summary>Was ist Page Speed?</summary><div class="faq-a">Page Speed beschreibt, wie schnell eine Website lädt und nutzbar wird. Schnelle Seiten verbessern Nutzererlebnis, mobile Nutzung, Anfragen und oft auch SEO-Signale.</div></details>
          <details class="faq-item"><summary>Was ist der Unterschied zwischen Website und Landingpage?</summary><div class="faq-a">Eine Website umfasst mehrere Seiten und Informationen. Eine Landingpage ist eine fokussierte Einzelseite mit einem klaren Ziel, zum Beispiel Anfrage, Buchung oder Download.</div></details>
          <details class="faq-item"><summary>Wie viele Keywords sollte eine Seite haben?</summary><div class="faq-a">Eine gute Seite konzentriert sich auf ein Hauptthema und wenige eng verwandte Suchbegriffe. Zu viele Keywords verwässern die Suchintention und machen Texte oft schlechter lesbar.</div></details>
          <details class="faq-item"><summary>Braucht jede Website ein Cookie-Banner?</summary><div class="faq-a">Ein Cookie-Banner ist nötig, wenn nicht notwendige Cookies oder Tracking eingesetzt werden. Rein technisch notwendige Cookies können anders behandelt werden; im Zweifel sollte rechtlich geprüft werden.</div></details>
          <details class="faq-item"><summary>Wie oft sollte man eine Website aktualisieren?</summary><div class="faq-a">Technisch sollte eine Website laufend aktuell bleiben. Inhalte sollten immer dann angepasst werden, wenn sich Leistungen, Preise, Team, Öffnungszeiten, rechtliche Angaben oder wichtige Suchanfragen ändern.</div></details>
        </div>
      </div>
    </section>

    <!-- ============ Handlungsaufforderung BAND ============ -->
    <section class="guarantee">
      <div class="container guarantee-inner">
        <span class="guarantee-icon">
          <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/></svg>
        </span>
        <div class="guarantee-text">
          <h3>Genug Theorie — starten wir?</h3>
          <p>Sie müssen nichts davon auswendig können. Sagen Sie uns einfach, was Sie brauchen — den Rest übersetzen wir in eine fertige Website.</p>
        </div>
        <a href="anfrage.php" class="btn btn-primary btn-lg">Angebot in 2 Min. starten <span class="arrow">→</span></a>
      </div>
    </section>
  </main>

  <?php render_site_footer('./'); ?>

  <script src="script.js"></script>
  <script src="cookies.js?v=2"></script>
  <script src="fab.js?v=3"></script>
  <script src="ratgeber.js?v=2"></script>
</body>
</html>

