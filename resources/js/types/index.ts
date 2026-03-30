// =============================================================================
// Type Definitions — Public API
// Import from this barrel to avoid deep relative paths:
//   import type { User, Material, QuestionType } from '@/types';
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
    // Computed / service shapes
    DifficultyStats,
    MaterialWithStats,
    RecentActivity,
    StudentProfile,
    LeaderboardEntry,
    UserAttempt,
    QuestionWithAttempt,
    QuizSessionState,
    Certification,
    CertificationType,
} from '@/types/models';

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
    MaterialStatsItem,
    MaterialWithProgress,
    MissingQuestionsItem,
    // Mahasiswa pages
    MahasiswaDashboardProps,
    MahasiswaInProgressProps,
    MahasiswaCompletedProps,
    MaterialsListProps,
    QuestionListProps,
    MaterialShowProps,
    QuestionLevelsProps,
    QuestionShowProps,
    QuestionReviewProps,
    ProfileProps,
    CertificatesPageProps,
    LeaderboardProps,
    UeqCreateProps,
    // View models
    LevelStatus,
    // API responses
    CheckAnswerResponse,
    AdaptiveResult,
    AdaptiveFact,
    UseHintResponse,
    Pagination,
    PaginatorLink,
} from '@/types/inertia';
