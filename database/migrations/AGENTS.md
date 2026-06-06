# DATABASE MIGRATIONS

[← Back to Project Knowledge Base](../../AGENTS.md)

## OVERVIEW

Manage db migrations to evolve schema across 20+ files safely.

## WHERE TO LOOK

- Path: `database/migrations`
- Naming: Laravel timestamp, e.g. `2026_04_29_000001_create_users_table.php`
- Tools: `php artisan migrate`, `php artisan migrate:status`, `php artisan migrate --path=database/migrations/<file>.php`
- Batch strategy: apply migrations in chronological batches.
- Convention: each migration has `up()` and `down()`. Avoid destructive edits in prod. Use separate migrations for data migrations.
- Verification: run migrations on local/dev db; run tests; verify schema state.

## ANTI-PATTERNS

- Do not modify existing migration after push; create new migration.
- Do not rename columns by dropping/adding; use `renameColumn` or create new column + copy data.
- Do not perform irreversible data migrations in prod migrations; plan downtime or write scripts.
- Do not cram changes; keep small + focused.
- Do not commit credentials in migrations; use env vars or config.
