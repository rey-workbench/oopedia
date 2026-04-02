# Services Directory

**Generated:** 2026-04-02
**Part of:** Adaptive E-Learning Platform

## OVERVIEW

Service layer implementing business logic with dependency injection via interfaces. 5 service clusters handle Adaptive Learning, Analytics, Gamification, LMS, and User management.

## STRUCTURE

```
app/Services/
├── Adaptive/       # Forward-chaining rule engine
├── Analytics/      # Dashboard, leaderboard, UEQ surveys
├── Gamification/   # Points, badges, achievements
├── Lms/            # Materials, questions, attempts
└── User/           # Auth, profiles, CSV import
```

## SERVICE CLUSTERS

| Cluster          | Purpose                         | Key Files                                                              |
| ---------------- | ------------------------------- | ---------------------------------------------------------------------- |
| **Adaptive**     | Rule-based learning adjustments | AdaptiveEngineService, FactGatheringService, NextActionResolverService |
| **Analytics**    | Stats and reporting             | DashboardService, LeaderboardService, AdminDashboardService            |
| **Lms**          | Content management              | MaterialService, QuestionService, QuestionAnswerService                |
| **User**         | User management                 | UserService, StudentService, PerformanceService                        |
| **Gamification** | Engagement mechanics            | GamificationService                                                    |

## PATTERNS

### Interface Binding

```php
// Consuming class binds to interface
$this->app->bind(AdaptiveEngineServiceInterface::class, AdaptiveEngineService::class);
```

### Service Method Pattern

```php
public function evaluate(array $facts, array $currentState, array $context): array
{
    // Business logic here
    return ['result' => $data];
}
```

### Concern Traits

```php
// For services with shared behavior
app/Services/User/Concerns/ImportsCsvUsers.php
```

## WHERE TO LOOK

| Task              | Service                 |
| ----------------- | ----------------------- |
| Quiz flow         | AdaptiveQuizFlowService |
| Rule evaluation   | AdaptiveEngineService   |
| Material CRUD     | MaterialService         |
| User registration | UserService             |
| Leaderboard       | LeaderboardService      |
| UEQ surveys       | UeqSurveyService        |
