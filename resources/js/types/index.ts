// =============================================================================
// Type Definitions — Public API
// Import from this barrel to avoid deep relative paths:
//   import type { User, Material, QuestionType } from '@/types';
// =============================================================================

export type {
    // Primitives
    QuestionType,
    DifficultyLevel,
    LearningStyle,
    CertificationType,
    User,
    Material,
    SubMaterial,
    Media,
    Question,
    Answer,
    QuizAttempt,
    GamificationData,
    PerformanceMetrics,
    TimeMetrics,
    LearningProfile,
    AdaptiveState,
    UeqSurvey,
    // Computed / service shapes
    DifficultyStats,
    MaterialWithStats,
    ContentCategory,
    RecentActivity,
    StudentProfile,
    LeaderboardEntry,
    UserAttempt,
    QuestionWithAttempt,
    QuizSessionState,
    Certification,
    MslqResult,
    MslqQuestion,
    MslqAnswer,
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
    UeqAverages,
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
    ProfileForm,
    UeqSurveyForm,
    AnswerPayload,
    // View models
    LevelStatus,
    // API responses
    CheckAnswerResponse,
    AdaptiveResult,
    AdaptiveFact,
    UseHintResponse,
    Pagination,
    PaginatorLink,
    // State interfaces
    FormStateOptions,
    FormSubmitOptions,
    AdminDashboardData,
    LevelItem,
    Toast,
} from '@/types/states';
