<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Application Routing (routes/)

## Purpose
Contains all Laravel routing definitions for the application, split into multiple domain-specific files for maintainability.

## Key Files
| File | Description |
|------|-------------|
| `web.php` | Main public entry points and generic routes |
| `auth.php` | Authentication and OAuth routes |
| `admin.php` | Routes specific to Admin role and dashboard |
| `mahasiswa.php` | Routes specific to Mahasiswa (Student) role |
| `api.php` | API endpoints (if any) |
| `channels.php` | Broadcasting/WebSocket channel authorizations |
| `console.php` | Artisan console commands and scheduled tasks |

## For AI Agents

### Working In This Directory
- **Domain Split**: New routes MUST be placed in their respective domain files (`admin.php`, `mahasiswa.php`, etc.). Do not dump everything into `web.php`.
- **Middleware**: Use Route Groups for middleware assignment to keep files clean.

### Common Patterns (ANTI-PATTERNS)
- Adding new route domains in `web.php` without creating modular files is STRICTLY PROHIBITED.

## Dependencies

### Internal
- `app/Http/Controllers/` - Target logic for these routes.
- `bootstrap/app.php` - Where these route files are actually registered and mapped.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
