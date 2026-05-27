# PROJECT KNOWLEDGE BASE

**Generated:** 2026-05-28

## OVERVIEW
Oopedia - Interactive E-Learning Platform with Adaptive Engine (Skripsi).
Stack: Laravel 12 + Inertia v3 + Svelte 5 + Tailwind v4 + MySQL.

## STRUCTURE
```
app/          # Backend Core (Interface-first)
resources/js/ # Frontend SPA (Svelte 5 + Inertia)
routes/       # API/Web routing, domain split
```

## WHERE TO LOOK
| Task | Location | Notes |
|------|----------|-------|
| Backend Logic | `app/Services/` | Interfaces in `app/Contracts/` |
| DB Access | `app/Repositories/` | Eloquent queries go here |
| Frontend State| `resources/js/states/` | Svelte 5 `.svelte.ts` classes |
| Pages | `resources/js/pages/` | `Role/Feature/Action/Index.svelte` |
| Adaptive Rules| `app/Services/Adaptive/` | Forward-chaining implementation |

## CONVENTIONS
- Backend: Interface-first design. Type-hint Contracts, never concrete classes.
- Frontend: Svelte 5 Runes only (`$state`, `$derived`). No old stores.
- Engine: Adaptive facts/actions must use `AdaptiveConstants`.

## ANTI-PATTERNS (THIS PROJECT)
- Bypassing service/repository interfaces.
- Duplicating frontend type contracts outside `resources/js/types` barrels.
- Adding new route domains in `web.php` without creating modular files.

## COMMANDS
```bash
# RTK proxy required for all shell commands
rtk php artisan test --compact
rtk vendor/bin/pint --dirty
rtk pnpm run format:check
```

## NOTES
- Graphify knowledge graph available at `graphify-out/GRAPH_REPORT.md`.
- Adaptive rules (R01-R15) and facts (G-Codes) documented in `rules.md`.
