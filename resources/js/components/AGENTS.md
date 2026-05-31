<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Frontend Components (resources/js/components/)

## Purpose
Shared UI, navigation, feedback, and interactive components utilized across different Inertia pages.

## Key Files
| File | Description |
|------|-------------|
| `index.ts` | Barrel export file for shared components |

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `ui/` | Primitive UI elements (Buttons, Inputs, Cards, etc.) |
| `navigation/` | Navbars, Sidebars, Menus |
| `feedback/` | Alerts, Toasts, Loaders, Modals |
| `quiz/` | Quiz-specific interactive components |
| `rulebase/` | Components for the Adaptive Rule engine UI |
| `Admin/` | Admin-specific shared components |
| `Landing/` | Components strictly used on the public landing page |

## For AI Agents

### Working In This Directory
- **Reusability**: Components here should be highly reusable and agnostic to the page they are placed on.
- **Svelte 5**: Use `{#snippet}` for slots and `$props()` for properties.
- **Styling**: Favor passing Tailwind classes via a `class` prop (merged using `clsx` or `tailwind-merge` if available).

### Common Patterns (ANTI-PATTERNS)
- Hardcoding business logic or making API calls directly from these presentational components.
- Skipping the `index.ts` barrel export when creating a new widely-used component.

## Dependencies

### Internal
- `resources/js/types/` - Props and state typing for these components.
- `resources/js/utils/` - Shared utility functions for formatting and logic.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
