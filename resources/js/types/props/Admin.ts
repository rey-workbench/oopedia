import type { SharedProps } from './Shared';
import type { AdaptiveFact, AdaptiveAction, AdaptiveRule } from '../models/adaptive/Core';

/**
 * resources/js/types/props/Admin.ts
 */

export interface AdminAdaptiveRuleProps extends SharedProps {
    rule?: AdaptiveRule;
    all_facts: AdaptiveFact[];
    all_actions: AdaptiveAction[];
}
