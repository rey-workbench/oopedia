export interface AdaptiveFact {
    id: string;
    name: string;
    category: string;
    description: string;
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
    action_ids: string[];
    required_fact_ids: string[];
    deduced_fact_ids: string[];
    is_active: boolean;
}
