// =============================================================================
// Types for State Classes
// =============================================================================

import type { DifficultyLevel } from '@/types/models';
import type {
    RecentProgressItem,
    MaterialStatsItem,
    StudentProgressItem,
    PopularMaterialItem,
    StudentAnalytics,
} from './admin';

export interface FormStateOptions {
    isEdit?: boolean;
    showSuccessToast?: string | boolean;
    showErrorToast?: boolean;
}

export interface FormSubmitOptions {
    forceFormData?: boolean;
    _method?: string;
    onSuccess?: () => void;
    onError?: (errors: Record<string, string>) => void;
    onFinish?: () => void;
    showSuccessToast?: string | boolean;
    showErrorToast?: boolean;
    [key: string]: unknown;
}

export interface AdminDashboardData {
    totalStudents: number;
    totalMaterials: number;
    totalQuestions: number;
    activeStudents: number;
    recentProgress: RecentProgressItem[];
    studentProgress: StudentProgressItem[];
    popularMaterials: PopularMaterialItem[];
    studentAnalytics: StudentAnalytics;
    materialStats: MaterialStatsItem[];
}

export interface LevelItem {
    level: number;
    status: 'completed' | 'in_progress' | 'locked';
    question_id?: number;
    difficulty?: DifficultyLevel;
    [key: string]: unknown;
}

export interface Toast {
    id: string;
    message: string;
    type: 'success' | 'error' | 'info' | 'warning';
    duration?: number;
}
