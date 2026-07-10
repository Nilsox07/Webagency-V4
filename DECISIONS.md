# Sartu — Entscheidungen & Begründungen

Das **Warum** hinter den Festlegungen (das **Was/Wo** steht in `CLAUDE.md`).
Zweck: nicht neu ausdiskutieren. Bei neuen Entscheidungen ergänzen (kurz halten).

## Produkt & Preise
- **Pakete 1.290 / 3.290 / 6.490 €, Rundum-Schutz 49/99/249 €/Mon.** — feste Portfolio-
  Entscheidung („Fähigkeits-Leiter"). Nicht neu herleiten.
- **Korrekturrunden 1 / 1 / 2** (vorher 2/3/4 → 1/2/3 → 1/1/2). Warum so wenig: der
  Self-Service-Editor fängt den operativen Kleinkram (Öffnungszeiten etc.), ein gutes
  Briefing macht den ersten Wurf schon zu ~90 % richtig. Der Deckel ist **stiller
  Vertragsschutz** gegen den einen Endlos-Kunden, kein Verkaufsargument. „Eine Runde =
  ein gesammeltes Feedback", damit „1" souverän statt geizig wirkt.
- **SEO-Betreuung: ein flacher Preis 490 €/Monat** (keine 3 Stufen). Alle Kundenseiten
  sagten längst 490; die 3 Stufen (149/390/790) in `pricing.js` waren Altbestand vor dem
  Umbau. Ein Preis = einfacher zu verkaufen.
- **KI-Chat-Assistent: EIN Produkt (990 € + 79 €/Mon.), den 490er-Chatbot entfernt.**
  Marktrecherche: 79 €/Mon. ist ggü. deutschen Full-Service-Anbietern (moinAI 475–790,
  Userlike 200+) wettbewerbsfähig. Problem war nicht der Preis, sondern die 490/990-
  **Doppelstruktur** (Kannibalisierung; der billige FAQ-Bot konkurriert mit Gratis-Tools
  und entwertet das Hauptprodukt).
- **Kein Raten-/Finanzierungsmodell.** Rechnung zeigte: „0 € + 129 €/Mon." wäre *billiger*
  als Vorkasse (als Finanzierung sinnlos); fair kalkuliert ~160 €/Mon., das sprengt das
  Zahlungsfenster der Zielgruppe (Handwerker/KMU: 35–150 €/Mon.).

## Aktionen / Rabatte
- **Diskret statt überall.** Premium-B2B zeigt Rabatte nicht plakativ öffentlich (erodiert
  Referenzpreis, senkt Zahlungsbereitschaft). Deshalb: Rabatt v. a. im **Angebot/PDF**
  (durchgestrichener Regulärpreis + Aktionspreis + „gültig bis"), dazu eine **dezente,
  schließbare Ankündigungs-Leiste** und das Banner auf der Preisseite. **Nicht** im
  Startseiten-Hero, nicht auf jeder Seite, kein Dauer-Countdown, kein „Sale/>30 %"-Wording.
- **Framing:** „Aktion / limitierte Plätze mit Deadline", nicht „Rabattschlacht".

## Kundenseiten-Workflow (Kern der Agentur)
- **Jede Kundenseite individuell mit KI gebaut — KEIN Vorlagen-Baukasten.** Eigene
  Positionierung ist „kein Baukasten, individuell". Ein Ein-Vorlagen-Klonsystem ließe alle
  Seiten gleich aussehen → Problem bei Referenzen/Weiterempfehlung.
- **Editierbare Felder schreibt die KI, nicht Nils.** Löst den Konflikt „bespoke Seite vs.
  No-Code-Selbstbedienung": die KI markiert Stellen mit `sc_text/sc_bild/sc_farbe`
  (Nils gibt nur einen Standard-Auftrag, fasst nie Code an). Der Kunde ändert **nur** diese
  Stellen (Öffnungszeiten, Kontakt, Haupttexte/-bilder, Farben); Layout/Struktur/Recht
  bleiben gesperrt. Prinzip: „ändern/deaktivieren ja, dazubauen/löschen nein".
- **Kein Pixel-Notiz-Feedback-Tool.** Würde genau das Mikro-Genörgel einladen, das Nils
  vermeiden will (Design-by-Kunde = Problem billiger Agenturen). Korrektur läuft über die
  begrenzten Runden + optional ein **Notizfeld pro Seite**, bewusst auf Inhalt/Fakten gelenkt.
- **KI-Bau-Prompt: modular & nur Bestelltes.** Aus Briefing-Bausteinen zusammengesetzt
  (Checkboxen), mit Ausschluss-Klausel (kein Shop/Login/laufende SEO/Extras, wenn nicht
  bestellt) — der Kunde bekommt genau das Bezahlte.
- **Herstellervermerk „Webdesign von Sartu"** (dofollow-Footer-Link, fest, nicht editierbar,
  AGB §9). Wie bei Agenturen → Backlinks. **Marken-Anker** (nicht Keyword-Anker), sonst
  wertet Google identische Footer-Links als Spam ab. Entfernung gegen Entgelt möglich.

## Design
- **Sartu-Teal `#12A594`** ist die echte Marke — nicht das Lime, das früher fürs Banner
  improvisiert war (überall auf Teal zurückgezogen).
- **Schrift Sora (Headlines) + Manrope (UI), nicht Inter.** Inter + Voll-Pillen lesen sich
  als „KI-Standard". SaaS-Look = charaktervolle Typo + ruhiger Akzent + Unterstrich-Tabs.
- **Hell/Dunkel, Standard = Gerät** (`prefers-color-scheme`), Umschalter setzt `data-theme`.

## Technik & Deploy
- **Plain PHP + MySQL, kein Framework/Node/Composer.** Läuft auf billigem Shared-Hosting
  per FTP, kein Build-Schritt, Nils behält Kontrolle.
- **Passwortlos (Magic-Link + Code).** Kein Passwort-Speichern/-Zurücksetzen; sicher + simpel.
- **Beträge in Cent (Ganzzahl).** Keine Fließkomma-Rundungsfehler in der Abrechnung.
- **E-Rechnung XRechnung + ZUGFeRD/Factur-X.** Deutsche B2B-Pflicht; hybrides PDF
  (menschenlesbar + maschinen-XML eingebettet).
- **Mollie hinter Schalter** (`mollie_enabled()`): fertig gebaut, aber Bezahlfunktion
  ausgeblendet bis API-Key gesetzt — Livegang = nur Key eintragen.
- **FTP-first-Deploy mit Web-Installer.** Nils hat nur FTP + zugeschickten MySQL-Zugang.
  `install.php` schreibt `config.local.php` per Formular, spielt das Schema ein, legt den
  ersten Admin an — kein phpMyAdmin, kein Datei-Bearbeiten. `install.php` +
  `check-umgebung.php` nach Setup löschen.
- **DB zuletzt.** Alles DB-Runtime-Abhängige (Runtime `sartu-edit.php`, echte Statistik)
  wird gebaut/getestet, wenn das Hosting steht — vorher nicht end-to-end prüfbar.
- **Kein Graphify/Obsidian/RAG.** Für dieses Repo marginal + Abhängigkeiten/Datenschutz-
  Risiko. Token-Hebel sind stattdessen `CLAUDE.md` + frische Sessions je Aufgabe.
