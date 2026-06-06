<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Database (database/)

## Purpose
Contains Laravel database migrations, model factories, seeders.

## Subdirectories
| Directory | Purpose |
| `migrations/` | Database schema definitions |
| `factories/` | Model factories for testing + seeding |
| `seeders/` | Database seeders for initial/fake data |

## For AI Agents

### Working In This Directory
- **Migrations**: Modifying column MUST include all previous attributes (otherwise drop).
- **Factories**: Create useful factories alongside models. Relied upon in tests.

### Testing Requirements
- Seeders idempotent where possible.
- Use factories extensively in Feature tests, not raw DB inserts.

## Dependencies

### Internal
- `app/Models/` - Eloquent models for migrations + factories.
