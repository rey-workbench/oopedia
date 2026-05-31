<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Inertia Pages (resources/js/pages/)

## Purpose
Inertia.js Svelte 5 views representing full web pages. These are directly returned by Laravel controllers.

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `Admin/` | Dashboards, Material management, Adaptive Rules |
| `Mahasiswa/` | Learning interface, Quizzes, Gamification |
| `Auth/` | Login, Register, Forgot Password |
| `Landing/` | Public landing page components |
| `Error/` | HTTP Error pages (404, 500, etc.) |

## For AI Agents

### Working In This Directory
- **Routing**: Must map to corresponding Laravel backend routes.
- **Component File Naming**: Use the pattern `Role/Feature/Action/Index.svelte`.
- **Data Fetching**: Relies on Inertia props passed from the backend controller. Do not make standalone `fetch` requests for page load data.

### Common Patterns (ANTI-PATTERNS)
- Huge bloated Svelte files are **ANTI-PATTERNS**. Extract logic to `states/` or break down into `components/`.
- Mixing Admin and Mahasiswa logic in one view is **STRICTLY PROHIBITED**. Keep role domains separated.

## Dependencies

### Internal
- `app/Http/Controllers/` - Where the `Inertia::render()` calls are made.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
