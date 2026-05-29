# ELOQUENT MODELS

[← Back to Backend Core](../AGENTS.md) | [← Back to Project Knowledge Base](../../AGENTS.md)

**Generated:** 2026-05-28

## OVERVIEW

Models map to database tables in `App\Models`.

## WHERE TO LOOK

- **Core Entities**: `User.php`, `Role.php`, `StudentState.php`
- **LMS Entities**: `Material.php`, `Question.php`, `Answer.php`, `QuizAttempt.php`, `Media.php`
- **Adaptive Engine Entities**: `AdaptiveRule.php`, `AdaptiveFact.php`, `AdaptiveAction.php`, `AdaptiveExecutionLog.php`
- **Survey Entities**: `MslqQuestion.php`, `MslqAnswer.php`, `MslqResult.php`, `SusResult.php`, `UeqSurvey.php`

## CONVENTIONS

- Keep models simple, defining relationships via methods (e.g. `public function relationName(): HasMany`).
- Use the `casts()` method instead of the `$casts` property (Laravel 12 convention).

## ANTI-PATTERNS

- Do not place heavy business logic in models; offload to services or repositories.
- Avoid using `$casts` property; use `casts()` method instead.
