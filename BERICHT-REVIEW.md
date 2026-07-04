# Kritischer Website-Review — Sartu (Stand: Juli 2026)

Analysiert wurde der komplette Stand aus diesem Repo (alle PHP-Seiten, styles.css, Assets), lokal gerendert und auf Desktop (1440px) und Mobil (390px) geprüft. **Preisstruktur und Leistungsumfang wurden wie vereinbart nicht angetastet — es geht nur um Darstellung, Texte, Aufbau und Bilder.**

---

## 1. Gesamteindruck

Das Fundament ist stark: ruhige, konsequente Optik (Dunkelblau + Petrol), klare Typo-Hierarchie, durchgängige Sie-Ansprache, ehrliche Tonalität ohne Agentur-Buzzwords, saubere Antwort-zuerst-Absätze und konsistente Zahlen über fast alle Seiten. Das Niveau liegt deutlich über dem Branchendurchschnitt für diese Zielgruppe.

Die Probleme liegen in drei Bereichen:
1. **Ein echter CSS-Bug macht Überschriften auf vielen Seiten unsichtbar** (weiß auf weiß).
2. **Textleichen aus globalen Suchen-Ersetzen-Aktionen** („Texte und Inhalte", alte Paketnamen, „Care") — teils bis in die Hauptnavigation.
3. **Vertrauens-Lücke**: keine Menschen, keine Beispiele, Platzhalter im Footer auf jeder Seite.

---

## 2. Kritische visuelle Fehler (zuerst beheben)

### 2.1 Weiße Schrift auf weißen Karten (CSS-Bug, viele Seiten)

Im Abschnitt „Globaler Hell-Dunkel-Rhythmus" (`styles.css` ab Z. 3366) färben die Regeln für dunkle Sektionen alle `h3`/`p` in `.svc-prose` bzw. Karten-Kontexten weiß (Z. 3397–3416). Die weißen Karten darin (`.sales-card`, Extras-Karten, Entscheidungs-Karten) erben bzw. bekommen diese weiße Schrift — **die Überschriften sind unsichtbar**.

Nachgewiesen betroffene Stellen (per Browser-Rendering geprüft):

| Seite | Unsichtbare Elemente |
|---|---|
| alle 7 `leistung-*.php` | die 3 „Sales-Karten" nach dem Hero (z. B. „Der erste Eindruck sitzt", „Saubere SEO-Grundlage", „Jede Seite führt weiter") |
| `preise.php` | Extras-Titel: „Logo", „Online-Terminbuchung", „Ihre Website in mehreren Sprachen", „Express", „Newsletter-Anmeldung" + 2 Abschnitts-H3 — es stehen nur noch Preise ohne Bezeichnung auf den Karten |
| alle `branche-*.php` / `vergleich-*.php` | 6 Karten-H3 pro Seite (Highlights + „Weiterlesen"-Karten) |
| `ratgeber.php` | H3 der beiden Entscheidungshilfe-Karten |

**Fix:** In den weißen Karten die Textfarbe explizit setzen, z. B.:
```css
main > .svc-hero + .svc-section .sales-card h3,
main > .svc-hero + .svc-section .sales-card p { color: var(--text-dark); }
```
(analog für die anderen Kartentypen — oder sauberer: die Weiß-Regeln mit `:not()` von Karten-Innenleben ausnehmen).

### 2.2 Fehlende Bilder im Repo

Referenziert werden **27 Asset-Dateien, vorhanden sind 4** (nur `contact-workspace-v1*`). Es fehlen u. a.:
- `logo-wordmark-teal.svg` / `logo-wordmark-teal-light.svg` (**Logo in Header & Footer!**)
- `logo-teal.png` (Favicon)
- alle `hero-*-petrol.*` (Startseite, alle Leistungs-/Ratgeber-Heros)
- `strategy-workspace-v2`, `quality-check-v1`, `photo-placeholder-studio-workspace`

Falls die Dateien auf dem Live-Server liegen: ins Repo aufnehmen, sonst ist der Stand nicht reproduzierbar. Falls nicht: Die Seite läuft aktuell mit gebrochenen Bildern inkl. Logo.

---

## 3. Optik — was ich ändern würde

### 3.1 Seitenlängen reduzieren (größter Hebel)
- **Startseite mobil ≈ 14.600 px hoch** — das sind rund 17 Bildschirmhöhen Scrollweg. Desktop ≈ 8.700 px. Kandidaten zum Straffen: Die Abschnitte „Positionierung" (Erst Klarheit…), „Das eigentliche Problem" und „Methode" transportieren dreimal hintereinander dieselbe Botschaft („erst Struktur, dann Design") — einer davon kann weg oder zwei verschmelzen. Die FAQ (8 Items) könnte auf 5 gekürzt werden, der Rest steht im Ratgeber.
- **`ratgeber.php` ≈ 9.600 px**: 50 Lexikon-Karten voll ausgerendert auf einer Seite. Vorschlag: Karten einklappen (nur Begriff + Kategorie sichtbar, Text per Klick) oder das Lexikon auf eine eigene Unterseite ziehen. Die Filter-Chips existieren schon — sie lösen aber das Scroll-Problem nicht.
- **Preiskarten auf `preise.php`**: Start hat 13, Platzhirsch 17 Feature-Zeilen — mobil wird jede Karte zu einer eigenen Seite Scrollweg. Vorschlag: 5–6 Kernpunkte pro Karte, Rest hinter „Alle Details zum Paket" (Akkordeon oder Detailbereich weiter unten, den es ja schon gibt).

