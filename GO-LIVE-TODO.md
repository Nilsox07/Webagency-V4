# Sartu — Go-Live-Checkliste

Diese Punkte sind bewusst bis zum Go-live offen. **Reihenfolge bei Domain-Wechsel beachten.**
> Diese Liste wird bei jedem Bau-Schritt mitgepflegt — Erledigtes ist abgehakt.

## Portal, Backend & Abrechnung — Setup auf dem Hosting
**Gebaut & einsatzbereit (Code fertig, per Vorschau/Playwright geprüft):**
- [x] Kundenportal (Cockpit, Website-Editor, Statistik, Wachsen, Paket & Rechnungen, Hilfe)
- [x] Selbst-Editor: nur Betriebliches editierbar, volle Farbsteuerung, Mehrseiten mit
      Pflichtseiten, Vorschau/Veröffentlichen, Versionen/Rückgängig, Bild-Upload + Alt-Texte
- [x] Impressum- + Datenschutz-Generator (füttert sich aus den Kundenangaben)
- [x] Auftragsmechanismus: Anfrage → Angebot (Admin) → verbindliche Zusage (§312j BGB) + Protokoll
- [x] Stufe-2-Briefing (12 Kapitel, kapitelbasiert) + Anzeige im Admin-Projekt
- [x] Admin-Portal: Anfragen, Angebote (+ Annahme-Protokoll), Rechnungen, Projekte, Kunden, Aktionen
- [x] Aktionen/Rabatte im Admin verwaltbar (Prozent/Fest/Gratis-Monate, Ziel, Zeitfenster) → Banner auf `preise.php`
- [x] Rechnungen + Angebot-/Rechnungs-PDF + E-Rechnung-XML + Mollie-Anbindung (hinter Schalter)
- [x] Sicherheits-Review: keine ausnutzbaren Lücken; `.htaccess`-Härtung ergänzt

**Vor dem ersten echten Kunden erledigen (braucht dein Hosting/MySQL):**
- [ ] **DB-Schema einspielen:** `database/mysql-schema.sql` importieren (alle Tabellen:
      profiles, login_tokens, briefings, projects, uploads, site_pages/site_blocks/site_page_versions,
      angebote, project_briefings, invoices/invoice_items/payments/subscriptions/rechnung_counter, aktionen).
- [ ] **Ersten Admin anlegen** (INSERT am Ende der Schema-Datei, deine E-Mail) → über `/login` einloggen.
- [ ] **`includes/config.local.php`** aus `config.local.example.php` anlegen: DB-Zugang,
      `SARTU_BASE_URL`, Mailabsender.
- [ ] **Mailversand** prüfen (Login-/Angebots-Mails kommen an; sonst SMTP statt `mail()`).
- [ ] **Kompletten Durchlauf testen:** Anfrage → Angebot senden → als Kunde einloggen →
      verbindlich beauftragen → Briefing ausfüllen → Editor/Vorschau/Veröffentlichen → Rechnung/PDF.

## Zahlungen aktivieren (Mollie)
- [ ] Mollie-Konto anlegen; im Dashboard PayPal/Kreditkarte/SEPA/Sofort aktivieren.
- [ ] `SARTU_MOLLIE_KEY` in `config.local.php` eintragen (erst `test_…`) → „Bezahlen" wird sichtbar.
- [ ] Rechnungs-Absenderdaten `SARTU_FIRMA_*` (Name, Anschrift, USt-IdNr./Steuernr., IBAN/BIC)
      sowie `SARTU_KLEINUNTERNEHMER=1` **oder** `SARTU_UST=19` setzen.
