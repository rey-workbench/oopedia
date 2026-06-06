<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Configuration (config/)

## Purpose
Laravel framework + application-specific config files.

## For AI Agents

### Working In This Directory
- Files read from env vars via `env()`.
- **NEVER** hardcode sensitive values. Use `.env`.
- Read config from anywhere using `config()` helper (`config('app.name')`).

### Common Patterns
- Laravel 12+ reduced config files. Settings moved to env vars or `bootstrap/app.php`. Check if setting should be env-based before adding new config file.
