// =============================================================================
// Inertia.js Page Props & Shared Data Types
// =============================================================================

import type { Material, Question, StudentState, User, UeqSurvey, DifficultyLevel } from './models';

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

export interface MahasiswaDashboardProps extends SharedProps {
    totalMaterials: number;
    totalQuestions: number;
    hardQuestions: number;
    recentActivities: RecentActivityItem[];
}

export interface RecentActivityItem {
    id: number;
    material: Pick<Material, 'id' | 'title'>;
    difficulty: DifficultyLevel;
    is_correct: boolean;
    created_at: string;
}

export interface MaterialsListProps extends SharedProps {
    materials: MaterialWithProgress[];
    studentState: StudentState | null;
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
    studentState: StudentStateViewModel;
}

/**
 * Flattened StudentState shape passed to the question page view.
 * Matches what MaterialQuestionController serializes for Inertia.
 */
export interface StudentStateViewModel {
    gamification: {
        global_xp: number;
        current_level: string;
        current_streak: number;
        max_streak: number;
    };
    performance: {
        total_questions_answered: number;
        correct_count: number;
        wrong_count: number;
        hints_available: number;
    };
    adaptive: {
        learning_style: string;
        fast_track_active: boolean;
        last_rule: string | null;
    };
}

export interface QuestionReviewProps extends SharedProps {
    material: Material;
    materials: Material[];
    questions: QuestionWithResult[];
    difficulty: DifficultyLevel | 'all';
}

export interface QuestionWithResult extends Question {
    user_answer: string | null;
    is_correct: boolean | null;
    hint_used: boolean;
}

export interface ProfileProps extends SharedProps {
    user: User;
    personalization: Personalization;
}

export interface Personalization {
    learning_style: string | null;
    current_level: string | null;
    global_xp: number;
    current_streak: number;
    badges: string[];
}

export interface LeaderboardProps extends SharedProps {
    leaderboardData: LeaderboardEntry[];
}

export interface LeaderboardEntry {
    rank: number;
    user: Pick<User, 'id' | 'name' | 'email'>;
    global_xp: number;
    current_level: string;
    correct_count: number;
    total_questions_answered: number;
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
    triggeredRule: string | null;
    action: string | null;
    facts: AdaptiveFact[];
    nextDifficulty?: DifficultyLevel | null;
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
