<?php
declare(strict_types=1);

/**
 * Feld-Schema für den Selbst-Editor (Stufe 1).
 *
 * Definiert, welche Sektionen und Felder eine Kundenseite je Vorlage hat.
 * Bewusst begrenzt: der Kunde füllt Felder, er baut kein Layout — das Design
 * bleibt geschützt. Muster analog includes/priority-pages.php (Array statt DB),
 * damit Editor (Formular) und Renderer (Ausgabe) dieselbe Quelle lesen.
 *
 * Feldtypen:
 *   text      – einzeilig
 *   textarea  – mehrzeilig
 *   image     – Verweis auf einen Upload (uploads.id), mit Auto-Alt-Text
 *   url/tel/email – wie text, nur andere Tastatur/Validierung im Editor
 *   color     – Auswahl aus fester Palette (siehe sartu_site_palette)
 *   hours     – Öffnungszeiten (Mo–So)
 *   list      – Wiederholung gleichartiger Einträge (item = Unterfelder)
 */

/** Feste Akzent-Palette — der Kunde wählt nur hieraus, nie freie Hex-Werte. */
function sartu_site_palette(): array
{
    return [
        ['value' => '#0f766e', 'label' => 'Petrol'],
        ['value' => '#1d4ed8', 'label' => 'Blau'],
        ['value' => '#b45309', 'label' => 'Bernstein'],
        ['value' => '#be123c', 'label' => 'Rot'],
        ['value' => '#7c3aed', 'label' => 'Violett'],
        ['value' => '#15803d', 'label' => 'Grün'],
        ['value' => '#334155', 'label' => 'Schiefer'],
    ];
}

/** Gültiger Hex-Farbwert (#rgb oder #rrggbb) — verhindert CSS-Injektion. */
function sartu_site_valid_hex(string $value): bool
{
    return (bool) preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value);
}

/**
 * Alle Vorlagen. Rückgabe: [vorlage => ['label' => …, 'sections' => [ … ]]].
 * Jede Sektion: ['key','label','fields'=>[ ['key','label','type', …opt] ]].
 */
