<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Repository Layer (app/Repositories/)

## Purpose
Contains data access implementations. This layer abstracts the database and Eloquent ORM away from the Service layer.

## Key Files
| File | Description |
|------|-------------|
| `UserRepository.php` | User queries and persistence |
| `MaterialRepository.php` | Material queries |
| `QuestionRepository.php` | Question queries |
| `ProgressRepository.php` | Handles complex logic for student progress and attempt tracking |

## For AI Agents

### Working In This Directory
- **Implement Interfaces**: Every class here MUST implement a corresponding interface from `app/Contracts/Repositories/`.
- **Database Operations**: ALL database queries (e.g., `Model::where()`, `Model::create()`) MUST happen inside this directory. Do not perform queries in Services or Controllers.

### Common Patterns (ANTI-PATTERNS)
- Returning arrays instead of Models/Collections when the caller expects objects.
- Business logic mixed inside Repositories. Repositories should only fetch and persist data; Services should contain the business rules.

## Dependencies

### Internal
- `app/Contracts/Repositories/` - The interfaces these classes implement.
- `app/Models/` - The Eloquent models they query.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
