<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Tests (tests/)

## Purpose
Automated test suites utilizing PHPUnit. Prove functionality before merging.

## Subdirectories
| Directory | Purpose |
| `Feature/` | Integration tests + broad HTTP endpoint tests |
| `Unit/` | Isolated tests for classes/functions |

## For AI Agents

### Working In This Directory
- **Testing Framework**: PHPUnit ONLY. Convert Pest to PHPUnit.
- **Coverage**: Cover happy paths, failure paths, edge cases.
- **Factories**: Use Model factories with states instead of manual DB seeding.

### Testing Requirements
- **DO NOT** create ad-hoc verification scripts or Tinker sessions if tests can prove functionality.
- **NEVER** remove tests without user approval.
- Use `rtk php artisan test --compact --filter=TestName` to run specific tests.

## Dependencies

### Internal
- `database/factories/` - Build test states.
- `phpunit.xml` - Root config.
