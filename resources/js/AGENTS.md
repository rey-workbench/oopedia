<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Frontend Javascript (resources/js/)

## Purpose
Svelte 5 SPA with Inertia v3 for server-rendered pages. This is the root of the frontend application logic.

## Key Files
| File | Description |
|------|-------------|
| `app.ts` | Inertia boot + dynamic page resolution |
| `bootstrap.ts` | Axios and global window bootstrapper |

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `pages/` | Role/feature/action page hierarchy (see `pages/AGENTS.md`) |
| `states/` | `.svelte.ts` state classes + UI state (see `states/AGENTS.md`) |
| `components/` | Shared UI/navigation/feedback/quiz components (see `components/AGENTS.md`) |
| `types/` | Public type barrels + model/state contracts |
| `utils/` | Routing, role, formatter, helper utilities |
| `layouts/` | Shared page wrapper layouts |

## For AI Agents

### Working In This Directory
- **Re-exports**: Re-export shared APIs through barrels (`components/index.ts`, `types/index.ts`, `states/index.ts`, `utils/index.ts`).
- **Svelte 5 Runes**: Use Svelte 5 syntax exclusively (`$state`, `$derived`, `{#snippet}`). Avoid Svelte 4 primitives and old stores.
- **Styling**: Use Tailwind v4 patterns already established in project.

### Common Patterns (ANTI-PATTERNS)
- Deep relative type imports bypassing `@/types` barrel.
- Page-local ad-hoc type/interface drift from backend contract shapes.
- New role/feature page trees that break existing role-first hierarchy.

## Dependencies

### External
- `Inertia v3` - Bridges Laravel and Svelte.
- `Svelte 5` - UI Framework.
- `Tailwind CSS v4` - Utility-first styling framework.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->