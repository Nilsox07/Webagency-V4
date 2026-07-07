# Portale — Feature-Brainstorm (Admin + Kunde)

> Ideensammlung, geordnet nach Nutzen und Aufwand. Nicht alles muss gebaut werden —
> die Philosophie bleibt „Tesla, nicht Mercedes": lieber wenige Dinge, die richtig gut
> funktionieren, als 50 Halbfunktionen. Prioritäten:
> **P1 = Grundgerüst / bald nötig** · **P2 = macht dich stark** · **P3 = später / Kür**

---

## TEIL A — ADMIN-PORTAL (dein Cockpit)

Das Admin-Portal ist deine Firmenzentrale. Alles, was du sonst in 5 Tools machen würdest
(Rechnung, Projektübersicht, Website pflegen, Support), soll hier an einem Ort passieren.

### A1 · Sartu-Website komplett selbst pflegen (CMS) — **P1**
Das, was du zuerst genannt hast. Du sollst deine eigene Seite ändern können, ohne Code.

- **Texte & Überschriften** aller Seiten (Startseite, Preise, Leistungen, Über uns …)
  direkt im Portal bearbeiten — Vorschau vor dem Speichern.
- **Preise & Pakete zentral** pflegen (1.290 / 3.290 / 6.490, Rundum-Schutz 49/99/249).
  Änderst du hier den Preis, ändert er sich überall auf der Seite automatisch (eine Quelle
  statt an 8 Stellen). Genau das Chaos, das wir gerade per Hand aufgeräumt haben, wäre dann weg.
- **Ratgeber / Blog** — neue Artikel schreiben (für SEO/KI-Sichtbarkeit sehr wichtig).
- **Portfolio / Referenzen** — neue Kundenprojekte mit Bild + Text hinzufügen.
- **FAQ** pflegen (die tauchen auch als JSON-LD bei Google/KI auf).
- **Bilder verwalten** — Mediathek, Bilder hochladen, automatisch komprimiert (Ladezeit!).
- **SEO-Felder** je Seite (Titel, Meta-Description, OG-Bild) — ohne im Code zu wühlen.
- **Entwurf / Veröffentlichen** — Änderung erst als Entwurf, dann live schalten.

> Technischer Hinweis: Die Seite ist heute statisch (PHP-Dateien). Für „live editierbar"
> wandern die Texte in die Datenbank und die Seiten lesen sie von dort. Das ist der größte
> Brocken hier — aber genau der Schritt, der dich unabhängig macht.

