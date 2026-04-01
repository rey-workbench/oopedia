import type { User } from './user';
import type {
    GamificationData,
    PerformanceMetrics,
    LearningProfile,
    AdaptiveState,
} from './studentState';

export interface StudentState {
    id: string;
    user_id: string;
    gamification_data: GamificationData;
    performance_metrics: PerformanceMetrics;
    learning_profile: LearningProfile;
    adaptive_state: AdaptiveState;
    last_active_at: string | null;
    created_at: string;
    updated_at: string;
    user?: User;
    global_xp: number;
    current_level: string;
    current_streak: number;
    max_streak: number;
    total_questions_answered: number;
    correct_count: number;
    wrong_count: number;
    wrong_streak: number;
    hints_used_count: number;
    hints_available: number;
    learning_style: string;
    unlocked_modules: string[];
}
