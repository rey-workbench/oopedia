<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Frontend State Management (resources/js/states/)

## Purpose
Svelte 5 class-based reactive state stores and controllers.

## Key Files
| File | Description |
|------|-------------|
| `BaseState.svelte.ts` | Abstract base class for domain states |
| `FormState.svelte.ts` | Form handling wrapper for Inertia forms |
| `index.ts` | Barrel export for state classes |

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `Admin/` | State classes for Admin features |
| `Mahasiswa/` | State classes for Student features |
| `Auth/` | Authentication states |
| `Error/` | Error handling states |
| `ui/` | Global UI states (Toasts, Navigation menus, Dialogs) |

## For AI Agents

### Working In This Directory
- **Class-Based Runes**: Use `class` with `$state` runes for reactive fields (e.g. `title = $state('')`).
- **Separation of Concerns**: Move complex form logic, API calls, and business rules OUT of the Svelte component file into these state classes.
- **Instantiation**: Initialize these classes in the context of the page or component.

### Common Patterns (ANTI-PATTERNS)
- Svelte 4 `writable` or `readable` stores are **STRICTLY PROHIBITED** (deprecated in favor of Runes).
- Hardcoding static data in state instead of passing via props/Inertia.

## Dependencies

### Internal
- `resources/js/types/` - Type definitions for the state properties.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
