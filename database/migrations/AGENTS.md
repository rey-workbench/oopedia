## OVERVIEW
Manage database migrations to evolve the schema across 20+ files safely and reproducibly.

## WHERE TO LOOK
- Path: database/migrations
- Naming: Laravel timestamp migrations, e.g. 2026_04_29_000001_create_users_table.php
- Tools: php artisan migrate, php artisan migrate:status, php artisan migrate --path=database/migrations/<file>.php
- Batch strategy: apply migrations in chronological batches to maintain dependency order; with >20 files, group by feature and apply in logical steps
- Convention: each migration has up() and down() using Laravel Schema builder; avoid destructive edits in production; use separate migrations for data migrations
- Verification: run migrations on a local/dev database; run unit/feature tests; verify schema state matches expectations
- Verification: run migrations on a local/dev database; run unit/feature tests; verify schema state matches expectations

## ANTI-PATTERNS
- Do not modify an existing migration after it's been pushed; create a new migration for changes
- Do not rename columns by dropping and adding; use renameColumn (requires doctrine/dbal) or create a new column and copy data
- Do not perform irreversible data migrations in migrations that run in production; plan downtime or write separate scripts
- Do not cram many changes into a single migration; keep migrations small and focused
- Do not commit credentials or secrets in migrations; use env vars or config
