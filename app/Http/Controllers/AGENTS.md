# HTTP CONTROLLERS

[← Back to Backend Core](../AGENTS.md) | [← Back to Project Knowledge Base](../../AGENTS.md)

**Generated:** 2026-05-28

## OVERVIEW
Laravel HTTP request handlers. Entry points for web and API routes.

## STRUCTURE
```
app/Http/Controllers/
├── Admin/         # Teacher endpoints
├── Mahasiswa/     # Student endpoints
└── Auth/          # Authentication
```

## CONVENTIONS
- Thin Controllers: Parse requests, authorize, pass to Service, return response.
- Dependency Injection: Inject Service Contracts, not concrete classes.
- Responses: Return Inertia responses (`Inertia::render`) or JSON responses.
- Validation: Use Form Requests (`app/Http/Requests/`) instead of inline validation.

## ANTI-PATTERNS
- Database queries (`Model::find()`) inside the controller.
- Complex business logic, calculations, or adaptive rule evaluations.
