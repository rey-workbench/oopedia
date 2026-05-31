<!-- Parent: ../AGENTS.md -->
<!-- Generated: 2026-06-01 | Updated: 2026-06-01 -->

# HTTP Layer (app/Http/)

## Purpose
Handles incoming HTTP requests, routing them to the appropriate services, validating input, and transforming output.

## Subdirectories
| Directory | Purpose |
|-----------|---------|
| `Controllers/` | Thin controllers that delegate logic |
| `Middleware/` | Request filters (auth, security headers, etc.) |
| `Requests/` | FormRequests for complex validation |
| `Resources/` | Eloquent API Resources for transforming JSON |

## For AI Agents

### Working In This Directory
- **Controllers MUST be thin**: Controllers parse requests, call `Services`, and return responses (`Inertia::render()` or API Resource). DO NOT write complex business logic here.
- **FormRequests**: Use custom Request classes in `Requests/` instead of inline validation for complex rules.
- **Middleware**: In Laravel 12+, middleware are registered declaratively in `bootstrap/app.php`, not in a Kernel file.

### Common Patterns
- Inject Service Contracts (`app/Contracts/Services/`) into Controller constructors.
- For API routes, ALWAYS use Eloquent API Resources (`app/Http/Resources/`) to format the JSON payload.

## Dependencies

### Internal
- `app/Services/` - Where the actual work is delegated.
- `resources/js/pages/` - Inertia controllers return components from this frontend directory.

<!-- MANUAL: Any manually added notes below this line are preserved on regeneration -->
