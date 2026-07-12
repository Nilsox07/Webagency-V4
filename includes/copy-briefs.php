<?php
declare(strict_types=1);

/**
 * Copy-Briefs je öffentlicher Seite — die inhaltliche Grundlage fürs Text-System.
 *
 * Jeder Brief = { slug, keyword, intent, audience, kernbotschaft, beweise[], cta,
 *                 title (<=60), description (<=155) }.
 * Texte werden aus Brief + TEXT-SYSTEM.md geschrieben; Fakten stammen aus pricing.js.
 * Bewusst maschinenlesbar: ein späteres Claude-API-Tool und die Kundenseiten nutzen
 * dieselbe Struktur (deckt sich mit den SEO-Feldern in site-content-schema.php).
 *
 * Zugriff: sartu_copy_brief('index') bzw. sartu_copy_briefs().
 */

function sartu_copy_briefs(): array
{
    return [
        'index' => [
            'slug'         => 'index',
            'keyword'      => 'Webdesign-Agentur Festpreis',
            'intent'       => 'kommerziell — Firma sucht eine Website, die Kunden bringt',
            'audience'     => 'Inhaber kleiner/mittlerer Betriebe ohne Web-Vorwissen',
            'kernbotschaft'=> 'Website zum Festpreis, die Anfragen bringt — fertig in 7–14 Werktagen, ohne Agentur-Nebel.',
            'beweise'      => ['3 Beispielprojekte live ansehen', 'Festpreis vorab, keine Miet-Website', 'feste Ansprechperson', 'Hosting in Deutschland, DSGVO', 'Zufriedenheits-Garantie'],
            'cta'          => 'Projekt starten',
            'title'        => 'Sartu — Webdesign zum Festpreis, das Kunden bringt',
            'description'  => 'Firmenwebsite zum Festpreis ab 1.290 €, fertig in 7–14 Werktagen. Klarer Ablauf, feste Ansprechperson, Hosting in Deutschland. Jetzt Projekt starten.',
        ],

        'leistungen' => [
            'slug'         => 'leistungen',
            'keyword'      => 'Webdesign Leistungen',
            'intent'       => 'orientierend — welche Leistung passt zu mir',
            'audience'     => 'Interessent, der Umfang und Möglichkeiten sondiert',
            'kernbotschaft'=> 'Alles für eine überzeugende Firmenwebsite aus einer Hand: Design, Text, SEO, Betrieb.',
            'beweise'      => ['7 klar umrissene Leistungen', 'jede zum Festpreis', 'ein Ansprechpartner für alles'],
            'cta'          => 'Passende Leistung finden',
            'title'        => 'Leistungen — Webdesign, SEO, Texte & Betrieb | Sartu',
            'description'  => 'Website, Redesign, SEO, Texte, Logo und Betrieb aus einer Hand — jede Leistung zum Festpreis. Finden Sie, was zu Ihrem Vorhaben passt.',
        ],

        'leistung-webdesign' => [
            'slug'         => 'leistung-webdesign',
            'keyword'      => 'Website erstellen lassen',
            'intent'       => 'kommerziell — neue Website beauftragen',
            'audience'     => 'Betrieb ohne Website oder mit veralteter Seite',
            'kernbotschaft'=> 'Neue Website zum Festpreis, mobil-optimiert und auf Anfragen ausgelegt.',
            'beweise'      => ['ab 1.290 € Festpreis', 'fertig in 7–14 Werktagen', 'Basis-SEO & DSGVO inklusive'],
            'cta'          => 'Website-Projekt starten',
            'title'        => 'Website erstellen lassen — Festpreis ab 1.290 € | Sartu',
            'description'  => 'Moderne Firmenwebsite zum Festpreis ab 1.290 €, mobil-optimiert, mit Basis-SEO und DSGVO, fertig in 7–14 Werktagen. Jetzt anfragen.',
        ],

        'leistung-redesign' => [
            'slug'         => 'leistung-redesign',
            'keyword'      => 'Website Redesign',
            'intent'       => 'kommerziell — bestehende Seite erneuern',
            'audience'     => 'Betrieb mit veralteter, schlecht laufender Website',
            'kernbotschaft'=> 'Aus einer alten Website wird eine, die wieder Anfragen bringt — ohne Totalumbau-Risiko.',
            'beweise'      => ['Festpreis vorab', 'Inhalte werden übernommen', 'schneller sichtbar besser'],
            'cta'          => 'Redesign anfragen',
            'title'        => 'Website-Redesign zum Festpreis | Sartu',
            'description'  => 'Ihre Website wirkt veraltet und bringt zu wenig? Redesign zum Festpreis, das wieder Kunden gewinnt. Ehrliche Einschätzung vorab.',
        ],

        'leistung-seo' => [
            'slug'         => 'leistung-seo',
            'keyword'      => 'Suchmaschinenoptimierung',
            'intent'       => 'kommerziell — bei Google gefunden werden',
            'audience'     => 'Betrieb, der mehr Sichtbarkeit will',
            'kernbotschaft'=> 'Laufend bei Google und in der KI-Suche gefunden werden — verständlich betreut, monatlich kündbar.',
            'beweise'      => ['monatlich, nach 3 Monaten kündbar', 'Klartext-Report', 'auch KI-Suche (GEO)'],
            'cta'          => 'SEO anfragen',
            'title'        => 'SEO-Betreuung — bei Google & KI-Suche gefunden | Sartu',
            'description'  => 'Laufende SEO-Betreuung für kleine Betriebe: bei Google und in der KI-Suche gefunden werden. Klartext-Report, monatlich nach 3 Monaten kündbar.',
        ],

        'leistung-lokales-seo' => [
            'slug'         => 'leistung-lokales-seo',
            'keyword'      => 'Lokales SEO Google-Profil',
            'intent'       => 'kommerziell — lokal gefunden werden',
            'audience'     => 'lokaler Betrieb mit Einzugsgebiet',
            'kernbotschaft'=> 'In Ihrer Region gefunden werden — Google-Profil und lokale Suche laufend gepflegt.',
            'beweise'      => ['Bewertungen ≤ 2 Werktage beantwortet', 'Profil-Pflege inklusive', 'Teil der SEO-Betreuung'],
            'cta'          => 'Lokal sichtbar werden',
            'title'        => 'Lokales SEO & Google-Profil-Pflege | Sartu',
            'description'  => 'In Ihrer Region gefunden werden: Google-Profil-Pflege, Bewertungen und lokale Suche laufend betreut. Teil der SEO-Betreuung.',
        ],

        'leistung-wartung' => [
            'slug'         => 'leistung-wartung',
            'keyword'      => 'Website Wartung Hosting',
            'intent'       => 'kommerziell — sicherer Betrieb',
            'audience'     => 'Website-Besitzer, der sich nicht kümmern will',
            'kernbotschaft'=> 'Ihre Website läuft sicher, aktuell und schnell — Sie müssen sich um nichts kümmern.',
            'beweise'      => ['Hosting in Deutschland + SSL', 'automatische Backups & Updates', 'Uptime-Monitoring'],
            'cta'          => 'Betrieb absichern',
            'title'        => 'Website-Wartung, Hosting & Betrieb | Sartu',
            'description'  => 'Sicherer Website-Betrieb: Hosting in Deutschland, SSL, automatische Backups, Updates und Monitoring. Rundum-Schutz ab 49 €/Monat.',
        ],

        'leistung-texte' => [
            'slug'         => 'leistung-texte',
            'keyword'      => 'Website Texte erstellen',
            'intent'       => 'kommerziell — Texte fürs Web',
            'audience'     => 'Betrieb ohne fertige Texte',
            'kernbotschaft'=> 'Texte, die verkaufen statt nur zu beschreiben — verständlich und für Google geschrieben.',
            'beweise'      => ['pro Seite oder als Paket', 'SEO-optimiert', 'klingt nach Ihnen, nicht nach KI'],
            'cta'          => 'Texte anfragen',
            'title'        => 'Website-Texte, die verkaufen | Sartu',
            'description'  => 'Verständliche, verkaufende Website-Texte, für Google und Leser geschrieben. Pro Seite oder als Paket. Jetzt anfragen.',
        ],

        'leistung-logo' => [
            'slug'         => 'leistung-logo',
            'keyword'      => 'Logo Branding erstellen',
            'intent'       => 'kommerziell — Logo/Marke',
            'audience'     => 'Betrieb ohne professionelles Logo',
            'kernbotschaft'=> 'Ein Logo und Erscheinungsbild, das seriös wirkt und überall passt.',
            'beweise'      => ['vom Logo bis Corporate Design', 'Festpreis', 'passend zur Website aus einer Hand'],
            'cta'          => 'Logo anfragen',
            'title'        => 'Logo & Branding zum Festpreis | Sartu',
            'description'  => 'Professionelles Logo und Erscheinungsbild zum Festpreis, das seriös wirkt und zur Website passt. Alles aus einer Hand.',
        ],

        'preise' => [
            'slug'         => 'preise',
            'keyword'      => 'Website Kosten Preise',
            'intent'       => 'kommerziell/vergleichend — was kostet eine Website',
            'audience'     => 'Interessent im Preisvergleich',
            'kernbotschaft'=> 'Klare Festpreise ohne Agentur-Nebel: Sie wissen vorab genau, was Ihre Website kostet.',
            'beweise'      => ['3 Pakete: 1.290 / 3.290 / 6.490 €', 'Rundum-Schutz ab 49 €/Mon. (Pflicht)', 'keine Miet-Website, Festpreis vorab'],
            'cta'          => 'Passendes Paket anfragen',
            'title'        => 'Preise — Website zum Festpreis ab 1.290 € | Sartu',
            'description'  => 'Klare Festpreise: Start 1.290 €, Wachstum 3.290 €, Platzhirsch 6.490 €. Plus Rundum-Schutz ab 49 €/Monat. Festpreis vorab, keine Miet-Website.',
        ],

        'ablauf' => [
            'slug'         => 'ablauf',
            'keyword'      => 'Website erstellen Ablauf',
            'intent'       => 'informierend — wie läuft ein Projekt',
            'audience'     => 'Interessent, der den Prozess verstehen will',
            'kernbotschaft'=> 'In 7 klaren Schritten zur fertigen Website — digital, ohne Termine, in 7–14 Werktagen.',
            'beweise'      => ['Sofort-Vorschau', 'Feedback per Klick', 'online in 7–14 Werktagen'],
            'cta'          => 'Anfrage starten',
            'title'        => 'Ablauf — in 7–14 Werktagen zur Website | Sartu',
            'description'  => 'So entsteht Ihre Website: 7 klare Schritte von der Anfrage bis online, digital und ohne Termine, in 7–14 Werktagen. Ablauf ansehen.',
        ],

        'ueber-uns' => [
            'slug'         => 'ueber-uns',
            'keyword'      => 'Webdesign-Agentur Deutschland',
            'intent'       => 'vertrauensbildend — wer steckt dahinter',
            'audience'     => 'Interessent, der Sartu einschätzen will',
            'kernbotschaft'=> 'Eine feste Ansprechperson statt Hotline und Weiterreicherei — ehrlich, erreichbar, deutschlandweit.',
            'beweise'      => ['persönliche Betreuung', 'klare Prinzipien', 'Hosting & Daten in Deutschland'],
            'cta'          => 'Kennenlernen & anfragen',
            'title'        => 'Über uns — persönlich statt Hotline | Sartu',
            'description'  => 'Sartu ist Ihre feste Ansprechperson für Webdesign: ehrlich, erreichbar, deutschlandweit. Kein Callcenter, keine Weiterreicherei.',
        ],

        'ratgeber' => [
            'slug'         => 'ratgeber',
            'keyword'      => 'Webdesign Ratgeber Lexikon',
            'intent'       => 'informierend — Fachbegriffe & Entscheidungen',
            'audience'     => 'Interessent in der Recherchephase',
            'kernbotschaft'=> 'Verständliche Antworten auf die häufigsten Website-Fragen — ohne Fachchinesisch.',
            'beweise'      => ['50 Fachbegriffe erklärt', 'Entscheidungshilfen', 'ehrlich statt Verkaufsprosa'],
            'cta'          => 'Frage offen? Anfragen',
            'title'        => 'Webdesign-Ratgeber & Lexikon | Sartu',
            'description'  => 'Verständliche Antworten und 50 erklärte Fachbegriffe rund um Webdesign, Kosten und SEO. Ohne Fachchinesisch, ehrlich erklärt.',
        ],

        'kontakt' => [
            'slug'         => 'kontakt',
            'keyword'      => 'Webdesign Kontakt Anfrage',
            'intent'       => 'transaktional — Kontakt aufnehmen',
            'audience'     => 'kaufbereiter Interessent',
            'kernbotschaft'=> 'Erzählen Sie kurz von Ihrem Vorhaben — Sie bekommen eine ehrliche Einschätzung, ohne Verkaufstermin-Zwang.',
            'beweise'      => ['Antwort meist innerhalb 24 h (werktags)', 'kostenlose Einschätzung', 'kein Verkaufsdruck'],
            'cta'          => 'Anfrage senden',
            'title'        => 'Kontakt — kostenlose Einschätzung | Sartu',
            'description'  => 'Starten Sie Ihr Website-Projekt: kurze Anfrage, ehrliche Einschätzung meist innerhalb von 24 Stunden, ohne Verkaufsdruck.',
        ],
    ];
}

function sartu_copy_brief(string $slug): ?array
{
    $briefs = sartu_copy_briefs();
    return $briefs[$slug] ?? null;
}
