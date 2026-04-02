# Oopedia Development Skill

## Project Overview

**Oopedia** - Laravel 12.x Inertia Svelte application for Object-Oriented Programming education with adaptive quiz system.

## Tech Stack

| Component | Technology            |
| --------- | --------------------- |
| Backend   | Laravel 12            |
| Frontend  | Inertia.js + Svelte 5 |
| Styling   | Tailwind CSS v4       |
| Database  | MySQL/PostgreSQL      |
| Auth      | Laravel Sanctum       |
| Testing   | PHPUnit 12            |

## Directory Structure

```
app/
├── AdaptiveRules/          # Adaptive learning rules
│   ├── Concerns/           # Trait-like concerns
│   └── Rules/              # Rule implementations
├── Console/                 # Artisan commands
├── Contracts/               # Interfaces (Repository, Service)
│   ├── Repositories/
│   └── Services/
├── DTOs/                    # Data Transfer Objects
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         # Admin controllers
│   │   ├── Auth/          # Auth controllers
│   │   └── Mahasiswa/     # Student controllers
│   ├── Middleware/
│   └── Requests/           # Form Requests
├── Models/                  # Eloquent models
├── Repositories/             # Repository implementations
├── Rules/                   # Custom validation rules
├── Services/                 # Service implementations
│   ├── Adaptive/
│   ├── Analytics/
│   └── Lms/
└── Traits/                  # Reusable traits
```

## Key Architecture Patterns

### 1. Repository Pattern

```
Controller → Service → Repository → Model
```

**Interface** (`app/Contracts/Repositories/`):

```php
interface UserRepositoryInterface
{
    public function find(string $id): ?User;
    public function all(): Collection;
}
```

**Implementation** (`app/Repositories/`):

```php
class UserRepository implements UserRepositoryInterface
{
    public function find(string $id): ?User
    {
        return User::find($id);
    }
}
```

**Binding** (`bootstrap/app.php` or Service Provider):

```php
App::bind(UserRepositoryInterface::class, UserRepository::class);
```

### 2. Service Layer

```
Service contains business logic
Repository handles data access
Controller orchestrates flow
```

### 3. Adaptive Learning System

```
┌─────────────────────────────────────────┐
│           Student State                   │
│  - current_level                        │
│  - learning_style                       │
│  - adaptive_state                       │
│  - performance_metrics                  │
└─────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────┐
│         Adaptive Engine                  │
│  - Evaluate rules                        │
│  - Determine next action                 │
│  - Calculate difficulty                  │
└─────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────┐
│        Next Action                       │
│  - Show hint                             │
│  - Provide explanation                   │
│  - Issue certificate                     │
│  - Promote/demote difficulty             │
└─────────────────────────────────────────┘
```

## Important Files

| File                          | Purpose                         |
| ----------------------------- | ------------------------------- |
| `app/Models/StudentState.php` | Student progress tracking       |
| `app/Services/Adaptive/`      | Adaptive learning logic         |
| `app/Rules/Adaptive/`         | Rule-based inference            |
| `app/Contracts/`              | Dependency injection interfaces |

## Common Development Tasks

### 1. Adding New Adaptive Rule

```bash
# 1. Create concern in app/Rules/Adaptive/Concerns/
# 2. Add trait to appropriate rule class
# 3. Register in adaptive engine

# Example:
# App\Rules\Adaptive\Concerns\AppliesAchievement.php
trait AppliesAchievement
{
    protected function applyCertificate(array $state): array
    {
        // Logic here
        return $state;
    }
}
```

### 2. Adding New Service

```bash
# 1. Create interface in app/Contracts/Services/
interface NewServiceInterface
{
    public function doSomething(): Result;
}

# 2. Create implementation in app/Services/
class NewService implements NewServiceInterface
{
    public function doSomething(): Result
    {
        // Implementation
    }
}

# 3. Bind in bootstrap/app.php or provider
App::bind(NewServiceInterface::class, NewService::class);
```

### 3. Running Tests

```bash
# All tests
php artisan test

# Specific test
php artisan test --filter=StudentStateTest

# With coverage
php artisan test --coverage
```

## Development Workflow

```bash
# 1. Create feature branch
git checkout -b feature/new-feature

# 2. Make changes
# ... edit files ...

# 3. Format code
composer pint

# 4. Check code style
composer phpcs

# 5. Run tests
php artisan test

# 6. Commit
git add .
git commit -m "feat: add new feature"

# 7. Push
git push origin feature/new-feature
```

## Code Quality

```bash
# Format
composer pint

# Lint
composer phpcs

# Both + verify
composer phpcs:check
```

## Key Dependencies

| Package                   | Version | Purpose                 |
| ------------------------- | ------- | ----------------------- |
| laravel/framework         | ^12.0   | Core framework          |
| inertiajs/inertia-laravel | ^2.0    | SSR + client navigation |
| laravel/sanctum           | ^4.0    | API authentication      |
| predis/predis             | ^3.4    | Redis client            |

## Environment Variables

```env
APP_NAME=OOPedia
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oopedia
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

## Resources

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Inertia.js](https://inertiajs.com/)
- [Svelte 5](https://svelte.dev/docs/svelte/5)
- [Tailwind CSS 4](https://tailwindcss.com/)
