import type { AdaptiveAction, AdaptiveRule } from './Core';

/**
 * resources/js/types/models/adaptive/Engine.ts
 * Runtime Logic and API Responses
 */

export interface HydratedAction extends AdaptiveAction {
    value: string;
}

export interface TriggeredRule {
    rule: AdaptiveRule;
    actions: HydratedAction[];
}

export interface EngineResultMetadata {
    engine_version: string;
    iterations: number;
    rule_chain: string[];
    priority: number;
    total_rules_evaluated?: number;
    execution_time_ms?: number;
}

export interface EngineResult {
    id: string;
    diagnosis: string;
    recommendation: string;
    actions: string[];
    facts: string[];
    deduced_facts: string[];
    timestamp: string;
    engine_metadata: EngineResultMetadata;
}

export interface GuidanceCharacter {
    char: string;
    revealed: boolean;
}

export interface GuidanceData {
    type: 'tts_hint' | 'reduce_option' | 'autofill';
    characters?: GuidanceCharacter[];
    removed_option_ids?: string[];
    autofilled_id?: string;
}

export interface AdaptiveResult extends EngineResult {
    triggered_rule: TriggeredRule | null;
    triggered_rules: TriggeredRule[];
    show_guidance?: boolean;
    guidance_data?: GuidanceData | null;
}

/**
 * PerformanceService::getStudentSessionState() return shape
 * Used as flash('student_state') and student_states DB field
 */
export interface StudentSessionState {
    gamification: {
        xp: number;
        level: string;
        streak: number;
        badges: string[];
    };
    performance: {
        total_answered: number;
        correct_count: number;
        accuracy: number;
        hints_used: number;
    };
    adaptive_engine: {
        session_history: string[];
        current_session: string[];
        performance_metrics: Record<string, number>;
        adaptive_state: Record<string, unknown>;
    };
}

export interface ChallengeQuestion {
    id: string;
    content: string;
    type: string;
    options: {
        id: string;
        text: string;
        is_correct: boolean;
    }[];
}

export interface CheckAnswerResponse {
    status: 'success' | 'error';
    message: string;
    is_correct: boolean;
    xp_earned: number;
    adaptive_result: AdaptiveResult | null;
    challenge_question?: ChallengeQuestion | null;
    next_url: string | null;
    correct_answer?: { id: string; text: string } | null;
}
