# Musterseiten (Beispielprojekte)

Selbst gebaute, **fiktive** Referenz-Websites für Sartus Wunschbranchen. Zweck:

1. **Portfolio-Belege**, solange es noch keine echten Kundenprojekte gibt.
2. **Bildquelle**: Screenshots dieser Seiten werden zu den echten Hero-/Sektionsbildern
   auf der Sartu-Website (statt generischer Stockfotos).
3. **Übungsfeld** für die Anti-KI-Qualität, die jede Kundenseite haben soll (nutzt die
   Skills `design-taste-frontend` + `review-animations`).

## Wichtig (Ehrlichkeit)
- Immer als **„Beispielprojekt"** kennzeichnen, wenn sie im Sartu-Portfolio gezeigt werden.
  **Nie** als echten Kunden ausgeben. Keine erfundenen Kundenlogos/Testimonials als „echt".
- Namen, Adressen, Zitate sind fiktiv.

## Bestand
| Datei | Branche | Paket-Demo | Status |
|---|---|---|---|
| `praxis.html` | Zahnarztpraxis (Muster, ohne echte Daten) | Wachstum | fertig |
| _(geplant)_ | Kanzlei/Steuerberater | Platzhirsch | offen |
| _(geplant)_ | Handwerk | Start/Wachstum | offen |

## Bilder & Rechte (wichtig)
- Fotos stammen von **Unsplash** (Unsplash-Lizenz: kostenlos, auch kommerziell, ohne
  Namensnennung). Lokal eingebettet in `assets/`.
- **Achtung Persönlichkeitsrecht:** Die Unsplash-Lizenz deckt nur das Foto, **kein
  Model-Release**. Ein fremdes Gesicht als „unsere Zahnärztin" auszugeben ist bei echter,
  öffentlicher Nutzung riskant. Deshalb:
  - **Hero-/Marketingbilder** möglichst **ohne erkennbare Gesichter** wählen (hier: helle
    Wartelounge).
  - Das Porträt „Dr. Meike Ahrens" ist reiner **Platzhalter** fürs Layout — für eine echte
    Seite kommt immer das **eigene Foto des Kunden** rein.
- Für Sartus eigene Website später ggf. KI-generierte Bilder mit klaren Rechten.

## Technik
- Self-contained: eigenes CSS inline, Schriften self-hosted (`assets/fonts/`, Bricolage
  Grotesque + Hanken Grotesk, OFL), Fotos lokal in `assets/` (Unsplash-Lizenz, frei nutzbar).
- Bewusst **andere** Optik als die Sartu-Seite (andere Schrift/Farbe), damit jede Muster-
  seite wie eine eigene Marke wirkt.
- `noindex,nofollow`. Screenshot-Workflow: PHP-Dev-Server + Playwright (Chromium
  `/opt/pw-browsers/chromium`).
