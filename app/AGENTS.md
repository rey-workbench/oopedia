<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# Backend Core (app/)

## Purpose
Laravel 12 Backend Core. This directory contains the entire backend application logic, enforcing an interface-first design philosophy.

## Key Files
| File | Description |
|------|-------------|
| `Providers/AppServiceProvider.php` | Main service provider for registering bindings |
| `Exceptions/Handler.php` | Global exception handling |

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `Contracts/` | Interfaces for Services & Repositories |
| `Http/` | Controllers & Middleware (see `Http/AGENTS.md`) |
| `Models/` | Eloquent Models (see `Models/AGENTS.md`) |
| `Repositories/` | Data access implementations (see `Repositories/AGENTS.md`) |
| `Services/` | Business Logic & Adaptive Engine (see `Services/AGENTS.md`) |

## For AI Agents

### Working In This Directory
- **Logic Location**: Controllers parse request -> call Service -> return response.
- **Dependency Injection**: Always type-hint `Contracts`, never concrete implementations.

### Common Patterns (ANTI-PATTERNS)
- Direct DB calls (`Model::where(...)`) inside Controllers are STRICTLY PROHIBITED (use Repositories).
- Business logic mixed inside Controllers is STRICTLY PROHIBITED.

## Dependencies

### Internal
- `app/Contracts/` - Extensively used across `Services` and `Repositories`.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
