<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Eloquent Models (app/Models/)

## Purpose
Contains Laravel Eloquent ORM Models that map to database tables.

## Key Files
| File | Description |
|------|-------------|
| `User.php`, `Role.php`, `StudentState.php` | Core Entities |
| `Material.php`, `Question.php`, `Answer.php`, `QuizAttempt.php`, `Media.php` | LMS Entities |
| `AdaptiveRule.php`, `AdaptiveFact.php`, `AdaptiveAction.php`, `AdaptiveExecutionLog.php` | Adaptive Engine Entities |
| `MslqQuestion.php`, `MslqAnswer.php`, `MslqResult.php`, `SusResult.php`, `UeqSurvey.php` | Survey Entities |

## For AI Agents

### Working In This Directory
- Keep models simple. They should primarily define relationships via methods (e.g., `public function relationName(): HasMany`).
- **Casting**: Use the `casts()` method instead of the `$casts` property (this is a Laravel 12 convention).

### Common Patterns (ANTI-PATTERNS)
- Do not place heavy business logic in models. Offload it to `Services` or `Repositories`.
- Avoid using the `$casts` array property; use the `casts()` method instead.

## Dependencies

### Internal
- `database/migrations/` - Table schemas for these models.
- `app/Repositories/` - Where the actual data access queries for these models should be executed.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
