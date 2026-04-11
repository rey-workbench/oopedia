# PROJECT KNOWLEDGE BASE

**Generated:** 2026-04-11 (Asia/Bangkok)
**Commit:** e486c3e
**Branch:** ongoing2

## OVERVIEW

Adaptive e-learning platform on Laravel 12 + Inertia + Svelte 5 + Tailwind v4. Core business logic is a rule-based adaptive engine (facts/actions) connected to LMS, analytics, and role-based admin/mahasiswa flows.

## STRUCTURE

```text
oopedia/
├── app/                    # Backend domains (Contracts, Services, Repositories, Rules)
│   ├── Rules/Adaptive/     # Forward-chaining adaptive rule engine
│   ├── Services/           # Interface-driven business orchestration
│   ├── Contracts/          # Service/Repository interfaces
│   └── Repositories/       # Data access implementations
├── resources/js/           # Inertia Svelte app (pages, states, components, utils, types)
├── routes/                 # Route modules: web/auth/admin/mahasiswa/api
├── bootstrap/app.php       # Laravel 12 app wiring (routing/middleware/exceptions)
├── config/                 # App and domain config (rate limits, etc.)
└── tests/                  # PHPUnit Unit + Feature suites
```

## WHERE TO LOOK

| Task                      | Location                                      | Notes                                           |
| ------------------------- | --------------------------------------------- | ----------------------------------------------- |
| Route wiring              | `bootstrap/app.php`, `routes/*.php`           | `web.php` includes auth/admin/mahasiswa modules |
| Adaptive decision flow    | `app/Rules/Adaptive`, `app/Services/Adaptive` | Rule registry + fact/action constants           |
| Service bindings          | `app/Providers/ServiceServiceProvider.php`    | Interface→implementation map                    |
| Repository bindings       | `app/Providers/RepositoryServiceProvider.php` | Persistence abstraction map                     |
| Frontend app entry        | `resources/js/app.ts`, `vite.config.ts`       | Inertia page resolver via `import.meta.glob`    |
| Shared frontend contracts | `resources/js/types`, `resources/js/utils`    | Public barrels and route/role helpers           |

## CODE MAP

| Symbol / Entry                                  | Location                              | Role                             |
| ----------------------------------------------- | ------------------------------------- | -------------------------------- |
| `Application::configure(...)->withRouting(...)` | `bootstrap/app.php`                   | Main backend composition root    |
| `Route::middleware(...)->prefix('admin')`       | `routes/admin.php`                    | Admin feature surface            |
| `Route::middleware(...)->prefix('mahasiswa')`   | `routes/mahasiswa.php`                | Student + guest learning surface |
| `createInertiaApp({ resolve })`                 | `resources/js/app.ts`                 | Frontend boot + page resolution  |
| `RuleRegistry::registerRules()`                 | `app/Rules/Adaptive/RuleRegistry.php` | Ordered adaptive rule set        |

## CONVENTIONS (PROJECT-SPECIFIC)

- Laravel 12 bootstrap style: middleware/exception/routing configuration lives in `bootstrap/app.php`.
- Route modules are split by domain (`auth`, `admin`, `mahasiswa`) and required from `routes/web.php`.
- Backend architecture is interface-first (`app/Contracts/*` + Provider bindings) rather than direct concrete coupling.
- Frontend uses role/domain page hierarchy: `resources/js/pages/{Role}/{Feature}/{Action}/Index.svelte`.
- Svelte state pattern uses `.svelte.ts` state classes in `resources/js/states`.

## ANTI-PATTERNS (THIS PROJECT)

- Bypassing service/repository interfaces when equivalent contracts already exist.
- Introducing new route domains directly in `web.php` without modular route files.
- Duplicating frontend type contracts outside `resources/js/types` barrels.
- Mixing adaptive fact/action literals in services/pages instead of `AdaptiveConstants`.

## COMMANDS

```bash
# backend
composer install
php artisan serve
php artisan test --compact

# frontend
pnpm install
pnpm run dev
pnpm run build

# quality
vendor/bin/pint --dirty --format agent
composer run phpcs
pnpm run format:check
```

## NOTES

- LSP servers are not installed in this environment (`intelephense`, `typescript-language-server` missing); rely on static scans/grep when needed.
- `.github/workflows` is currently absent; CI policy is repo-external or not yet committed.
