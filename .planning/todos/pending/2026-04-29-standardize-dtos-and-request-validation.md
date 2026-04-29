---
created: 2026-04-29T23:45:00+07:00
title: Standardize DTOs and Request Validation
area: planning
files:
  - app/Http/Controllers/Mahasiswa/MaterialQuestionController.php
  - app/Services/Lms/QuizService.php
  - app/Services/User/PerformanceService.php
---

## Problem

The project currently has inconsistent data passing patterns between Controllers and Services. While the Adaptive Engine has been refactored to use DTOs and structured result objects, other core services like QuizService and PerformanceService still rely on raw associative arrays or multiple loose parameters. This reduces type safety, makes refactoring difficult, and obscures the data contract between layers.

## Solution

- Implement `php artisan make:request` for all controller methods to formalize input validation.
- Define DTOs (Data Transfer Objects) in `app/DTOs/` for all complex data structures passed to services.
- Refactor existing services to accept these DTOs as their primary input.
- Ensure services return DTOs (like `EngineResultDTO`) instead of raw arrays to maintain a consistent output contract.
- Priority areas: Quiz submission flow, Performance metric updates, and Material/Question CRUD operations.
