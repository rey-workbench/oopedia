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
    domain: string;
    priority: number;
    action: string; // Primary action ID
    action_id: string;
    required_facts: string[];
    deduced_facts: string[];
    is_active: boolean;
}
