<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Database (database/)

## Purpose
Contains Laravel database migrations, model factories, and database seeders.

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `migrations/` | Database schema definitions |
| `factories/` | Model factories for testing and seeding |
| `seeders/` | Database seeders for populating initial/fake data |

## For AI Agents

### Working In This Directory
- **Migrations**: When modifying a column, the migration MUST include all of the attributes that were previously defined on the column (otherwise they drop).
- **Factories**: Create useful factories alongside models. They are heavily relied upon in tests.

### Testing Requirements
- Seeders should be idempotent where possible.
- Use factories extensively in Feature tests rather than raw database inserts.

## Dependencies

### Internal
- `app/Models/` - The Eloquent models that these migrations and factories pertain to.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
