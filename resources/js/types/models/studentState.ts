import type { LearningStyle, DifficultyLevel } from './primitives';

export interface GamificationData {
    global_xp: number;
    current_level: string;
    current_streak: number;
    max_streak: number;
    badges: string[];
}

export interface PerformanceMetrics {
    speed?: 'slow' | 'normal' | 'fast';
    trend?: 'up' | 'down' | 'stable';
    stagnant_count: number;
    total_questions_answered?: number;
    correct_count?: number;
}

export interface TimeMetrics {
    avg_time_per_question: number;
    total_time_spent: number;
}

export interface LearningProfile {
    learning_style: LearningStyle;
    unlocked_modules: string[];
    certifications: string[];
}

export interface AdaptiveState {
    fast_track_active: boolean;
    current_material_id: string | null;
    target_difficulty: DifficultyLevel | null;
    module_progress: Record<string, unknown>;
    time_metrics: TimeMetrics;
    scaffold_mode?: 'minimal' | 'normal';
    last_diagnosis?: string;
    active_interventions?: string[];
}
