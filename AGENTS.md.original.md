# PROJECT KNOWLEDGE BASE

**Generated:** 2026-04-11 (Asia/Bangkok)
**Commit:** e486c3e
**Branch:** ongoing2

## OVERVIEW

Adaptive e-learning platform Laravel 12 + Inertia + Svelte 5 + Tailwind v4. Logic rule-based adaptive engine (facts/actions) connect LMS, analytics, role-based admin/mahasiswa flows.

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

| Task                      | Location                                      | Notes                                    |
| ------------------------- | --------------------------------------------- | ---------------------------------------- |
| Route wiring              | `bootstrap/app.php`, `routes/*.php`           | `web.php` include auth/admin/mahasiswa   |
| Adaptive decision flow    | `app/Rules/Adaptive`, `app/Services/Adaptive` | Rule registry + fact/action constants    |
| Service bindings          | `app/Providers/ServiceServiceProvider.php`    | Interface→implementation map             |
| Repository bindings       | `app/Providers/RepositoryServiceProvider.php` | Persistence abstraction map              |
| Frontend app entry        | `resources/js/app.ts`, `vite.config.ts`       | Inertia page resolver import.meta.glob   |
| Shared frontend contracts | `resources/js/types`, `resources/js/utils`    | Public barrels, route/role helpers       |

## CODE MAP

| Symbol / Entry                                  | Location                              | Role                             |
| ----------------------------------------------- | ------------------------------------- | -------------------------------- |
| `Application::configure(...)->withRouting(...)` | `bootstrap/app.php`                   | Backend composition root         |
| `Route::middleware(...)->prefix('admin')`       | `routes/admin.php`                    | Admin feature surface            |
| `Route::middleware(...)->prefix('mahasiswa')`   | `routes/mahasiswa.php`                | Student + guest learning surface |
| `createInertiaApp({ resolve })`                 | `resources/js/app.ts`                 | Frontend boot + page resolution  |
| `RuleRegistry::registerRules()`                 | `app/Rules/Adaptive/RuleRegistry.php` | Ordered adaptive rule set        |

## CONVENTIONS (PROJECT-SPECIFIC)

