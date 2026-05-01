# Architecture

## Core Patterns

- **Adaptive Engine (Forward Chaining):**
    - Uses `AdaptiveEngineService` as the orchestrator to evaluate the student state.
    - Constants mapping: Rules are dynamic (database-driven via `AdaptiveRule`, `AdaptiveFact`, `AdaptiveAction`).
    - Separation of Concerns: Evaluation, Fact Gathering, and Action Resolution are modular.

- **Monolithic SPA (Inertia + Svelte):**
    - Backend (Laravel) controls routing and provides data via Inertia responses.
    - Frontend (Svelte) renders pages component-wise inside `resources/js/pages/`.
    - Shared state uses Svelte 5 `states` classes for reactive data management.

- **Interface-Driven Design (Backend):**
    - Dependency Injection with explicit interfaces (`app/Contracts`).
    - Repositories (`app/Repositories`) isolate data persistence logic.
    - Services (`app/Services`) encapsulate business rules (LMS, Analytics, User, Adaptive).

- **Routing Modularity:**
    - Route groups are segregated by domain (`auth.php`, `admin.php`, `mahasiswa.php`) and loaded inside `web.php`.
