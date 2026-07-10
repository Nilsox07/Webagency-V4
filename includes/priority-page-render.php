<?php
declare(strict_types=1);

function priority_page_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function priority_page_url(string $slug): string
{
    return 'https://[DOMAIN]/' . $slug;
}

function render_priority_page_card(array $card, string $layout): void
{
    $isServiceCard = $layout === 'service-cards';
    $className = $isServiceCard ? 'service-card' : 'decision-card';
    if (($card['variant'] ?? '') === 'blue') {
        $className .= ' is-blue';
    }
    ?>
          <article class="<?= $className ?>">
            <?php if (!empty($card['label']) && !$isServiceCard): ?><span class="label"><?= priority_page_e($card['label']) ?></span><?php endif; ?>
            <h3><?= priority_page_e($card['title']) ?></h3>
            <p><?= priority_page_e($card['text']) ?></p>
            <?php if (!empty($card['items'])): ?>
              <ul>
                <?php foreach ($card['items'] as $item): ?><li><?= priority_page_e($item) ?></li><?php endforeach; ?>
              </ul>
            <?php endif; ?>
            <?php if (!empty($card['href'])): ?>
              <a href="<?= priority_page_e($card['href']) ?>" class="svc-link"><?= priority_page_e($card['linkText'] ?? 'Mehr erfahren') ?> <span aria-hidden="true">&rarr;</span></a>
            <?php endif; ?>
          </article>
    <?php
}

function render_priority_page_section(array $section, int $index): void
{
    $className = $section['class'] ?? ($index % 2 === 0 ? 'svc-section tint' : 'svc-section light-bg');
    $cardsLayout = $section['layout'] ?? 'decision-grid';
    ?>
    <section class="<?= priority_page_e($className) ?>">
      <div class="container">
        <div class="pricing-head">
          <?php if (!empty($section['eyebrow'])): ?><p class="eyebrow eyebrow-green center"><?= priority_page_e($section['eyebrow']) ?></p><?php endif; ?>
          <h2 class="section-title"><?= priority_page_e($section['title']) ?></h2>
          <?php if (!empty($section['intro'])): ?><p><?= priority_page_e($section['intro']) ?></p><?php endif; ?>
        </div>

        <?php if (!empty($section['answer'])): ?>
          <p class="answer-first on-light"><?= priority_page_e($section['answer']) ?></p>
        <?php endif; ?>

        <?php if (!empty($section['cards'])): ?>
          <div class="<?= priority_page_e($cardsLayout) ?>">
            <?php foreach ($section['cards'] as $card): ?>
              <?php render_priority_page_card($card, $cardsLayout); ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($section['table'])): ?>
          <div class="price-table-wrap">
            <table class="price-table">
              <thead>
                <tr>
                  <?php foreach ($section['table']['headers'] as $header): ?><th><?= priority_page_e($header) ?></th><?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($section['table']['rows'] as $row): ?>
                  <tr>
                    <?php foreach ($row as $cellIndex => $cell): ?>
                      <td class="<?= $cellIndex === 0 ? 'feature' : '' ?>"><?= priority_page_e($cell) ?></td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

        <?php if (!empty($section['note'])): ?>
          <div class="callout callout-guarantee">
            <span class="callout-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg>
            </span>
            <div>
              <h3><?= priority_page_e($section['note']['title']) ?></h3>
              <p><?= priority_page_e($section['note']['text']) ?></p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>
    <?php
}