### 3.2 Hell/Dunkel-Kartenmix wirkt zufällig
In Kartenrastern ist je eine Karte dunkel (`is-blue`), die anderen weiß — auf `leistungen.php` sind es zwei dunkle in einem 6er-Raster, auf `ueber-uns.php` ist die erste Prinzipien-Karte dunkel, drei weiß. Es ist nicht erkennbar, *warum* eine Karte hervorgehoben ist (kein „Meistgewählt"-Badge o. ä.). Entweder konsequent als bewusstes Hervorhebungs-System nutzen (mit erkennbarem Grund) oder vereinheitlichen.

### 3.3 Menschen fehlen komplett
- `ueber-uns.php` heißt „**Menschen** statt Hotline — kontaktlos, aber persönlich" und zeigt: keinen einzigen Menschen. Das Platzhalterfoto („Sartu · digitaler Arbeitsplatz") steht sogar noch mit GO-LIVE-Kommentar im Code. Signiert ist mit „— Nils & das Sartu-Team". **Ein echtes Foto von dir ist hier der größte einzelne Vertrauens-Hebel der ganzen Website** — gerade beim kontaktlosen Modell wollen Leute sehen, wer dahintersteht.
- Alle anderen Bilder sind generische Workspace-Renderings (Laptop/Notizbuch/Smartphone). Einheitlich im Stil (gut), aber austauschbar. Mindestens auf Startseite-Hero und Über-uns würde ich Echtes zeigen.

### 3.4 Keine Beispiele / Referenzen
Die Seite `leistungen.php` wirbt selbstbewusst mit „Qualität ohne künstliche Referenzen" — trotzdem gibt es **nirgends ein visuelles Beispiel**, wie eine Sartu-Website aussieht. Die „Beispielhafte Seitenstruktur" (Demo-Browser auf der Startseite) ist ein guter Anfang, bleibt aber abstrakt. Vorschlag ohne Fake: 1–2 anonymisierte Muster-Mockups („So sieht ein Sartu-Onepager für einen Malerbetrieb aus") als Bild oder klickbarer Demo-Link. Bei einer Design-Dienstleistung ist „zeigen statt beschreiben" fast Pflicht.

### 3.5 Kleinere optische Punkte
- **Cookie-Banner** legt sich beim Einstieg über die Hero-Zone; auf `anfrage.php` verdeckt er genau das Assistenten-Widget (das eigentliche Conversion-Element). Kompakter machen (einzeilig) oder erst nach kurzem Scroll zeigen.
- **Alt-Texte als Platzhalter**: „Realistische Szene passend zum Seitenthema im Sartu-Stil" auf ~10 Seiten — das ist eine stehengebliebene Bild-Arbeitsanweisung, kein Alt-Text (und die Seite berät selbst zu BFSG/Barrierefreiheit). Sichtbar wird das auch bei jedem gebrochenen Bild.
- **Briefing-Widget**: Avatar zeigt ein „**L**" (Relikt des alten Assistenten-Namens „Lumi", die CSS-Klassen heißen noch `lumi-*`), Kopfzeile sagt doppelt „Website-Assistent / Ihr Website-Assistent". → Avatar auf „S" oder Icon, eine Zeile streichen.
- **Footer-Social-Icons** sind ausgegraut ohne Funktion — solange keine Profile existieren, lieber ganz weglassen als „tot" zeigen.

