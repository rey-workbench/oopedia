<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Services Layer (app/Services/)

## Purpose
The Service layer orchestrates business use-cases through interface-first contracts and repository abstractions.

## Key Files
| File | Description |
|------|-------------|
| `Adaptive/AdaptiveEngineService.php` | Evaluate adaptive facts/actions |
| `Adaptive/NextActionResolverService.php` | Resolve quiz next step |
| `Lms/MaterialService.php` | Material business flow |
| `Lms/QuestionService.php` | Question business flow |
| `Analytics/DashboardService.php` | Student analytics |
| `Analytics/AdminDashboardService.php`| Admin analytics |
| `User/UserService.php` | User lifecycle |

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `Adaptive/` | Rule-engine orchestration (facts, evaluation, next action) |
| `Analytics/` | Dashboard/leaderboard/survey aggregations |
| `Lms/` | Materials, questions, attempts, guest progress |
| `User/` | Registration, profile, performance |
| `Security/` | Security and honeypot services |

## For AI Agents

### Working In This Directory
- **Contracts**: Depend on `App\Contracts\Services\*Interface` in controllers/consumers.
- **Persistence**: Keep persistence in repositories; services coordinate rules + repositories.
- **Bindings**: Register bindings only in `app/Providers/AppServiceProvider.php` (or a dedicated ServiceProvider).

### Common Patterns (ANTI-PATTERNS)
- **DO NOT** inject concrete repository/service classes into controllers when an interface is already bound.
- **DO NOT** re-implement adaptive fact/action logic in non-Adaptive services.
- **DO NOT** embed request validation concerns inside service methods (keep in Form Requests).
- **DO NOT** perform direct DB calls here. Use Repositories.

## Dependencies

### Internal
- `app/Contracts/Services/` - Interfaces that these services implement.
- `app/Contracts/Repositories/` - Interfaces that these services consume to get data.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
