import type { AppRole } from '../core';

/**
 * resources/js/types/models/User.ts
 */

export interface Role {
    id: string;
    role_name: AppRole;
    created_at: string;
    updated_at: string;
}

export interface User {
    id: string;
    name: string;
    email: string;
    nim?: string;
    class?: string;
    is_approved: boolean;
    role?: {
        role_name: AppRole;
    };
    overall_progress?: number;
    total_answered?: number;
    last_active?: string;
    created_at?: string;
    updated_at?: string;
}

export interface LearningPersonalization {
    learning_style?: 'deep' | 'motivated' | 'strategic' | 'balanced' | 'unknown';
    learning_profile_label?: string;
    mslq_filled?: boolean;
    total_motivation?: number | null;
    total_strategy?: number | null;
    current_level?: string;
    global_xp?: number;
    current_streak?: number;
    max_streak?: number;
    total_questions_answered?: number;
    correct_count?: number;
    wrong_count?: number;
    hints_used_count?: number;
    hints_available?: number;
    accuracy?: number;
    last_diagnosis?: string | null;
    active_interventions?: string[];
    needs_remedial?: boolean;
    target_difficulty?: string;
}

export interface StudentProfile extends User {
    nim: string;
    phone?: string;
    class?: string;
    avatar_url?: string;
    personalization?: LearningPersonalization;
}

export interface Certification {
    material_id: string;
    material_title: string;
    type: string;
    issued_at: string | null;
}
