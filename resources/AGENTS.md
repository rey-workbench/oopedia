<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Frontend Resources (resources/)

## Purpose
Contains the frontend assets of the Oopedia application, primarily built using Svelte 5 and Inertia.js v3, alongside Tailwind CSS v4 styling.

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `css/` | Tailwind CSS configuration and base styles |
| `js/` | Svelte 5 SPA application root (see `js/AGENTS.md`) |
| `views/` | Laravel Blade templates (mainly `app.blade.php` for Inertia root) |

## For AI Agents

### Working In This Directory
- **Frontend SPA**: The core logic lives in `js/`. The `views/` directory is intentionally kept minimal, only serving as the Inertia root.
- **Styling**: Tailwind CSS v4 is used. Configuration might be minimal in `css/app.css` as v4 uses CSS-based configuration.

### Common Patterns
- **Inertia Rendering**: Controller returns `Inertia::render()`, which maps directly to Svelte components in `resources/js/pages/`.

## Dependencies

### Internal
- `vite.config.ts` - Builds these assets.
- `tailwind.config.js` or CSS equivalent - Processes the styles.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
