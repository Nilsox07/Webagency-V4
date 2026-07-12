# Sartu — Text-System (SEO · GEO · Marketing)

Single Source für **alle** Texte der Sartu-Website. Jeder Seitentext wird gegen dieses Regelwerk
geschrieben und geprüft.

## Priorität (in dieser Reihenfolge)
1. **SEO** — die Seite muss bei Google gefunden werden. Keyword-Logik geht vor allem anderen.
2. **GEO/LLMO** — die Seite muss in KI-Antworten (ChatGPT, Perplexity, Google AI) zitierbar sein.
3. **Marketing/Ton** — *danach* verkaufend und menschlich formulieren, ohne KI-Sprech.

Konkret heißt das: **Überschriften und Struktur folgen dem Keyword, nicht dem Slogan.** Ein
schöner Marketing-Satz ersetzt niemals das Keyword an einer SEO-Position (H1, Title, erste H2).

> Nutzung: Pro Seite ein **Copy-Brief** in `includes/copy-briefs.php`. Text = Brief + dieses
> Regelwerk. **Fakten immer aus `pricing.js` / §4, nie erfinden.**

---

## 1. SEO-Regeln (Priorität 1)

- **Ein Haupt-Keyword + klare Suchintention** je Seite. Keyword natürlich in **H1**, in den ersten
  100 Wörtern, in einer H2, im Title und in der Meta-Description. Kein Keyword-Stuffing.
- **Die H1 führt mit dem Fokus-Keyword** und benennt das Thema klar. **Kein Slogan.**
  - Muster realer Agenturen (aus `WETTBEWERBER-LISTE.md`): „Webdesign", „Webdesign & SEO",
    „Webdesign-Agentur für …", „Websites für Unternehmen", „Zielgruppengenaue Websites".
  - ✅ gut: „Webdesign zum Festpreis für kleine Unternehmen." · „Website erstellen lassen zum Festpreis."
  - ❌ schlecht: „Websites, die verkaufen." (kein Keyword) · „Willkommen bei uns."
