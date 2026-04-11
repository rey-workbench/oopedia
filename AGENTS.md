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

===

<!-- PROJECT CONTEXT - OOPEDIA E-LEARNING PLATFORM -->

<project-context>

# Oopedia - E-Learning Platform

## Overview

- **Type**: Interactive E-Learning Platform (Skripsi/Thesis Project)
- **Purpose**: Adaptive learning system with forward chaining algorithm
- **Language**: Indonesian (Bahasa Indonesia)

## Tech Stack

| Layer    | Technology               |
| -------- | ------------------------ |
| Backend  | Laravel 12.x             |
| Frontend | Svelte 5 + Inertia.js v3 |
| Styling  | Tailwind CSS 4           |
| Database | MySQL                    |
| Auth     | Laravel Sanctum v4       |
| Build    | Vite 7                   |

## Database Schema

### Core Tables

| Table            | Purpose                                                                                                  |
| ---------------- | -------------------------------------------------------------------------------------------------------- |
| `users`          | User accounts (id, name, email, password, role_id, is_approved)                                          |
| `roles`          | User roles (admin, mahasiswa)                                                                            |
| `materials`      | Main learning modules (title, content, module_id, is_final_project)                                      |
| `sub_materials`  | Sub-topics within materials (title, content, jenis_konten, learning_style, order)                        |
| `questions`      | Quiz questions (question_text, question_type, type, difficulty, hint)                                    |
| `answers`        | Answer options (answer_text, is_correct, explanation, drag_source, drag_target, blank_position)          |
| `quiz_attempts`  | Student quiz history (user_id, question_id, answer_id, is_correct, score, time_spent)                    |
| `student_states` | Per-student progress tracking (gamification_data, learning_profile, performance_metrics, adaptive_state) |
| `ueq_surveys`    | User Experience Questionnaire responses (26 Likert scale items)                                          |
| `media`          | Media attachments (media_type, media_url)                                                                |

### Question Types

- `question_type`: multiple_choice, drag_drop, fill_blank
- `difficulty`: easy, medium, hard
- `type`: textual, visual

## Models (Eloquent)

```
User, Answer, Material, Media, Question, QuizAttempt, Role, StudentState, SubMaterial, UeqSurvey
```

## Key Features

### 1. Adaptive Quiz Engine

- Adjusts question difficulty based on student performance
- Uses **forward chaining algorithm** for rule evaluation
- 26 adaptive rules in `app/Rules/Adaptive/`

### 2. Rule Types

| Category           | Rules                                                               |
| ------------------ | ------------------------------------------------------------------- |
| Promotion          | StandardPromotion, AcceleratedJump, ModuleGraduation, MasteryMedium |
| SafetyNet          | PersistentTextualSafetyNet, PersistentVisualSafetyNet               |
| Certificate        | BronzeCertificate, SilverCertificate, GoldCertificate               |
| CrisisIntervention | TextualCrisisIntervention, VisualCrisisIntervention                 |
| Recovery           | SyntaxRecovery, LogicRecovery, RemedialIndependent                  |
| ProjectRevision    | TextualProjectRevision, VisualProjectRevision                       |

### 3. Adaptive Services

```
app/Services/Adaptive/
├── AdaptiveEngineService.php       # Main orchestrator
├── AdaptiveQuizFlowService.php    # Quiz flow management
├── FactGatheringService.php       # Collects student facts
└── NextActionResolverService.php  # Determines next action
```

### 4. Additional Features

- UEQ Survey (User Experience Questionnaire - 26 items)
- Leaderboard
- Certificates (Bronze/Silver/Gold)
- Gamification system

## Directory Structure

### Backend (`app/`)

```
app/
├── Http/           # Controllers, Middleware, Requests
├── Models/         # Eloquent models
├── DTOs/           # Data Transfer Objects (Analytics, Material, Question, Quiz, Survey, User)
├── Services/       # Business logic (Adaptive, Analytics, Gamification, Lms, User)
├── Repositories/   # Data access layer
├── Rules/          # Adaptive rules (Adaptive/*)
├── Schemas/        # JSON schemas
├── Contracts/      # Interface definitions
├── Traits/         # Reusable traits
└── View/           # Blade views
```

### Frontend (`resources/js/`)

