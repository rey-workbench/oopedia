import type { StudentLevel, DifficultyLevel } from './core';
import type { Material } from './learning';

// =============================================================================
// Student State & Performance (Synced with App\Models\StudentState)
// =============================================================================

export interface StudentPerformanceMetrics {
    trend: 'up' | 'down' | 'stable';
    speed: 'slow' | 'normal' | 'fast';
    stagnant_count: number;
}

export interface StudentCurrentSession {
    correct: number;
    total: number;
    hints: number;
    time_spent: number;
    question_ids: string[];
}

export interface StudentState {
    id: string;
    user_id: string;
    xp: number;
    level: StudentLevel;
    streak: number;
    max_streak: number;
    badges: string[];
    total_answered: number;
    correct_count: number;
    accuracy: number;
    hints_used: number;
    hints_available: number;
    session_history: number[];
    current_session: StudentCurrentSession;
    performance_metrics: StudentPerformanceMetrics;
    adaptive_state: Record<string, any>;
    current_material_id: string | null;
    target_difficulty: DifficultyLevel | null;
    last_active_at: string | null;
    created_at?: string;
    updated_at?: string;
}

// =============================================================================
// Analytics & Dashboards Shapes
// =============================================================================

export interface StudentProfile {
    learning_style: string;
    learning_profile_label: string;
    mslq_filled: boolean;
    total_motivation: number | null;
    total_strategy: number | null;
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
    last_diagnosis: string | null;
    active_interventions: string[];
    needs_remedial: boolean;
    target_difficulty: string;
}

export interface LeaderboardEntry {
    id: string;
    rank: number;
    name: string;
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

export interface RecentActivity {
    id?: string;
    material_id: string;
    material_title?: string;
    difficulty: DifficultyLevel | string;
    is_correct: boolean;
    total_correct?: number;
    time_ago?: string;
    created_at?: string;
    type: 'achievement' | 'milestone' | 'progress';
}

export interface StudentSessionState {
    accuracy: number;
    xp: number;
    streak: number;
    level: string;
    hints_available: number;
    target_difficulty: string | null;
    adaptive_state: Record<string, any>;
    performance_metrics: Record<string, any>;
}

export interface StudentNeedingAttention {
    id: string;
    name: string;
    email: string;
    student_state: StudentState;
}

export interface MaterialWithProgress {
    material: Material;
    progress: number;
    correct_count: number;
    total_questions: number;
}

export interface MissingQuestionsItem {
    id: string;
    question_text: string;
    difficulty: string;
    missing_count: number;
}
