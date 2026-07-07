<?php
declare(strict_types=1);

/**
 * Feld-Schema für den Selbst-Editor (Stufe 1).
 *
 * Definiert, welche Sektionen und Felder eine Kundenseite je Vorlage hat.
 *
 * Zuständigkeiten (bewusst getrennt):
 *   - Marketing/SEO-Inhalte (Kopfbereich, Über uns, Leistungs-Texte, Beiträge,
 *     Google-Snippet) macht SARTU. Sie stehen im Schema (für Renderer + späteres
 *     Admin-CMS), sind aber im KUNDEN-Editor nicht sichtbar.
 *   - Der Kunde ändert nur BETRIEBLICHES: Öffnungszeiten, Kontakt, Impressum, Farbe.
 *   - Zusätzlich darf der Kunde einzelne Leistungen/Bereiche EIN-/AUSBLENDEN —
 *     aber nichts hinzufügen oder löschen (Hinzufügen = kostenpflichtige Arbeit).
 *
 * Sektions-Flags:
 *   'customer' => 'edit'   Kunde darf die Felder bearbeiten
 *   'customer' => 'toggle' Kunde darf nur ein-/ausblenden (Felder gesperrt)
 *   (fehlt)                nur Sartu (nicht im Kunden-Editor)
 *   'hideable' => true     ganze Sektion kann vom Kunden aus-/eingeblendet werden
 * List-Feld-Flag:
 *   'toggle_items' => true einzelne Einträge kann der Kunde aus-/einblenden
 *
 * Feldtypen: text, textarea, image, url/tel/email, color, hours, list.
 */

/** Feste Akzent-Palette (Vorschläge; der Kunde darf aber auch frei per Hex/Wähler). */
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
 * Alle Farb-Rollen der Kundenseite. Jede Rolle = eine CSS-Variable im Renderer und
 * ein Farbfeld in der Design-Sektion. Eine Quelle für Schema und Ausgabe.
 * Rückgabe: [ ['key','label','default','var','help'?], … ].
 */
function sartu_site_theme(): array
{
    return [
        ['key' => 'akzentfarbe',  'var' => 'accent',      'label' => 'Akzent (Buttons, Links)', 'default' => '#0f766e'],
        ['key' => 'akzent_text',  'var' => 'accent-text', 'label' => 'Schrift auf Buttons',      'default' => '#ffffff'],
        ['key' => 'text',         'var' => 'text',        'label' => 'Fließtext',                'default' => '#1c2530'],
        ['key' => 'ueberschrift', 'var' => 'heading',     'label' => 'Überschriften',            'default' => '#1c2530'],
        ['key' => 'text_leise',   'var' => 'muted',       'label' => 'Gedämpfter Text',          'default' => '#5c6672'],
        ['key' => 'hintergrund',  'var' => 'bg',          'label' => 'Seitenhintergrund',        'default' => '#ffffff'],
        ['key' => 'abschnitt',    'var' => 'soft',        'label' => 'Abschnitts-Hintergrund',   'default' => '#f5f7f8'],
        ['key' => 'karte',        'var' => 'card',        'label' => 'Karten-Hintergrund',       'default' => '#ffffff'],
        ['key' => 'rahmen',       'var' => 'line',        'label' => 'Rahmen & Linien',          'default' => '#e6eaed'],
        ['key' => 'navigation',   'var' => 'nav',         'label' => 'Navigationsleiste',        'default' => '#ffffff'],
    ];
}

/** Design-Sektions-Felder aus den Farb-Rollen bauen. */
function sartu_site_theme_fields(): array
{
    $fields = [];
    foreach (sartu_site_theme() as $role) {
        $fields[] = ['key' => $role['key'], 'label' => $role['label'], 'type' => 'color'];
    }
    return $fields;
}

/**
 * Alle Vorlagen. Rückgabe: [vorlage => ['label' => …, 'sections' => [ … ]]].
 */
