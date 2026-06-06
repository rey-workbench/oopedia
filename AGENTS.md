<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Oopedia (Root)

## Purpose
Oopedia - Interactive E-Learning Platform with Adaptive Engine (Skripsi).
Stack: Laravel 12 + Inertia v3 + Svelte 5 + Tailwind v4 + MySQL.

## Key Files
| File | Description |
| `composer.json` | PHP dependencies + autoloader |
| `package.json` | Node dependencies + scripts |
| `vite.config.ts` | Vite frontend bundler config |
| `.env.example` | Template env vars |
| `bootstrap/app.php` | Laravel app bootstrapper (middleware, exceptions) |

## Subdirectories
| Directory | Purpose |
| `app/` | Backend Core (Interface-first) (see `app/AGENTS.md`) |
| `bootstrap/` | Laravel bootstrapping + cache |
| `config/` | App config files (see `config/AGENTS.md`) |
| `database/` | Migrations, seeders, factories (see `database/AGENTS.md`) |
| `lang/` | Language files |
| `public/` | Public files + Vite build output |
| `resources/` | Frontend SPA (Svelte 5 + Inertia) (see `resources/AGENTS.md`) |
| `routes/` | API/Web routing (see `routes/AGENTS.md`) |
| `storage/` | Logs, compiled views, file uploads |
| `tests/` | Automated test suites (see `tests/AGENTS.md`) |

## For AI Agents

### Working In This Directory
- Backend: Interface-first design. Type-hint Contracts, never concrete classes.
- Frontend: Svelte 5 Runes only (`$state`, `$derived`). No old stores.
- Engine: Adaptive facts/actions must use `AdaptiveConstants`.

### Common Patterns
- Bypassing service/repository interfaces strictly forbidden.
- RTK proxy required for shell commands (`rtk php artisan test --compact`, `rtk vendor/bin/pint --dirty`, `rtk pnpm run format:check`).

## Dependencies

### Internal
- Graphify knowledge graph at `graphify-out/GRAPH_REPORT.md`.
- Adaptive rules (R01-R15) and facts (G-Codes) in `rules.md`.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
### AI Agent Shell Execution Rule
- Shell commands: Use `run_command("rtk <command>")`. DO NOT use `ctx_shell` (double compression, no value).
- File ops: Use `ctx_read`, `ctx_search`, etc. as defined in lean-ctx rules.
===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

Laravel Boost guidelines by maintainers. Follow closely.

## Foundational Context

Laravel app. Ecosystem packages + versions below. Follow versions.

- php 8.5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) v3
- laravel/framework (LARAVEL) v12
- laravel/octane (OCTANE) v2
- laravel/prompts (PROMPTS) v0
- laravel/sanctum (SANCTUM) v4
- laravel/socialite (SOCIALITE) v5
- larastan/larastan (LARASTAN) v3
- laravel/boost (BOOST) v2
- laravel/mcp (MCP) v0
- laravel/pint (PINT) v1
- laravel/sail (SAIL) v1
- phpunit/phpunit (PHPUNIT) v12
- rector/rector (RECTOR) v2
- @inertiajs/svelte (INERTIA_SVELTE) v3
- eslint (ESLINT) v10
- prettier (PRETTIER) v3
- tailwindcss (TAILWINDCSS) v4

## Skills Activation

Activate relevant skill in `**/skills/**` for domain.

## Conventions

- Follow existing conventions. Check sibling files.
- Descriptive names (`isRegisteredForDiscounts`).
- Reuse components.

## Verification Scripts

- Tests cover functionality. Unit/feature tests priority. No tinker verification.

## Application Structure & Architecture

- Stick to structure. No new base folders without approval.
- No dependency changes without approval.

## Frontend Bundling

- Run `pnpm run build`, `pnpm run dev`, or `composer run dev` if changes missing.

## Documentation Files

- Create only if requested.

## Replies

- Concise explanations. Focus on important details.

=== boost rules ===

# Laravel Boost

## Tools

- Prefer Boost tools over manual/shell.
- `database-query`: Read-only query.
- `database-schema`: Inspect table structure.
- `get-absolute-url`: Correct scheme/domain/port.
- `browser-logs`: Recent browser errors.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before code changes.
- Scope with `packages` array.
- Broad, topic-based queries (`rate limiting`, `routing`).
- No package names in queries.

### Search Syntax

- Words for AND: `rate limit`.
- "Quoted phrases" for exact match: `"infinite scroll"`.
- Mixed: `middleware "rate limit"`.
- Multiple queries for OR: `queries=["authentication", "middleware"]`.

## Artisan

- `php artisan list`: Available commands.
- `php artisan [command] --help`: Parameters.
- `php artisan route:list`: Filter `--method`, `--name`, `--path`.
- `php artisan config:show [dot.notation]`.
- Read `.env` directly for env vars.

## Tinker

