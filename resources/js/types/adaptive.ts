// =============================================================================
// Adaptive Engine Domain (Synced with App\Models\Adaptive*)
// =============================================================================

export interface AdaptiveFact {
    id: string;
    name: string;
    category: string;
    logic: string | null;
}

export interface AdaptiveAction {
    id: string;
    name: string;
    description: string;
    variant: string | null;
    instructions: Record<string, any>;
}

export interface AdaptiveRule {
    id: string;
    name: string;
    recommendation: string;
    priority: number;
    actions: {
        id: string;
        metadata: Record<string, any>;
    }[];
    required_fact_ids: string[];
    deduced_fact_ids: string[];
    is_active: boolean;

    // Accessors
    action_models?: AdaptiveAction[];
    required_facts?: AdaptiveFact[];
    deduced_facts?: AdaptiveFact[];
}

export interface AdaptiveResult {
    id: string;
    diagnosis: string;
    recommendation: string;
    recommendations: Array<{
        id: string;
        metadata: Record<string, any>;
    }>;
    facts: string[];
    deduced_facts: string[];
    timestamp: string;
    triggered_rule?: {
        id: string;
        name: string;
        action: string | null;
        priority: number;
        variant: string;
        message: string;
        title: string;
    } | null;
    triggered_rules?: Array<{
        id: string;
        name: string;
        action: string | null;
        priority: number;
        variant: string;
    }>;
    engine_metadata: {
        engine_version: string;
        iterations: number;
        rule_chain: string[];
        priority: number;
        fact_labels?: Record<string, string>;
        rule_count?: number;
    };
    new_state?: {
        title: string;
        [key: string]: any;
    };
}

export interface AdaptiveRuleDiagnosis {
    diagnosis_name: string;
    count: number;
    rules: AdaptiveRule[];
}

export interface AdaptiveStateDistribution {
    difficulty: string;
    count: number;
}

export interface AdaptiveTriggerItem {
    id: string;
    rule_id: string;
    rule_name: string;
    action: string;
    user_name: string;
    material_title?: string;
    created_at: string;
}

export interface AdaptiveRuleTriggerStat {
    rule_id: string;
    rule_name: string;
    trigger_count: number;
}
