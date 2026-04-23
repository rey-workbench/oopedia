// =============================================================================
// Types for API Responses
// =============================================================================

export interface CheckAnswerResponse {
    status: 'success' | 'wrong' | 'error';
    message: string;
    nextUrl: string;
    adaptiveResult: AdaptiveResult;
    xpEarned?: number;
    newLevel?: string | null;
    streakBonus?: boolean;
}

export interface AdaptiveResult {
    triggered_rule?: {
        id?: string;
        name?: string;
        action?: string | null;
        priority?: number;
        variant?: string;
    } | null;
    triggered_rules?: Array<{
        id?: string;
        name?: string;
        action?: string | null;
        priority?: number;
        variant?: string;
    }>;
    facts?: string[];
    engine_metadata?: {
        rule_count: number;
        engine_version: string;
        fact_labels: Record<string, string>;
        fact_categories: Record<string, string>;
    } | null;
    global_xp_earned?: number;
    streak_bonus?: string | null;
    new_state?: {
        next_action?: string | null;
        next_action_data?: {
            label?: string;
            url?: string;
            type?: string;
        } | null;
        recommendation?: string | null;
        certification?: string | null;
        intervention_type?: string | null;
        recovery_type?: string | null;
        fast_track_active?: boolean;
        message?: string | null;
        title?: string | null;
    } | null;
}

export interface AdaptiveFact {
    key: string;
    value: string | number | boolean | null;
    label: string;
}

export interface UseHintResponse {
    success: boolean;
    hint: string | null;
    hintsRemaining: number;
    message?: string;
}