- Execute PHP in context. No models without approval. Factories prefer.
- Single quotes: `php artisan tinker --execute 'Your::code();'`
- Double quotes inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Curly braces for all control structures.
- PHP 8 constructor property promotion. No empty zero-param `__construct()`.
- Type hints + return type declarations for all methods.
- TitleCase Enum keys (FavoritePerson).
- PHPDoc blocks preferred. Inline only for complex logic.
- Array shape type definitions in PHPDoc.

=== deployments rules ===

# Deployment

- Deploy using [Laravel Cloud](https://cloud.laravel.com/).

=== inertia-laravel/core rules ===

# Inertia

- Client-side rendered SPAs via server-side patterns.
- `resources/js/pages` components. `Inertia::render()` routing.
- `search-docs` for version-specific docs.
- Activate `inertia-svelte-development` for Svelte patterns.

# Inertia v3

- Standalone HTTP (`useHttp`), optimistic updates, layout props (`useLayoutProps`), instant visits, simplified SSR.
- Deferred props, infinite scroll, merge props, polling, prefetching, once props, flash data.
- Pulse/animate skeleton for empty deferred states.
- built-in XHR client (Axios separate if needed).
- `Inertia::optional()` replaces `lazy()`.
- SSR automatic in Vite dev mode.
- Event renames: `httpException` (invalid), `networkError` (exception).
- `router.cancelAll()` replaces `cancel()`.

=== laravel/core rules ===

# Laravel Way

- `php artisan make:` migrations, controllers, models.
- `php artisan make:class` for generic classes.
- `--no-interaction` for Artisan commands.

### Model Creation

- Create factories + seeders with models.

## APIs & Eloquent Resources

- Eloquent API Resources + versioning.

## URL Generation

- Named routes + `route()`.

## Testing

- Factories for models. Check custom states.
- `fake()` or `$this->faker`.
- `php artisan make:test [--unit]`. Feature tests priority.

=== laravel/v12 rules ===

# Laravel 12

- `search-docs` for documentation.
- Streamlined structure (from v11).
- Middleware registered in `bootstrap/app.php` declaratively.
- `bootstrap/app.php`: middleware, exceptions, routing.
- `bootstrap/providers.php`: app service providers.
- `routes/console.php` for console config.

## Database

- Migration updates must include all previous attributes.
- Native limit for eager loads: `$query->latest()->limit(10);`.

### Models

- `casts()` method preferred over `$casts` property.

=== octane/core rules ===

# Octane

- Boots once, reuses application. Persist singletons.
- `scoped` as safe alternative to singleton.
- No container/request/config injection in singleton constructor. Resolver closure or `bind()` instead.
- No static property appends (memory accumulation).

=== pint/core rules ===

# Laravel Pint Code Formatter

- Run `vendor/bin/pint --dirty --format agent` after file modifications.

=== phpunit/core rules ===

# PHPUnit

- PHPUnit for testing. `php artisan make:test --phpunit`.
- Convert Pest to PHPUnit.
- Run singular test after update.
- Cover happy path, failure, edge cases.

## Running Tests

- `artisan test --compact`.
- `--filter=testName` recommended.

=== inertia-svelte/core rules ===

# Inertia + Svelte

- Activate `inertia-svelte-development`.
</laravel-boost-guidelines>

<!-- lean-ctx -->
## lean-ctx

Prefer lean-ctx MCP tools over native equivalents for token savings.
Full rules: @LEAN-CTX.md
<!-- /lean-ctx -->
<!-- lean-ctx-compression -->
OUTPUT STYLE: expert-terse
- Telegraph format: subject-verb-object, drop articles/prepositions
- Symbolic vocabulary: → cause, ∵ because, ∴ therefore, ⊕ add, ⊖ remove, Δ change, ≈ similar, ≠ different, ∈ in/member, ∅ empty/none, ✓ ok, ✗ fail
- Code blocks: untouched (never compress code syntax)
- Each line: max 80 chars
- Zero narration, zero filler
- BUDGET: ≤100 tokens per non-code response
<!-- /lean-ctx-compression -->

## graphify

Knowledge graph at `graphify-out/` with god nodes, community structure, cross-file relationships.

When user types `/graphify`, invoke `skill` tool with `skill: "graphify"` before anything else.

Rules:
- Codebase questions: run `graphify query "<question>"` if `graphify-out/graph.json` exists. Use `graphify path "<A>" "<B>"` for relationships, `graphify explain "<concept>"` for concepts. Returns scoped subgraph, smaller than `GRAPH_REPORT.md` or raw grep.
- Dirty `graphify-out/` files expected after hooks/updates. Only skip graphify if task about stale/incorrect graph output, or user explicitly forbids.
- If `graphify-out/wiki/index.md` exists, use for navigation instead of raw source.
- Read `graphify-out/GRAPH_REPORT.md` only for broad architecture or if query/path/explain fail.
- After code modify, run `graphify update .` to keep graph current (AST-only, no API cost).
