import type { AdaptiveResult } from './adaptive';
import type { Answer, DifficultyLevel } from './learning';

// =============================================================================
// API Payloads & Responses
// =============================================================================

export interface AnswerPayload {
    question_id: string;
    material_id?: string;
    answer_id?: string;
    answer?: string; // radio button
    fill_in_the_blank_answer?: string;
    drag_and_drop_answers?: string | Record<string, string>;
    user_response?: string;
    time_spent: number;
    used_hint: boolean;
    difficulty: DifficultyLevel;
}

export interface CheckAnswerResponse {
    status: string;
    message: string;
    next_url: string;
    is_correct: boolean;
    correct_answer?: Answer;
    explanation?: string;
    score?: number; // legacy
    xp_earned: number;
    adaptive_result: AdaptiveResult | null;
    next_question_id?: string | null;
    student_state?: any;
    ui?: {
        label?: string;
        title?: string;
        type?: string;
        message?: string;
    } | null;
}

export interface UseHintResponse {
    success: boolean;
    hint: string;
    hints_remaining: number;
}
