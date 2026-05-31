<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Configuration (config/)

## Purpose
Contains Laravel framework and application-specific configuration files.

## For AI Agents

### Working In This Directory
- These files read from environment variables via the `env()` helper.
- **NEVER** hardcode sensitive values directly into these configuration files. Always use `.env`.
- Configurations can be read from anywhere using the `config()` helper, e.g., `config('app.name')`.

### Common Patterns
- In Laravel 12+, the number of configuration files is often reduced as many settings are moved to environment variables or `bootstrap/app.php`. Check if a setting should be environment-based before adding a new config file.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
