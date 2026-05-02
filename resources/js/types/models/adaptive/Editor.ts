/**
 * resources/js/types/models/adaptive/Editor.ts
 * Editor UI State (Form DTOS)
 */

export interface AdaptiveRuleFactItem {
    id: string;
    key?: string;
    name?: string;
    operator: string;
    value: string | number;
    isManual?: boolean;
}

export interface AdaptiveRuleDeductionItem {
    id: string;
    name?: string;
    isManual?: boolean;
}

export interface AdaptiveRuleActionItem {
    id: string;
    metadata: Record<string, any>;
}

export interface AdaptiveRuleForm {
    name: string;
    recommendation: string;
    priority: number;
    is_active: boolean;
    facts: AdaptiveRuleFactItem[];
    deduced_facts: AdaptiveRuleDeductionItem[];
    actions: AdaptiveRuleActionItem[];
}
