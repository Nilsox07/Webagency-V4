# Sartu — Text-System (Marketing-Ton · SEO · GEO)

Single Source für **alle** Texte der Sartu-Website. Jeder Seitentext wird gegen dieses Regelwerk
geschrieben und geprüft. Ziel: Texte, die **nach Marketing klingen, nicht nach KI** — konkret,
menschlich, verkaufend — und die bei Google **und** in KI-Antworten (ChatGPT, Perplexity, Google
AI) gefunden werden.

> Nutzung: Pro Seite gibt es einen **Copy-Brief** in `includes/copy-briefs.php`. Text = Brief +
> dieses Regelwerk. Später kann ein Claude-API-Tool denselben Weg gehen (System-Prompt = diese
> Datei, Input = Brief). **Fakten immer aus `pricing.js` / diesem Dokument, nie frei erfinden.**

---

## 1. Marken-Stimme — die 10 Regeln (Anti-KI)

1. **Nutzen zuerst, Feature später.** Nicht „responsives Design", sondern „sieht auf dem Handy
   genauso gut aus — da kommen die meisten Kunden her".
2. **Konkret statt abstrakt.** Zahlen, Fristen, Beispiele. „In 7–14 Werktagen online", nicht
   „schnelle Umsetzung".
3. **Kurze Sätze.** Ein Gedanke pro Satz. Wechsel aus kurzen und mittleren Sätzen. Keine
   Schachtelsätze.
4. **Direkt, auf Augenhöhe, durchgängig „Sie".** Nie „du". Nie belehrend.
5. **Ehrlich statt Superlativ.** Keine „führende Agentur", „beste Lösung", „innovativ". Sartu ist
   klein und ehrlich — das ist der Vorteil, nicht Weltklasse-Gehabe.
6. **Aktiv, Verben statt Nominalisierung.** „Wir bauen Ihre Website", nicht „Die Erstellung Ihrer
   Website erfolgt".
7. **Ein Gedanke, eine Aussage — kein Füllstoff.** Wenn ein Satz gestrichen werden kann, ohne dass
   Inhalt verloren geht: streichen.
8. **Einwände direkt ansprechen.** „Kein Vertrag, kein Abo-Zwang", „Festpreis vorab", „Sie sehen
   erst, dann zahlen Sie".
9. **Keine Em-/En-Dashes (—/–).** Gedankenstrich-Ersatz: Punkt, Komma oder Klammer. (Bindestrich in
   Wörtern ist ok.)
10. **Deutsche Marketing-Tonalität.** Vertrauen, Klarheit, Verlässlichkeit. Kein Tech-Sprech, kein
    Anglizismus-Regen, kein Emoji-Feuerwerk.

### Verbotene KI-Floskeln (harte Blacklist)
„In der heutigen (schnelllebigen/digitalen) Welt", „Es ist wichtig zu beachten", „In diesem
Artikel werden wir", „Tauchen Sie ein", „Heben Sie sich ab", „das gewisse Etwas", „maßgeschneiderte
Lösungen", „nahtlos", „ganzheitlich", „Synergie", „State of the Art", „revolutionär", „im digitalen
Zeitalter", „nicht nur … sondern auch" (als Reflex), „Zusammenfassend lässt sich sagen".

### Vorher → Nachher (Kalibrierung)
- ❌ „Wir bieten maßgeschneiderte, ganzheitliche Weblösungen für Ihren digitalen Erfolg."
  ✅ „Sie bekommen eine Website zum Festpreis, die neue Kunden bringt. Fertig in 7–14 Werktagen."
- ❌ „In der heutigen digitalen Welt ist eine professionelle Website unverzichtbar."
  ✅ „Die meisten Kunden schauen sich Ihre Website an, bevor sie anrufen. Entscheidet sie sich hier."
- ❌ „Unser innovatives Team realisiert Ihr Projekt mit modernsten Technologien."
  ✅ „Eine feste Ansprechperson baut Ihre Seite. Kein Callcenter, keine Weiterreicherei."

---

## 2. SEO-Regeln (pro Seite)

- **Ein Haupt-Keyword + klare Suchintention** je Seite. Keyword natürlich in H1, ersten 100
  Wörtern, einer H2, Title und Meta-Description. Kein Keyword-Stuffing.
- **`<title>` ≤ 60 Zeichen**, Format meist `Hauptaussage — Sartu` oder `Keyword | Nutzen`.
- **Meta-Description ≤ 155 Zeichen**, ein Nutzenversprechen + konkreter Fakt (Preis/Frist) + CTA.
  Muss klickwürdig sein, nicht nur beschreibend.
