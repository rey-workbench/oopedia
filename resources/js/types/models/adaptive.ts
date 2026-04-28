export interface AdaptiveFact {
    id: number;
    code: string;
    name: string;
    category: string;
    description: string;
}

export interface AdaptiveAction {
    id: number;
    code: string;
    name: string;
    description: string;
    variant: string | null;
    instructions: Record<string, any>;
}

export interface AdaptiveRule {
    id: string | number; // This is rule_code for frontend identification
    real_id: number; // Database primary key
    code: string;
    name: string;
    domain: string;
    priority: number;
    action: string; // Action code (e.g. H10)
    action_id: number;
    required_facts: string[];
    forbidden_facts: string[] | null;
    deduced_facts: string[] | null;
    is_active: boolean;
}