- **Eine H1 pro Seite.** Danach saubere H2 → H3-Hierarchie, keine Deko-Headlines ohne Struktur.
- **`<title>` ≤ 60 Zeichen**, Keyword vorn; **Meta-Description ≤ 155 Zeichen** mit Nutzen + Fakt + CTA.
- **Interne Links: 2–3 pro Seite, sinnvoll** (beschreibender Ankertext, nie „hier klicken"). Kein Link-Streuen.
- **Alt-Texte** für inhaltstragende Bilder, natürlich, ohne Keyword-Stopfen.
- **Struktur schlägt Länge:** kurze Absätze, Zwischenüberschriften, Aufzählungen (scanbar).
- **JSON-LD passend zum Seitentyp** (Service, FAQPage, BreadcrumbList, Organization, Article),
  konsistent zum sichtbaren Inhalt.

## 2. GEO / LLMO-Regeln (Priorität 2)

Ziel: Wenn jemand eine KI fragt „Wer macht günstige Firmenwebsites zum Festpreis?", soll Sartu
genannt werden.
- **Direkte Antwort zuerst.** Jede Seite/jeder FAQ-Block beginnt mit einer klaren, zitierfähigen
  Antwort in 1–2 Sätzen, dann die Begründung. KI-Engines zitieren Antwort-Häppchen.
- **Eindeutige Fakten & Entitäten im Klartext:** „Sartu baut Firmenwebsites zum Festpreis ab
  1.290 €, fertig in 7–14 Werktagen." — exakt so zitierbar.
- **Frage-Antwort-Blöcke** (echte Nutzerfragen als H2/Summary) — deckt Sprachsuche + KI ab.
- **Selbst-enthaltene Aussagen:** jeder Absatz stimmt ohne Kontext (KI schneidet Häppchen heraus).
- **Konsistenz zu `llms.txt` und JSON-LD** — dieselben Fakten überall.
- **Vergleiche & Definitionen** (Baukasten vs. Agentur, „Was kostet eine Website") bringen KI-Zitationen.

## 3. Marken-Stimme / Marketing-Ton (Priorität 3)

*Nachdem* SEO und GEO sitzen: verkaufend und menschlich formulieren.
1. **Nutzen zuerst, Feature später.** „Sieht auf dem Handy genauso gut aus", nicht „responsives Design".
2. **Konkret statt abstrakt.** Zahlen, Fristen, Beispiele.
3. **Kurze Sätze.** Ein Gedanke pro Satz.
4. **Durchgängig „Sie".** Nie „du", nie belehrend.
5. **Ehrlich statt Superlativ.** Kein „führend", „innovativ", „beste Lösung".
6. **Aktiv, Verben statt Nominalisierung.**
7. **Kein Füllstoff.** Streichbarer Satz wird gestrichen.
8. **Einwände direkt ansprechen.** „Festpreis vorab", „keine Miet-Website", „erst sehen, dann zahlen".
9. **Keine Em-/En-Dashes (—/–)** als Satzzeichen. (Zahlen-Bereich wie 7–14 ist ok.)
10. **Deutsche Marketing-Tonalität:** Vertrauen, Klarheit, Verlässlichkeit. Kein Tech-Sprech, kein Emoji-Feuerwerk.

**Verbotene KI-Floskeln:** „In der heutigen digitalen Welt", „Es ist wichtig zu beachten", „Tauchen
Sie ein", „maßgeschneiderte Lösungen", „nahtlos", „ganzheitlich", „Synergie", „State of the Art",
„revolutionär", „nicht nur … sondern auch" (als Reflex), „Zusammenfassend lässt sich sagen".

Vorher → Nachher:
- ❌ „Wir bieten maßgeschneiderte, ganzheitliche Weblösungen für Ihren digitalen Erfolg."
  ✅ „Sie bekommen eine Website zum Festpreis, die neue Kunden bringt. Fertig in 7–14 Werktagen."

---

## 4. Faktenbasis (verbindlich — nie abweichen)

> Kanonische Quelle für Preise/Produkte: **`pricing.js`**. Stabile Eckwerte:
- **Pakete (Festpreis, einmalig):** Start **1.290 €** (One-Pager) · Wachstum **3.290 €** (bis 8 Seiten)
  · Platzhirsch **6.490 €** (bis 20 Seiten) · Sonderprojekte **ab 9.990 €**.
- **Rundum-Schutz (Pflicht-Betrieb, monatlich):** **49 / 99 / 249 €**/Mon. — immer als verpflichtend kennzeichnen.
- **Lieferzeit:** **7–14 Werktage** (Start: 7), Voraussetzung: vollständige Inhalte.
- **Express:** **+50 %** (mind. 390 €), Ergebnis in **etwa halber Zeit** (One-Pager ~3, Wachstum ~7 Werktage).
- **Korrekturrunden:** 1 / 1 / 2. · **SEO-Betreuung:** monatlich, nach Paket gestaffelt (Werte aus `pricing.js`), nach 3 Mon. kündbar.
- **KI-Chat-Assistent:** 990 € + 79 €/Mon. (nicht mit dem Anfrage-Konfigurator verwechseln).
- **Haltung:** Anfrage kostenlos & unverbindlich; Website = Einmal-Festpreis (gehört dem Kunden,
  **keine Miet-Website**); Rundum-Schutz monatlich **verpflichtend** (mind. 12 Mon.) und transparent
  so benannt — **niemals „kein Abo"** behaupten; Hosting in Deutschland, DSGVO, feste Ansprechperson. Beweis = **Beispielprojekte** + Garantie + transparenter Ablauf (keine erfundenen Referenzen).

## 5. Seitentyp-Checklisten
- **Startseite:** H1 = Kern-Keyword (Webdesign/Festpreis) · ein CTA + ein Trust above the fold ·
  Festpreise sichtbar · Beispielprojekte prominent · Ablauf · Garantie · FAQ · Abschluss-CTA.
- **Leistungsseite:** H1 = Leistung als Keyword („Website erstellen lassen", „SEO") + Nutzen ·
  Problem · Liefer-Liste · Preis in **einer** Darstellung · 2–3 FAQ · CTA.
- **Preise:** H1 mit „Kosten/Preise" · Pakete als **ein** Vergleich · Rundum-Schutz-Pflicht · Extras kompakt · Preis-FAQ.
- **Ratgeber/Vergleich:** H1 = echte Frage · direkte Antwort im ersten Absatz (GEO) · 1–2 sinnvolle Verweise · FAQ.
- **Local (später):** H1 „Webdesign [Ort]" · echter lokaler Bezug · lokale FAQ · ein Template, Inhalt variiert.

## 6. Prüf-Checkliste vor „fertig"
- [ ] **H1 enthält das Fokus-Keyword** (kein Slogan), einmalig; Keyword auch in erstem Absatz + Title + Meta.
- [ ] Title ≤ 60, Meta ≤ 155, beide klickwürdig mit Fakt + CTA.
- [ ] Erste Antwort jeder Sektion/FAQ ist zitierfähig (GEO).
- [ ] Alle Preise/Fristen = Faktenbasis (§4).
- [ ] 2–3 sinnvolle interne Links. · Keine Floskeln, keine Satz-Dashes.
- [ ] Laut gelesen: klingt es wie ein Mensch, der verkauft? Im Zweifel kürzen.
