// =============================================================================
// Domain Model Types
// Mirrors the PHP Eloquent models in app/Models/
// =============================================================================

// ---------------------------------------------------------------------------
// Primitives / Enums
// ---------------------------------------------------------------------------

export type QuestionType = 'fill_in_the_blank' | 'radio_button' | 'drag_and_drop';

export type DifficultyLevel = 'beginner' | 'intermediate' | 'advanced' | 'final';

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
    status: "draft" | "published";
    module_id: number | null;
    created_by: number | null;
    created_at: string;
    updated_at: string;
    sub_materials?: SubMaterial[];
    questions?: Question[];
    media?: Media[];
    // Computed / appended fields from controllers
    progress?: number;
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
}

// ---------------------------------------------------------------------------
// Media
// ---------------------------------------------------------------------------

export interface Media {
    id: number;
    material_id: number;
    file_path: string;
    file_type: string;
    title: string | null;
    created_at: string;
    updated_at: string;
    /** Accessor: public URL to the stored file */
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
    current_module_id: number | null;
    last_rule: string | null;
    last_action: string | null;
    time_metrics: TimeMetrics;
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