function sartu_site_schema(): array
{
    return [
        'standard' => [
            'label' => 'Standard (Onepager für lokale Betriebe)',
            'sections' => [
                // ---- Von Sartu gepflegt (nicht im Kunden-Editor) ----
                [
                    'key' => 'hero',
                    'label' => 'Kopfbereich',
                    'fields' => [
                        ['key' => 'headline', 'label' => 'Überschrift', 'type' => 'text', 'max' => 90],
                        ['key' => 'subline', 'label' => 'Unterzeile', 'type' => 'textarea', 'max' => 220],
                        ['key' => 'bild', 'label' => 'Hauptbild', 'type' => 'image'],
                        ['key' => 'cta_text', 'label' => 'Button-Text', 'type' => 'text', 'max' => 40],
                        ['key' => 'cta_ziel', 'label' => 'Button-Ziel', 'type' => 'text', 'max' => 120],
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

                // ---- Leistungen: Texte macht Sartu; Kunde darf nur ein-/ausblenden ----
                [
                    'key' => 'leistungen',
                    'label' => 'Leistungen / Angebot',
                    'customer' => 'toggle',
                    'hideable' => true,
                    'customer_help' => 'Einzelne Leistungen können Sie aus- und wieder einblenden. Neue Leistungen oder Textänderungen übernimmt Sartu — schreiben Sie uns einfach.',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'einleitung', 'label' => 'Einleitung', 'type' => 'textarea', 'max' => 400],
                        ['key' => 'items', 'label' => 'Einträge', 'type' => 'list', 'max_items' => 12, 'toggle_items' => true, 'item' => [
                            ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 80],
                            ['key' => 'text', 'label' => 'Beschreibung', 'type' => 'textarea', 'max' => 300],
                        ]],
                    ],
                ],

                // ---- Team: von Sartu gepflegt; Kunde darf Personen ein-/ausblenden ----
                [
                    'key' => 'team',
                    'label' => 'Team',
                    'customer' => 'toggle',
                    'hideable' => true,
                    'customer_help' => 'Personen können Sie aus- und einblenden. Neue Personen oder Änderungen übernimmt Sartu.',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'members', 'label' => 'Personen', 'type' => 'list', 'max_items' => 20, 'toggle_items' => true, 'item' => [
                            ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'max' => 80],
                            ['key' => 'rolle', 'label' => 'Rolle', 'type' => 'text', 'max' => 80],
                            ['key' => 'bild', 'label' => 'Foto', 'type' => 'image'],
                        ]],
                    ],
                ],

                // ---- Betrieblich: der Kunde bearbeitet ----
                [
                    'key' => 'oeffnungszeiten',
                    'label' => 'Öffnungszeiten',
                    'customer' => 'edit',
                    'hideable' => true,
                    'fields' => [
                        ['key' => 'zeiten', 'label' => 'Zeiten', 'type' => 'hours'],
                        ['key' => 'hinweis', 'label' => 'Hinweis', 'type' => 'text', 'max' => 160, 'placeholder' => 'z. B. An Feiertagen geschlossen · Betriebsurlaub 1.–14. August'],
                    ],
                ],
                [
                    'key' => 'kontakt',
                    'label' => 'Kontakt',
                    'customer' => 'edit',
                    'fields' => [
                        ['key' => 'adresse', 'label' => 'Adresse', 'type' => 'textarea', 'max' => 240],
                        ['key' => 'telefon', 'label' => 'Telefon', 'type' => 'tel', 'max' => 60],
                        ['key' => 'email', 'label' => 'E-Mail', 'type' => 'email', 'max' => 120],
                        ['key' => 'maps', 'label' => 'Karten-Link', 'type' => 'url', 'max' => 300, 'help' => 'Link zu Google Maps (optional)'],
                    ],
                ],
                [
                    'key' => 'design',
                    'label' => 'Farben',
                    'customer' => 'edit',
                    'help' => 'Alle Farben Ihrer Website — je Farbe ein Wähler, Hex-Code oder Vorschlag. Gilt für die ganze Website.',
                    'fields' => sartu_site_theme_fields(),
                ],

                // ---- Von Sartu gepflegt (nicht im Kunden-Editor) ----
                [
                    'key' => 'beitraege',
                    'label' => 'Aktuelles / Beiträge',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 90],
                        ['key' => 'posts', 'label' => 'Beiträge', 'type' => 'list', 'max_items' => 30, 'item' => [
                            ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 120],
                            ['key' => 'datum', 'label' => 'Datum', 'type' => 'text', 'max' => 40],
                            ['key' => 'text', 'label' => 'Text', 'type' => 'textarea', 'max' => 1200],
                            ['key' => 'bild', 'label' => 'Bild', 'type' => 'image'],
                        ]],
                    ],
                ],
                [
                    'key' => 'seo',
                    'label' => 'Suchmaschine (Google)',
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Seitentitel', 'type' => 'text', 'max' => 65],
                        ['key' => 'description', 'label' => 'Beschreibung', 'type' => 'textarea', 'max' => 160],
                    ],
                ],
            ],
        ],

        // ---- Pflichtseite: Impressum (Kunde pflegt seine Firmendaten) ----
        'impressum' => [
            'label' => 'Impressum',
            'sections' => [
                [
                    'key' => 'impressum',
                    'label' => 'Impressum',
                    'customer' => 'edit',
                    'customer_help' => 'Gesetzlich vorgeschrieben. Bitte halten Sie diese Angaben aktuell.',
                    'fields' => [
                        ['key' => 'firmenname', 'label' => 'Firmenname', 'type' => 'text', 'max' => 160],
                        ['key' => 'inhaber', 'label' => 'Inhaber / Vertretungsberechtigter', 'type' => 'text', 'max' => 160],
                        ['key' => 'adresse', 'label' => 'Anschrift', 'type' => 'textarea', 'max' => 240],
                        ['key' => 'telefon', 'label' => 'Telefon', 'type' => 'tel', 'max' => 60],
                        ['key' => 'email', 'label' => 'E-Mail', 'type' => 'email', 'max' => 120],
                        ['key' => 'ust_id', 'label' => 'USt-IdNr. (falls vorhanden)', 'type' => 'text', 'max' => 60],
                        ['key' => 'kleinunternehmer', 'label' => 'Kleinunternehmer nach § 19 UStG (keine Umsatzsteuer)', 'type' => 'checkbox', 'help' => 'Anhaken, wenn Sie keine Umsatzsteuer ausweisen.'],
                        ['key' => 'register', 'label' => 'Handelsregister / Nr. (falls vorhanden)', 'type' => 'text', 'max' => 120],
                        ['key' => 'verantwortlich', 'label' => 'Verantwortlich für den Inhalt', 'type' => 'text', 'max' => 160],
                    ],
                ],
            ],
        ],

        // ---- Generische Inhaltsseite (von Sartu erstellt; Kunde kann sie ein-/ausblenden) ----
        'inhalt' => [
            'label' => 'Inhaltsseite',
            'sections' => [
                [
                    'key' => 'inhalt',
                    'label' => 'Inhalt',
                    // kein 'customer' => Texte macht Sartu; der Kunde blendet nur die ganze Seite ein/aus
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 120],
                        ['key' => 'text', 'label' => 'Text', 'type' => 'textarea', 'max' => 8000],
                    ],
                ],
            ],
        ],

        // ---- Pflichtseite: Datenschutz (von Sartu gepflegt, nicht kundenseitig) ----
        'datenschutz' => [
            'label' => 'Datenschutz',
            'sections' => [
                [
                    'key' => 'datenschutz',
                    'label' => 'Datenschutzerklärung',
                    // kein 'customer' => von Sartu gepflegt (rechtssicherer Text)
                    'fields' => [
                        ['key' => 'titel', 'label' => 'Titel', 'type' => 'text', 'max' => 120],
                        ['key' => 'text', 'label' => 'Text', 'type' => 'textarea', 'max' => 20000],
                    ],
                ],
            ],
        ],
    ];
}