---

## 4. Texte — die wichtigsten Funde

### 4.1 Suchen-Ersetzen-Leichen (peinlichste Fehlerklasse, schnell behoben)
| Stelle | Problem |
|---|---|
| **Hauptnavigation** (site-partials.php:24) + leistungen.php:253 + JSON-LD | „Texterstellung & **Texte und Inhalte**" — „Content" wurde global durch „Texte und Inhalte" ersetzt |
| leistung-texte.php:13/17/42/218/348 | „Texte & **Texte und Inhalte** — kurz erklärt", „Webtexte und Texte und Inhalte-Erstellung" |
| **ratgeber-bfsg.php:220** | „WCAG (Web **Texte und Inhalte** Accessibility Guidelines)" — zerstörter Eigenname auf der Fachartikel-Seite |
| leistung-seo.php:325/330 | „(KI-Suche / KI-Suche)" und „KI-/Answer-Suche (KI-Suche)" — Relikt der GEO→KI-Suche-Umbenennung |
| leistung-wartung.php:340/354 | „Alle **Care**-Stufen", „**Care XL**" — altes Naming, heißt jetzt Rundum-Schutz / Schutz S/M/L |
| Alle Paket-Links | `anfrage.php?paket=basis/pro/platin/enterprise` vs. Namen Start/Wachstum/Platzhirsch/Sonderprojekte |

→ Empfehlung: einmal gezielt greppen nach `Texte und Inhalte`, `Care`, `platin`, `basis`, `(KI-Suche)`.

### 4.2 Sachliche Widersprüche
1. **Lieferzeit**: Startseite/Trust-Bar „7–14 Werktage", ablauf.php „in der Regel in 7 Werktagen", ueber-uns ebenso, Paketkarten je anders, Platzhirsch ganz ohne Angabe, JSON-LD `P7D` (= Kalendertage). → Eine Sprachregelung: „Start: 7 Werktage, größere Pakete 7–14 — nach vollständigen Inhalten."
2. **Korrekturrunden**: leistung-texte.php sagt oben „2 Korrekturschleifen" (Meta, Chip, Tabelle) und unten „Eine Korrekturrunde je Text" (Preiskarte, Scope-Box). Zudem uneinheitlich „Schleifen"/„Runden".
3. **„SEO ist keine Paket-Leistung"** (leistung-seo.php:343) widerspricht „Basis-SEO ist in jedem Sartu-Paket enthalten" (ebd.:255). Gemeint ist die laufende Betreuung — so auch formulieren.
4. **Geld-zurück auf kontakt.php:235**: „Überzeugt Sie die erste Vorschau nicht, erhalten Sie Ihr Geld zurück" — der Check ist aber kostenlos, und die Garantie gilt lt. allen anderen Seiten auf den ersten *Design-Entwurf* (Anzahlung). Falsches Versprechen, abmahn-/streitanfällig.
5. **ratgeber-website-kosten.php**: „Sartu **Pro**" statt „Wachstum" (Z. 220); „**140 €** pro zusätzlicher Korrekturrunde" (Z. 247) — diese Zahl existiert sonst nirgends (Kanon: 150 €/Std.); „Texte kosten bei Sartu 120 €/Seite" (Z. 207) klingt, als kosteten Texte grundsätzlich extra — sie sind im Paket drin.
6. **„minutengenau im 5-Minuten-Takt"** (leistung-webdesign.php:333, leistung-seo.php:374, ratgeber-website-kosten.php:244) — Widerspruch in sich. → „abgerechnet im 5-Minuten-Takt".
7. **ratgeber.php:342**: Überschrift „**20** Fragen aus der Praxis" — es sind 14.
8. **Wartung**: Karten versprechen „**Echtzeit**-Sicherheitsmonitoring", Meta/Tabelle sagen „Uptime-Monitoring im 5-Minuten-Takt". Ehrlichere Variante überall verwenden.