```
resources/js/
├── pages/          # Inertia pages (Admin/, Auth/, Mahasiswa/, Landing/, Error/)
├── components/     # Svelte components
│   ├── ui/         # 33 UI components (Button, Card, Modal, etc.)
│   ├── quiz/       # Quiz components (MultipleChoice, DragAndDrop, FillInTheBlank)
│   ├── layout/     # Layout components
│   ├── navigation/ # Navigation components
│   └── feedback/   # Feedback components
├── stores/         # Svelte stores
├── states/         # State management
├── types/          # TypeScript types
└── utils/          # Utility functions
```

## Routes Overview (84 total)

### Admin Routes (`/admin/*`)

- `/admin/dashboard` - Admin dashboard
- `/admin/materials/*` - Material CRUD
- `/admin/questions/*` - Question CRUD
- `/admin/students/*` - Student management + import
- `/admin/users/*` - User management + approval system
- `/admin/ueq-survey/*` - UEQ survey results

### Mahasiswa Routes (`/mahasiswa/*`)

- `/mahasiswa/dashboard` - Student dashboard
- `/mahasiswa/materials/*` - Material browsing
- `/mahasiswa/materials/{id}/questions/*` - Quiz flow
- `/mahasiswa/profile/*` - Profile management
- `/mahasiswa/leaderboard` - Rankings
- `/mahasiswa/certificates` - Achievement certificates
- `/mahasiswa/ueq-survey/*` - UEQ survey

### Auth Routes

- `/login`, `/register`, `/logout`

## UI Components Available

```
Accordion, Alert, Badge, Button, Card, Chart, Checkbox, ContentDisplay,
DataTable, Dropdown, EmptyState, FileUploadZone, FloatingItem, ImageUpload,
InfoPanel, Input, MasonryCard, Modal, PageHeader, Pagination, Panel,
ProgressBar, QuillEditor, Radio, Section, Select, Skeleton, StatCard,
Textarea, Toast, Toggle, UserAvatar
```

## Quiz Question Types

1. **MultipleChoice** - Single correct answer selection
2. **DragAndDrop** - Drag items to targets
3. **FillInTheBlank** - Complete the sentence

## Student State JSON Structure

```json
{
  "gamification_data": { "xp", "streak", "badges" },
  "learning_profile": { "preferred_style", "weaknesses" },
  "performance_metrics": { "accuracy", "avg_time", "streak" },
  "adaptive_state": { "current_difficulty", "consecutive_correct" }
}
```

</project-context>

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel appl- php - 8.4
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/framework (LARAVEL) - v12
- laravel/octane (OCTANE) - v2
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/svelte (INERTIA_SVELTE) - v3
- eslint (ESLINT) - v10
- prettier (PRETTIER) - v3
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `inertia-svelte-development` — Develops Inertia.js v3 Svelte 5 client-side applications. Activates when creating Svelte pages, forms, or navigation; using Link, Form, useForm, useHttp, setLayoutProps, or router; working with deferred props, prefetching, optimistic updates, instant visits, or polling; or when user mentions Svelte with Inertia, Svelte pages, Svelte forms, or Svelte navigation.
- `tailwindcss-development` — Always invoke when the user's message includes 'tailwind' in any form. Also invoke for: building responsive grid layouts (multi-column card grids, product grids), flex/grid page structures (dashboards with sidebars, fixed topbars, mobile-toggle navs), styling UI components (cards, tables, navbars, pricing sections, forms, inputs, badges), adding dark mode variants, fixing spacing or typography, and Tailwind v3/v4 work. The core use case: writing or fixing Tailwind utility classes in HTML templates (Blade, JSX, Vue). Skip for backend PHP logic, database queries, API routes, JavaScript with no HTML/CSS component, CSS file audits, build tool configuration, and vanilla CSS.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `pnpm run build`, `pnpm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-svelte-development` when working with Inertia Svelte client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `pnpm run build` or ask the user to run `pnpm run dev` or `composer run dev`.

### Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== octane/core rules ===

# Octane

- Octane boots the application once and reuses it across requests, so singletons persist between requests.
- The Laravel container's `scoped` method may be used as a safe alternative to `singleton`.
- Never inject the container, request, or config repository into a singleton's constructor; use a resolver closure or `bind()` instead:

```php
// Bad
$this->app->singleton(Service::class, fn (Application $app) => new Service($app['request']));

// Good
$this->app->singleton(Service::class, fn () => new Service(fn () => request()));
```

- Never append to static properties, as they accumulate in memory across requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== inertia-svelte/core rules ===

# Inertia + Svelte

- IMPORTANT: Activate `inertia-svelte-development` when working with Inertia Svelte client-side patterns.


</laravel-boost-guidelines>
