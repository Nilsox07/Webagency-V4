<?php
declare(strict_types=1);

/**
 * Aktionen / Rabatte — im Admin verwaltbar, öffentlich als Banner sichtbar.
 * Reine Logik + Renderer; Persistenz über die Tabelle `aktionen`.
 */

require_once __DIR__ . '/db.php';

/** Zieloptionen: Schlüssel => Anzeigename (auch für das Admin-Dropdown). */
function aktion_ziele(): array
{
    return [
        'alle'                => 'Alle Pakete',
        'paket:start'         => 'Paket Start',
        'paket:wachstum'      => 'Paket Wachstum',
        'paket:platzhirsch'   => 'Paket Platzhirsch',
        'addon:ki-assistent'  => 'KI-Chat-Assistent',
        'addon:seo-betreuung' => 'SEO-Betreuung',
        'addon:logo'          => 'Logo & Branding',
    ];
}

/** Aktionstypen: Schlüssel => Anzeigename. */
function aktion_typen(): array
{
    return [
        'prozent'       => 'Prozent-Rabatt (%)',
        'fest'          => 'Fester Betrag (€)',
        'gratis_monate' => 'Gratis-Monate',
    ];
}

function aktion_ziel_label(string $ziel): string { return aktion_ziele()[$ziel] ?? $ziel; }
function aktion_typ_label(string $typ): string { return aktion_typen()[$typ] ?? $typ; }

/** Kurzer Rabatt-Text, z. B. „-30 %", „-200 €", „2 Monate gratis". */
function aktion_wert_text(array $a): string
{
    $w = (int) ($a['wert'] ?? 0);
    switch ((string) ($a['typ'] ?? 'prozent')) {
        case 'fest':          return '-' . number_format($w, 0, ',', '.') . ' €';
        case 'gratis_monate': return $w . ' ' . ($w === 1 ? 'Monat' : 'Monate') . ' gratis';
        default:              return '-' . $w . ' %';
    }
}

/** Ist die Aktion aktuell gültig (aktiv + im Zeitfenster)? */
function aktion_ist_gueltig(array $a, ?string $heute = null): bool
{
    if ((int) ($a['aktiv'] ?? 0) !== 1) { return false; }
    $heute = $heute ?? date('Y-m-d');
    if (!empty($a['start_am']) && (string) $a['start_am'] > $heute) { return false; }
    if (!empty($a['end_am']) && (string) $a['end_am'] < $heute) { return false; }
    return true;
}

/** Alle Aktionen (neueste zuerst). */
function aktionen_all(PDO $pdo): array
{
    return $pdo->query('select * from aktionen order by created_at desc')->fetchAll();
}

/** Aktuell gültige Aktionen. */
function aktionen_aktiv(PDO $pdo): array
{
    $heute = date('Y-m-d');
    $out = [];
    foreach (aktionen_all($pdo) as $a) {
        if (aktion_ist_gueltig($a, $heute)) { $out[] = $a; }
    }
    return $out;
}

/** Beste gültige Aktion für ein Ziel — exaktes Ziel schlägt „alle". */
function aktion_fuer(PDO $pdo, string $ziel): ?array
{
    $fallback = null;
    foreach (aktionen_aktiv($pdo) as $a) {
        if ((string) $a['ziel'] === $ziel) { return $a; }
        if ((string) $a['ziel'] === 'alle' && $fallback === null) { $fallback = $a; }
    }
    return $fallback;
}

/** Rabattierten Preis (€) berechnen. gratis_monate lässt den Einmalpreis unverändert. */
function aktion_preis(int $preis, array $a): int
{
    $w = (int) ($a['wert'] ?? 0);
    if ((string) ($a['typ'] ?? '') === 'prozent') {
        $preis = (int) round($preis * (100 - max(0, min(100, $w))) / 100);
    } elseif ((string) ($a['typ'] ?? '') === 'fest') {
        $preis = max(0, $preis - $w);
    }
    return $preis;
}

/** Reines Banner-Markup aus einer Aktions-Row (ohne DB — dadurch testbar). */
function aktion_banner_html(array $a): string
{
    $e = static function ($v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
    $badge = !empty($a['badge']) ? (string) $a['badge'] : aktion_wert_text($a);
    $text  = !empty($a['hinweis']) ? (string) $a['hinweis'] : ((string) ($a['name'] ?? 'Aktion') . ' — jetzt sichern');
    $bis = '';
    if (!empty($a['end_am'])) {
        $ts = strtotime((string) $a['end_am']);
        if ($ts) { $bis = ' <span class="aktion-bis">nur bis ' . $e(date('d.m.Y', $ts)) . '</span>'; }
    }
    return '<div class="aktion-banner" role="note">'
        . '<span class="aktion-badge">' . $e($badge) . '</span>'
        . '<span class="aktion-text">' . $e($text) . $bis . '</span></div>';
}

/** Öffentliches Aktions-Banner für ein Ziel — leiser No-op ohne DB oder ohne aktive Aktion. */
function render_aktion_banner(string $ziel = 'alle'): string
{
    try {
        $a = aktion_fuer(db(), $ziel);
    } catch (Throwable $e) {
        return '';
    }
    return $a ? aktion_banner_html($a) : '';
}

/**
 * Dezente, schließbare Ankündigungs-Leiste (site-weit im Header).
 * Zeigt nur eine gültige „Alle Pakete"-Aktion, ein CTA, mit Deadline; per localStorage
 * ausblendbar. Leiser No-op ohne DB oder ohne aktive Aktion.
 */
function render_promo_bar(): string
{
    try {
        $a = aktion_fuer(db(), 'alle');
    } catch (Throwable $e) {
        return '';
    }
    if (!$a) { return ''; }
    $e = static function ($v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
    $badge = !empty($a['badge']) ? (string) $a['badge'] : aktion_wert_text($a);
    $text  = !empty($a['hinweis']) ? (string) $a['hinweis'] : ((string) ($a['name'] ?? 'Aktion') . ' — jetzt sichern');
    $bis = '';
    if (!empty($a['end_am'])) {
        $ts = strtotime((string) $a['end_am']);
        if ($ts) { $bis = ' <span class="promo-bar-bis">nur bis ' . $e(date('d.m.Y', $ts)) . '</span>'; }
    }
    $id = $e((string) $a['id']);
    return '<div class="promo-bar" id="promoBar" data-akt="' . $id . '" hidden>'
        . '<div class="container promo-bar-inner">'
        . '<span class="promo-bar-badge">' . $e($badge) . '</span>'
        . '<span class="promo-bar-text">' . $e($text) . $bis . '</span>'
        . '<a class="promo-bar-cta" href="preise.php">Zu den Preisen &rarr;</a>'
        . '<button type="button" class="promo-bar-x" aria-label="Aktion ausblenden">&times;</button>'
        . '</div>'
        . '<script>(function(){var b=document.getElementById("promoBar");if(!b)return;try{if(localStorage.getItem("promoDismiss")===b.dataset.akt){b.remove();return;}}catch(e){}b.hidden=false;var x=b.querySelector(".promo-bar-x");if(x)x.addEventListener("click",function(){try{localStorage.setItem("promoDismiss",b.dataset.akt);}catch(e){}b.remove();});})();</script>'
        . '</div>';
}