### 4.3 Du/Sie-Brüche (Sie-Site, 8 Fundstellen)
- kontakt.php:6 (Meta!): „**fordere** Ihren kostenlosen Website-Check an"
- leistungen.php:6 (Meta): „**Finde** jetzt das passende Paket"
- leistung-lokales-seo.php:14/18: „**Werde** in Ihrer Region gefunden"
- ratgeber-website-kosten.php:291: „Wissen, was **DEINE** Website kostet?" (direkt neben Sie-Text)
- ratgeber-bfsg.php:236: „Erstens **erreichst** Sie mehr Menschen"
- ratgeber-onepager.php:248: „oder **steigst** aufs größere Paket um"
- ratgeber.php:333: „**Versuch es** mit einem anderen Suchwort"
- datenschutz.php:92: „…Kontaktformular **kontaktierst**"

### 4.4 Grammatik/Rechtschreibung (Auswahl)
- datenschutz.php:114: „das Recht, **Sie** … zu beschweren" → „sich"
- preise.php:178: „zwischen 1.290 € und **ab** 9.990 €" → schief
- preise.php:361: „mehr können? **geschützter** Kundenbereich" → groß
- ablauf.php:278: „läuft sie **in Rundum-Schutz** weiter" → „im"
- preise.php:307 / ueber-uns.php:281: „**Website-Assistent sagt** Ihnen…" → Artikel fehlt („Der Website-Assistent…")
- leistung-lokales-seo.php:316: „gehört … **zum** SEO-Betreuung"
- ratgeber-onepager.php:259: „**Das** geführte 2-Minuten-Anfrage"
- leistung-logo.php:280: „Von **der** Logo Lite"
- leistung-webdesign.php:258: „responsive**s** Webdesign"
- Typografie: schließende Anführungszeichen teils falsch (`„Kontaktlos"` mit geradem `"`) — sitewide vereinheitlichen auf „…“

### 4.5 Konsistenz-Entscheidungen treffen (Wording-Glossar)
- **Onepager vs. One-Pager** — beides quer durch alle Seiten, teils in derselben Datei. Eine Schreibweise festlegen.
- **CTA-Wildwuchs**: dasselbe Ziel (anfrage.php) heißt „Projekt einschätzen lassen" (Header), „Website-Anfrage starten", „Angebot in 2 Min. starten", „Geführte Anfrage starten", „Los geht's". → Einen Primär-CTA festlegen und durchziehen; nur die Paketbuttons („Wachstum anfragen") bleiben spezifisch.
- **„Programm"** auf leistung-lokales-seo.php vs. „SEO-Betreuung" überall sonst.
- **SEO/GEO/KI-Suche**: „GEO" taucht als unerklärtes Label auf (priority-pages), Meta-Texte sagen „SEO/GEO", sichtbarer Text „KI-Suche". Sichtbar konsequent „SEO & KI-Suche"; GEO höchstens einmal erklärt (und als Lexikon-Begriff Nr. 51 aufnehmen).
- **FAQ-Schema ≠ sichtbare FAQ** an einigen Stellen (index.php, leistung-seo.php, leistung-lokales-seo.php) — für Rich Results angleichen.

---

## 5. Aufbau & Struktur