/** Alle Vorlagen, die als eigene Seite dienen können (Slug-Kandidaten). */
function sartu_site_page_types(): array
{
    return [
        'home'        => ['vorlage' => 'standard', 'titel' => 'Startseite', 'nav_label' => 'Start', 'typ' => 'start', 'position' => 0],
        'impressum'   => ['vorlage' => 'impressum', 'titel' => 'Impressum', 'nav_label' => 'Impressum', 'typ' => 'pflicht', 'position' => 90],
        'datenschutz' => ['vorlage' => 'datenschutz', 'titel' => 'Datenschutz', 'nav_label' => 'Datenschutz', 'typ' => 'pflicht', 'position' => 91],
    ];
}

/** Eine Vorlage holen (Fallback: 'standard'). */
function sartu_site_template(string $vorlage): array
{
    $all = sartu_site_schema();
    return $all[$vorlage] ?? $all['standard'];
}

/** Eine Sektion holen (oder null). */
function sartu_site_section(string $vorlage, string $sectionKey): ?array
{
    foreach (sartu_site_template($vorlage)['sections'] as $section) {
        if ($section['key'] === $sectionKey) {
            return $section;
        }
    }
    return null;
}

/** Kunden-Modus einer Sektion: 'edit', 'toggle' oder null (nur Sartu). */
function sartu_site_customer_mode(string $vorlage, string $sectionKey): ?string
{
    $section = sartu_site_section($vorlage, $sectionKey);
    return $section['customer'] ?? null;
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
                'customer' => $section['customer'] ?? null,
                'spec' => $field,
            ];
        }
    }
    return $out;
}
