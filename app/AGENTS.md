# BACKEND CORE

**Generated:** 2026-05-28

## OVERVIEW
Laravel 12 Backend Core. Interface-first design philosophy.

## STRUCTURE
```
app/
├── Contracts/    # Interfaces for Services & Repositories
├── Http/         # Controllers & Middleware
├── Models/       # Eloquent Models
├── Repositories/ # Data access implementations
└── Services/     # Business Logic & Adaptive Engine
```

## WHERE TO LOOK
| Task | Location | Notes |
|------|----------|-------|
| HTTP Endpoints | `Http/Controllers/` | Thin controllers |
| Core Logic | `Services/` | Implementations of `Contracts/Services/` |
| Database Ops | `Repositories/` | Implementations of `Contracts/Repositories/` |

## CONVENTIONS
- Dependency Injection: Always type-hint `Contracts`, never implementations.
- Logic Location: Controllers parse request -> call Service -> return response.

## ANTI-PATTERNS
- Direct DB calls (`Model::where(...)`) inside Controllers (use Repositories).
- Business logic mixed inside Controllers.
