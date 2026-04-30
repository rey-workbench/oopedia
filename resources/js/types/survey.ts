// =============================================================================
// MSLQ (Motivated Strategies for Learning Questionnaire)
// =============================================================================

export interface MslqQuestion {
    id: number;
    text: string;
    order: number;
    scale: string;
    is_reverse: boolean;
}

export interface MslqAnswer {
    question_id: number;
    value: number;
    question: MslqQuestion;
}

export interface MslqResult {
    id: string;
    user_id: string;
    user: { name: string };
    nim?: string;
    class?: string;
    total_motivation: number;
    total_strategy: number;
    dimension_scores: Record<string, number>;
    scores_by_scale: Record<string, number>;
    answers: MslqAnswer[];
    created_at: string;
}

// =============================================================================
// SUS (System Usability Scale)
// =============================================================================

export interface SusResult {
    id: string;
    user_id: string;
    nim?: string;
    class?: string;
    scores: number[];
    total_score: number;
    feedback: string | null;
    comments?: string | null;
    suggestions?: string | null;
    created_at: string;
}

// =============================================================================
// UEQ (User Experience Questionnaire)
// =============================================================================

export interface UeqSurvey {
    id: string;
    user_id: string;
    nim?: string;
    class?: string;
    attractiveness: number;
    perspicuity: number;
    efficiency: number;
    dependability: number;
    stimulation: number;
    novelty: number;
    feedback: string | null;
    comments?: string | null;
    suggestions?: string | null;
    created_at: string;
}

// =============================================================================
// Survey Forms
// =============================================================================

export interface ProfileForm {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

export interface SusSurveyForm {
    nim: string;
    class: string;
    q1: number;
    q2: number;
    q3: number;
    q4: number;
    q5: number;
    q6: number;
    q7: number;
    q8: number;
    q9: number;
    q10: number;
    comments: string;
    suggestions: string;
}

export interface UeqSurveyForm {
    nim: string;
    class: string;
    [key: string]: number | string;
}

export interface UeqAverages {
    attractiveness: number;
    perspicuity: number;
    efficiency: number;
    dependability: number;
    stimulation: number;
    novelty: number;
    [key: string]: number;
}
