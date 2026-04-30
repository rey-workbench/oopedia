// =============================================================================
// OOPedia Unified Type System
// =============================================================================

export * from './core';
export * from './auth';
export * from './learning';
export * from './adaptive';
export * from './analytics';
export * from './survey';
export * from './props';
export * from './api';

// =============================================================================
// Helper Types & UI States
// =============================================================================

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

export interface LevelItem {
    level: number;
    status: 'completed' | 'in_progress' | 'locked';
    question_id?: number;
    difficulty?: import('./core').DifficultyLevel;
    [key: string]: unknown;
}
