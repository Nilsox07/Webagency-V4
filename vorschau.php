<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/site-content.php';
require_once __DIR__ . '/includes/uploads.php';

/** Nav-Eintrag-URL für die Vorschau bauen. */
function vorschau_url(string $slug, string $suffix): string
{
    return 'vorschau.php?' . $suffix . 'page=' . rawurlencode($slug);
}

/** Slug aus Query säubern. */
function vorschau_slug(): string
{
    return preg_replace('~[^a-z0-9\-]~', '', strtolower((string) ($_GET['page'] ?? 'home'))) ?: 'home';
}

// ---- Vorschau-Modus mit Beispiel-Inhalten (kein Login, keine DB) ----
if (isset($_GET['preview']) && $_GET['preview'] === '1') {
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Robots-Tag: noindex, nofollow');

    $demoPages = [
        'home' => ['vorlage' => 'standard', 'titel' => 'Startseite', 'nav_label' => 'Start'],
        'aktuelles' => ['vorlage' => 'inhalt', 'titel' => 'Aktuelles', 'nav_label' => 'Aktuelles'],
        'impressum' => ['vorlage' => 'impressum', 'titel' => 'Impressum', 'nav_label' => 'Impressum'],
        'datenschutz' => ['vorlage' => 'datenschutz', 'titel' => 'Datenschutz', 'nav_label' => 'Datenschutz'],
    ];
    $demoContent = [
        'home' => [
            'hero' => ['headline' => 'Muster Bäckerei', 'subline' => 'Frisch & handgemacht, seit 1985.', 'cta_text' => 'Jetzt anrufen', 'cta_ziel' => 'tel:030123456'],
            'ueber' => ['titel' => 'Über uns', 'text' => "Wir backen jeden Morgen frisch — mit Zeit und guten Zutaten."],
            'leistungen' => ['titel' => 'Unser Angebot', 'items' => [
                ['titel' => 'Brot & Brötchen', 'text' => 'Über 20 Sorten, täglich frisch.'],
                ['titel' => 'Kuchen & Torten', 'text' => 'Hausgemacht, auch auf Bestellung.'],
            ]],
            'oeffnungszeiten' => ['zeiten' => ['Montag' => '7–18 Uhr', 'Samstag' => '7–13 Uhr', 'Sonntag' => 'geschlossen'], 'hinweis' => 'An Feiertagen geschlossen.'],
            'kontakt' => ['adresse' => "Hauptstraße 1\n12345 Musterstadt", 'telefon' => '030 123456', 'email' => 'hallo@muster-baeckerei.de'],
            'design' => ['akzentfarbe' => '#b45309', 'abschnitt' => '#faf5ee', 'ueberschrift' => '#3d2b1f'],
        ],
        'aktuelles' => ['inhalt' => ['titel' => 'Aktuelles', 'text' => "Diese Inhaltsseite pflegt Sartu für Sie.\n\nSie können sie im Portal jederzeit ein- oder ausblenden."]],
        'impressum' => ['impressum' => ['firmenname' => 'Muster Bäckerei GmbH', 'inhaber' => 'Max Mustermann', 'adresse' => "Hauptstraße 1\n12345 Musterstadt", 'telefon' => '030 123456', 'email' => 'hallo@muster-baeckerei.de', 'ust_id' => 'DE123456789']],
        'datenschutz' => ['datenschutz' => ['titel' => 'Datenschutzerklärung', 'text' => "Der Schutz Ihrer Daten ist uns wichtig.\n\nDiese Erklärung wird von Sartu rechtssicher erstellt und hier eingesetzt."]],
    ];

    $slug = vorschau_slug();
    if (!isset($demoPages[$slug])) {
        $slug = 'home';
    }
    $theme = sc_resolve_theme($demoContent['home']['design'] ?? []);
    $nav = [];
    foreach ($demoPages as $s => $def) {
        $nav[] = ['slug' => $s, 'label' => $def['nav_label'], 'url' => vorschau_url($s, 'preview=1&'), 'current' => $s === $slug];
    }
    $page = ['id' => 'demo', 'project_id' => 'demo', 'slug' => $slug, 'vorlage' => $demoPages[$slug]['vorlage'], 'titel' => $demoPages[$slug]['titel']];
    render_customer_site($page, $demoContent[$slug], [], $nav, $theme);
    exit;
}

// ---- Echt: nur eingeloggte Kunden ----
$profile = current_profile();
if (!$profile) {
    header('Location: login.php');
    exit;
}

$pdo = db();
$stmt = $pdo->prepare('select id from projects where customer_id = ? order by created_at asc limit 1');
$stmt->execute([$profile['id']]);
$project = $stmt->fetch();

if (!$project) {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><meta charset="utf-8"><p style="font-family:sans-serif;padding:40px">Noch kein Projekt vorhanden.</p>';
    exit;
}
$projectId = (string) $project['id'];
sc_ensure_pages($pdo, $projectId);

$variant = (isset($_GET['live']) && $_GET['live'] === '1') ? 'published' : 'draft';
$suffix = $variant === 'published' ? 'live=1&' : '';
$slug = vorschau_slug();
$page = sc_page_by_slug($pdo, $projectId, $slug) ?: sc_page_by_slug($pdo, $projectId, 'home');
if (!$page) {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><meta charset="utf-8"><p style="font-family:sans-serif;padding:40px">Keine Seite vorhanden.</p>';
    exit;
}
$content = sc_load_content($pdo, $page, $variant);

// Site-Theme aus der Startseite (gilt für alle Seiten).
$homePage = sc_page_by_slug($pdo, $projectId, 'home');
$homeContent = $homePage ? sc_load_content($pdo, $homePage, $variant) : [];
$theme = sc_resolve_theme($homeContent['design'] ?? []);

// Media-Map.
$media = [];
$mStmt = $pdo->prepare('select id, alt_text from uploads where project_id = ?');
$mStmt->execute([$projectId]);
foreach ($mStmt->fetchAll() as $u) {
    $media[$u['id']] = ['url' => upload_public_url((string) $u['id']), 'alt' => (string) ($u['alt_text'] ?? '')];
}

// Navigation aus den aktiven Seiten.
$nav = [];
foreach (sc_project_pages($pdo, $projectId) as $p) {
    if ((int) $p['aktiv'] !== 1) {
        continue;
    }
    $nav[] = ['slug' => $p['slug'], 'label' => ($p['nav_label'] ?: $p['titel'] ?: $p['slug']), 'url' => vorschau_url((string) $p['slug'], $suffix), 'current' => $p['slug'] === $page['slug']];
}

header('Content-Type: text/html; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow');
render_customer_site($page, $content, $media, $nav, $theme);
