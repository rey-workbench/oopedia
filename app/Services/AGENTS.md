# Services Domain Guide

[← Back to Backend Core](../AGENTS.md) | [← Back to Project Knowledge Base](../../AGENTS.md)

**Generated:** 2026-05-28
**Parent:** `/AGENTS.md`

## OVERVIEW

Service layer orchestrates business use-cases through interface-first contracts and repository abstractions.

## STRUCTURE

```text
app/Services/
├── Adaptive/       # Rule-engine orchestration (facts, evaluation, next action)
├── Analytics/      # Dashboard/leaderboard/survey aggregations
├── Lms/            # Materials, questions, attempts, guest progress
├── User/           # Registration, profile, performance
└── Gamification/   # Points, badges, progression rewards
```

## WHERE TO LOOK

| Task                            | Service                                                                 |
| ------------------------------- | ----------------------------------------------------------------------- |
| Evaluate adaptive facts/actions | `Adaptive/AdaptiveEngineService.php`                                    |
| Resolve quiz next step          | `Adaptive/NextActionResolverService.php`                                |
| Material/question business flow | `Lms/MaterialService.php`, `Lms/QuestionService.php`                    |
| Student and admin analytics     | `Analytics/DashboardService.php`, `Analytics/AdminDashboardService.php` |
| User lifecycle                  | `User/UserService.php`, `User/StudentService.php`                       |

## CONVENTIONS

- Depend on `App\Contracts\Services\*Interface` in controllers/consumers.
- Keep persistence in repositories; services coordinate rules + repositories.
- Register bindings only in `app/Providers/ServiceServiceProvider.php`.
- Return DTO/array structures consistent with existing service APIs; avoid ad-hoc shapes.

## COUPLING BOUNDARIES

- **Allowed:** Service → Repository interface, Service → Domain rules/constants, Service → DTO.
- **Avoid:** Controller directly querying repositories/models when equivalent service exists.
- **Avoid:** Cross-domain service calls that bypass contract boundaries.

## ANTI-PATTERNS

- Injecting concrete repository/service classes into controllers when an interface is already bound.
- Re-implementing adaptive fact/action logic in non-Adaptive services.
- Embedding request validation concerns inside service methods (keep in Form Requests).
