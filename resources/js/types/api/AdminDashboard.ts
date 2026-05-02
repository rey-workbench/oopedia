import type { User } from '../models/User';
import type { Material } from '../models/Material';

/**
 * resources/js/types/api/AdminDashboard.ts
 */

export interface RecentProgressItem {
    id: string;
    user?: User;
    material?: {
        id: string | null;
        title: string | null;
    };
    progress: number;
    updated_at?: string;
    created_at: string;
}

export interface StudentProgressItem {
    id: string;
    user?: {
        id: string;
        name: string;
    };
    accuracy: number;
    correct_count: number;
    completed_materials: number;
    average_score: number;
}

export interface StudentAnalytics {
    distribution: Record<string, number>;
    module_performance: {
        labels: string[];
        data: number[];
    };
}

export interface StudentNeedingAttention {
    id: string;
    user: User;
    low_score_count: number;
    last_activity: string;
    student_state?: {
        adaptive_state?: Record<string, any>;
    };
}

export interface AdminDashboardData {
    user_name: string;
    user_role: string;
    total_students: number;
    total_materials: number;
    total_questions: number;
    active_students: number;
    recent_progress: RecentProgressItem[];
    student_progress: StudentProgressItem[];
    material_stats: Material[];
    popular_materials: Material[];
    student_analytics: StudentAnalytics;
    students_needing_attention: StudentNeedingAttention[];
}
