# Laravel Code Quality Skill

## Overview

Project ini menggunakan kombinasi PHPCS dan Pint untuk code quality:

| Tool      | Purpose                     | Standard     |
| --------- | --------------------------- | ------------ |
| **PHPCS** | Linting (detect errors)     | PSR-12       |
| **Pint**  | Formatting (auto-fix style) | Laravel Pint |

## Quick Commands

```bash
# PHPCS - Check violations
composer phpcs

# PHPCS - Auto-fix
composer phpcs:fix

# Pint - Format code
composer pint

# Full check (fix + format + lint)
composer phpcs:check
```

## Workflow

```
1. Write Code
       ↓
2. Run composer pint (format first)
       ↓
3. Run composer phpcs (check)
       ↓
4. Fix errors (manual or composer phpcs:fix)
       ↓
5. Verify: composer phpcs (0 errors)
```

## PHPCS vs Pint

| Aspect           | PHPCS                  | Pint              |
| ---------------- | ---------------------- | ----------------- |
| **Purpose**      | Detect violations      | Fix formatting    |
| **Changes Code** | Only with phpcbf       | Yes               |
| **Strictness**   | Configurable           | opinionated       |
| **Errors Fixed** | Line length, structure | Spaces, alignment |

## Laravel-Specific Patterns

### Controller Constructor (Property Promotion)

```php
// ✅ Good (PHP 8+ constructor property promotion)
class MyController extends Controller
{
    public function __construct(
        protected ServiceInterface $service,
    ) {
    }
}
```

### Form Request Validation

```php
// ✅ Good
public function store(StoreRequest $request): RedirectResponse
{
    // ...
}
```

### Route Model Binding

```php
// ✅ Good
public function show(Request $request, string $id): Response
{
    // Use $id directly
}

// ❌ Bad - unnecessary find()
$model = Model::find($id);
```

### Return Types

```php
// ✅ Good - always use return types
public function index(): Response
{
    return Inertia::render('Index');
}

public function store(Request $request): RedirectResponse
{
    return redirect()->route('index');
}
```

### Eloquent Relationships

```php
// ✅ Good - with return types
class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
```

### Service Layer Pattern

```php
// ✅ Good - interface + implementation
interface UserServiceInterface
{
    public function getUser(string $id): ?User;
}

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepo,
    ) {
    }

    public function getUser(string $id): ?User
    {
        return $this->userRepo->find($id);
    }
}
```

## Common Laravel Violations

### 1. Using env() Outside Config

```php
// ❌ Bad
config('app.name', env('APP_NAME'));

// ✅ Good
config('app.name');
```

### 2. Raw Queries Instead of Eloquent

```php
// ❌ Bad
DB::table('users')->where('id', $id)->first();

// ✅ Good
User::find($id);
```

### 3. Missing Type Hints

```php
// ❌ Bad
public function handle($request)
{
    $id = $request->id;
}

// ✅ Good
public function handle(Request $request): Response
{
    $id = $request->input('id');
}
```

### 4. N+1 Query Problems

```php
// ❌ Bad - N+1 query
foreach ($users as $user) {
    echo $user->profile->name;
}

// ✅ Good - eager loading
$users = User::with('profile')->get();
```

## CI/CD Integration

### GitHub Actions

```yaml
name: Code Quality

on: [push, pull_request]

jobs:
    lint:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v3

            - name: Install Dependencies
              run: composer install --no-interaction

            - name: Format Code
              run: composer pint

            - name: Run PHPCS
              run: composer phpcs

            - name: Run Tests
              run: composer test
```

## Pre-commit Hook

Tambahkan di `.git/hooks/pre-commit`:

```bash
#!/bin/bash
composer pint
composer phpcs
```

## Configuration Files

| File        | Purpose             |
| ----------- | ------------------- |
| `phpcs.xml` | PHPCS configuration |
| `pint.json` | Pint configuration  |

## Resources

- [PSR-12 Standard](https://www.php-fig.org/psr/psr-12/)
- [Laravel Pint](https://github.com/laravel/pint)
- [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
