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
| `praxis.html` | Zahnarztpraxis (Freiburg) | Wachstum + Terminbuchung | fertig |
| _(geplant)_ | Kanzlei/Steuerberater | Platzhirsch | offen |
| _(geplant)_ | Handwerk | Start/Wachstum | offen |

## Technik
- Self-contained: eigenes CSS inline, Schriften self-hosted (`assets/fonts/`, Bricolage
  Grotesque + Hanken Grotesk, OFL), Fotos lokal in `assets/` (Unsplash-Lizenz, frei nutzbar).
- Bewusst **andere** Optik als die Sartu-Seite (andere Schrift/Farbe), damit jede Muster-
  seite wie eine eigene Marke wirkt.
- `noindex,nofollow`. Screenshot-Workflow: PHP-Dev-Server + Playwright (Chromium
  `/opt/pw-browsers/chromium`).
