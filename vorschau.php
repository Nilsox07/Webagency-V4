<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/site-content.php';
require_once __DIR__ . '/includes/uploads.php';

// Vorschau der eigenen Kundenseite. Standard: Entwurf; ?live=1 zeigt den
// veröffentlichten Stand. Nur für eingeloggte Kunden (bzw. Admin).

// Sicherer Vorschau-Modus mit Beispiel-Inhalten (kein Login, keine DB).
if (isset($_GET['preview']) && $_GET['preview'] === '1') {
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Robots-Tag: noindex, nofollow');
    $demoPage = ['id' => 'demo', 'project_id' => 'demo', 'slug' => 'home', 'vorlage' => 'standard', 'titel' => 'Muster Bäckerei'];
    $demoContent = [
        'hero' => ['headline' => 'Muster Bäckerei', 'subline' => "Frisch & handgemacht, seit 1985.", 'cta_text' => 'Jetzt anrufen', 'cta_ziel' => 'tel:030123456'],
        'ueber' => ['titel' => 'Über uns', 'text' => "Wir backen jeden Morgen frisch — mit Zeit, guten Zutaten und ohne Zusatzstoffe.\n\nSeit drei Generationen im Herzen von Musterstadt."],
        'leistungen' => ['titel' => 'Unser Angebot', 'einleitung' => 'Das gibt es bei uns täglich.', 'items' => [
            ['titel' => 'Brot & Brötchen', 'text' => 'Über 20 Sorten, täglich frisch aus dem Ofen.'],
            ['titel' => 'Kuchen & Torten', 'text' => 'Hausgemacht, auch auf Bestellung für Feiern.'],
            ['titel' => 'Frühstück', 'text' => 'Gemütlich bei uns im Café, mit Kaffee aus der Rösterei.'],
        ]],
        'oeffnungszeiten' => ['titel' => 'Öffnungszeiten', 'zeiten' => ['Montag' => '7–18 Uhr', 'Dienstag' => '7–18 Uhr', 'Mittwoch' => '7–18 Uhr', 'Donnerstag' => '7–18 Uhr', 'Freitag' => '7–18 Uhr', 'Samstag' => '7–13 Uhr', 'Sonntag' => 'geschlossen'], 'hinweis' => 'An Feiertagen geschlossen.'],
        'kontakt' => ['titel' => 'Kontakt', 'adresse' => "Hauptstraße 1\n12345 Musterstadt", 'telefon' => '030 123456', 'email' => 'hallo@muster-baeckerei.de'],
        'design' => ['akzentfarbe' => '#b45309'],
    ];
    render_customer_site($demoPage, $demoContent, []);
    exit;
}

$profile = current_profile();
if (!$profile) {
    header('Location: login.php');
    exit;
}

$pdo = db();
$stmt = $pdo->prepare('select id, titel, paket from projects where customer_id = ? order by created_at asc limit 1');
$stmt->execute([$profile['id']]);
$project = $stmt->fetch();

if (!$project) {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><meta charset="utf-8"><p style="font-family:sans-serif;padding:40px">Noch kein Projekt vorhanden.</p>';
    exit;
}

$page = sc_get_or_create_page($pdo, (string) $project['id'], 'home', 'standard');
$variant = (isset($_GET['live']) && $_GET['live'] === '1') ? 'published' : 'draft';
$content = sc_load_content($pdo, $page, $variant);

// Media-Map für den Renderer: [upload_id => ['url','alt']].
$media = [];
$mStmt = $pdo->prepare('select id, alt_text from uploads where project_id = ?');
$mStmt->execute([(string) $project['id']]);
foreach ($mStmt->fetchAll() as $u) {
    $media[$u['id']] = ['url' => upload_public_url((string) $u['id']), 'alt' => (string) ($u['alt_text'] ?? '')];
}

header('Content-Type: text/html; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow');
render_customer_site($page, $content, $media);