function render_priority_page(string $slug): void
{
    $pages = require __DIR__ . '/priority-pages.php';
    if (!isset($pages[$slug])) {
        http_response_code(404);
        echo 'Seite nicht gefunden.';
        return;
    }

    $page = $pages[$slug];
    $url = priority_page_url($slug);
    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Start',
            'item' => 'https://[DOMAIN]/',
        ],
    ];

    if (!empty($page['parent'])) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $page['parent']['label'],
            'item' => priority_page_url($page['parent']['slug']),
        ];
    }

    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => count($breadcrumbItems) + 1,
        'name' => strip_tags($page['h1'] . ' ' . $page['accent']),
        'item' => $url,
    ];

    $graph = [
        [
            '@type' => 'Organization',
            '@id' => 'https://[DOMAIN]/#organization',
            'name' => 'Sartu',
            'url' => 'https://[DOMAIN]/',
            'logo' => 'https://[DOMAIN]/assets/logo-teal.png',
            'email' => 'hallo@sartu.de',
            'description' => 'Webdesign-Agentur für Festpreis-Websites: Webdesign, SEO, lokales SEO, Wartung und Hosting in Deutschland.',
            'areaServed' => 'DE',
            'sameAs' => [],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ],
        [
            '@type' => 'WebPage',
            '@id' => $url . '#webpage',
            'url' => $url,
            'name' => $page['title'],
            'description' => $page['description'],
            'inLanguage' => 'de-DE',
            'isPartOf' => [
                '@id' => 'https://[DOMAIN]/#website',
            ],
            'about' => $page['schemaAbout'] ?? 'Webdesign, SEO und Website-Strategie',
        ],
    ];

    if (!empty($page['faqs'])) {
        $graph[] = [
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
            }, $page['faqs']),
        ];
    }

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
  <meta name="description" content="<?= priority_page_e($page['description']) ?>" />
  <title><?= priority_page_e($page['title']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css?v=45" />
  <link rel="canonical" href="<?= priority_page_e($url) ?>" />
  <meta name="robots" content="noindex,nofollow" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Sartu" />
  <meta property="og:locale" content="de_DE" />
  <meta property="og:url" content="<?= priority_page_e($url) ?>" />
  <meta property="og:title" content="<?= priority_page_e($page['title']) ?>" />
  <meta property="og:description" content="<?= priority_page_e($page['description']) ?>" />
  <meta property="og:image" content="https://[DOMAIN]/[OG-IMAGE]" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= priority_page_e($page['title']) ?>" />
  <meta name="twitter:description" content="<?= priority_page_e($page['description']) ?>" />
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
          <nav class="breadcrumb" aria-label="Brotkrumen">
            <a href="./">Start</a>
            <?php if (!empty($page['parent'])): ?><span class="sep">/</span><a href="<?= priority_page_e($page['parent']['slug']) ?>.php"><?= priority_page_e($page['parent']['label']) ?></a><?php endif; ?>
            <span class="sep">/</span><span aria-current="page"><?= priority_page_e($page['breadcrumb'] ?? strip_tags($page['h1'])) ?></span>
          </nav>
          <p class="eyebrow eyebrow-green"><?= priority_page_e($page['eyebrow']) ?></p>
          <h1><?= priority_page_e($page['h1']) ?><br /><span class="accent"><?= priority_page_e($page['accent']) ?></span></h1>
          <p class="page-lead"><?= priority_page_e($page['lead']) ?></p>
          <?php if (!empty($page['heroLinks'])): ?>
            <div class="hero-actions">
              <?php foreach ($page['heroLinks'] as $link): ?>
                <a href="<?= priority_page_e($link['href']) ?>" class="<?= priority_page_e($link['class'] ?? 'btn btn-outline btn-lg') ?>"><?= priority_page_e($link['label']) ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        <figure class="hero-photo page-hero-media">
          <picture><source srcset="assets/<?= priority_page_e($page['image']) ?>.webp" type="image/webp" /><img src="assets/<?= priority_page_e($page['image']) ?>.png" alt="<?= priority_page_e($page['imageAlt']) ?>" loading="eager" width="1792" height="1024" /></picture>
        </figure>
      </div>
    </section>

    <section class="hero-answer-strip">
      <div class="container">
        <p class="answer-first on-light"><?= priority_page_e($page['answer']) ?></p>
        <div class="decision-grid">
          <?php foreach ($page['highlights'] as $highlight): ?>
            <article class="decision-card">
              <span class="label"><?= priority_page_e($highlight['label']) ?></span>
              <h3><?= priority_page_e($highlight['title']) ?></h3>
              <p><?= priority_page_e($highlight['text']) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <?php foreach ($page['sections'] as $sectionIndex => $section): ?>
      <?php render_priority_page_section($section, $sectionIndex); ?>
    <?php endforeach; ?>

    <?php if (!empty($page['related'])): ?>
      <section class="pricing alt">
        <div class="container">
          <div class="pricing-head">
            <p class="eyebrow eyebrow-green center"><?= priority_page_e($page['relatedEyebrow'] ?? 'Weiterlesen') ?></p>
            <h2 class="section-title"><?= priority_page_e($page['relatedTitle'] ?? 'Passende nächste Schritte.') ?></h2>
          </div>
          <div class="service-cards">
            <?php foreach ($page['related'] as $card): ?>
              <?php render_priority_page_card($card, 'service-cards'); ?>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php if (!empty($page['faqs'])): ?>
      <section class="faq alt" id="faq">
        <div class="container">
          <div class="faq-head">
            <p class="eyebrow eyebrow-green center">Häufige Fragen</p>
            <h2 class="section-title"><?= priority_page_e($page['faqTitle'] ?? 'Kurz beantwortet.') ?></h2>
          </div>
          <div class="faq-list">
            <?php foreach ($page['faqs'] as $faq): ?>
              <details class="faq-item"><summary><?= priority_page_e($faq['q']) ?></summary><div class="faq-a"><?= priority_page_e($faq['a']) ?></div></details>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <section class="guarantee">
      <div class="container guarantee-inner">
        <span class="guarantee-icon">
          <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/></svg>
        </span>
        <div class="guarantee-text">
          <h3><?= priority_page_e($page['cta']['title']) ?></h3>
          <p><?= priority_page_e($page['cta']['text']) ?></p>
        </div>
        <a href="<?= priority_page_e($page['cta']['href'] ?? 'anfrage.php') ?>" class="btn btn-primary btn-lg"><?= priority_page_e($page['cta']['label'] ?? 'Website-Anfrage starten') ?> <span class="arrow">&rarr;</span></a>
      </div>
    </section>
  </main>

  <?php render_site_footer('./'); ?>

  <script src="script.js"></script>
  <script src="cookies.js?v=2"></script>
  <script src="fab.js?v=3"></script>
</body>
</html>
<?php
}

