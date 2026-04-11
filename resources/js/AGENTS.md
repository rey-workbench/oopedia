# Frontend Domain Guide

**Generated:** 2026-04-02
**Part of:** Adaptive E-Learning Platform (Svelte 5 + Inertia v3)

## OVERVIEW

Svelte 5 SPA with Inertia v3 for server-rendered pages. Role-based routing (Admin, Mahasiswa), adaptive learning UI, gamification components.

## STRUCTURE

```text
resources/js/
├── app.ts             # Inertia boot + dynamic page resolution
├── pages/             # Role/feature/action page hierarchy
├── states/            # `.svelte.ts` state classes + UI state
├── components/        # Shared UI/navigation/feedback/quiz components
├── types/             # Public type barrels + model/state contracts
└── utils/             # Routing, role, formatter, helper utilities
```

## WHERE TO LOOK

| Task                            | Location                                                       |
| ------------------------------- | -------------------------------------------------------------- |
| Inertia page resolution failure | `app.ts` (`import.meta.glob('./pages/**/*.svelte')`)           |
| Shared state contracts          | `states/index.ts`, `states/**/*.svelte.ts`                     |
| Shared type contracts           | `types/index.ts`, `types/models/*`, `types/states/*`           |
| Navigation and route helpers    | `utils/route.ts`, `utils/router.ts`, `components/navigation/*` |

## CONVENTIONS

- Pages follow `pages/{Role}/{Feature}/{Action}/Index.svelte`.
- Re-export shared APIs through barrels (`components/index.ts`, `types/index.ts`, `states/index.ts`, `utils/index.ts`).
- Keep domain state in `.svelte.ts` classes and avoid duplicating prop-shape logic across pages.
- Use Tailwind v4 patterns already established in project (no deprecated v3 utility syntax).

## COUPLING BOUNDARIES

- **Allowed:** Page → state class + typed props + shared utils/components.
- **Avoid:** Duplicating route constants/role checks in pages when utilities exist.
- **Avoid:** Defining duplicate model/state types outside `types/` barrels.

## ANTI-PATTERNS

- Deep relative type imports bypassing `@/types` barrel.
- Page-local ad-hoc type/interface drift from backend contract shapes.
- New role/feature page trees that break existing role-first hierarchy.
