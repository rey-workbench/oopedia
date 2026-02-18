// =============================================================================
// Type Definitions — Public API
// Import from this barrel to avoid deep relative paths:
//   import type { User, Material, QuestionType } from '../types';
// =============================================================================

export type {
    // Primitives
    QuestionType,
    DifficultyLevel,
    JenisKonten,
    // Domain models
    Role,
    User,
    Material,
    SubMaterial,
    Media,
    Question,
    Answer,
    QuizAttempt,
    StudentState,
    GamificationData,
    PerformanceMetrics,
    TimeMetrics,
    LearningProfile,
    AdaptiveState,
    UeqSurvey,
} from './models';

export type {
    // Shared / Inertia
    FlashMessages,
    AuthData,
    SharedProps,
    // Admin pages
    AdminDashboardProps,
    AdminStudentProgressProps,
    AdminUeqIndexProps,
    AdminUeqDetailProps,
    RecentProgressItem,
    StudentProgressItem,
    PopularMaterialItem,
    StudentAnalytics,
    MaterialWithProgress,
    MissingQuestionsItem,
    // Mahasiswa pages
    MahasiswaDashboardProps,
    MaterialsListProps,
    MaterialShowProps,
    QuestionLevelsProps,
    QuestionShowProps,
    QuestionReviewProps,
    ProfileProps,
    LeaderboardProps,
    UeqCreateProps,
    // View models
    StudentStateViewModel,
    LevelStatus,
    QuestionWithResult,
    RecentActivityItem,
    Personalization,
    LeaderboardEntry,
    // API responses
    CheckAnswerResponse,
    AdaptiveResult,
    AdaptiveFact,
    UseHintResponse,
} from './inertia';
