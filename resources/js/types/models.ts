// =============================================================================
// Domain Model Types
// Mirrors the PHP Eloquent models in app/Models/
// =============================================================================

// ---------------------------------------------------------------------------
// Primitives / Enums
// ---------------------------------------------------------------------------

export type QuestionType =
    | 'fill_in_the_blank'
    | 'radio_button'
    | 'drag_and_drop'
    | 'multiple_choice';

export type DifficultyLevel = 'beginner' | 'medium' | 'hard';

export type JenisKonten = 'teori' | 'sintaks' | 'mixed';

// ---------------------------------------------------------------------------
// Role
// ---------------------------------------------------------------------------

export interface Role {
    id: number;
    role_name: string;
}

// ---------------------------------------------------------------------------
// User
// ---------------------------------------------------------------------------

export interface User {
    id: number;
    name: string;
    email: string;
    nim: string | null;
    class: string | null;
    role_id: number;
    is_approved: boolean;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    role?: Role;
}

// ---------------------------------------------------------------------------
// Material
// ---------------------------------------------------------------------------

export interface Material {
    id: number;
    title: string;
    description: string | null;
    content: string | null;
    cover_image: string | null;
    level: DifficultyLevel;
    status: 'draft' | 'published';
    module_id: number | null;
    is_final_project?: boolean;
    is_locked?: boolean;
    created_by: number | null;
    created_at: string;
    updated_at: string;
    sub_materials?: SubMaterial[];
    questions?: Question[];
    media?: Media[];
    // Computed / appended fields from controllers
    progress?: number;
    creator?: User;
    total_questions?: number;
    student_count?: number;
    progress_percentage?: number;
    completed_questions?: number;
}

// ---------------------------------------------------------------------------
// SubMaterial
// ---------------------------------------------------------------------------

export interface SubMaterial {
    id: number;
    material_id: number;
    title: string;
    content: string;
    jenis_konten: JenisKonten;
    learning_style: string | null;
    order: number;
    created_at: string;
    updated_at: string;
    material?: Material;
    questions?: Question[];
}

// ---------------------------------------------------------------------------
// Media
// ---------------------------------------------------------------------------

export interface Media {
    id: number;
    material_id: number;
    /** Raw stored path/URL from DB column */
    media_url: string;
    media_type?: string;
    file_path?: string;
    file_type?: string;
    title: string | null;
    created_at: string;
    updated_at: string;
    /** Accessor: absolute public URL (computed by PHP getFullUrlAttribute) */
    full_url: string;
}

// ---------------------------------------------------------------------------
// Question
// ---------------------------------------------------------------------------

export interface Question {
    id: number;
    material_id: number;
    sub_material_id: number | null;
    question_text: string;
    question_type: QuestionType;
    difficulty: DifficultyLevel;
    hint: string | null;
    created_by: number | null;
    created_at: string;
    updated_at: string;
    answers?: Answer[];
}

// ---------------------------------------------------------------------------
// Answer
// ---------------------------------------------------------------------------

export interface Answer {
    id: number;
    question_id: number;
    answer_text: string | null;
    is_correct: boolean;
    explanation: string | null;
    drag_source: string | null;
    drag_target: string | null;
    blank_position: number | null;
    created_at: string;
    updated_at: string;
}

// ---------------------------------------------------------------------------
// QuizAttempt
// ---------------------------------------------------------------------------

export interface QuizAttempt {
    id: number;
    user_id: number;
    question_id: number;
    material_id: number;
    difficulty: DifficultyLevel;
    is_correct: boolean;
    time_spent: number;
    hint_used: boolean;
    created_at: string;
    updated_at: string;
}

// ---------------------------------------------------------------------------
// StudentState — Gamification / Adaptive data (JSON columns)
// ---------------------------------------------------------------------------

export interface GamificationData {
    global_xp: number;
    current_level: string;
    current_streak: number;
    max_streak: number;
    badges: string[];
}

export interface PerformanceMetrics {
    total_questions_answered: number;
    correct_count: number;
    wrong_count: number;
    wrong_streak: number;
    hints_used_count: number;
    hints_available: number;
}

export interface TimeMetrics {
    avg_time_per_question: number;
    total_time_spent: number;
}

export interface LearningProfile {
    learning_style: string;
    mastery_levels: Record<string, number>;
    unlocked_modules: number[];
}

export interface AdaptiveState {
    fast_track_active: boolean;
    current_material_id: number | null;
    target_difficulty: string | null;
    last_rule: string | null;
    time_metrics: TimeMetrics;
    module_progress: Record<string, unknown>;
}

