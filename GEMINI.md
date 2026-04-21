<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

Laravel Boost guidelines curated by maintainers. Follow closely for best experience.

## Foundational Context

Laravel app. Ecosystem packages + versions below. Follow these versions.

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
