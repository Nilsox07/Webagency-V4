# Sartu — Portal-Bauplan (Kunde + Admin)

Einfaches PHP + MySQL, gleicher Webhosting-Server, gleicher Stil wie die Website
(`includes/site-partials.php`, `styles.css`). Baut auf dem vorhandenen Gerüst auf.

## Vorhandenes Fundament (nicht neu bauen)
- `includes/auth.php` — passwortloses Login (E-Mail-Code + Magic-Link), Sessions, CSRF, Rollen customer/admin
- `login.php`, `auth-callback.php`, `api/auth/*` — Login-Flow
- `portal.php` + `portal-local.js`, `admin.php` + `admin-local.js` — Grundgerüste
- `api/portal/*`, `api/admin/*` — erste Endpunkte
- `database/mysql-schema.sql` — Schema (profiles, login_tokens, …)

## Zwei getrennte Oberflächen, ein Auth-System
- **Kundenportal** (`portal.php`, role=customer) — was der Kunde sieht
- **Admin** (`admin.php`, role=admin) — was Sartu/Nils sieht
- Gleiche Anmeldung, Rolle entscheidet über Zugang (`require_admin()` existiert).

---

## Kundenportal — Bereiche

1. **🏠 Cockpit (Startseite)** — Health-Dashboard: online, Ladezeit (Ampel), Sicherheit,
   letztes Backup, Besucher diesen Monat. Macht den Rundum-Schutz sichtbar.
2. **✏️ Website bearbeiten** — einfaches Backend (Texte, Bilder, Öffnungszeiten, Team,
   Blog, Farbpalette). *Design/Layout gesperrt, nur Inhalt + freigegebene Palette.*
3. **📊 Besucher & Statistik** — Zahlen aus der (datenschutzkonformen) Statistik.
4. **🚀 Wachsen** — Seite dazu (199 €), Paket-Upgrade (nur Differenz), SEO-Betreuung, KI-Assistent.
5. **💳 Mein Paket & Rechnungen** — Paket, Schutz-Stufe, Rechnungen, Zahlung, AVV.
6. **❓ Hilfe & Anfragen** — Kurz-Videos „wie ändere ich X", Kontakt, Status offener Wünsche.

**Zwei Phasen desselben Werkzeugs:**
- *Onboarding (Neukunde, vor Go-Live):* Briefing Stufe 2 — Sitemap bestätigen, Stichpunkte je
  Seite, Farben, Logo, Bilder, Uploads.
- *Betrieb (nach Go-Live):* aus dem Briefing wird „Website bearbeiten".

## Admin — Bereiche (für Nils)
1. **Leads / Anfragen** — eingehende Kurz-Anfragen (Stufe 1), Empfehlung + Status.
2. **Kunden** — Profile, Paket, Schutz-Stufe, Server/Domain-Status.
3. **Briefings** — eingereichte Stufe-2-Briefings je Projekt.
4. **Projekte** — Status (in Bau / live), Lieferzeit, Freigaben.
5. **Rechnungen/Abo** — laufende Schutz-Abos, offene Posten (später).
6. **Health-Übersicht** — alle Sites auf einen Blick (Ladezeit/Backup/online) (später).

---

## MVP-Reihenfolge (Meilensteine)
1. **Auth + Portal-Grundgerüst** im Website-Stil (Login → Portal-Shell mit Tabs, Rolle trennt Kunde/Admin). ⟵ Checkpoint
2. **Onboarding/Briefing Stufe 2** im Portal (das Herzstück zum Ausliefern).
3. **Website bearbeiten** — einfacher Content-Editor (Inhalt + Palette, Design gesperrt).
4. **Rechnungen/Paket** — Basis fürs Kassieren.
5. **Cockpit** — erst simpel (online + Ladezeit + Backup), dann ausbauen.
6. **Wachsen/Upsell + Hilfe-Videos.**
7. **Admin** parallel: Leads → Kunden → Briefings → Projekte.

## Grundsätze
- Design/Layout für Kunden **gesperrt** (nur Inhalt + freigegebene Farbpalette) — schützt die Qualität.
- Alles automatisierbare (Ladezeit-Check etc.) läuft als geplanter Job → ins Cockpit, keine Handarbeit.
- Keine Änderungsminuten mehr (Self-Service statt „wir machen's").

## Backlog (Ideen — erst nach Freigabe bauen, NICHT automatisch)
- Uptime-Verlauf & Security-Status im Cockpit
- Automatische Besucher-Statistik-Mail (monatlich) mit Upsell-Links
- „Großes Bild verlangsamt die Seite"-Hinweis im Editor (arbeitsfreie Qualitätskontrolle)
- Rechnungs-PDF-Erzeugung / Zahlungsanbindung
- Terminbuchung-/Newsletter-Verwaltung im Portal (für Platzhirsch)
- Mehrsprachigkeit-Verwaltung

## Test-Realität (wichtig)
Login + Portal brauchen **MySQL**. In dieser Umgebung kann ich den Code bauen, PHP-Syntax prüfen
und Seiten teilrendern — aber den **vollen Login-/DB-Flow testest du auf deinem Hosting** (echte
MySQL + E-Mail-Versand). Ich liefere Code, der gegen das vorhandene Schema läuft, + kurze Testschritte.