1. **Redesign fehlt in der Leistungs-Übersicht**: `leistung-redesign.php` existiert und ist in der Navigation, aber weder in den Service-Karten auf `leistungen.php` noch in deren JSON-LD-ItemList noch im Footer. → Karte ergänzen.
2. **Zwei konkurrierende Einstiege**: „Kostenloser Website-Check" (kontakt.php) vs. „geführte Anfrage" (überall). Nirgends steht, was wofür ist. → Ein Satz Abgrenzung auf kontakt.php („Check = bestehende Website, Anfrage = neues Projekt") oder Check klar unterordnen.
3. **Kontaktformular ist ein mailto-Formular** („Mit dem Absenden öffnet sich Ihre E-Mail-App"). Auf Desktop ohne konfiguriertes Mailprogramm ist das eine Sackgasse; mobil ok. Da es API/DB fürs Briefing schon gibt, wäre ein echter Versand konsequenter — mindestens aber die E-Mail-Adresse als Fallback direkt daneben groß anzeigen (steht aktuell nur in der Seitenleiste).
4. **Startseite: Abschnitte 2–4 straffen** (siehe 3.1) — Positionierung, Problem und Methode erzählen dreimal „erst Klarheit, dann Design".
5. **Branchen-/Vergleichsseiten**: solide Template-Qualität, aber die Card-Texte sind zwischen den Branchen austauschbar; die Tabellen (z. B. Handwerker-Kundenfragen) sind das Beste daran. Pro Seite ein konkretes Branchen-Szenario mit echten Begriffen/Zahlen ergänzen. Außerdem lecken interne Roadmap-Notizen in den öffentlichen Text („Regionale Stadtseiten kommen später").
6. **BFSG-Kurzfassung** (ratgeber-bfsg.php:180): Ausnahme verkürzt auf „< 10 Mitarbeiter und ≤ 2 Mio. € Umsatz" — es fehlt „oder Bilanzsumme" (im Artikel selbst korrekt).

---

## 6. Go-Live-Blocker (Platzhalter & Technik)

- `[DOMAIN]` und `[OG-IMAGE]` in canonical/og/JSON-LD **auf allen Seiten**
- `noindex,nofollow` auf allen Seiten (bewusst, aber nicht vergessen)
- Footer auf jeder Seite: `[FIRMENNAME / INHABER]`, `[TELEFON]`, `[E-MAIL]`, `© [JAHR]` → `[JAHR]` durch `<?= date('Y') ?>` ersetzen
- Impressum/AGB/Datenschutz: `[USt-IdNr.]`, `[X] Tagen` Zahlungsziel, `[Widerrufsbelehrung]`, `[Monat/Jahr]` u. a.
- datenschutz.php:95 nennt „**TTDSG**" — heißt seit Mai 2024 **TDDDG**
- „Stand: Juni 2026" + `dateModified` hart codiert in allen Ratgebern → zentralisieren (eine Konstante); BAFA-Angabe „bis 31.12.2026" mit Wiedervorlage versehen
- Empfehlung: vor Livegang einmal `grep -rE '\[([A-ZÄÖÜ/ -]+)\]' *.php includes/` als Schlusscheck

---

## 7. Priorisierte Reihenfolge

1. **CSS-Bug weiße Überschriften** (betrifft Preise-Extras & alle Leistungsseiten — Conversion-relevant)
2. **Suchen-Ersetzen-Leichen** inkl. Hauptnavigation („Texte und Inhalte"), WCAG, Care, paket=basis/pro/platin
3. **Widersprüche bei Versprechen**: Lieferzeit-Sprachregelung, Geld-zurück auf kontakt.php, Korrekturrunden, „Sartu Pro"/140 €
4. **Du/Sie-Brüche + Grammatikfehler** (Liste oben, ~20 Stellen)
5. **Bilder**: fehlende Assets klären, echtes Foto auf Über-uns, Alt-Texte schreiben
6. **Straffen**: Startseite (v. a. mobil), Preiskarten-Listen, Ratgeber-Lexikon einklappen
7. **CTA vereinheitlichen** + Redesign-Karte auf leistungen.php ergänzen
8. **Go-Live-Checkliste** aus Abschnitt 6 in GO-LIVE-TODO.md übernehmen
