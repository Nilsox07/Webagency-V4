# Sartu — Projektkarte (für Claude Code)

Kurzkarte, damit man nicht das halbe Repo lesen muss. Bei Änderungen an Struktur/
Entscheidungen mitpflegen.

## Was das ist
Marketing-Website + **Kunden-/Admin-Portal** einer deutschen Webdesign-Agentur („Sartu").
Reines **PHP + MySQL**, kein Framework, kein Composer, kein Node zur Laufzeit.

## Stack & Konventionen (unbedingt einhalten)
- PHP mit `declare(strict_types=1)`. **Alle** DB-Zugriffe über **PDO Prepared Statements**
  (`EMULATE_PREPARES=false`). IDs = `uuidv4()`. Geldbeträge **in Cent** (Ganzzahl).
- Auth **passwortlos** (Magic-Link + 6-stelliger Code), Sessions, **CSRF** auf allen
  Nicht-GET-Requests, Rate-Limiting. Admin-Check serverseitig (`require_admin()`).
- Ausgaben **immer escapen** (XSS). Farben/Hex + URL-Schemes validieren.
- Assets mit Versions-Query bumpen bei Änderung: `datei.css?v=N` / `datei.js?v=N`.
- **Vorschau-Modus:** viele Portal-Seiten laufen mit `?preview=1` (Demo-Daten, kein Login,
  keine DB) — so testet/screenshotet man ohne Hosting.

## Wo was liegt
**Öffentliche Website (Root, `.php`):** index, preise, leistungen, leistung-*, ablauf,
ueber-uns, ratgeber*, agb, impressum, datenschutz, kontakt, anfrage/briefing (Konfigurator).
Header/Promo-Leiste: `includes/site-partials.php`. Marketing-Content-Bausteine:
`includes/priority-pages.php`, `priority-page-render.php`, `ratgeber-*.php`, `content-index.php`.
CSS: **`styles.css`** (öffentlich), Marken-Tokens in dessen `:root` (Teal `--green #12A594`).

**Portal (Kunde):** `portal.php` + `portal-local.js` + `portal-editor.js`. Angebot:
`angebot.php`/`angebot.js`. Briefing-Wizard: `onboarding.php`/`onboarding.js`. Login:
`login.php`/`login-local.js`, `auth-callback.php`.

**Admin:** `admin.php` + `admin-local.js` (Tabs: Anfragen, Angebote, Rechnungen, Projekte,
Kunden, Aktionen; Modals; **KI-Bau-Prompt-Generator** = `buildKiPrompt`/`showPromptBuilder`).

**API:** `api/portal/*` (content, briefing, offer, offer-pdf, invoices, invoice-file, pay),
`api/admin/*` (offers, briefings, invoices, aktionen, projects, customers, briefing),
`api/auth/*`, `api/upload.php`, `api/file.php`, `api/mollie-webhook.php`.

**includes/ (Kern):**
- `bootstrap.php` Header · `config.php` (+ `config.local.php` merge) · `db.php` (`db()`, `app_config()`)
- `auth.php` (Login/CSRF/`require_admin`/`send_offer_mail`) · `http.php` (`json_response/json_input/uuidv4`)
- `billing.php` (Angebots-/Rechnungs-PDF, XRechnung-UBL, ZUGFeRD-CII) · `pdf.php` (`SartuPdf`)
- `mollie.php` (Zahlungen) · `aktionen.php` (Rabatte + Banner/Promo-Leiste)
- `site-content.php` + `site-content-schema.php` (strukturiertes Editor-Modell, `render_customer_site`)
- `legal-generator.php` (Impressum/Datenschutz) · `uploads.php` · `briefing2-schema.php` (12-Kapitel-Briefing)

**DB-Schema:** `database/mysql-schema.sql` (alle Tabellen; Beträge in Cent).

## Design-System (Portal)
`portal.css` = Token-System, **Hell/Dunkel** über `data-theme` (Standard = Gerät via
`prefers-color-scheme`), Umschalter `theme.js` (System→Hell→Dunkel, `localStorage`).
Schrift: **Sora** (Headlines) + **Manrope** (UI). Akzent Teal `#12A594` sparsam. Unterstrich-
Tabs, Radien Buttons/Inputs ~10px, Karten ~14px. FOUC-Guard-Inline-Script im `<head>` jeder
Portal-Seite. Kein Lime mehr (früherer Fehlgriff, überall auf Teal umgestellt).

## Feste Produkt-Entscheidungen (nicht neu herleiten)
- Pakete: **Start 1.290 € · Wachstum 3.290 € · Platzhirsch 6.490 €**; Rundum-Schutz **49/99/249 €/Mon.**
- **Korrekturrunden 1 / 1 / 2** (eine Runde = ein gesammeltes Feedback).
- **SEO-Betreuung: ein Preis, flach 490 €/Monat** (keine 3 Stufen).
- **KI-Chat-Assistent: ein Produkt, 990 € + 79 €/Mon.** (kein zweiter 490er-Bot).
- Zentrale Preisdaten: `pricing.js` (+ `pricing-calc.js`, Test `pricing.test.js`), Doku `KONFIGURATOR.md`.

## KI-Bau-Prompt & editierbare Kundenseiten
Kundenseiten werden **individuell mit KI gebaut** (kein Vorlagen-Baukasten). Der Admin erzeugt
aus dem Briefing einen fertigen Prompt (`admin-local.js` → „Prompt für KI kopieren"): nur
bestellte Seiten/Funktionen (Checkboxen), Ausschluss-Klausel, Herstellervermerk „Webdesign
von Sartu" (Konstante `SARTU_URL`, aktuell `nils.nelten.de/SARTU`), plus die geplante
**editierbare-Felder-Konvention** `sc_text/sc_richtext/sc_bild/sc_farbe/sc_site/sc_track`.
Die **Runtime `sartu-edit.php`** dazu ist noch NICHT gebaut (braucht die echte DB → Go-Live).

## Verifikation (in dieser Umgebung)
- PHP: `php -l datei.php`. JS: `/opt/node22/bin/node --check datei.js`. Test: `node pricing.test.js`.
- Screenshots: PHP-Dev-Server starten, dann Playwright — Chromium `/opt/pw-browsers/chromium`,
  `NODE_PATH=/opt/node22/lib/node_modules`. Vorschau via `?preview=1`.
- **`pkill` des eigenen PHP-Servers gibt Exit 144 zurück — das ist harmlos** (nicht als Fehler werten).
- Keine MySQL in dieser Umgebung → DB-abhängiges End-to-End erst auf dem Hosting testbar.

## Go-Live & Sicherheit
- Offene Punkte in **`GO-LIVE-TODO.md`** (mitpflegen). Hosting: `HOSTING.md`, Web-Installer
  `install.php`, Umgebungscheck `check-umgebung.php` (beide nach Setup **löschen**).
- **`includes/config.local.php`** (DB-Passwort/Keys) **nie committen** (steht in `.gitignore`).
- `.sql`/config/`storage/` nicht web-erreichbar (`.htaccess`). Bei Livegang: `noindex` entfernen,
  `[DOMAIN]`/NAP/USt-IdNr. füllen, `SARTU_URL` auf `https://sartu.de`.

## Git
Branch **`claude/german-text-review-ivc459`**. Commits mit Autor `noreply@anthropic.com`.
**Nie** Modell-ID in Commits/Artefakte. DB/Deploy macht der Betreiber zuletzt.
