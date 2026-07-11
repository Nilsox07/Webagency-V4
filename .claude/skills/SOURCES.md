# Skill-Quellen (Attribution)

Diese Skills sind projekt-lokal installiert, damit sie via git mitreisen und in
Claude-Code-Sessions in diesem Repo automatisch greifen. Jeweils aus der
**Original-Quelle des Autors** übernommen (keine bezahlten Registry-Kopien).

| Skill-Ordner | Frontmatter-`name` | Original-Quelle | Autor | Lizenz | Commit |
|---|---|---|---|---|---|
| `frontend-design/` | `frontend-design` | github.com/anthropics/claude-code → `plugins/frontend-design/skills/frontend-design` | Anthropic | Anthropic Commercial ToS („All rights reserved"), offizieller Claude-Code-Skill | `d4d8fbb` |
| `design-taste-frontend/` | `design-taste-frontend` | github.com/Leonxlnx/taste-skill → `skills/taste-skill` (tasteskill.dev) | Leonxlnx | MIT | `b177427` |
| `review-animations/` | `review-animations` | github.com/emilkowalski/skills → `skills/review-animations` (animations.dev / emilkowal.ski) | Emil Kowalski | MIT | `220e860` |

## Hinweise
- **frontend-design** ist Anthropics offizieller Skill (nicht MIT). Nutzung erfolgt im
  Rahmen von Claude Code; nicht separat weiterverbreiten.
- **design-taste-frontend** = „Taste" (Anti-Slop-Regelsatz für Landingpages/Portfolios/
  Redesigns). MIT-Lizenz liegt im Ordner bei.
- **review-animations** = Emil Kowalskis animations.dev-Regelwerk. Enthält `STANDARDS.md`
  (Easing-Kurven, Dauer-Tabellen, Spring-Config, a11y). MIT-Lizenz liegt im Ordner bei.
  Hat `disable-model-invocation: true` — bewusst manuell aufzurufen („review the animations").
- Emils Repo enthält außerdem `emil-design-eng` (breiterer Design-Engineering-Skill) und
  `animation-vocabulary`; hier bewusst nur der animationsspezifische `review-animations`
  installiert.

Stand: 2026-07-11.
