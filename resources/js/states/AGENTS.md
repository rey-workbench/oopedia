# FRONTEND STATE MANAGEMENT

**Generated:** 2026-05-28

## OVERVIEW
Svelte 5 class-based reactive state stores and controllers.

## STRUCTURE
```
resources/js/states/
├── Admin/      # State classes for Admin features
├── Mahasiswa/  # State classes for Student features
└── ui/         # Global UI states (Toasts, Navigation)
```

## CONVENTIONS
- Class-Based: Use `class` with `$state` runes for reactive fields.
- Instantiation: Initialize in the context of the page component.
- Separation of Concerns: Move complex form logic, API calls, and business rules out of the Svelte component file into these state classes.

## ANTI-PATTERNS
- Svelte 4 `writable` or `readable` stores (deprecated in favor of Runes).
- Hardcoding static data in state instead of passing via props/Inertia.
