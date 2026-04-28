import type { DifficultyLevel } from './primitives';
import type { Material } from './material';
import type { Question } from './question';
import type {
    PerformanceMetrics,
    AdaptiveState,
} from './studentState';
import type { CertificationType } from './primitives';

export interface DifficultyStats {
    correct: number;
    total: number;
    configured_total: number;
    percentage: number;
}

export interface MaterialWithStats {
    material: Material;
    stats: {
        overall: DifficultyStats;
        beginner: DifficultyStats;
        medium: DifficultyStats;
        hard: DifficultyStats;
    };
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
    // Real adaptive engine data
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

export interface UserAttempt {
    is_correct: boolean;
    answer_id: string;
    attempt_number: number;
    score: number;
}

export type QuestionWithAttempt = Question & {
    user_attempt: UserAttempt | null;
};

export interface QuizSessionState {
    accuracy: number;
    xp: number;
    streak: number;
    level: string;
    hints_available: number;
    target_difficulty: DifficultyLevel | null;
    adaptive_state: AdaptiveState;
    performance_metrics: PerformanceMetrics;
}

export interface Certification {
    material_id: string;
    material_title: string;
    type: CertificationType;
    issued_at: string | null;
}