export interface StudentState {
    id: number;
    user_id: number;
    gamification_data: GamificationData;
    performance_metrics: PerformanceMetrics;
    learning_profile: LearningProfile;
    adaptive_state: AdaptiveState;
    created_at: string;
    updated_at: string;
    user?: User;
    // Virtual / @property-read accessors
    global_xp: number;
    current_level: string;
    current_streak: number;
    max_streak: number;
    total_questions_answered: number;
    correct_count: number;
    wrong_count: number;
    hints_available: number;
    learning_style: string;
}

// ---------------------------------------------------------------------------
// UeqSurvey
// ---------------------------------------------------------------------------

export interface UeqSurvey {
    id: number;
    user_id: number;
    nim: string | null;
    class: string | null;
    // Scale items (1–7 Likert scale)
    annoying_enjoyable: number;
    not_understandable_understandable: number;
    creative_dull: number;
    easy_difficult: number;
    valuable_inferior: number;
    boring_exciting: number;
    not_interesting_interesting: number;
    unpredictable_predictable: number;
    fast_slow: number;
    inventive_conventional: number;
    obstructive_supportive: number;
    good_bad: number;
    complicated_easy: number;
    unlikable_pleasing: number;
    usual_leading_edge: number;
    unpleasant_pleasant: number;
    secure_not_secure: number;
    motivating_demotivating: number;
    meets_expectations_does_not: number;
    inefficient_efficient: number;
    clear_confusing: number;
    impractical_practical: number;
    organized_cluttered: number;
    attractive_unattractive: number;
    friendly_unfriendly: number;
    conservative_innovative: number;
    comments: string | null;
    suggestions: string | null;
    created_at: string;
    updated_at: string;
    user?: User;
}

// ---------------------------------------------------------------------------
// Dashboard / Services — Computed data shapes (not DB models)
// ---------------------------------------------------------------------------

/** Per-difficulty breakdown returned by DashboardService.processMaterialsWithStats */
export interface DifficultyStats {
    correct: number;
    total: number;
    configured_total: number;
    percentage: number;
}

/** One entry in materialsWithStats array from DashboardService */
export interface MaterialWithStats {
    material: Material;
    stats: {
        overall: DifficultyStats;
        beginner: DifficultyStats;
        medium: DifficultyStats;
        hard: DifficultyStats;
    };
}

/** Recent activity item returned by ProgressRepository.getRecentActivities */
export interface RecentActivity {
    id?: number;
    material_id: number;
    material_title?: string;
    difficulty: DifficultyLevel | string;
    is_correct: boolean;
    total_correct?: number;
    time_ago?: string;
    created_at?: string;
    /** Computed by DashboardService: 'achievement' | 'milestone' | 'progress' */
    type: 'achievement' | 'milestone' | 'progress';
}

/**
 * Flattened learning data returned by ProfileController.show().
 * Built from StudentState accessors — NOT the nested LearningProfile sub-object.
 */
export interface StudentProfile {
    learning_style: string;
    current_level: string;
    global_xp: number;
    current_streak: number;
    max_streak: number;
    total_questions_answered: number;
    correct_count: number;
    wrong_count: number;
    hints_used_count: number;
    hints_available: number;
    accuracy: number;
    fast_track_active: boolean;
}

/** One row in the leaderboard from LeaderboardService.processLeaderboardData */
export interface LeaderboardEntry {
    id: number;
    name: string;
    rank: number;
    total_correct_questions: number;
    hard_completed: number;
    medium_completed: number;
    beginner_completed: number;
    weighted_score: number;
    formatted_score: string;
    percentage: number;
    badge: string;
    badge_color: string;
}

/** A single recorded attempt on a question (nested inside QuestionWithAttempt) */
export interface UserAttempt {
    is_correct: boolean;
    answer_id: number;
    attempt_number: number;
    score: number;
}

/** Question augmented with the student's attempt data — used on the Review page */
export interface QuestionWithAttempt extends Question {
    user_attempt: UserAttempt | null;
}

/**
 * Serialised StudentState sent to the quiz page via Inertia.
 * Uses Pick<> to stay in sync with the base data interfaces.
 */
export interface QuizSessionState {
    gamification: Pick<
        GamificationData,
        'global_xp' | 'current_level' | 'current_streak' | 'max_streak'
    >;
    performance: Pick<
        PerformanceMetrics,
        'total_questions_answered' | 'correct_count' | 'wrong_count' | 'hints_available'
    >;
    adaptive: Pick<AdaptiveState, 'fast_track_active' | 'last_rule'> &
        Pick<LearningProfile, 'learning_style'>;
}

export type CertificationType = 'gold' | 'silver' | 'bronze';

export interface Certification {
    material_id: string;
    material_title: string;
    type: CertificationType;
    issued_at: string | null;
}
