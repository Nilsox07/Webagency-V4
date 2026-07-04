# Sartu — Go-Live-Checkliste

Diese Punkte sind bewusst bis zum Go-live offen. **Reihenfolge bei Domain-Wechsel beachten.**

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