function sartu_site_schema(): array
{
    return [
        'standard' => [
            'label' => 'Standard (Onepager für lokale Betriebe)',
            'sections' => [
                [
                    'key' => 'hero',
                    'label' => 'Kopfbereich',
                    'help' => 'Das Erste, was Besucher sehen.',
                    'fields' => [
                        ['key' => 'headline', 'label' => 'Überschrift', 'type' => 'text', 'max' => 90, 'placeholder' => 'Ihre Bäckerei in Musterstadt'],
                        ['key' => 'subline', 'label' => 'Unterzeile', 'type' => 'textarea', 'max' => 220, 'placeholder' => 'Frisch, handgemacht, seit 1985.'],
                        ['key' => 'bild', 'label' => 'Hauptbild', 'type' => 'image'],
                        ['key' => 'cta_text', 'label' => 'Button-Text', 'type' => 'text', 'max' => 40, 'placeholder' => 'Jetzt anrufen'],
                        ['key' => 'cta_ziel', 'label' => 'Button-Ziel', 'type' => 'text', 'max' => 120, 'placeholder' => 'tel:+49… oder #kontakt', 'help' => 'Telefon (tel:…), E-Mail (mailto:…) oder #kontakt'],
                    ],
                ],
                [
                    'key' => 'ueber',
                    'label' => 'Über uns',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'text', 'label' => 'Text', 'type' => 'textarea', 'max' => 1200],
                        ['key' => 'bild', 'label' => 'Bild', 'type' => 'image'],
                    ],
                ],
                [
                    'key' => 'leistungen',
                    'label' => 'Leistungen / Angebot',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'einleitung', 'label' => 'Einleitung', 'type' => 'textarea', 'max' => 400],
                        ['key' => 'items', 'label' => 'Einträge', 'type' => 'list', 'max_items' => 12, 'item' => [
                            ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 80],
                            ['key' => 'text', 'label' => 'Beschreibung', 'type' => 'textarea', 'max' => 300],
                        ]],
                    ],
                ],
                [
                    'key' => 'team',
                    'label' => 'Team',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'members', 'label' => 'Personen', 'type' => 'list', 'max_items' => 20, 'item' => [
                            ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'max' => 80],
                            ['key' => 'rolle', 'label' => 'Rolle', 'type' => 'text', 'max' => 80],
                            ['key' => 'bild', 'label' => 'Foto', 'type' => 'image'],
                        ]],
                    ],
                ],
                [
                    'key' => 'oeffnungszeiten',
                    'label' => 'Öffnungszeiten',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'zeiten', 'label' => 'Zeiten', 'type' => 'hours'],
                        ['key' => 'hinweis', 'label' => 'Hinweis', 'type' => 'text', 'max' => 160, 'placeholder' => 'An Feiertagen geschlossen'],
                    ],
                ],
                [
                    'key' => 'beitraege',
                    'label' => 'Aktuelles / Beiträge',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'posts', 'label' => 'Beiträge', 'type' => 'list', 'max_items' => 30, 'item' => [
                            ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 120],
                            ['key' => 'datum', 'label' => 'Datum', 'type' => 'text', 'max' => 40, 'placeholder' => 'z. B. Juli 2026'],
                            ['key' => 'text', 'label' => 'Text', 'type' => 'textarea', 'max' => 1200],
                            ['key' => 'bild', 'label' => 'Bild', 'type' => 'image'],
                        ]],
                    ],
                ],
                [
                    'key' => 'kontakt',
                    'label' => 'Kontakt',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'adresse', 'label' => 'Adresse', 'type' => 'textarea', 'max' => 240],
                        ['key' => 'telefon', 'label' => 'Telefon', 'type' => 'tel', 'max' => 60],
                        ['key' => 'email', 'label' => 'E-Mail', 'type' => 'email', 'max' => 120],
                        ['key' => 'maps', 'label' => 'Karten-Link', 'type' => 'url', 'max' => 300, 'help' => 'Link zu Google Maps (optional)'],
                    ],
                ],
                [
                    'key' => 'seo',
                    'label' => 'Suchmaschine (Google)',
                    'help' => 'So erscheint Ihre Seite in den Google-Ergebnissen.',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Seitentitel', 'type' => 'text', 'max' => 65, 'help' => 'Erscheint als blaue Überschrift bei Google (ca. 60 Zeichen).', 'placeholder' => 'Muster Bäckerei – frisches Brot in Musterstadt'],
                        ['key' => 'description', 'label' => 'Beschreibung', 'type' => 'textarea', 'max' => 160, 'help' => 'Der graue Text unter dem Titel bei Google (ca. 155 Zeichen).'],
                    ],
                ],
                [
                    'key' => 'design',
                    'label' => 'Design',
                    'help' => 'Ihre Akzentfarbe — als Wähler, per Hex-Code oder aus den Vorschlägen.',
                    'fields' => [
                        ['key' => 'akzentfarbe', 'label' => 'Akzentfarbe', 'type' => 'color'],
                    ],
                ],
            ],
        ],
    ];
}

/** Eine Vorlage holen (Fallback: 'standard'). */
function sartu_site_template(string $vorlage): array
{
    $all = sartu_site_schema();
    if (isset($all[$vorlage])) {
        return $all[$vorlage];
    }
    return $all['standard'];
}

/** Ist der Wert in der Palette erlaubt? (für serverseitige Validierung) */
function sartu_site_palette_has(string $value): bool
{
    foreach (sartu_site_palette() as $c) {
        if (hash_equals($c['value'], $value)) {
            return true;
        }
    }
    return false;
}

/**
 * Flache Liste aller (section_key, field_key, type)-Tripel einer Vorlage —
 * praktisch für Validierung/Speichern im Content-API.
 */
function sartu_site_fields(string $vorlage): array
{
    $out = [];
    foreach (sartu_site_template($vorlage)['sections'] as $section) {
        foreach ($section['fields'] as $field) {
            $out[$section['key'] . '.' . $field['key']] = [
                'section' => $section['key'],
                'field' => $field['key'],
                'type' => $field['type'],
                'spec' => $field,
            ];
        }
    }
    return $out;
}
