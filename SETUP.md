# Sartu Setup

Diese Version läuft auf einem eigenen PHP-Server mit MySQL. Externe Hosting- oder Backend-Dienste werden nicht benötigt.

## 1. Dateien hochladen

Den Inhalt dieses Ordners in den Document Root der Domain hochladen. Der Server sollte PHP 8.1+ und MySQL oder MariaDB bereitstellen.

## 2. Datenbank anlegen

Eine leere MySQL-Datenbank erstellen und `database/mysql-schema.sql` importieren.

## 3. Konfiguration

`includes/config.local.example.php` nach `includes/config.local.php` kopieren und Datenbank, Absenderadresse und öffentliche Basis-URL eintragen.

Alternativ können diese Umgebungsvariablen gesetzt werden:

- `SARTU_DB_DSN`
- `SARTU_DB_USER`
- `SARTU_DB_PASSWORD`
- `SARTU_MAIL_FROM`
- `SARTU_MAIL_REPLY_TO`
- `SARTU_BASE_URL`
- `SARTU_DEBUG`

## 4. Admin anlegen

Den ersten Admin in der Tabelle `profiles` anlegen. Ein Beispiel steht am Ende von `database/mysql-schema.sql`. Danach ist der Login über `/login` möglich.

## 5. Prüfen

- `/anfrage` absenden und prüfen, ob ein Eintrag in `briefings` entsteht.
- Als Admin einloggen und unter `/admin` die Anfrage in ein Projekt umwandeln.
- Als Kunde über `/portal` prüfen, ob das Projekt angezeigt wird.
