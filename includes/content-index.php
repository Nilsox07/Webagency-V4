<?php
declare(strict_types=1);

function sartu_ratgeber_hub_cards(): array
{
    $articles = require __DIR__ . '/ratgeber-articles.php';
    $cards = [
        [
            'slug' => 'ratgeber-website-erstellen',
            'title' => 'Website erstellen lassen',
            'text' => 'Ablauf, Seitenumfang, Inhalte, SEO, Kosten und Checkliste für den Start einer professionellen Firmenwebsite.',
            'linkText' => 'Website-Planung verstehen',
        ],
        [
            'slug' => 'ratgeber-website-kosten',
            'title' => 'Was kostet eine Website 2026?',
            'text' => 'Ehrliche Zahlen statt "kommt drauf an": Preisbestandteile, laufende Kosten und typische Kostenfallen.',
            'linkText' => 'Was eine Website 2026 kostet',
        ],
        [
            'slug' => 'ratgeber-seo-kosten',
            'title' => 'SEO Kosten & Dauer',
            'text' => 'Was Suchmaschinenoptimierung kostet, wie lange Ergebnisse dauern und welche Maßnahmen wirklich zählen.',
            'linkText' => 'SEO realistisch einschätzen',
        ],
        [
            'slug' => 'ratgeber-lokales-seo',
            'title' => 'Lokales SEO & Google Maps',
            'text' => 'Wie regionale Unternehmen bei "in der Nähe"-Suchen, Maps und lokalen Leistungsbegriffen sichtbar werden.',
            'linkText' => 'Lokal besser gefunden werden',
        ],
        [
            'slug' => 'ratgeber-relaunch-seo',
            'title' => 'Website-Relaunch ohne Rankingverlust',
            'text' => 'URLs, Weiterleitungen, Inhalte und Technik: die SEO-Checkliste für Redesign und Relaunch.',
            'linkText' => 'Relaunch sauber planen',
        ],
        [
            'slug' => 'ratgeber-website-wartung',
            'title' => 'Website-Wartung & Hosting',
            'text' => 'Monatliche Kosten, Backups, SSL, Updates, Sicherheit und was nach dem Onlinegang wichtig bleibt.',
            'linkText' => 'Wartung verstehen',
        ],
        [
            'slug' => 'ratgeber-website-texte',
            'title' => 'Website-Texte schreiben',
            'text' => 'SEO-Texte, Kundenansprache, Keywords, Seitenstruktur und der Spagat zwischen Fachsprache und Klarheit.',
            'linkText' => 'Texte besser planen',
        ],
        [
            'slug' => 'ratgeber-website-angebot',
            'title' => 'Website-Angebot prüfen',
            'text' => 'Welche Punkte in ein Angebot gehören, welche Kosten fehlen können und wie Sie Website-Angebote fair vergleichen.',
            'linkText' => 'Angebot sicher prüfen',
            'variant' => 'blue',
        ],
        [
            'slug' => 'ratgeber-website-bilder',
            'title' => 'Website-Bilder & Fotos',
            'text' => 'Welche Bilder Firmenwebsites wirklich brauchen: Team, Standort, Arbeitssituationen, Bildrechte, SEO und Ladezeit.',
            'linkText' => 'Bilder richtig planen',
        ],
        [
            'slug' => 'ratgeber-bfsg',
            'title' => 'BFSG: Barrierefreiheit Pflicht?',
            'text' => 'Seit 28.06.2025 in Kraft: wer betroffen ist, wer ausgenommen ist und was barrierefrei konkret heißt.',
            'linkText' => 'BFSG-Pflichten prüfen',
        ],
        [
            'slug' => 'ratgeber-onepager',
            'title' => 'One-Pager oder mehrseitig?',
            'text' => 'Wann eine Seite reicht, wann Google mehrere braucht und welche Struktur zu Ihrem Angebot passt.',
            'linkText' => 'One-Pager vs. mehrseitig',
        ],
        [
            'slug' => 'ratgeber-foerderung',
            'title' => 'Förderung für Websites',
            'text' => 'Wann eine Website gefördert werden kann und warum "Antrag vor Auftrag" entscheidend ist.',
            'linkText' => 'Förder-Grundregeln lesen',
        ],
    ];

    return array_map(static function (array $card) use ($articles): array {
        $slug = $card['slug'];
        if (!isset($card['text']) && isset($articles[$slug]['description'])) {
            $card['text'] = $articles[$slug]['description'];
        }

        $card['href'] = $slug . '.php';
        $card['variant'] = $card['variant'] ?? '';

        return $card;
    }, $cards);
}

function sartu_contextual_seo_hub_cards(): array
{
    $priorityPages = require __DIR__ . '/priority-pages.php';
    $cards = [
        [
            'slug' => 'vergleiche',
            'title' => 'Website-Vergleiche',
            'text' => 'Baukasten oder Agentur, Freelancer oder Agentur, WordPress oder individuell: fair und ohne Verkaufsdruck erklärt.',
            'linkText' => 'Vergleiche lesen',
        ],
        [
            'slug' => 'branchen',
            'title' => 'Webdesign nach Branche',
            'text' => 'Welche Seitenstruktur für Handwerker, Praxen, Kanzleien, Dienstleister und lokale Unternehmen sinnvoll ist.',
            'linkText' => 'Branchen ansehen',
        ],
        [
            'slug' => 'qualitaet',
            'title' => 'Qualität statt Fake-Referenzen',
            'text' => 'Wie ein transparenter Prüf- und Abnahmeprozess Vertrauen schafft, ohne künstliche Beweise oder Bewertungen einzubauen.',
            'linkText' => 'Qualitätsprozess ansehen',
        ],
    ];

    return array_map(static function (array $card) use ($priorityPages): array {
        $slug = $card['slug'];
        if (!isset($card['text']) && isset($priorityPages[$slug]['description'])) {
            $card['text'] = $priorityPages[$slug]['description'];
        }

        $card['href'] = $slug . '.php';
        $card['variant'] = $card['variant'] ?? '';

        return $card;
    }, $cards);
}