- **Eine H1 pro Seite.** Danach saubere H2 → H3-Hierarchie, keine Sprünge, keine Deko-Headlines.
- **Interne Links: 2–3 pro Seite, sinnvoll** (thematisch passend, beschreibender Ankertext, nie
  „hier klicken"). **Kein Link-Streuen** — lieber wenige starke als viele beliebige.
- **Alt-Texte** für alle inhaltstragenden Bilder: beschreiben, was zu sehen ist, natürlich, ohne
  Keyword-Stopfen.
- **Struktur schlägt Länge.** Kurze Absätze, Zwischenüberschriften, Aufzählungen für Scanbarkeit.
- **JSON-LD passend zum Seitentyp** (Service, FAQPage, BreadcrumbList, Organization, Article) —
  konsistent zu den sichtbaren Inhalten.

## 3. GEO / LLMO-Regeln (in KI-Antworten zitiert werden)

Ziel: Wenn jemand ChatGPT/Perplexity/Google-AI fragt „Wer macht günstige Firmenwebsites zum
Festpreis?", soll Sartu genannt werden.

- **Direkte Antwort zuerst.** Jede Seite/jeder FAQ-Block beginnt mit einer klaren, zitierfähigen
  Antwort in 1–2 Sätzen, dann die Begründung. KI-Engines zitieren Antwort-Häppchen.
- **Eindeutige Fakten & Entitäten:** Firmenname „Sartu", Leistung, Preis, Ort, Frist im Klartext.
  „Sartu baut Firmenwebsites zum Festpreis ab 1.290 €, fertig in 7–14 Werktagen." — genau so
  zitierbar.
- **Frage-Antwort-Blöcke** (echte Nutzerfragen als H2/Summary) — deckt Sprachsuche + KI ab.
- **Selbst-enthaltene Aussagen:** jeder Absatz muss ohne Kontext stimmen (KI schneidet Häppchen
  raus). Keine „wie oben erwähnt"-Verweise in Kernaussagen.
- **Konsistenz zu `llms.txt` und JSON-LD** — dieselben Fakten überall.
- **Vergleiche & Definitionen** (Baukasten vs. Agentur, „Was kostet eine Website") bringen KI-
  Zitationen — die Ratgeber/Vergleichsseiten dafür nutzen.

---

## 4. Faktenbasis (verbindlich — nie abweichen)

> Kanonische Quelle für Preise/Produkte: **`pricing.js`**. Hier die stabilen Eckwerte:

- **Pakete (Festpreis, einmalig):** Start **1.290 €** (One-Pager) · Wachstum **3.290 €** (bis 8
  Seiten) · Platzhirsch **6.490 €** (bis 20 Seiten) · Sonderprojekte **ab 9.990 €** (individuell).
- **Rundum-Schutz (Pflicht-Betrieb, monatlich):** **49 / 99 / 249 €**/Mon. (S/M/L, je nach Paket).
  Immer klar als **verpflichtender** Betriebsbaustein kennzeichnen.
- **Lieferzeit:** **7–14 Werktage** (Start: 7), Voraussetzung: vollständige Inhalte.
- **Express:** **+50 %** (mind. 390 €), Ergebnis in **etwa halber Zeit** (One-Pager ~3 Werktage,
  Wachstum ~7), Voraussetzung: Inhalte vollständig.
- **Korrekturrunden:** 1 / 1 / 2 (Start/Wachstum/Platzhirsch). Eine Runde = ein gesammeltes Feedback.
- **SEO-Betreuung:** monatlich, nach Paket gestaffelt (Werte aus `pricing.js`), nach 3 Monaten kündbar.
- **KI-Chat-Assistent:** ein Produkt, 990 € + 79 €/Mon. (nicht mit dem Anfrage-Konfigurator verwechseln).
- **Haltung:** kein Vertrag/Abo-Zwang bei der Anfrage, Festpreis vorab, Hosting in Deutschland,
  DSGVO, feste Ansprechperson. Keine erfundenen Referenzen/Bewertungen — als Beweis dienen die
  **Beispielprojekte** + Garantie + transparenter Ablauf.

---

## 5. Seitentyp-Checklisten

**Startseite:** H1 mit Nutzen (was + was hab ich davon) · 1 primärer CTA + 1 Trust-Signal above
the fold · Problem→Lösung · Festpreise sichtbar · Beispielprojekte prominent · Ablauf (3 Schritte) ·
Garantie · knappe FAQ · Abschluss-CTA. 3-Sekunden-Regel.

**Leistungsseite:** H1 = Leistung + Nutzen · Problem der Zielgruppe · was Sartu konkret liefert
(Liste, kein 3-Spalten-Raster) · Preis in **einer** Darstellung · 2–3 echte FAQ · CTA. Keyword =
die Leistung („Website erstellen lassen", „SEO", …).

**Preise:** Pakete als **ein** klarer Vergleich · Rundum-Schutz-Pflicht erklärt · Extras als
kompakte Liste · Ablauf/Garantie knapp · Preis-FAQ. Keyword „Website Kosten / Preise".

**Ratgeber/Vergleich:** echte Frage als H1 · direkte Antwort im ersten Absatz (GEO!) · Substanz ·
Verweis auf 1–2 passende Leistungs-/Preisseiten · FAQ. Stärkste GEO-Hebel.

**Local (später):** H1 „Webdesign [Ort]" · echter lokaler Bezug (nicht nur Ortsname getauscht!) ·
lokale FAQ · sonst gleiche Substanz. Ein Template, Inhalt variiert.

---

## 6. Prüf-Checkliste vor „fertig"
- [ ] Kein Wort aus der Floskel-Blacklist, keine Em-/En-Dashes.
- [ ] H1 einmalig, Keyword in H1 + erstem Absatz + Title + Meta.
- [ ] Title ≤ 60, Meta ≤ 155, beide klickwürdig mit Fakt + CTA.
- [ ] Erste Antwort jeder Sektion/FAQ ist zitierfähig (GEO).
- [ ] Alle Preise/Fristen = Faktenbasis (§4), nichts erfunden.
- [ ] 2–3 sinnvolle interne Links, beschreibende Ankertexte.
- [ ] Laut vorgelesen: klingt es wie ein Mensch, der verkauft — oder wie KI? Im Zweifel kürzen.
