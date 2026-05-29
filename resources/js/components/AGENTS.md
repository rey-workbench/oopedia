# UI COMPONENTS

[← Back to Frontend Core](../AGENTS.md) | [← Back to Project Knowledge Base](../../../AGENTS.md)

**Generated:** 2026-05-28

## OVERVIEW
Reusable Svelte 5 presentational components styled with Tailwind v4.

## STRUCTURE
```
resources/js/components/
├── ui/         # Base UI elements (buttons, inputs, cards)
├── navigation/ # Sidebars, headers, breadcrumbs
├── feedback/   # Toasts, alerts, modals
└── rulebase/   # Adaptive rule visualizer components
```

## CONVENTIONS
- Dumb components: Accept props and emit events. Do not fetch data.
- Svelte 5: Use `{#snippet}` for composable content.
- Props: Type with TypeScript interfaces imported from `types/`.

## ANTI-PATTERNS
- Tying components to specific Inertia page logic or backend endpoints.
- Inline styles (use Tailwind utility classes).
