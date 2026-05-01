# Project Structure

## High-Level Root Directory

- `app/`: Backend logic (Controllers, Models, Services, Repositories).
- `bootstrap/`: Application bootstrapping, route middleware, and exceptions.
- `config/`: Application configuration.
- `database/`: Migrations, Seeders, and Factories.
- `public/`: Web entry point (`index.php`) and compiled assets.
- `resources/`: Frontend code (Svelte pages, CSS, JS utilities).
- `routes/`: Modular route definitions (`web.php`, `api.php`, `admin.php`, `mahasiswa.php`).
- `tests/`: Feature and Unit tests.

## App Sub-Domains (`app/`)

- `Contracts/`: Interfaces for Services and Repositories.
- `Enums/`: Domain constants and enumerations.
- `Http/`: Controllers, Middleware, FormRequests.
- `Models/`: Eloquent entity definitions (e.g. `User`, `StudentState`, `AdaptiveRule`).
- `Providers/`: Service Providers (`ServiceServiceProvider`, `RepositoryServiceProvider`).
- `Repositories/`: Database interaction implementations.
- `Rules/Adaptive/Constants/`: Constants for Facts (`Gxx`), Actions (`Hxx`), and Pedagogy.
- `Services/`: Business logic implementations organized by domain (`Adaptive`, `Analytics`, `Lms`, `User`).

## Resources (`resources/js/`)

- `components/`: Reusable UI components and primitive layout elements.
- `pages/`: Svelte page components mapped directly to Inertia routes (grouped by `Admin`, `Auth`, `Mahasiswa`).
- `states/`: Svelte 5 reactive state stores.
- `types/`: Shared TypeScript type definitions.
- `utils/`: Helper functions.
- `app.ts` / `bootstrap.ts`: Frontend initialization points.
