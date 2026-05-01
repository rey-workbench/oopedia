## OVERVIEW

Models map to database tables in `App\Models`.

## WHERE TO LOOK

- **Core Entities**: `User.php`, `Role.php`, `StudentState.php`
- **LMS Entities**: `Material.php`, `Question.php`, `Answer.php`, `QuizAttempt.php`, `Media.php`
- **Adaptive Engine Entities**: `AdaptiveRule.php`, `AdaptiveFact.php`, `AdaptiveAction.php`, `AdaptiveExecutionLog.php`
- **Survey Entities**: `MslqQuestion.php`, `MslqAnswer.php`, `MslqResult.php`, `SusResult.php`, `UeqSurvey.php`

### Notes:

- Migrations: Check `database/migrations` for corresponding tables.
- Eager loading: Use `with()` to load related data to avoid N+1 queries.
- Attributes: Inspect `$fillable` or `$guarded` to guard mass assignment.
- Casting: Review `casts()` method to enforce attribute data types (PHP 8.4 style).

## CONVENTIONS

- Keep models simple, defining relationships via methods (e.g. `public function relationName(): HasMany`).
- Use the `casts()` method instead of the `$casts` property (Laravel 12 convention).

## ANTI-PATTERNS

- Do not place heavy business logic in models; offload to services or repositories.
- Avoid using `$casts` property; use `casts()` method instead.
