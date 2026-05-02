import type { AdaptiveRule } from './Core';

/**
 * resources/js/types/models/adaptive/Analytics.ts
 * Admin Dashboard and Analytics Shapes
 */

export interface AdaptiveTriggerItem {
    id: string;
    rule_id: string;
    rule_name: string;
    user_name: string;
    action_name: string;
    created_at: string;
}

export interface AdaptiveStateDistribution {
    state_key: string;
    value: string;
    count: number;
    difficulty?: string; // Added for UI compatibility
}

export interface AdaptiveRuleTriggerStat {
    rule_id: string;
    rule_name: string;
    trigger_count: number;
}

export interface AdaptiveRuleDiagnosis {
    diagnosis: string;
    count: number;
    rules: AdaptiveRule[]; // Added for ForwardChaining view
}

export interface DecisionTreeNode {
    id: string;
    type: 'rule' | 'fact';
    label: string;
    data?: AdaptiveRule;
}

export interface DecisionTreeEdge {
    id: string;
    source: string;
    target: string;
}

export interface AdaptiveDecisionTree {
    nodes: DecisionTreeNode[];
    edges: DecisionTreeEdge[];
}
