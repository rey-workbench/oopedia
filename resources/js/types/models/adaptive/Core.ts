/**
 * resources/js/types/models/adaptive/Core.ts
 * Pure Database Model Shapes
 */

export interface AdaptiveFact {
    id: string;
    name: string;
    category: string;
    logic: string | Record<string, unknown> | null;
}

export interface AdaptiveAction {
    id: string;
    name: string;
    description: string | null;
    variant: string;
}

export interface AdaptiveRule {
    id: string;
    name: string;
    recommendation: string;
    priority: number;
    is_active: boolean;
    required_fact_ids: string[];
    deduced_fact_ids: string[];
    actions: string[];
}
