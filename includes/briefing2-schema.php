<?php
declare(strict_types=1);

/**
 * Stufe-2-Briefing (Detail-Onboarding im Portal, nach Login, vor dem Bau).
 *
 * Sammelt Rohmaterial für den Website-Bau — bewusst KEINE fertigen Texte, sondern
 * Stichpunkte, Dateien und Wünsche. Der Feinschliff macht Sartu (ggf. mit KI).
 *
 * Feldtypen: text, textarea, tel, email, choice (eine Auswahl), multi (mehrere),
 *            file (eine Datei), files (mehrere Dateien).
 */
function sartu_briefing2_schema(): array
{
    return [
        'version' => 1,
        'steps' => [
            [
                'key' => 'betrieb',
                'title' => 'Über Ihren Betrieb',
                'help' => 'Damit wir verstehen, wer Sie sind und für wen.',
                'fields' => [
                    ['key' => 'firmenname', 'label' => 'Firmen-/Betriebsname', 'type' => 'text', 'max' => 160],
                    ['key' => 'steckbrief', 'label' => 'Kurz beschrieben: Was machen Sie, seit wann, was ist besonders?', 'type' => 'textarea', 'max' => 1200, 'placeholder' => 'Stichpunkte reichen — wir formulieren daraus die Texte.'],
                    ['key' => 'zielgruppe', 'label' => 'Wen möchten Sie erreichen?', 'type' => 'textarea', 'max' => 600, 'placeholder' => 'z. B. Familien aus der Region, Handwerksbetriebe, Berufstätige …'],
                ],
            ],
            [
                'key' => 'medien',
                'title' => 'Logo & Bilder',
                'help' => 'Was Sie schon haben. Fehlt etwas, kümmert sich Sartu darum.',
                'fields' => [
                    ['key' => 'hat_logo', 'label' => 'Haben Sie ein Logo?', 'type' => 'choice', 'options' => [
                        ['value' => 'ja', 'label' => 'Ja, ich lade es hoch'],
                        ['value' => 'nein', 'label' => 'Nein — Sartu soll eins gestalten'],
                    ]],
                    ['key' => 'logo', 'label' => 'Logo hochladen', 'type' => 'file', 'when' => ['hat_logo' => 'ja']],
                    ['key' => 'fotos', 'label' => 'Fotos hochladen (Team, Räume, Produkte …)', 'type' => 'files', 'help' => 'Je mehr, desto besser. Auch Handyfotos sind okay.'],
                    ['key' => 'medien_hinweis', 'label' => 'Hinweise zu den Bildern (optional)', 'type' => 'textarea', 'max' => 400],
                ],
            ],
            [
                'key' => 'inhalte',
                'title' => 'Inhalte',
                'help' => 'Stichpunkte genügen — daraus baut Sartu die fertigen Texte.',
                'fields' => [
                    ['key' => 'ueber_uns', 'label' => 'Über uns — Stichpunkte', 'type' => 'textarea', 'max' => 1500],
                    ['key' => 'leistungen', 'label' => 'Ihre Leistungen / Angebote (eine je Zeile)', 'type' => 'textarea', 'max' => 1500, 'placeholder' => "Brot & Brötchen\nKuchen & Torten\nPartyservice"],
                    ['key' => 'usp', 'label' => 'Was macht Sie besser als andere?', 'type' => 'textarea', 'max' => 800],
                ],
            ],
            [
                'key' => 'stil',
                'title' => 'Vorbilder & Stil',
                'help' => 'Damit das Design Ihren Geschmack trifft.',
                'fields' => [
                    ['key' => 'vorbilder', 'label' => '1–3 Websites, die Ihnen gefallen (+ was daran)', 'type' => 'textarea', 'max' => 800, 'placeholder' => "www.beispiel.de — klare Struktur, schöne Fotos"],
                    ['key' => 'stimmung', 'label' => 'Welche Stimmung passt?', 'type' => 'multi', 'options' => [
                        ['value' => 'modern', 'label' => 'Modern'],
                        ['value' => 'klassisch', 'label' => 'Klassisch'],
                        ['value' => 'minimal', 'label' => 'Minimalistisch'],
                        ['value' => 'warm', 'label' => 'Warm / einladend'],
                        ['value' => 'hochwertig', 'label' => 'Hochwertig / edel'],
                        ['value' => 'verspielt', 'label' => 'Verspielt / bunt'],
                    ]],
                    ['key' => 'farbwunsch', 'label' => 'Farbwunsch (optional)', 'type' => 'text', 'max' => 160, 'placeholder' => 'z. B. Grün wie unser Logo, oder „warme Töne"'],
                ],
            ],
            [
                'key' => 'kontakt',
                'title' => 'Kontakt & Öffnungszeiten',
                'help' => 'Diese Angaben landen auch im Impressum.',
                'fields' => [
                    ['key' => 'adresse', 'label' => 'Anschrift', 'type' => 'textarea', 'max' => 240],
                    ['key' => 'telefon', 'label' => 'Telefon', 'type' => 'tel', 'max' => 60],
                    ['key' => 'email', 'label' => 'E-Mail', 'type' => 'email', 'max' => 120],
                    ['key' => 'oeffnungszeiten', 'label' => 'Öffnungszeiten (grob reicht)', 'type' => 'textarea', 'max' => 400],
                    ['key' => 'social', 'label' => 'Social-Media-Links (optional)', 'type' => 'textarea', 'max' => 400, 'placeholder' => 'Instagram, Facebook …'],
                ],
            ],
            [
                'key' => 'sonstiges',
                'title' => 'Zum Schluss',
                'help' => 'Alles, was uns noch helfen könnte.',
                'fields' => [
                    ['key' => 'muss_rein', 'label' => 'Was MUSS unbedingt auf die Website?', 'type' => 'textarea', 'max' => 800],
                    ['key' => 'lieber_nicht', 'label' => 'Was möchten Sie auf keinen Fall?', 'type' => 'textarea', 'max' => 800],
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
