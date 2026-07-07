<?php
declare(strict_types=1);

/**
 * Stufe-2-Briefing (Detail-Onboarding im Portal, nach der verbindlichen Zusage).
 *
 * Umfassendes, kapitelbasiertes Briefing: sammelt ALLES, was Sartu für den Bau
 * braucht — bewusst als Rohmaterial (Stichpunkte, Dateien, Wünsche), keine
 * fertigen Texte. Der Feinschliff macht Sartu (ggf. mit KI). Struktur nach
 * gängigen Webdesign-Briefing-Standards (Unternehmen, Zielgruppe, Ziele, Ist-Stand,
 * Marke, Inhalte, Medien, Funktionen, Kontakt, Recht, SEO, Ton).
 *
 * Feldtypen: text, textarea, tel, email, url, choice (eine), multi (mehrere),
 *            file (eine Datei), files (mehrere).
 * Bedingte Felder über 'when' => ['feldkey' => 'wert'].
 */
function sartu_briefing2_schema(): array
{
    return [
        'version' => 2,
        'steps' => [
            [
                'key' => 'unternehmen',
                'title' => 'Ihr Unternehmen',
                'help' => 'Damit wir verstehen, wer Sie sind.',
                'fields' => [
                    ['key' => 'firmenname', 'label' => 'Firmen-/Betriebsname', 'type' => 'text', 'max' => 160],
                    ['key' => 'branche', 'label' => 'Branche / Tätigkeit', 'type' => 'text', 'max' => 120],
                    ['key' => 'gegruendet', 'label' => 'Seit wann gibt es Sie?', 'type' => 'text', 'max' => 60],
                    ['key' => 'mitarbeiter', 'label' => 'Ungefähre Mitarbeiterzahl', 'type' => 'text', 'max' => 60],
                    ['key' => 'steckbrief', 'label' => 'Kurz beschrieben: Was machen Sie und was ist besonders?', 'type' => 'textarea', 'max' => 1500, 'placeholder' => 'Stichpunkte reichen — wir formulieren daraus die Texte.'],
                    ['key' => 'werte', 'label' => 'Werte / Philosophie (optional)', 'type' => 'textarea', 'max' => 800],
                ],
            ],
            [
                'key' => 'zielgruppe',
                'title' => 'Zielgruppe & Wettbewerb',
                'help' => 'Für wen bauen wir — und wer sind die anderen?',
                'fields' => [
                    ['key' => 'zielgruppe', 'label' => 'Wen möchten Sie erreichen? (Alter, Interessen, Typ)', 'type' => 'textarea', 'max' => 800],
                    ['key' => 'einzugsgebiet', 'label' => 'Einzugsgebiet / Region', 'type' => 'text', 'max' => 160],
                    ['key' => 'wettbewerber', 'label' => 'Hauptmitbewerber (gern mit Links)', 'type' => 'textarea', 'max' => 600],
                    ['key' => 'usp', 'label' => 'Was macht Sie besser / anders als die?', 'type' => 'textarea', 'max' => 800],
                ],
            ],
            [
                'key' => 'ziele',
                'title' => 'Ziele der Website',
                'help' => 'Was soll die Website für Sie tun?',
                'fields' => [
                    ['key' => 'hauptziele', 'label' => 'Wichtigste Ziele', 'type' => 'multi', 'options' => [
                        ['value' => 'anfragen', 'label' => 'Anfragen gewinnen'],
                        ['value' => 'termine', 'label' => 'Termine / Buchungen'],
                        ['value' => 'verkaufen', 'label' => 'Produkte verkaufen'],
                        ['value' => 'info', 'label' => 'Informieren'],
                        ['value' => 'vertrauen', 'label' => 'Vertrauen / Image'],
                        ['value' => 'bewerber', 'label' => 'Bewerber finden'],
                    ]],
                    ['key' => 'aktionen', 'label' => 'Was sollen Besucher konkret tun?', 'type' => 'textarea', 'max' => 400, 'placeholder' => 'z. B. anrufen, Formular ausfüllen, Termin buchen'],
                    ['key' => 'erfolg', 'label' => 'Woran merken Sie, dass die Website erfolgreich ist?', 'type' => 'textarea', 'max' => 400],
                ],
            ],
            [
                'key' => 'iststand',
                'title' => 'Ist-Stand',
                'help' => 'Gibt es schon etwas, worauf wir aufbauen?',
                'fields' => [
                    ['key' => 'hat_website', 'label' => 'Haben Sie bereits eine Website?', 'type' => 'choice', 'options' => [
                        ['value' => 'ja', 'label' => 'Ja'], ['value' => 'nein', 'label' => 'Nein'],
                    ]],
                    ['key' => 'alte_url', 'label' => 'Adresse Ihrer bisherigen Website', 'type' => 'url', 'max' => 200, 'when' => ['hat_website' => 'ja']],
                    ['key' => 'was_bleibt', 'label' => 'Was gefällt Ihnen / soll bleiben?', 'type' => 'textarea', 'max' => 600, 'when' => ['hat_website' => 'ja']],
                    ['key' => 'was_weg', 'label' => 'Was stört Sie / soll weg?', 'type' => 'textarea', 'max' => 600, 'when' => ['hat_website' => 'ja']],
                    ['key' => 'domain', 'label' => 'Wunsch-Domain (falls neu) oder vorhandene Domain', 'type' => 'text', 'max' => 160],
                ],
            ],
            [
                'key' => 'marke',
                'title' => 'Marke & Design',
                'help' => 'Damit das Design Ihren Geschmack trifft.',
                'fields' => [
                    ['key' => 'hat_logo', 'label' => 'Haben Sie ein Logo?', 'type' => 'choice', 'options' => [
                        ['value' => 'ja', 'label' => 'Ja, ich lade es hoch'],
                        ['value' => 'nein', 'label' => 'Nein — Sartu gestaltet eins'],
                    ]],
                    ['key' => 'logo', 'label' => 'Logo hochladen (am besten als Vektor/PDF/SVG)', 'type' => 'file', 'when' => ['hat_logo' => 'ja']],
                    ['key' => 'ci_farben', 'label' => 'Vorgegebene Farben? (Hex-Codes oder Beschreibung)', 'type' => 'text', 'max' => 200],
                    ['key' => 'schriften', 'label' => 'Vorgegebene Schriften? (optional)', 'type' => 'text', 'max' => 160],
                    ['key' => 'stimmung', 'label' => 'Welche Stimmung passt?', 'type' => 'multi', 'options' => [
                        ['value' => 'modern', 'label' => 'Modern'], ['value' => 'klassisch', 'label' => 'Klassisch'],
                        ['value' => 'minimal', 'label' => 'Minimalistisch'], ['value' => 'warm', 'label' => 'Warm / einladend'],
                        ['value' => 'hochwertig', 'label' => 'Hochwertig / edel'], ['value' => 'verspielt', 'label' => 'Verspielt / bunt'],
                    ]],
                    ['key' => 'vorbilder', 'label' => '1–3 Websites, die Ihnen gefallen (+ was daran)', 'type' => 'textarea', 'max' => 800],
                    ['key' => 'nogos', 'label' => 'Was gefällt Ihnen gar nicht? (No-Gos)', 'type' => 'textarea', 'max' => 600],
                ],
            ],
            [
                'key' => 'inhalte',
                'title' => 'Seiten & Inhalte',
                'help' => 'Welche Seiten braucht die Website — und was steht drauf? Stichpunkte genügen.',
                'fields' => [
                    ['key' => 'seiten', 'label' => 'Welche Seiten soll die Website haben?', 'type' => 'multi', 'options' => [
                        ['value' => 'start', 'label' => 'Startseite'], ['value' => 'ueber', 'label' => 'Über uns'],
                        ['value' => 'leistungen', 'label' => 'Leistungen'], ['value' => 'team', 'label' => 'Team'],
                        ['value' => 'referenzen', 'label' => 'Referenzen'], ['value' => 'galerie', 'label' => 'Galerie'],
                        ['value' => 'faq', 'label' => 'FAQ'], ['value' => 'blog', 'label' => 'Blog / News'],
                        ['value' => 'kontakt', 'label' => 'Kontakt'], ['value' => 'karriere', 'label' => 'Karriere / Jobs'],
                    ]],
                    ['key' => 'texte_vorhanden', 'label' => 'Haben Sie schon Texte?', 'type' => 'choice', 'options' => [
                        ['value' => 'ja', 'label' => 'Ja, vorhanden'], ['value' => 'teils', 'label' => 'Teilweise'],
                        ['value' => 'nein', 'label' => 'Nein — Sartu schreibt sie'],
                    ]],
                    ['key' => 'ueber_uns', 'label' => 'Über uns — Stichpunkte', 'type' => 'textarea', 'max' => 1500],
                    ['key' => 'leistungen_inhalt', 'label' => 'Ihre Leistungen (je Zeile: Name – kurze Beschreibung – ggf. Preis)', 'type' => 'textarea', 'max' => 2000],
                    ['key' => 'team_inhalt', 'label' => 'Team (je Person: Name, Rolle) — optional', 'type' => 'textarea', 'max' => 1200],
                    ['key' => 'referenzen_inhalt', 'label' => 'Referenzen / Projekte — optional', 'type' => 'textarea', 'max' => 1200],
                    ['key' => 'faq_inhalt', 'label' => 'Häufige Fragen + Antworten — optional', 'type' => 'textarea', 'max' => 1500],
                ],
            ],
            [
                'key' => 'medien',
                'title' => 'Bilder & Dateien',
                'help' => 'Je mehr Material, desto besser. Auch Handyfotos sind okay.',
                'fields' => [
                    ['key' => 'fotos', 'label' => 'Fotos hochladen (Team, Räume, Produkte, Arbeit)', 'type' => 'files'],
                    ['key' => 'dokumente', 'label' => 'Vorhandene Texte / Flyer / Broschüren', 'type' => 'files'],
                    ['key' => 'videos', 'label' => 'Videos (Links oder Hinweis) — optional', 'type' => 'textarea', 'max' => 400],
                    ['key' => 'medien_hinweis', 'label' => 'Hinweise zu den Bildern (optional)', 'type' => 'textarea', 'max' => 400],
                ],
            ],
            [
                'key' => 'funktionen',
                'title' => 'Funktionen',
                'help' => 'Was soll die Website können?',
                'fields' => [
                    ['key' => 'funktionen', 'label' => 'Gewünschte Funktionen', 'type' => 'multi', 'options' => [
                        ['value' => 'kontaktformular', 'label' => 'Kontaktformular'], ['value' => 'termine', 'label' => 'Terminbuchung'],
                        ['value' => 'newsletter', 'label' => 'Newsletter'], ['value' => 'karte', 'label' => 'Karte / Anfahrt'],
                        ['value' => 'bewertungen', 'label' => 'Google-Bewertungen'], ['value' => 'downloads', 'label' => 'Downloads'],
                        ['value' => 'chat', 'label' => 'Chat / KI-Assistent'], ['value' => 'mehrsprachig', 'label' => 'Mehrsprachig'],
                    ]],
                    ['key' => 'formular_felder', 'label' => 'Welche Felder soll das Kontaktformular haben? (optional)', 'type' => 'textarea', 'max' => 400],
                    ['key' => 'integrationen', 'label' => 'Bestehende Tools anbinden? (Buchungssystem, Kasse, Newsletter …)', 'type' => 'textarea', 'max' => 600],
                ],
            ],
            [
                'key' => 'kontakt',
                'title' => 'Kontakt & Öffnungszeiten',
                'help' => 'Diese Angaben landen auch auf der Website.',
                'fields' => [
                    ['key' => 'adresse', 'label' => 'Anschrift', 'type' => 'textarea', 'max' => 240],
                    ['key' => 'telefon', 'label' => 'Telefon', 'type' => 'tel', 'max' => 60],
                    ['key' => 'email', 'label' => 'E-Mail', 'type' => 'email', 'max' => 120],
                    ['key' => 'oeffnungszeiten', 'label' => 'Öffnungszeiten (grob reicht)', 'type' => 'textarea', 'max' => 400],
                    ['key' => 'maps', 'label' => 'Google-Maps-Link (optional)', 'type' => 'url', 'max' => 300],
                    ['key' => 'social', 'label' => 'Social-Media-Profile (optional)', 'type' => 'textarea', 'max' => 400],
                ],
            ],
            [
                'key' => 'rechtliches',
                'title' => 'Rechtliches',
                'help' => 'Für Impressum & Datenschutz (gesetzlich Pflicht). Wird von Sartu geprüft.',
                'fields' => [
                    ['key' => 'firmierung', 'label' => 'Vollständige Firmierung (wie im Impressum)', 'type' => 'text', 'max' => 200],
                    ['key' => 'inhaber', 'label' => 'Inhaber / Vertretungsberechtigte Person', 'type' => 'text', 'max' => 160],
                    ['key' => 'rechtsform', 'label' => 'Rechtsform (Einzelunternehmen, GmbH …)', 'type' => 'text', 'max' => 80],
                    ['key' => 'register', 'label' => 'Handelsregister-Nr. (falls vorhanden)', 'type' => 'text', 'max' => 120],
                    ['key' => 'ust_id', 'label' => 'USt-IdNr. (falls vorhanden)', 'type' => 'text', 'max' => 60],
                    ['key' => 'kleinunternehmer', 'label' => 'Kleinunternehmer nach § 19 UStG?', 'type' => 'choice', 'options' => [
                        ['value' => 'ja', 'label' => 'Ja'], ['value' => 'nein', 'label' => 'Nein'],
                    ]],
                    ['key' => 'aufsicht', 'label' => 'Aufsichtsbehörde / Kammer (falls relevant)', 'type' => 'text', 'max' => 160],
                ],
            ],
            [
                'key' => 'seo',
                'title' => 'Auffindbarkeit (SEO)',
                'help' => 'Damit man Sie bei Google findet.',
                'fields' => [
                    ['key' => 'suchbegriffe', 'label' => 'Wonach sollen Kunden suchen, um Sie zu finden?', 'type' => 'textarea', 'max' => 600, 'placeholder' => 'z. B. Bäckerei Musterstadt, Sauerteigbrot, Torten auf Bestellung'],
                    ['key' => 'orte', 'label' => 'Wichtige Orte / Regionen', 'type' => 'text', 'max' => 200],
                    ['key' => 'google_profil', 'label' => 'Google-Unternehmensprofil vorhanden?', 'type' => 'choice', 'options' => [
                        ['value' => 'ja', 'label' => 'Ja'], ['value' => 'nein', 'label' => 'Nein / weiß nicht'],
                    ]],
                ],
            ],
            [
                'key' => 'abschluss',
                'title' => 'Ton & Zum Schluss',
                'help' => 'Der letzte Feinschliff.',
                'fields' => [
                    ['key' => 'ansprache', 'label' => 'Wie sollen wir Besucher ansprechen?', 'type' => 'choice', 'options' => [
                        ['value' => 'du', 'label' => 'Du (locker)'], ['value' => 'sie', 'label' => 'Sie (förmlich)'],
                    ]],
                    ['key' => 'ton', 'label' => 'Tonfall', 'type' => 'multi', 'options' => [
                        ['value' => 'sachlich', 'label' => 'Sachlich'], ['value' => 'herzlich', 'label' => 'Herzlich'],
                        ['value' => 'hochwertig', 'label' => 'Hochwertig'], ['value' => 'humorvoll', 'label' => 'Humorvoll'],
                    ]],
                    ['key' => 'deadline', 'label' => 'Fester Termin/Anlass? (Eröffnung, Event …)', 'type' => 'text', 'max' => 160],
                    ['key' => 'muss_rein', 'label' => 'Was MUSS unbedingt auf die Website?', 'type' => 'textarea', 'max' => 800],
                    ['key' => 'sonstiges', 'label' => 'Sonstige Wünsche / Anmerkungen', 'type' => 'textarea', 'max' => 1000],
                ],
            ],
        ],
    ];
}

/** Flache Feldliste [key => spec] über alle Schritte (für Validierung im API). */
function sartu_briefing2_fields(): array
{
    $out = [];
    foreach (sartu_briefing2_schema()['steps'] as $step) {
        foreach ($step['fields'] as $field) {
            $out[$field['key']] = $field;
        }
    }
    return $out;
}