- Laravel 12 bootstrap: middleware/exception/routing config in bootstrap/app.php.
- Route modules split by domain (auth, admin, mahasiswa), require in routes/web.php.
- Backend: interface-first (app/Contracts/* + Provider bindings). No direct concrete coupling.
- Frontend: role/domain page hierarchy resources/js/pages/{Role}/{Feature}/{Action}/Index.svelte.
- Svelte state: .svelte.ts state classes in resources/js/states.

## ANTI-PATTERNS (THIS PROJECT)

- Bypass service/repository interface when contracts exist.
- Avoid new route domains in web.php without modular files.
- No duplicate frontend type contracts outside resources/js/types barrels.
- No adaptive fact/action literals in services/pages. Use AdaptiveConstants.

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

- LSP servers not installed (intelephense, typescript-language-server missing); rely on static scan/grep.
- .github/workflows absent; CI repo-external or not committed.

===

<!-- PROJECT CONTEXT - OOPEDIA E-LEARNING PLATFORM -->

<project-context>

# Oopedia - E-Learning Platform

## Overview

- **Type**: Interactive E-Learning Platform (Skripsi/Thesis Project)
- **Purpose**: Adaptive learning system + forward chaining algorithm
- **Language**: Indonesian (Bahasa Indonesia)

## Tech Stack

| Layer    | Tech                     |
| -------- | ------------------------ |
| Backend  | Laravel 12.x             |
| Frontend | Svelte 5 + Inertia.js v3 |
| Styling  | Tailwind CSS 4           |
| Database | MySQL                    |
| Auth     | Laravel Sanctum v4       |
| Build    | Vite 7                   |

## Database Schema

### Core Tables

| Table            | Purpose                                                    |
| ---------------- | ---------------------------------------------------------- |
| `users`          | User accounts (id, name, email, password, role_id)         |
| `roles`          | User roles (admin, mahasiswa)                              |
| `materials`      | Main learning modules (title, content, module_id)          |
| `sub_materials`  | Sub-topics (title, content, jenis_konten, learning_style)  |
| `questions`      | Quiz questions (text, type, difficulty, hint)              |
| `answers`        | Answer options (text, correct, explanation)                |
| `quiz_attempts`  | Quiz history (user_id, question_id, score, time)           |
| `student_states` | Progress (gamification, profile, metrics, adaptive_state)  |
| `ueq_surveys`    | UEQ responses (26 Likert items)                            |
| `media`          | Media attachments                                          |

### Question Types

- `question_type`: multiple_choice, drag_drop, fill_blank
- `difficulty`: easy, medium, hard
- `type`: textual, visual

## Models (Eloquent)

User, Answer, Material, Media, Question, QuizAttempt, Role, StudentState, SubMaterial, UeqSurvey

## Key Features

### 1. Adaptive Quiz Engine

- Adjust question difficulty based on student performance
- Forward chaining algorithm rule evaluation
- 26 adaptive rules in app/Rules/Adaptive/

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

app/Services/Adaptive/
AdaptiveEngineService.php (Orchestrator)
AdaptiveQuizFlowService.php (Quiz flow)
FactGatheringService.php (Collect facts)
NextActionResolverService.php (Determine action)

### 4. Additional Features

- UEQ Survey (26 items)
- Leaderboard
- Certificates (Bronze/Silver/Gold)
- Gamification

## Directory Structure

Backend: app/Http, Models, DTOs, Services, Repositories, Rules, Schemas, Contracts, Traits, View
Frontend: resources/js/pages, components, stores, states, types, utils

## Routes Overview (84 total)

Admin: dashboard, materials, questions, students, users, ueq-survey
Student: dashboard, materials, quiz, profile, leaderboard, certificates, ueq-survey
Auth: login, register, logout

## UI Components Available

Accordion, Alert, Badge, Button, Card, Chart, Checkbox, ContentDisplay,
DataTable, Dropdown, EmptyState, FileUploadZone, FloatingItem, ImageUpload,
InfoPanel, Input, MasonryCard, Modal, PageHeader, Pagination, Panel,
ProgressBar, QuillEditor, Radio, Section, Select, Skeleton, StatCard,
Textarea, Toast, Toggle, UserAvatar

## Quiz Question Types

1. MultipleChoice
2. DragAndDrop
3. FillInTheBlank

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

Laravel Boost guidelines curated by maintainers. Follow for best experience.

- php 8.4
- inertiajs/inertia-laravel v3
- laravel/framework v12
... (Keep package list) ...
- tailwindcss v4

## Skills Activation

Activate relevant skill for domain:
- laravel-best-practices: Backend PHP code patterns.
- inertia-svelte-development: Inertia.js v3 Svelte 5 client-side.
- tailwindcss-development: Tailwind utility classes in templates.

## Conventions

- Follow existing conventions. Check sibling files for structure/naming.
- Descriptive names (isRegisteredForDiscounts).
- Reuse components.

## Verification Scripts

- Tests cover functionality. Unit/feature tests priority.

## Application Structure & Architecture

- Stick to structure. No new base folders without approval.
- No dependency changes without approval.

## Frontend Bundling

- Run pnpm run build, pnpm run dev, or composer run dev if changes not reflected.

## Documentation Files

- Create only if explicitly requested.

## Replies

- Concise explanations. Focus on important details.

=== boost rules ===

# Laravel Boost

## Tools

- Prefer Boost tools over manual/shell.
- database-query: Read-only query.
- database-schema: Inspect table structure.
- get-absolute-url: Correct scheme/domain/port.
- browser-logs: Recent browser errors.

## Searching Documentation (IMPORTANT)

- Use search-docs before code changes.
- Scope with packages array.
- Broad, topic-based queries (rate limiting, routing).
- No package names in queries.

### Search Syntax

- Words for AND: rate limit.
- "Quoted phrases" for exact match: "infinite scroll".
- Mixed: middleware "rate limit".
- Multiple queries for OR: ["auth", "middleware"].

## Artisan

- php artisan list: Available commands.
- php artisan [command] --help: Parameters.
- php artisan route:list: Filter --method, --name, --path.
- php artisan config:show [dot.notation].
- Read .env directly for env vars.

## Tinker

- Execute PHP in context. No models without approval. Factories prefer.
- Single quotes: php artisan tinker --execute 'Your::code();'
- Double quotes inside: php artisan tinker --execute 'User::where("active", true)->count();'

=== php rules ===

# PHP

- Curly braces for all control structures.
- PHP 8 constructor property promotion. No empty zero-param __construct().
- Type hints + return type declarations for all methods.
- TitleCase Enum keys (FavoritePerson).
- PHPDoc blocks preferred. Inline only for complex logic.
- Array shape type definitions in PHPDoc.

=== inertia-laravel/core rules ===

# Inertia

- Client-side rendered SPAs via server-side patterns.
- resources/js/pages components. Inertia::render() routing.
- search-docs for version-specific docs.
- Activate inertia-svelte-development for Svelte patterns.

# Inertia v3

-Standalone HTTP (useHttp), optimistic updates, layout props (useLayoutProps), instant visits, simplified SSR.
- Deferred props, infinite scroll, merge props, polling, prefetching, once props, flash data.
- Pulse/animate skeleton for empty deferred states.
- built-in XHR client (Axios separate if needed).
- Inertia::optional() replaces lazy().
- SSR automatic in Vite dev mode.
- Event renames: httpException (invalid), networkError (exception).
- router.cancelAll() replaces cancel().

=== laravel/core rules ===

# Laravel Way

- php artisan make: migrations, controllers, models.
- php artisan make:class for generic classes.
- --no-interaction for Artisan commands.

### Model Creation

- Create factories + seeders with models.

## APIs & Eloquent Resources

- Eloquent API Resources + versioning.

## URL Generation

- Named routes + route().

## Testing

- Factories for models. Check custom states.
- fake() or $this->faker.
- php artisan make:test [--unit]. Feature tests priority.

=== laravel/v12 rules ===

# Laravel 12

- search-docs for documentation.
- Streamlined structure (from v11).
- Middleware registered in bootstrap/app.php declaratively.
- bootstrap/app.php: middleware, exceptions, routing.
- bootstrap/providers.php: app service providers.
- routes/console.php for console config.

## Database

- Migration updates must include all previous attributes.
- Native limit for eager loads: $query->latest()->limit(10).

### Models

- casts() method preferred over $casts property.

=== octane/core rules ===

# Octane

- Boots once, reuses application. Persist singletons.
- scoped as safe alternative to singleton.
- No container/request/config injection in singleton constructor. Resolver closure or bind() instead.
- No static property appends (memory accumulation).

=== pint/core rules ===

# Laravel Pint

- Run vendor/bin/pint --dirty --format agent after file modifications.

=== phpunit/core rules ===

# PHPUnit

- PHPUnit for testing. php artisan make:test --phpunit.
- Convert Pest to PHPUnit.
- Run singular test after update.
- Cover happy path, failure, edge cases.

## Running Tests

- artisan test --compact.
- --filter=testName recommended.

=== inertia-svelte/core rules ===

# Inertia + Svelte

- Activate inertia-svelte-development.
</laravel-boost-guidelines>
