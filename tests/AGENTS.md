<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Tests (tests/)

## Purpose
Automated test suites utilizing PHPUnit. The application relies on these tests to prove functionality before merging code.

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `Feature/` | Integration tests and broad HTTP endpoint tests |
| `Unit/` | Isolated tests for individual classes/functions |

## For AI Agents

### Working In This Directory
- **Testing Framework**: PHPUnit ONLY. If you see Pest tests, convert them to PHPUnit.
- **Coverage**: Tests should cover happy paths, failure paths, and edge cases.
- **Factories**: Always use Model factories with appropriate states instead of manual DB seeding where possible.

### Testing Requirements
- **DO NOT** create ad-hoc verification scripts or Tinker sessions when tests can prove the functionality.
- **NEVER** remove tests from this directory without user approval.
- Use `rtk php artisan test --compact --filter=TestName` to run specific tests quickly.

## Dependencies

### Internal
- `database/factories/` - Essential for building test states.
- `phpunit.xml` - Root configuration for the test suites.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
