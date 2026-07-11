<?php

function ratgeber_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function render_ratgeber_article(string $slug): void
{
    $articles = require __DIR__ . '/ratgeber-articles.php';
    if (!isset($articles[$slug])) {
        http_response_code(404);
        echo 'Ratgeber nicht gefunden.';
        return;
    }

    $article = $articles[$slug];
    $url = 'https://[DOMAIN]/' . $slug;
    $graph = [
        [
            '@type' => 'Organization',
            '@id' => 'https://[DOMAIN]/#organization',
            'name' => 'Sartu',
            'url' => 'https://[DOMAIN]/',
            'logo' => 'https://[DOMAIN]/assets/logo-teal.png',
            'email' => 'hallo@sartu.de',
            'description' => 'Webdesign-Agentur für Festpreis-Websites: Webdesign, SEO, lokales SEO, Wartung & Hosting in Deutschland — kontaktlos, transparent, DSGVO-konform.',
            'areaServed' => 'DE',
            'sameAs' => [],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Start',
                    'item' => 'https://[DOMAIN]/',
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Ratgeber',
                    'item' => 'https://[DOMAIN]/ratgeber',
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => strip_tags($article['h1'] . ' ' . $article['accent']),
                    'item' => $url,
                ],
            ],
        ],
        [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(static function (array $faq): array {
                return [
                    '@type' => 'Question',
                    'name' => $faq['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['a'],
                    ],
                ];
            }, $article['faqs']),
        ],
        [
            '@type' => 'Article',
            'headline' => strip_tags($article['h1'] . ' ' . $article['accent']),
            'description' => $article['description'],
            'datePublished' => '2026-06-20',
            'dateModified' => '2026-06-20',
            'author' => [
                '@type' => 'Organization',
                'name' => 'Sartu',
            ],
            'publisher' => [
                '@id' => 'https://[DOMAIN]/#organization',
            ],
        ],
    ];

    $jsonLd = json_encode(
        ['@context' => 'https://schema.org', '@graph' => $graph],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
    );

    require_once __DIR__ . '/site-partials.php';
    ?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?= ratgeber_e($article['description']) ?>" />
  <title><?= ratgeber_e($article['title']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css?v=47" />
  <link rel="canonical" href="<?= ratgeber_e($url) ?>" />
  <meta name="robots" content="noindex,nofollow" />
  <meta property="og:type" content="article" />
  <meta property="og:site_name" content="Sartu" />
  <meta property="og:locale" content="de_DE" />
  <meta property="og:url" content="<?= ratgeber_e($url) ?>" />
  <meta property="og:title" content="<?= ratgeber_e($article['title']) ?>" />
  <meta property="og:description" content="<?= ratgeber_e($article['description']) ?>" />
  <meta property="og:image" content="https://[DOMAIN]/[OG-IMAGE]" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= ratgeber_e($article['title']) ?>" />
  <meta name="twitter:description" content="<?= ratgeber_e($article['description']) ?>" />
  <meta name="twitter:image" content="https://[DOMAIN]/[OG-IMAGE]" />
  <script type="application/ld+json">
  <?= $jsonLd . "\n" ?>
  </script>
</head>
<body>
  <?php render_site_header('./'); ?>

  <main>
    <section class="page-hero">
      <div class="container page-hero-grid">
        <div class="page-hero-copy">
          <nav class="crumb" aria-label="Brotkrumen"><a href="ratgeber.php">Ratgeber</a> · <?= ratgeber_e(strip_tags($article['h1'])) ?></nav>
          <p class="eyebrow eyebrow-green">Ratgeber &amp; Wissen</p>
          <h1><?= ratgeber_e($article['h1']) ?><br /><span class="accent"><?= ratgeber_e($article['accent']) ?></span></h1>
          <p class="page-lead"><?= ratgeber_e($article['lead']) ?></p>
          <p class="article-stand">Stand: Juni&nbsp;2026 · Lesezeit ca. 7&nbsp;Minuten</p>
          <nav class="page-toc" aria-label="Inhalt">
            <?php foreach ($article['toc'] as $item): ?>
              <a href="#<?= ratgeber_e($item['id']) ?>"><?= ratgeber_e($item['label']) ?></a>
            <?php endforeach; ?>
          </nav>
        </div>
        <figure class="hero-photo page-hero-media">
          <picture><source srcset="assets/hero-knowledge-petrol.webp" type="image/webp" /><img src="assets/hero-knowledge-petrol.png" alt="Arbeitsplatz mit Notizen passend zum Ratgeberthema" loading="eager" width="1792" height="1024" /></picture>
        </figure>
      </div>
    </section>

    <section class="hero-answer-strip">
      <div class="container">
        <p class="answer-first on-light"><?= ratgeber_e($article['answer']) ?></p>
        <div class="decision-grid two">
          <article class="decision-card">
            <span class="label">Einordnung</span>
            <h3><?= ratgeber_e($article['story_label']) ?></h3>
            <p><?= ratgeber_e($article['story']) ?></p>
          </article>
          <article class="decision-card is-blue">
            <span class="label">Nächster Schritt</span>
            <h3>Erst verstehen, dann entscheiden</h3>
            <p>Der Artikel beantwortet die wichtigsten Suchfragen kurz oben und erklärt Details darunter mit Beispielen, Kosten, Risiken und sinnvollen internen Links.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="legal">
      <div class="container">
        <article class="legal-content">
          <?php foreach ($article['sections'] as $section): ?>
            <h2 id="<?= ratgeber_e($section['id']) ?>"><?= ratgeber_e($section['h2']) ?></h2>
            <?= $section['html'] . "\n" ?>
          <?php endforeach; ?>
        </article>
      </div>
    </section>

    <section class="faq alt" id="faq">
      <div class="container">
        <div class="faq-head">
          <p class="eyebrow eyebrow-green center">Häufige Fragen</p>
          <h2 class="section-title">Kurz beantwortet.</h2>
        </div>
        <div class="faq-list">
          <?php foreach ($article['faqs'] as $faq): ?>
            <details class="faq-item"><summary><?= ratgeber_e($faq['q']) ?></summary><div class="faq-a"><?= ratgeber_e($faq['a']) ?></div></details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="guarantee">
      <div class="container guarantee-inner">
        <span class="guarantee-icon">
          <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/></svg>
        </span>
        <div class="guarantee-text">
          <h3>Aus der Frage soll eine Website werden?</h3>
          <p>Starten Sie die geführte Website-Anfrage. Sie bekommen eine klare Empfehlung, welcher nächste Schritt sinnvoll ist.</p>
        </div>
        <a href="anfrage.php" class="btn btn-primary btn-lg">Website-Anfrage starten <span class="arrow">→</span></a>
      </div>
    </section>
  </main>

  <?php render_site_footer('./'); ?>

  <script src="script.js?v=2"></script>
  <script src="cookies.js?v=2"></script>
  <script src="fab.js?v=3"></script>
</body>
</html>
<?php
}

