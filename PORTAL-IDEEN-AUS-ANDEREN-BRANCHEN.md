# Portal-Ideen aus anderen Branchen — übertragen auf Sartu

> Durchgang durch ~15 Portal-Welten (SaaS, Buchhaltung, Hosting, Baukästen, Agentur-CRM,
> Support, Banking, Telekom, Versicherung, Shop). Pro Muster: **was die haben → wie es für
> Sartu Sinn ergibt.** Am Ende eine kuratierte „Top-Neu"-Liste und die ehrliche Warnung,
> was man *nicht* bauen sollte.
>
> Kennzeichnung: 💎 = starker, seltener Vorteil · ⚙️ = solides Standard-Feature ·
> ⚠️ = Mercedes-Falle (klingt gut, macht komplex — eher weglassen)

---

## 1. SaaS-Dashboards (Stripe, Notion, Slack-Admin, Linear)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **„Als Nutzer anmelden" / Impersonate** – Support loggt sich als Kunde ein, um zu helfen | 💎 **Admin: „In dieses Kundenkonto wechseln".** Du siehst das Portal genau wie dein Kunde, kannst für ihn eine Änderung machen oder ein Problem nachstellen. Riesige Zeitersparnis im Support. |
| **Onboarding-Checkliste mit Fortschritt** („3 von 5 Schritten erledigt") | 💎 **Kunde: Einrichtungs-Fortschritt** („Logo hochladen ✓, Texte liefern ✓, Farbe wählen ○"). Nimmt Unsicherheit, treibt Onboarding voran. |
| **Command-Palette / globale Suche** (Strg+K) | ⚙️ **Admin: Schnellsuche** über Kunden, Projekte, Rechnungen. Bei 100 Kunden Gold wert. |
| **Aktivitäts-Feed / Audit-Log** („X hat Y geändert") | ⚙️ **Admin: Verlauf** — wer hat wann was geändert. Wichtig bei Streit („ich hab das nie geändert"). |
| **„Was ist neu"-Changelog im Portal** | ⚙️ **Beide: „Neu im Portal".** Zeigt Kunden, dass sich was tut → Bindung. |
| **Leere Zustände, die erklären** (statt leerer Tabelle ein „So legst du los") | ⚙️ Überall. Kleiner Aufwand, großer Unterschied im Gefühl. |
| **Produkt-Tour beim ersten Login** | ⚙️ Kurze geführte Tour durchs Kundenportal. |

## 2. Buchhaltung / Rechnung (lexoffice, sevDesk, Stripe Billing, Chargebee)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **Automatisches Mahnwesen** (Stufe 1/2/3, Zahlungserinnerung automatisch) | 💎 **Admin: Mahn-Automatik.** Offene Rechnung → nach X Tagen freundliche Erinnerung von selbst. Du jagst niemandem hinterher. |
| **Fehlversuch-Wiederholung** (failed SEPA → automatisch erneut) | 💎 Bei geplatzter Lastschrift automatisch neuer Versuch + Info. Rettet Abo-Umsatz (Rundum-Schutz). |
| **Gutschriften / Storno** | ⚙️ Rechnung korrigieren rechtssicher (nicht löschen, sondern Gutschrift). |
| **Kleinunternehmer §19 / USt korrekt** | ⚙️ **Admin: Steuer-Einstellung.** Als Gründer evtl. Kleinunternehmer → keine USt ausweisen. Muss auf Rechnung stimmen. |
| **Gutscheine / Aktionscodes** | ⚙️ „10 % Startrabatt", „Empfehlungscode". Nur wenn du wirklich Aktionen fährst. |
| **Ratenzahlung** | ⚠️ Für 6.490 € evtl. nett, aber Verwaltungsaufwand. Später. |
| **Bank-Abgleich** (Zahlung landet auf Konto → Rechnung automatisch „bezahlt") | ⚙️ Über Mollie kommt der Status ohnehin automatisch. |
| **DATEV-Export für Steuerberater** | ⚙️ Ein Klick, Steuerberater glücklich, du sparst Gebühren. |

## 3. Hosting-Panels (cPanel, Plesk, Netlify, Vercel)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **Öffentliche Status-Seite** (status.xyz.com „alle Systeme grün") | 💎 **status.sartu.de** — zeigt Uptime aller Systeme. Schafft Vertrauen, entlastet Support bei Störungen („ist bekannt, wir arbeiten dran"). |
| **Staging / Vorschau-Umgebung** | 💎 **Kunde/Admin: Testversion der Seite.** Änderung erst auf Kopie testen, dann live. Passt exakt zu „Design geschützt". |
| **Ein-Klick-Backup & Restore** | ⚙️ Kunde sieht Backups, kann „Stand von gestern wiederherstellen". Macht Rundum-Schutz anfassbar. |
| **SSL-Auto-Verlängerung + Ablauf-Warnung** | ⚙️ Läuft im Hintergrund, im Cockpit sichtbar. |
| **Umleitungs-Manager** (alte URL → neue) | ⚙️ Wichtig bei Relaunch, damit SEO nicht abstürzt. |
| **Ressourcen-/Traffic-Anzeige** | ⚙️ Einfach: „Speicher genutzt, Besucher-Traffic". |

## 4. Website-Baukästen (Wix, Squarespace, Webflow, WordPress)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **SEO-Assistent je Seite mit Punktzahl** („Titel zu lang, Alt-Text fehlt — 72/100") | 💎 **Kunde: SEO-Ampel je Seite.** Konkrete To-dos statt Fachchinesisch. Führt sanft zur SEO-Betreuung (490 €). |
| **Barrierefreiheits-Check (BFSG/WCAG)** | 💎 **Kunde: BFSG-Check im Portal.** Genau dein Verkaufsargument — als Dauer-Feature. Kontrast, Alt-Texte, Struktur. Ampel + „jetzt beheben lassen". |
| **Formular-Baukasten + Eingangs-Postfach** | 💎 **Die Anfragen von der Kundenseite landen im Kundenportal** („Deine Anfragen: 9 diesen Monat"). Der Kunde *sieht seinen eigenen Umsatz entstehen* → stärkstes Bindungs-Argument überhaupt. |
| **Mediathek mit Bildbearbeitung** (zuschneiden, komprimieren) | ⚙️ Bilder hochladen, auto-komprimiert (Ladezeit), Zuschnitt. |
| **Versions-Verlauf / Auto-Speichern / Rückgängig** | ⚙️ Kunde traut sich zu ändern, weil nichts kaputtgehen kann. |
| **Geplantes Veröffentlichen** (Beitrag für Montag 8 Uhr) | ⚙️ Für Aktionen/News. |
| **Mehrsprachigkeit** | ⚠️ Nur wenn Zielgruppe es braucht — sonst Komplexität. |
| **Defekte-Links-Prüfung** | ⚙️ Automatisch, meldet tote Links. |
| **App-Marktplatz** | ⚠️ Klar die Mercedes-Falle. Bewusst *nicht*. |

## 5. Agentur-/Kundenportale (Basecamp, HoneyBook, Dubsado, Bonsai)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **Angebot mit E-Signatur** (Kunde unterschreibt online) | 💎 **Admin→Kunde: Angebot digital annehmen/unterschreiben.** Kein PDF-Hin-und-Her. Angebot angenommen → automatisch Projekt + Anzahlungsrechnung. |
| **Vertrag / AVV digital signieren** | 💎 AVV (bietest du an) + AGB einmal digital abhaken, revisionssicher abgelegt. |
| **Freigabe-Schleifen** („Design freigeben"-Button) | 💎 Kunde gibt Entwurf/Korrektur mit einem Klick frei → dokumentiert, wann Korrekturrunden verbraucht sind (deine 1/2/3-Regel wird sichtbar & fair). |
| **Marken-Kit / Brand-Ablage** (Logo, Farben, Schriften zentral) | ⚙️ Kunde lädt einmal hoch, du greifst immer drauf zu. |
| **Zeiterfassung** | 💎 **Admin: Zeit erfassen je Projekt/Kunde.** Du rechnest 150 €/Std. im 5-Min-Takt ab — ohne Erfassung verschenkst du Geld bei Zusatzarbeiten. |
| **Fragebogen-Onboarding** | = Briefing Stufe 2. |
| **Termin-Buchung (Calendly-Stil)** | 💎 **„Erstgespräch buchen"** direkt im Anfrage-Flow und im Portal. Kein Terminping-Pong. |
| **Kunden-Zufriedenheit / NPS** | ⚙️ Nach Go-Live „Wie zufrieden? 0–10" → Testimonial-Trigger. |

## 6. Projektmanagement (Asana, Trello, Monday, Linear)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **Projekt-Vorlagen** (immer gleicher Ablauf) | 💎 **Admin: Vorlage „Neue Website".** Neues Projekt = alle Phasen/Aufgaben/Checklisten automatisch da. Skaliert dich. |
| **Automatisierungen** („wenn Phase = Live → Rechnung erstellen + Kunde benachrichtigen") | 💎 Spart bei jedem Projekt Handarbeit. Der eigentliche Skalierungshebel. |
| **Kanban-Board** | ⚙️ Projekte als Spalten (Anfrage → Design → Korrektur → Live) statt nur Tabelle. |
| **Aufgaben mit Fälligkeit + Erinnerung** | ⚙️ Nichts fällt hinten runter. |
| **Kalender-Ansicht** | ⚙️ Liefertermine auf einen Blick. |

## 7. CRM / Vertrieb (HubSpot, Pipedrive)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **Lead-Quelle** („woher kam die Anfrage": Google, Empfehlung, Checkliste) | 💎 Zeigt dir, was Marketing bringt — wo du Geld/Zeit investieren sollst. |
| **Kontakt-Verlauf** (jede E-Mail/Notiz/Anruf chronologisch) | ⚙️ Eine Zeitleiste pro Kunde — nie wieder „was hatten wir besprochen?". |
| **Nachfass-Erinnerungen / Wiedervorlage** | ⚙️ „Peter am Do. anrufen." Verloren geglaubte Leads zurückholen. |
| **Segmente / Tags** | ⚙️ „Handwerker", „hat SEO", „Churn-Gefahr" → gezielt ansprechen. |
| **Umsatz-Prognose (Pipeline-Wert)** | ⚙️ „In Verhandlung: 12.000 € potenziell." |
| **Dublettenprüfung** | ⚙️ Verhindert doppelte Kundenanlage. |

## 8. Support-Tools (Zendesk, Intercom, Help Scout)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **Hilfe-Center mit Suche** (durchsuchbare Anleitungen) | ⚙️ **Kunde: durchsuchbare Kurzanleitungen + Videos.** Reduziert deine Support-Last massiv. |
| **KI-Chat-Assistent** | 💎 **Du verkaufst ihn — nutze ihn selbst im Portal.** Beantwortet Kundenfragen rund um die Uhr, ist gleichzeitig lebende Demo für den Verkauf. |
| **Ticket-Status + Verlauf** | ⚙️ Anfrage → in Arbeit → gelöst, transparent. |
| **Vorgefertigte Antworten (Textbausteine)** | ⚙️ Admin beantwortet Standardfragen in Sekunden. |
| **Zufriedenheit nach Ticket** (👍/👎) | ⚙️ Qualitäts-Feedback. |

## 9. Banking- / Fintech-Apps (N26, Revolut, PayPal)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **„Nächste Abbuchung am …" glasklar** | 💎 Kunde sieht jederzeit: Paket + Rundum-Schutz, nächste Abbuchung, Betrag. Null Überraschungen → weniger Kündigungen aus Verunsicherung. |
| **Benachrichtigung bei jedem Ereignis** | ⚙️ Zahlung eingegangen, Rechnung fällig, Seite wieder online. |
| **Umsatz-/Verlaufsgrafik** | ⚙️ Einfache Kurven statt Zahlensalat. |
| **Vertrauens-/Sicherheitssignale** (Schloss-Symbole, „geschützt") | ⚙️ Kleine Hinweise, dass alles sicher & gesichert ist. Passt zu Rundum-Schutz. |
| **2-Faktor / Biometrie** | ⚙️ **Sicherheit: 2-Faktor**, Login-Verlauf, aktive Geräte, Fernabmeldung. |

## 10. Telekom / Energie „Mein Vodafone / Mein Telekom / Stadtwerke"

| Muster dort | Übertragen auf Sartu |
|---|---|
| **Tarif selbst hoch-/runterstufen** | 💎 **Kunde: Paket-Upgrade self-service** (nur Differenz zahlen). Runterstufen mit Frist ebenso — Ehrlichkeit bindet. |
| **Vertragslaufzeit + Kündigungsfrist sichtbar** | ⚙️ Transparent statt Kleingedrucktes → Vertrauen. |
| **Rechnungs-Archiv** (alle Rechnungen ewig abrufbar) | ⚙️ Kunde lädt jede alte Rechnung selbst → keine Support-Anfrage. |
| **Störung melden + Status** | ⚙️ „Meine Seite geht nicht" → Meldung + Status, statt Panik-Anruf. |
| **Treue-Vorteile** | ⚠️ Später, wenn überhaupt. |
| **Monats-/Jahresreport per Mail** | 💎 **„Dein Monat bei Sartu"** — Besucher, Uptime 99,9 %, Backups gemacht, Updates eingespielt. Automatisch per Mail. Macht den unsichtbaren Wert des Rundum-Schutzes *sichtbar* → das beste Mittel gegen Kündigung. |

## 11. Versicherung / Shop / Membership (Verallgemeinerung)

| Muster dort | Übertragen auf Sartu |
|---|---|
| **Dokumenten-Tresor** (alle Policen/Verträge an einem Ort) | ⚙️ **Kunde: Unterlagen** — Vertrag, AVV, AGB, Rechnungen, Impressum-Daten gebündelt. |
| **Schaden melden + Status verfolgen** (Versicherung) | = Störungsmeldung/Ticket. |
| **Kundenwert / Lifetime-Value** (Shop-Admin) | ⚙️ Admin sieht je Kunde Gesamtumsatz + Dauer → wer sind deine besten Kunden. |
| **Abgebrochene Anfragen zurückholen** (Warenkorb) | 💎 Wer den Anfrage-Flow abbricht → automatische freundliche Erinnerungsmail. Holt verlorene Leads. |
| **Empfehlungsprogramm** (Kunde wirbt Kunde) | 💎 **„Empfiehl Sartu, beide bekommen Vorteil."** Handwerker kennen Handwerker — günstigster Kanal, den du hast. |
| **Team-Zugänge** (mehrere Mitarbeiter des Kunden) | ⚙️ Größerer Kunde will, dass 2 Leute Inhalte pflegen → Einladung per Mail. Nur bei Platzhirsch. |

## 12. AI-native (dein Positionierungs-Trumpf — quer durch alles)

Da Sartu auf **KI-Sichtbarkeit** setzt, sollte KI *im Werkzeug* spürbar sein — das ist
gelebter Beweis statt Behauptung:

- 💎 **KI-Textassistent im Editor** — „Schreib mir einen Startseiten-Text für meine Bäckerei"
  → Vorschlag, den der Kunde anpasst. Senkt die größte Hürde (Kunde weiß nicht, was schreiben).
- 💎 **Auto-Alt-Texte** — KI beschreibt hochgeladene Bilder (BFSG + SEO in einem, ohne Arbeit).
- ⚙️ **KI-SEO-Vorschläge** — bessere Seitentitel/Meta-Texte auf Knopfdruck.
- ⚙️ **KI-Antwortentwürfe im Support** — du bearbeitest nur noch, statt neu zu tippen.
- ⚙️ **Bild-Freisteller / Hintergrund entfernen** für Produktbilder.

## 13. Recht & DSGVO (Pflicht, kein Nice-to-have)

- ⚙️ **Datenexport / DSGVO-Auskunft** + **Konto löschen** (Kunde muss das können).
- ⚙️ **Cookie-/Consent-Manager** für die Kundenseite (du bist eh die „Rechts-sicher"-Agentur).
- ⚙️ **Impressum/Datenschutz-Generator**, der bei Firmendaten-Änderung mitzieht.
- ⚙️ **Einwilligungs-Protokoll** (wann AVV/AGB akzeptiert) revisionssicher.

---

## Kuratierte „Top 12 Neu" (das würde ich wirklich bauen)

Reihenfolge nach Wirkung fürs Geschäft, nicht nach Aufwand:

1. **Anfragen der Kundenseite ins Kundenportal** — der Kunde sieht seinen eigenen Umsatz
   entstehen. Stärkstes Bindungs- und Verkaufsargument. *(B, 💎)*
2. **„Dein Monat bei Sartu"-Report** (automatisch) — macht Rundum-Schutz sichtbar,
   verhindert Kündigungen. *(B, 💎)*
3. **Mahn-Automatik + SEPA-Wiederholung** — sichert deinen wiederkehrenden Umsatz. *(A, 💎)*
4. **Angebot/Vertrag digital annehmen (E-Signatur)** → löst automatisch Projekt+Rechnung aus. *(A→B, 💎)*
5. **Freigabe-Schleifen** — macht deine Korrekturrunden-Regel fair & dokumentiert. *(B, 💎)*
6. **„In Kundenkonto wechseln" (Impersonate)** — Support-Superkraft. *(A, 💎)*
7. **BFSG- + SEO-Ampel je Seite** — dein Fachthema als Dauer-Feature, führt zu SEO-Upsell. *(B, 💎)*
8. **KI-Textassistent + Auto-Alt-Texte im Editor** — senkt die Onboarding-Hürde, beweist KI-Kompetenz. *(B, 💎)*
9. **Projekt-Vorlagen + Automatisierungen** — der Hebel, mit dem du 100 statt 10 Kunden schaffst. *(A, 💎)*
10. **Zeiterfassung je Projekt** — sonst verschenkst du Zusatzarbeit-Umsatz. *(A, 💎)*
11. **Termin-Buchung** — kein Ping-Pong beim Erstgespräch. *(A+B, 💎)*
12. **Öffentliche Status-Seite + Monats-Uptime** — Vertrauen + weniger Störungs-Anfragen. *(💎)*

## Bewusst NICHT (Mercedes-Falle)

App-Marktplatz · Mehrsprachigkeit als Standard · frei konfigurierbarer Editor mit 100 Reglern ·
Ratenzahlung · Treueprogramme · Reseller/White-Label · Community-Forum · komplexe Automations-
Baukästen für den Kunden. Alles klingt gut, jedes einzelne macht das Produkt schwerer statt
besser. Wenn Zweifel: **weglassen** und beobachten, ob überhaupt jemand danach fragt.

## Roter Faden

Fast alle 💎-Ideen zahlen auf **drei Ziele** ein:
1. **Wiederkehrenden Umsatz sichern** (Report, Mahn-Automatik, Upgrade-self-service).
2. **Kündigungen verhindern**, indem der unsichtbare Wert sichtbar wird (Cockpit, Report,
   Status-Seite, Anfragen-Postfach).
3. **Dich skalierbar machen** (Vorlagen, Automatisierungen, Impersonate, KI im Werkzeug),
   damit du mit gleicher Zeit mehr Kunden trägst — ohne Team.

Das ist der Filter für jede künftige Idee: *Sichert sie Umsatz, bindet sie Kunden, oder
skaliert sie mich?* Wenn nichts davon → nicht bauen.

---

## Entscheidungen aus dem Gespräch (Stand laufend)

Durchgang durch die „Top 12 Neu" mit Nils — was bleibt, was fliegt, was sich ändert:

- **#1 Anfragen im Kundenportal** — mit Vorsicht. Ein nacktes „0 Anfragen" geht nach hinten
  los. Deshalb: **Schlagzeile = Besucher/Findbarkeit** (fast immer positiv), Anfragen nur als
  Bonus mit Kontext. Schwache Zahl führt **immer zu einem nächsten Schritt** (SEO-Angebot),
  nie ins Leere. Anfangsphase (~8 Wochen) abfedern: „deine Seite kommt bei Google an" statt
  harter Konversionszahlen. **Kein** manuelles Erfassen von Offline-/Telefon-Anfragen (= Arbeit).
- **#2 „Dein Monat bei Sartu"-Report** — ✅ bauen, aber **vollautomatisch** (null Handarbeit)
  und gefüllt mit dem, was Sartu geleistet hat (Uptime, Updates, Backups, Besucher) — alles
  verlässlich positiv. Riskante Anfrage-Zahlen bewusst raus. Bester Kündigungs-Schutz.
- **#3 Mahn-Automatik + SEPA-Wiederholung** — ✅ ja.
- **#4 Angebot digital annehmen (E-Signatur) → Projekt+Rechnung** — ✅ ja.
- **#5 Freigabe-Schleifen** — ✅ ja, **erweitert**: Entwurf auf Sub-Domain (`vorschau.sartu.de/…`),
  Kunde setzt **Notizen direkt auf der Seite** („Bild größer, Text weg"), du bekommst sie als
  Liste. „Freigeben"-Knopf, wenn zufrieden. Killt das E-Mail-Ping-Pong im Projekt.
- **#6 Impersonate „In Kundenkonto wechseln"** — ✅ ja.
- **#7 BFSG- + SEO-Ampel je Seite** — ✅ ja.
- **#8 KI im Editor** — **aufgeteilt**: **Auto-Alt-Texte bleiben** (unsichtbare Automatik,
  BFSG, spart *dir* Arbeit — kein Baukasten). **KI-Textassistent für den Kunden RAUS** —
  macht's zum „mach's selbst"-Baukasten, widerspricht „Sartu macht das". KI nutzt **Nils
  intern**, um Texte schneller zu erstellen; nach außen bleibt „Sartu schreibt deine Texte".
- **#9 Projekt-Vorlagen + Automatisierungen** — ✅ **Kern-Hebel**, um große Agenturen
  auszuperformen. Hohe Priorität.
- **#10 Zeiterfassung** — ❌ raus. Fixpreis-Modell, „nicht für Zeit arbeiten".
- **#11 Termin-Buchung** — ❌ raus. Widerspricht dem Versprechen „schriftlich, ohne Termin".
- **#12 Öffentliche Status-Seite** — ❌ streichen (bzw. viel später). Nutzen v. a. für große
  Anbieter; stellt Ausfälle öffentlich zur Schau. Uptime-Beweis lieber privat im Cockpit +
  Monatsreport.
