# INERTIA PAGES

**Generated:** 2026-05-28

## OVERVIEW
Inertia.js Svelte 5 views representing full web pages.

## STRUCTURE
```
resources/js/pages/
├── Admin/      # Dashboards, Material management, Adaptive Rules
├── Mahasiswa/  # Learning interface, Quizzes, Gamification
└── Auth/       # Login, Register, Forgot Password
```

## CONVENTIONS
- Routing: Must map to corresponding Laravel backend routes.
- Component File Naming: `Role/Feature/Action/Index.svelte`.
- Data Fetching: Relies on Inertia props from the backend controller.
- State: Initialize complex page state into `states/` classes.

## ANTI-PATTERNS
- Huge bloated Svelte files (extract to `states` or `components`).
- Mixing Admin and Mahasiswa logic in one view.