### A2 · Anfragen & Sales-Pipeline (CRM-light) — **P1**
- Eingehende Anfragen (aus dem Zwei-Stufen-Flow) als Liste + Detailansicht. **(steht schon)**
- **Pipeline-Ansicht** (Neu → Angebot → Gewonnen/Verloren) als einfache Spalten.
- **Notizen & Wiedervorlage** je Anfrage („nachfassen am …").
- **Branchenexklusivität**: sehen, welche Branche in welcher Stadt schon vergeben ist,
  bevor du eine neue Anfrage annimmst (dein Verkaufsargument, aber auch Schutz vor Konflikten).
- **Leadmagnet-Auswertung**: wer hat welche Checkliste heruntergeladen → warme Leads.

### A3 · Projektsteuerung — **P1**
- Projekt aus Anfrage erstellen. **(steht schon)**
- **Phasen/Status** (Angebot → Inhalte → Design → Korrektur 1–3 → Live). **(steht schon)**
- **Aufgaben-Checkliste** je Projekt (was fehlt noch, was ist erledigt).
- **Dateiaustausch** mit dem Kunden (Logo, Bilder, Texte hoch- und runterladen).
- **Freigaben**: Kunde klickt „Design freigegeben" → du siehst es hier.
- **Liefertermin** + automatische Erinnerung, wenn er näher rückt.

### A4 · Rechnungen & Zahlungen — **P1 (rechtlich + Geld)**
Das Herzstück fürs Überleben. Zwei Dinge, die du genannt hast:

- **Mollie-Anbindung**:
  - **Einmalzahlungen** für Pakete (1.290 / 3.290 / 6.490) — Anzahlung + Restzahlung.
  - **Wiederkehrende Zahlungen (Abo/SEPA)** für Rundum-Schutz (49/99/249 €/Mon.) und
    SEO-Betreuung (490 €/Mon.). Das ist deine „Ruhe-Rendite" — läuft dann automatisch ein.
    Mollie kann echte Abos (recurring) inkl. SEPA-Lastschrift; wichtig, sonst hinterläufst
    du jeden Monat 40 Kunden per Hand.
  - Zahlungsstatus landet automatisch im Portal (bezahlt / offen / fehlgeschlagen).
- **E-Rechnung (XRechnung / ZUGFeRD)**:
  - Seit 2025 musst du als Unternehmen E-Rechnungen **empfangen** können; das **Ausstellen**
    wird 2027/2028 Pflicht. Wer B2B (Handwerker, Firmen) bedient, sollte das früh können.
  - Rechnungen als **ZUGFeRD-PDF** (PDF mit eingebettetem XML) erzeugen — sieht aus wie
    eine normale PDF, ist aber maschinenlesbar. Das ist der pragmatische Standard.
  - **Fortlaufende Rechnungsnummern**, korrekte Pflichtangaben, **GoBD-konform** speichern
    (Rechnungen dürfen nachträglich nicht änderbar sein → als PDF einfrieren).
- **Mahnwesen**: offene Posten sehen, Zahlungserinnerung mit einem Klick.
- **DATEV-/Steuerberater-Export** (CSV) — spart dir am Jahresende viel Ärger.
- **Angebote**: Angebot als PDF generieren, Kunde nimmt online an (→ wird zur Rechnung).

### A5 · Betrieb & Monitoring (alle Kundenseiten auf einen Blick) — **P2**
Da du auf **eigenen Servern** hostest, brauchst du eine Ops-Übersicht:

- **Uptime aller Kundenseiten** (grün/rot), Ladezeiten, SSL-Ablauf, letzte Backups.
- **Update-Übersicht**: welche Seite braucht ein Sicherheitsupdate.
- **Ein-Klick-Backup / Restore** je Kunde.
- **Alarm** (E-Mail/Portal), wenn eine Seite offline geht — bevor der Kunde es merkt.
  Das ist genau die „Rundum-Schutz"-Leistung, die du verkaufst — hier steuerst du sie.

### A6 · Marketing & Reputation — **P3**
- **Newsletter** an Interessenten/Kunden (DSGVO-konform, Double-Opt-in).
- **Testimonials** sammeln (Kunde bekommt Link, gibt Bewertung → landet auf Portfolio).
- **Content-Kalender** für Ratgeber/Social.

### A7 · Auswertung / Zahlen — **P2**
- **MRR** (monatlich wiederkehrender Umsatz aus Schutz + SEO) — deine wichtigste Kennzahl.
- Umsatz einmalig vs. wiederkehrend, offene Rechnungen, Anzahl aktiver Kunden, Churn.
- Einfaches Dashboard, keine Excel-Wüste.

### A8 · Einstellungen & Sicherheit — **P1**
- Deine Firmendaten (fürs Impressum/Rechnungen an einer Stelle).
- **Zugangsschutz** hart server-seitig (nicht nur im Browser), 2-Faktor für Admin.
- Rollen (falls du später jemanden dazunimmst): Admin / Mitarbeiter.

---

## TEIL B — KUNDENPORTAL (das Erlebnis für deine Kunden)

Der Kunde soll sich fühlen wie bei einem großen Anbieter — aber ohne die Komplexität.
„WordPress, nur einfacher" + das Gefühl, dass sich jemand kümmert.

### B1 · Website selbst bearbeiten (Self-Service-Editor) — **P1**
Dein Kernversprechen statt „Änderungsminuten":

- **Texte ändern** direkt auf der eigenen Seite (klicken, tippen, speichern).
- **Bilder tauschen** aus eigener Mediathek (automatisch komprimiert).
- **Öffnungszeiten, Kontakt, Adresse** pflegen.
- **Team & Beiträge/News** hinzufügen.
- **Akzentfarbe** aus einer festen Palette wählen — **Design bleibt geschützt**
  (er kann es nicht „kaputt" machen, das ist bewusst begrenzt).
- **Vorschau + Rückgängig** (Versionen), damit niemand Angst haben muss, etwas zu zerstören.

### B2 · Onboarding / Briefing Stufe 2 — **P1**
- Nach der kurzen öffentlichen Anfrage: **geführtes Briefing** im Portal
  (Logo hochladen, Texte/Stichpunkte liefern, Wunschbeispiele, Farbwelt).
- Fortschrittsbalken „So weit sind wir" — nimmt dem Kunden Unsicherheit.

### B3 · Gesundheits-Cockpit — **P1 (steht als Anzeige, braucht echte Werte)**
- **Online-Status, Ladezeit, Sicherheit, letztes Backup** — automatisch gefüttert
  (kleiner Hintergrund-Job misst regelmäßig). Das macht „Rundum-Schutz" sichtbar/wertvoll.

### B4 · Statistik (datenschutzkonform) — **P2**
- Besucher, Herkunft (Google/direkt/KI), Anfragen — ohne Cookie-Banner-Zwang.
  Einfache Kacheln, keine Google-Analytics-Wüste.

### B5 · SEO- & KI-Sichtbarkeit — **P2 (dein Alleinstellungsmerkmal)**
- „Werde ich bei Google gefunden?" — Sichtbarkeit für die wichtigsten Suchbegriffe.
- **KI-Suche-Check**: Taucht die Firma bei ChatGPT & Co. auf? (GEO/LLMO — kann kaum wer.)
- Konkrete To-dos „So kommst du weiter nach oben" → Upsell zu SEO-Betreuung 490 €.

### B6 · Rechnungen & Zahlungen (Kundensicht) — **P1**
- **Rechnungen als PDF** herunterladen (die ZUGFeRD-Rechnungen aus A4).
- **Zahlungsmethode** hinterlegen/ändern (Mollie: SEPA-Lastschrift fürs Abo).
- **Laufende Kosten** transparent (Paket + Rundum-Schutz), nächste Abbuchung.
- **Upgrade** buchen (nur die Differenz zahlen) — direkt online.

### B7 · Wachsen / Upsells — **P2 (steht schon als Karten)**
- Weitere Seite (199 € inkl. Text), größeres Paket, SEO-Betreuung, KI-Chat-Assistent.
- Alles mit einem Klick anfragen/buchen → landet als Anfrage in deinem Admin.

### B8 · Support & Hilfe — **P1**
- **Nachrichten an Sartu** (schriftlich, ohne Termin) — als kleines Ticketsystem,
  Verlauf sichtbar. **(Grundgerüst steht)**
- **Kurzanleitungen / Videos** („Wie tausche ich ein Bild?").
- Statusmeldungen zum Projekt.

### B9 · Domain & E-Mail — **P3**
- Domain-Status (läuft bis …), Verknüpfung sehen.
- Einfache **E-Mail-Postfächer** verwalten (falls du das als Leistung anbietest).

### B10 · Benachrichtigungen — **P2**
- E-Mail/Portal-Hinweis bei: Rechnung fällig, Design zur Freigabe, Website wieder online,
  neuer Ratgeber-Tipp. Hält den Kunden ohne Aufwand „warm".

---

## Empfohlene Reihenfolge (damit es nicht ausufert)

**Stufe 1 — „Ich kann Geld verdienen & liefern":**
Anfragen/Projekte (A2/A3, steht großteils) → **Rechnungen + Mollie + E-Rechnung (A4/B6)**
→ Zugangsschutz härten (A8). Ohne Rechnung & Zahlung läuft nichts.

**Stufe 2 — „Der Kunde erlebt den Unterschied":**
Self-Service-Editor (B1) + Onboarding (B2) + echtes Gesundheits-Cockpit (B3).
Das ersetzt die „Änderungsminuten" und macht Rundum-Schutz fühlbar.

**Stufe 3 — „Ich werde unabhängig & skaliere":**
Eigene Website im Portal pflegen (A1) + Monitoring aller Seiten (A5) + Zahlen/MRR (A7)
+ SEO/KI-Sichtbarkeit (B5).

**Stufe 4 — Kür:**
Marketing/Newsletter (A6), Domain/E-Mail (B9), Benachrichtigungen (B10).

---

## Rechtliche / technische Stolpersteine (früh mitdenken)

- **E-Rechnung**: ZUGFeRD-PDF ist der pragmatische Weg (Empfangen ab 2025 Pflicht,
  Ausstellen 2027/28). Lieber jetzt sauber aufsetzen als später umbauen.
- **GoBD**: Rechnungen unveränderbar archivieren, fortlaufende Nummern, 10 Jahre aufbewahren.
- **Mollie**: übernimmt PCI/Kartendaten — du speicherst nie Kartennummern selbst. Für Abos
  brauchst du „recurring/first payment"-Setup + SEPA-Mandat.
- **DSGVO**: AVV mit Kunden (bietest du an), Statistik cookiefrei, Datensparsamkeit.
- **Backups**: extern & getestet — sonst ist „Rundum-Schutz" ein leeres Versprechen.
- **Sicherheit**: Portale server-seitig absichern (nicht nur JS), 2-Faktor für Admin.
