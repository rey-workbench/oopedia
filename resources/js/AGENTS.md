# Frontend Directory

**Generated:** 2026-04-02
**Part of:** Adaptive E-Learning Platform (Svelte 5 + Inertia v3)

## OVERVIEW

Svelte 5 SPA with Inertia v3 for server-rendered pages. Role-based routing (Admin, Mahasiswa), adaptive learning UI, gamification components.

## STRUCTURE

```
resources/js/
├── pages/          # Inertia pages by role
│   ├── Admin/       # Admin dashboard, CRUD
│   ├── Mahasiswa/   # Student interface
│   ├── Auth/        # Login, register
│   └── Landing/
├── components/      # Reusable components
│   ├── ui/          # Button, Card, Input, Modal, etc.
│   ├── layout/      # Layout components
│   ├── quiz/        # Quiz components
│   ├── feedback/    # Feedback modals
│   └── navigation/ # Navbar, Sidebar
├── layouts/         # App layout wrapper
├── states/          # Svelte 5 state classes
├── stores/          # Zustand-like stores
├── types/           # TypeScript types
└── utils/           # Helper functions
```

## PAGES ORGANIZATION

```
pages/{Role}/{Feature}/{Action}/Index.svelte

Examples:
├── Admin/Dashboard/Index.svelte
├── Admin/Materials/Create/Index.svelte
├── Admin/Materials/Edit/Index.svelte
├── Mahasiswa/Dashboard/Index.svelte
├── Mahasiswa/Materials/Questions/Show/Index.svelte
└── Auth/Login/Index.svelte
```

## STATE MANAGEMENT

### Svelte 5 $state Pattern

```typescript
// resources/js/states/Mahasiswa/QuizState.svelte.ts
class QuizState {
    currentQuestion = $state<Question | null>(null);
    answers = $state<Answer[]>([]);

    submitAnswer(answer: Answer) { ... }
}
```

### State Files

| Domain            | File                               |
| ----------------- | ---------------------------------- |
| Admin Dashboard   | Admin/DashboardState.svelte.ts     |
| Admin Questions   | Admin/QuestionState.svelte.ts      |
| Admin Users       | Admin/UserState.svelte.ts          |
| Student Quiz      | Mahasiswa/QuizState.svelte.ts      |
| Student Dashboard | Mahasiswa/DashboardState.svelte.ts |
| Auth              | Auth/AuthState.svelte.ts           |

## COMPONENTS

| Category     | Components                                                                                  |
| ------------ | ------------------------------------------------------------------------------------------- |
| **ui**       | Button, Card, Input, Select, Modal, Badge, Toast, DataTable, Chart, Pagination, Accordion   |
| **quiz**     | MultipleChoice, FillInTheBlank, DragAndDrop, DragDropEditor                                 |
| **feedback** | FeedbackModal, ResultFeedback, CertificateFeedback, AccelerationFeedback, BacktrackFeedback |
| **layout**   | LevelMapCanvas, ImportInstructions, GuestBanner, DifficultyFilterBar                        |

## TYPES

```
types/
├── models/        # Eloquent model types (Material, Question, User, etc.)
├── states/        # State types (mahasiswa.ts, admin.ts)
├── primitives.ts  # Shared types
└── index.ts       # Re-exports
```

## NAVIGATION

```svelte
import {(inertia, Link)} from '@inertiajs/svelte'

<a href="/admin" use:inertia>Admin</a>
<Link href="/mahasiswa">Mahasiswa</Link>
```

## UTILITIES

| File                | Purpose                 |
| ------------------- | ----------------------- |
| utils/route.ts      | Route helpers           |
| utils/router.ts     | Navigation helpers      |
| utils/quizUtils.ts  | Quiz logic              |
| utils/formatters.ts | Date, number formatting |
| utils/roles.ts      | Role constants          |

## TAILWIND V4

- CSS-first config in `resources/css/app.css`
- Custom utilities via `@utility`
- Dark mode via `dark:` prefix
- NO deprecated v3 utilities (use `bg-black/_50` not `bg-opacity-50`)
