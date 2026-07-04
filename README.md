# Sartu PHP/MySQL-Webseite

Eigenständige PHP-Version der Sartu-Webseite für einen eigenen Server mit MySQL. Die Seite benötigt kein Vercel, kein Supabase, keinen Composer-Build und keinen externen App-Host.

## Inhalt

- Öffentliche Seiten: `index.php`, `leistungen.php`, Leistungsseiten, `preise.php`, `ablauf.php`, `ratgeber*.php`, `kontakt.php`, `anfrage.php`
- Website-Anfrage: `briefing.php`, `briefing.js`, `briefing-schema.js`, `briefing.css`
- Preise: `pricing.js`, `pricing-calc.js`, `payment-terms.js`
- Admin/Login/Projektansicht: `admin.php`, `login.php`, `portal.php`, `api/`
- Serverlogik: `includes/`, `database/mysql-schema.sql`, `.htaccess`, `router.php`
- Assets: `assets/`

## Content-Struktur

- Branchen-, Vergleichs- und Qualitätsseiten werden zentral in `includes/priority-pages.php` gepflegt und mit `includes/priority-page-render.php` ausgegeben.
- Ratgeber-Artikel im neuen Datenformat liegen in `includes/ratgeber-articles.php` und werden mit `includes/ratgeber-render.php` ausgegeben.
- Ratgeber-Hub-Karten und kontextuelle SEO-Einstiege liegen in `includes/content-index.php`, damit Teasertexte nicht mehrfach im Template gepflegt werden.
- Branchen- und spätere Local-Landingpages bleiben aus Header/Footer heraus, bleiben aber über Sitemap, `llms.txt` und passende interne Kontextlinks erreichbar.

## Einrichtung

1. MySQL-Datenbank anlegen.
2. `database/mysql-schema.sql` importieren.
3. `includes/config.local.example.php` nach `includes/config.local.php` kopieren.
4. Datenbank, Mailabsender und öffentliche Basis-URL eintragen.
5. Ersten Admin laut Kommentar in `database/mysql-schema.sql` anlegen.
6. Mit `/login` anmelden und unter `/admin` arbeiten.

## Lokal starten

```bash
php -S localhost:8080 router.php
```

Danach `http://localhost:8080` öffnen. Auf Apache übernimmt `.htaccess` die Clean-URL-Regeln.

## Wichtige Hinweise

- `includes/config.local.php` enthält Serverdaten und wird nicht versioniert.
- Markdown-, SQL-, Log- und lokale Konfigdateien werden per `.htaccess` und `router.php` vor direktem Abruf geschützt.
- Alte `.html`-URLs werden auf die passenden PHP-Seiten abgebildet.
- Vor Go-live die offenen Punkte in `GO-LIVE-TODO.md` prüfen.
