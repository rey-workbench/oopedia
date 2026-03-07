// =============================================================================
// Inertia.js Page Props & Shared Data Types
// =============================================================================

import type {
    Material,
    Question,
    User,
    UeqSurvey,
    DifficultyLevel,
    RecentActivity,
    MaterialWithStats,
    StudentProfile,
    LeaderboardEntry,
    QuestionWithAttempt,
    QuizSessionState,
} from '@/types/models';

// ---------------------------------------------------------------------------
// Shared props injected by HandleInertiaRequests middleware
// ---------------------------------------------------------------------------

export interface FlashMessages {
    success?: string;
    error?: string;
    info?: string;
    warning?: string;
}

export interface AuthData {
    user: User;
}

export interface SharedProps {
    auth: AuthData;
    flash: FlashMessages;
    errors: Record<string, string>;
}

// ---------------------------------------------------------------------------
// Admin page props
// ---------------------------------------------------------------------------

export interface AdminDashboardProps extends SharedProps {
    totalStudents: number;
    totalMaterials: number;
    totalQuestions: number;
    activeStudents: number;
    recentProgress: RecentProgressItem[];
    studentProgress: StudentProgressItem[];
    popularMaterials: PopularMaterialItem[];
    studentAnalytics: StudentAnalytics;
}

export interface RecentProgressItem {
    id: number;
    user: Pick<User, 'id' | 'name' | 'email'>;
    material: Pick<Material, 'id' | 'title'>;
    progress: number;
    updated_at: string;
}

export interface StudentProgressItem {
    user: Pick<User, 'id' | 'name' | 'email'>;
    total_attempts: number;
    correct_count: number;
    accuracy: number;
}

export interface PopularMaterialItem {
    id: number;
    title: string;
    total_attempts: number;
    unique_students: number;
}

export interface StudentAnalytics {
    distribution: Record<string, number>;
    radar: Record<string, number>;
}

export interface AdminStudentProgressProps extends SharedProps {
    student: User;
    materials: MaterialWithProgress[];
    missingQuestionsByMaterial: MissingQuestionsItem[];
    certifications: Record<number, string>;
}

export interface MaterialWithProgress extends Material {
    progress: number;
}

export interface MissingQuestionsItem {
    material_id: number;
    material_title: string;
    missing_count: number;
}

export interface AdminUeqIndexProps extends SharedProps {
    surveys: UeqSurvey[];
    averages: Record<string, number>;
    classes: string[];
    activeClass: string;
}

export interface AdminUeqDetailProps extends SharedProps {
    user: User;
    survey: UeqSurvey;
}

// ---------------------------------------------------------------------------
// Mahasiswa page props
// ---------------------------------------------------------------------------

/** Dashboard index — matches DashboardService.getDashboardIndexData() */
export interface MahasiswaDashboardProps extends SharedProps {
    totalMaterials: number;
    totalQuestions: number;
    easyQuestions: number;
    mediumQuestions: number;
    hardQuestions: number;
    materialProgressPercentage: number;
    questionProgressPercentage: number;
    completedMaterials: number;
    inProgressMaterials: number;
    totalMaterialProgress: number;
    totalAnsweredQuestions: number;
    totalCorrectQuestions: number;
    recentActivities: RecentActivity[];
    allMaterials: Material[];
    currentUserRank: LeaderboardEntry | null;
    certifications: Record<number, string>;
}

/** Dashboard in-progress list — DashboardService.getInProgressData() */
export interface MahasiswaInProgressProps extends SharedProps {
    materialsWithStats: MaterialWithStats[];
}

/** Dashboard completed list — DashboardService.getCompletedData() */
export interface MahasiswaCompletedProps extends SharedProps {
    materialsWithStats: MaterialWithStats[];
}

/** Material catalog — MaterialViewService.getMaterialsList() */
export interface MaterialsListProps extends SharedProps {
    materials: Material[];
    isGuest: boolean;
}

/** Question catalog — QuestionListingService.getMaterialsListWithStudentCount() */
export interface QuestionListProps extends SharedProps {
    materials: Material[];
    isGuest: boolean;
}

export interface MaterialShowProps extends SharedProps {
    material: Material;
    isGuest: boolean;
}

export interface QuestionLevelsProps extends SharedProps {
    material: Material;
    levels: LevelStatus[];
    isGuest: boolean;
}

export interface LevelStatus {
    level: number;
    difficulty: DifficultyLevel;
    status: 'locked' | 'available' | 'completed';
    score: number | null;
    attempts: number;
}

export interface QuestionShowProps extends SharedProps {
    material: Material;
    currentQuestion: Question;
    currentQuestionNumber: number;
    totalQuestions: number;
    answeredCount: number;
    difficulty: DifficultyLevel;
    isGuest: boolean;
    studentState: QuizSessionState;
}

/**
 * Serialised StudentState sent to the quiz page via Inertia.
 * Defined in models.ts as QuizSessionState — re-exported here for page prop use.
 */
export type { QuizSessionState } from '@/types/models';

/** Review page — MaterialQuestionController.reviewQuestions() */
export interface QuestionReviewProps extends SharedProps {
    material: Material;
    materials: Material[];
    questions: QuestionWithAttempt[];
    difficulty: DifficultyLevel | 'all';
    isGuest: boolean;
}

/** Profile page — ProfileController.show() */
export interface ProfileProps extends SharedProps {
    user: User;
    personalization: StudentProfile;
    materials: Material[];
}

/** Leaderboard page — LeaderboardService.getLeaderboardData() */
export interface LeaderboardProps extends SharedProps {
    leaderboardData: LeaderboardEntry[];
    currentUserRank: LeaderboardEntry | null;
}

export interface UeqCreateProps extends SharedProps {
    hasSubmitted: boolean;
}

// ---------------------------------------------------------------------------
// JSON API response shapes (used in axios calls from question page)
// ---------------------------------------------------------------------------

export interface CheckAnswerResponse {
    status: 'success' | 'wrong' | 'error';
    message: string;
    nextUrl: string;
    adaptiveResult: AdaptiveResult;
    xpEarned?: number;
    newLevel?: string | null;
    streakBonus?: boolean;
}

export interface AdaptiveResult {
    /** Backend uses snake_case */
    triggered_rule?: {
        id?: string;
        name?: string;
        action?: string | null;
        priority?: number;
    } | null;
    facts?: AdaptiveFact[];
    global_xp_earned?: number;
    streak_bonus?: string | null;
    new_state?: {
        next_action?: string | null;
        next_action_data?: {
            label?: string;
            url?: string;
            type?: string;
        } | null;
        recommendation?: string | null;
        certification?: string | null;
        intervention_type?: string | null;
        recovery_type?: string | null;
        fast_track_active?: boolean;
        message?: string | null;
    } | null;
}

export interface AdaptiveFact {
    key: string;
    value: string | number | boolean | null;
    label: string;
}

export interface UseHintResponse {
    success: boolean;
    hint: string | null;
    hintsRemaining: number;
    message?: string;
}
// ---------------------------------------------------------------------------
// Pagination helper types
// ---------------------------------------------------------------------------

export interface PaginatorLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Pagination<T> {
    data: T[];
    links: PaginatorLink[];
    current_page: number;
    from: number | null;
    last_page: number;
    path: string;
    per_page: number;
    to: number | null;
    total: number;
}
