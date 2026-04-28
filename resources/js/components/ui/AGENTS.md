AGENTS for: resources/js/components/ui

Overview: A catalog of 30+ UI components with high complexity to guide development, testing, and maintenance.

STRUCTURE
- The UI component set is organized into 6+ directories by pattern:
  - buttons/
  - inputs/
  - cards/
  - modals/
  - layout/
  - navigation/
  - feedback/
  - data-display/
  Each directory includes shared primitives, variants, and accessibility considerations.

WHERE TO LOOK
- Look for documentation in:
  - index.ts exporting component surface APIs
  - README.md inside each subdirectory
  - tests/ or __tests__ patterns alongside components
  - props/types definitions in types/ or index.d.ts
  - any integration tests in e2e/ if present
- When updating a component, ensure it follows:
  - a11y with proper aria attributes
  - consistent sizing, spacing, and tokens per design system
  - no inline styles; prefer utility classes or CSS modules

CONVENTIONS
- Naming: ComponentNameVariant for variants; file names reflect component purpose
- Styling: Tailwind-based utilities; avoid custom CSS unless necessary
- Accessibility: Keyboard navigability, focus visibility, aria-labels
- Testing: Unit tests per component; visual regression tests where applicable
- Exports: Centralized barrel files to simplify imports

ANTI-PATTERNS
- Avoid duplicating components across directories; prefer composition over new wrappers
- Do not rely on inline styles; use utility classes or CSS modules
- Never couple components to a single page; ensure reusability
- Avoid large, monolithic components; split into smaller, composable pieces
- Do not export non-deterministic variants; tests should be deterministic