- [ ] Testzahlung im Mollie-Testmodus (Webhook `…/api/mollie-webhook.php` erreichbar? Rechnung wird „bezahlt"?).
- [ ] Danach auf `live_…`-Key umstellen.

## E-Rechnung
- [x] XRechnung/EN16931-UBL-XML je Rechnung (Download im Portal & Admin)
- [x] ZUGFeRD/Factur-X-PDF (E-Rechnungs-XML eingebettet ins Rechnungs-PDF, PDF/A-3-Metadaten)
- [ ] Generierte E-Rechnung einmal gegen einen Online-Validator prüfen (z. B. XRechnung-/ZUGFeRD-Validator).
- [ ] Rechnungen GoBD-konform archivieren (unveränderbar, 10 Jahre).

## Indexierung (ZUERST)
- [ ] **noindex entfernen:** `meta robots` auf allen Seiten zurück auf
      `index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1`
      + den `X-Robots-Tag`-Header aus der Server-Konfiguration löschen.
      **OHNE DIESEN SCHRITT IST DIE SEITE FÜR GOOGLE UNSICHTBAR.**
      (Portal-Seiten login/portal/admin/auth-callback bleiben `noindex,nofollow`.)

## Domain & Platzhalter
- [ ] `[DOMAIN]` überall ersetzen (Canonical, og:url, JSON-LD, robots.txt Sitemap, sitemap.xml).
- [ ] `[OG-IMAGE]` Social-Sharing-Bild hinterlegen und Pfad eintragen.
- [ ] NAP füllen: `[FIRMENNAME / INHABER]`, `[STRASSE UND HAUSNUMMER]`, `[PLZ] [ORT]`, `[TELEFON]`, `[E-MAIL]`, `[NACHNAME]`, `[JAHR]`.
- [ ] Social-Links als echte `<a>`-Tags einsetzen (`[INSTAGRAM-URL]`, `[FACEBOOK-URL]`, `[LINKEDIN-URL]`).

## Performance / Assets
- [ ] **Fehlende Bild-Dateien ins Repo aufnehmen:** Es werden 27 Asset-Dateien referenziert, im Repo
      liegen nur 4 (`contact-workspace-v1*`). Es fehlen u. a. `logo-wordmark-teal.svg`,
      `logo-wordmark-teal-light.svg` (Header/Footer-Logo!), `logo-teal.png` (Favicon),
      alle `hero-*-petrol.*`, `strategy-workspace-v2-petrol.*`, `quality-check-v1-petrol.*`,
      `photo-placeholder-studio-workspace-petrol.*`. Falls sie nur auf dem Server liegen:
      committen, sonst ist der Stand nicht reproduzierbar.
- [ ] **Favicon fehlt:** favicon.ico / SVG-Favicon + `<link rel="icon">` auf allen Seiten ergänzen.
- [x] Hero-Bilder und `assets/logo.png` als WebP-Varianten bereitgestellt und eingebunden.
- [ ] **Platzhalter-Fotos ersetzen:** `assets/photo-placeholder-*.png/.webp` und `assets/hero-agency-workspace-v2.png/.webp` vor dem finalen Launch durch echte Sartu-Fotos ersetzen
      (Arbeitsplatz, Prozess, Qualitätsprüfung, Website-Struktur). Keine erfundenen Referenzen, keine Fake-Bewertungen,
      keine Kundenlogos und keine Vorher-Nachher-Beweise verwenden, solange sie nicht echt freigegeben sind.

## Suchmaschinen / KI
- [ ] robots.txt: `[DOMAIN]` in der Sitemap-Zeile ersetzen.
- [ ] Bing Webmaster Tools + IndexNow einrichten.
- [ ] Google Search Console einrichten + Sitemap einreichen.

## KI-Chat-Assistent (vor erstem Verkauf)
- [ ] **EU-Inferenz-Anbieter festlegen (DSGVO-/CLOUD-Act-konform) VOR dem ersten Verkauf** des
      KI-Chat-Assistenten (Datenverarbeitung in der EU, AVV, keine Übermittlung in Drittländer).
- [ ] **AGB-Klauseln durch Kanzlei prüfen:** Fair-Use (bis 500 Unterhaltungen/Monat),
      Mindestlaufzeit 12 Monate (analog Rundum-Schutz) und die Auskunfts-Abgrenzung
      („Verbindliche Auskünfte — Preise, Termine, Rechtliches — bestätigen wir Ihnen persönlich").

## Website-Assistent-Anfrageweg-Tracking (optional, Opt-in)
- [ ] `briefing.js` → `CONFIG.trackingEndpoint` (`[ANALYTICS_BEACON_ENDPOINT]`) auf einen echten
      Beacon-Endpoint setzen. `trackStep()` sendet dann pro Schritt nur den anonymen Schrittnamen
      (keine Antworten, keine Kontaktdaten) und feuert ausschließlich nach Statistik-Einwilligung
      (`SartuConsent.has('analytics')`). Ohne Endpoint bleibt es ein No-op.

## Strukturierte Daten (erst mit echter Adresse)
- [ ] LocalBusiness/ProfessionalService-Schema mit echter NAP ergänzen + Organization um `address` erweitern.
- [ ] Organization `sameAs` mit den echten Social-URLs füllen.

## Rechtstexte (Lücken aus dem Review, BERICHT-REVIEW.md Abschnitt 6)
- [ ] Impressum: `[Vor- und Nachname / Firmenname]`, `[USt-IdNr.]` u. a. Musterdaten ersetzen.
- [ ] AGB: `[X] Tagen` Zahlungsziel festlegen, `[Widerrufsbelehrung gesondert beifügen]`,
      `[Monat/Jahr]` ersetzen.
- [ ] Datenschutz: `[Anbieter und ggf. Auftragsverarbeitungsvertrag ergänzen]` füllen
      (TDDDG-Umbenennung ist bereits erledigt).
- [ ] Ratgeber: „Stand: Juni 2026" + `dateModified` beim nächsten Review aktualisieren
      (ideal: eine zentrale Konstante). BAFA-Angabe „bis 31.12.2026" mit Wiedervorlage.
- [ ] Schlusscheck vor Livegang: `grep -rE '\[([A-ZÄÖÜ/ -]+)\]' *.php includes/` → keine Treffer.

## Rechtliches / AGB (Nachtrag Extras + Mehrsprachigkeit)
- [ ] **AGB von Kanzlei prüfen lassen:** Sprachversions-Klausel („Änderungen und Kontingente
      zählen je Sprachversion") + Inklusiv-Definitionen für Texte, Umzug und Besucher-Statistik
      sowie die Mehrsprachigkeits-Definition (maschinelle Übersetzung mit menschlicher Prüfung;
      Rechtstexte bleiben deutsch, Übersetzung über Kanzlei = Drittkosten).
- [ ] Stundensatz 150 €/Std (5-Minuten-Takt, ab 30 Min. Kostenschätzung) in AGB/Angebotsvorlage spiegeln.

## Redaktionsplan / Backlog
- [ ] Ratgeber-Artikel als Nachfrage-Generator: „Lohnt sich eine polnische oder tschechische
      Website für Betriebe in der Lausitz?" (Mehrsprachigkeit Grenzregion).

## Sicherheit vor dem Live-Gang (geprüft Juli 2026)
Code-Review ergab keine ausnutzbaren Lücken: alle DB-Abfragen mit Prepared Statements,
Rechteprüfung serverseitig (jeder nur eigene Daten), CSRF auf allen Schreib-Aktionen,
Ausgaben escaped (XSS-fest), Uploads geprüft + außerhalb Web-Zugriff, Farben/URLs validiert.
Vor dem Hochladen noch erledigen:
- [ ] `.htaccess`-Sperren wirken (Apache): `/database/mysql-schema.sql`, `/includes/config.local.php`
      und `/storage/…` dürfen im Browser **nicht** ladbar sein (404/403). Bei nginx entsprechende
      location-Blöcke setzen.
- [ ] Starkes, eigenes **DB-Passwort** in `includes/config.local.php` (nie ins Git).
- [ ] **HTTPS** erzwingen (Zertifikat + Weiterleitung http→https).
- [ ] Uploads/Geheimnisse möglichst **außerhalb** des Web-Ordners (`SARTU_STORAGE_PATH`).
- [ ] PHP/MySQL aktuell halten; `SARTU_DEBUG` in Produktion **aus**.
