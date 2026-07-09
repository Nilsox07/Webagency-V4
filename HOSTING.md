# Sartu — Was dein Webspace können muss

Kurzfassung: ein **ganz normales deutsches Webhosting** mit PHP, MySQL/MariaDB und SSL
reicht. Kein Node, kein Docker, kein externer Backend-Dienst. Diese Datei sagt dir genau,
worauf du beim Buchen achten musst — und mit `check-umgebung.php` prüfst du es nach dem
Hochladen in einem Klick.

## Was überhaupt gehostet wird
1. **Deine Sartu-Seite + das Portal/Backend** (dieser Code) — läuft auf deiner Hauptdomain.
2. **Die Websites deiner Kunden** — jede auf eigener Domain oder Sartu-Subdomain.

Zum Start deckt **ein** Webhosting-Paket mit **mehreren Domains** beides ab. Wenn viele
Kunden dazukommen, wächst du auf einen gemanagten vServer.

## Pflicht (ohne das läuft es nicht)
- **PHP 8.1 oder neuer** (ideal 8.2/8.3), im Hosting-Panel umstellbar.
- **MySQL 5.7.8+ oder MariaDB 10.2+** (wegen JSON-Feldern & utf8mb4). Fast jedes Hosting
  hat MariaDB 10.5+. Mindestens **1 Datenbank**.
- **PHP-Erweiterungen** (bei Standard-Hosting alle dabei):
  `pdo_mysql`, `mbstring`, `gd`, `fileinfo`, `iconv`, `curl`, `json`, `openssl`.
- **SSL/HTTPS** (Let's Encrypt, kostenlos — Standard).
- **Apache mit `.htaccess`** (die Sicherheitsregeln liegen bei) **oder** nginx (dann die
  Sperren als `location`-Blöcke nachziehen).

## Wichtig für dein Geschäftsmodell
- **Server-Standort Deutschland/EU** — du verkaufst „Hosting in Deutschland" und DSGVO.
  Also unbedingt einen **deutschen/EU-Hoster** nehmen.
- **Mailversand, der ankommt.** PHP-`mail()` landet auf Shared-Hosting oft im Spam. Deine
  Login-Codes MÜSSEN ankommen → am besten **SMTP** (Postfach beim Hoster oder ein
  Mail-Dienst). Vor dem ersten Kunden einmal testen.
- **Tägliche Backups + Uptime-Monitoring** — versprichst du im Rundum-Schutz. Backup bietet
  fast jeder Hoster; Uptime-Monitoring über einen kostenlosen Dienst (z. B. UptimeRobot).
- **Dateien außerhalb des Web-Ordners ablegen können** (für `storage/` mit Uploads/Rechnungen)
  — die meisten Hoster erlauben einen Ordner oberhalb von `public_html`. Pfad über
  `SARTU_STORAGE_PATH` setzen.

## Was du NICHT brauchst
- Kein Node.js, kein Composer, kein Framework, kein Redis, kein externer API-Dienst.
- Keine großen Ressourcen — reines PHP, läuft auf dem kleinsten sinnvollen Paket.

## Konkrete Empfehlung
Ein **deutsches Webhosting der Mittelklasse** (mehrere Domains, PHP 8.2, MariaDB,
Let's Encrypt, E-Mail-Postfächer/SMTP, tägliches Backup). Wenn du später viele Kundenseiten
hostest: **gemanagter vServer** in Deutschland.

## Ablauf nach dem Buchen — nur mit FTP, ohne phpMyAdmin
1. **Alle Dateien per FTP** in den Document-Root hochladen (oft `public_html`/`httpdocs`).
2. Im **Panel deines Hosters eine MySQL-Datenbank anlegen** (das eine, was FTP nicht kann) —
   du bekommst dabei Host, DB-Name, Benutzer, Passwort.
3. `includes/config.local.example.php` lokal kopieren nach `includes/config.local.php`,
   die DB-Daten + Basis-URL eintragen, per FTP hochladen.
4. `https://deine-domain.de/check-umgebung.php` aufrufen → alles grün?
5. `https://deine-domain.de/install.php` aufrufen → **Knopf „Datenbank einrichten"**
   (legt alle Tabellen an) → **ersten Admin anlegen** (deine E-Mail).
6. **`install.php` und `check-umgebung.php` per FTP löschen.**
7. Über `/login` einloggen, Mail testen, kompletten Durchlauf testen (siehe `GO-LIVE-TODO.md`).

Nur Schritt 2 (Datenbank anlegen) läuft übers Panel deines Hosters — den Rest macht der
Installer. Details: `SETUP.md` und `GO-LIVE-TODO.md`.
