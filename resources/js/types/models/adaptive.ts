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
    id: string | number; // rule_code in some contexts
    real_id: number;     // database primary key
    rule_code: string;
    name: string;
    domain: string;
    priority: number;
    required_facts: string[];
    forbidden_facts: string[] | null;
    action_id: number;
    is_active: boolean;
}
