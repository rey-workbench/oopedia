# Oopedia - E-Learning Platform

**Type**: Interactive E-Learning Platform (Skripsi/Thesis Project)
**Purpose**: Adaptive learning system + forward chaining algorithm
**Stack**: Laravel 12 + Inertia v3 + Svelte 5 + Tailwind v4 + MySQL

## Commands & Toolchain
- **RTK (Mandatory)**: All shell commands MUST use the `rtk` proxy (e.g. `rtk git`, `rtk composer`, `rtk php artisan`). Interactive tools like `php artisan serve` or `pnpm dev` are exceptions.
- **Backend Quality**: `rtk vendor/bin/pint --dirty`, `rtk composer run phpcs`, `rtk composer run deadcode` (PHPStan)
- **Frontend Quality**: `rtk pnpm run format:check`
- **Testing**: `rtk php artisan test --compact`

## Architecture & Code Map
- **Backend**: Interface-first design. No direct concrete coupling.
  - `bootstrap/app.php`: Backend composition root (routing/middleware).
  - `routes/*.php`: Route modules split by domain (`admin`, `mahasiswa`, `auth`).
  - `app/Services/Adaptive/AdaptiveEngineService.php`: Core forward-chaining rule engine orchestrator.
  - `app/Rules/Adaptive/Constants`: Holds enums for Facts, Actions, Pedagogical Constants.
  - `app/Contracts/` & `app/Repositories/`: Service/Repository interfaces and Data access implementations.
- **Frontend**: Role/domain page hierarchy `resources/js/pages/{Role}/{Feature}/{Action}/Index.svelte`.
  - `resources/js/app.ts`: Frontend boot + page resolution.
  - `resources/js/states`: Svelte 5 `.svelte.ts` state classes.
  - Shared frontend contracts: `resources/js/types`, `resources/js/utils`.

## Database & Core Entities
- `users` & `roles`: admin, mahasiswa.
- `materials`, `questions`, `answers`, `quiz_attempts`, `media`.
- `student_states`: Progress (gamification, performance, adaptive metrics).
  - Contains JSON: `{"gamification": {...}, "performance": {...}, "adaptive_engine": {...}}`.
- `adaptive_rules`: Forward chaining rules (conditions, execution priority).
- Survey results: `mslq_results`, `sus_results`, `ueq_surveys`.
- **Question Types**: `multiple_choice`, `drag_drop`, `fill_blank`.
- **Difficulty**: `easy`, `medium`, `hard`.
- **Type**: `textual`, `visual`.

## Anti-Patterns
- Bypassing service/repository interface when contracts exist.
- Adding new route domains in `web.php` without creating modular files.
- Duplicating frontend type contracts outside `resources/js/types` barrels.
- Using adaptive fact/action literals in services/pages (always use `AdaptiveConstants`).

## Notes
- **Graphify**: Use the knowledge graph in `graphify-out/GRAPH_REPORT.md` for architecture context.
- **Adaptive Engine Rules**: See `rules.md` for the exact Forward Chaining matrix (R01-R15), Diagnosis (V-Codes), and Input Facts (G-Codes) documentation.
- No LSP servers configured (rely on static scan/grep).

<!-- lean-ctx-compression -->
OUTPUT STYLE: expert-terse

- Telegraph format: subject-verb-object, drop articles/prepositions
- Symbolic vocabulary: → cause, ∵ because, ∴ therefore, ⊕ add, ⊖ remove, Δ change, ≈ similar, ≠ different, ∈ in/member, ∅ empty/none, ✓ ok, ✗ fail
- Code blocks: untouched (never compress code syntax)
- Each line: max 80 chars
- Zero narration, zero filler
- BUDGET: ≤100 tokens per non-code response
<!-- /lean-ctx-compression -->
