import { BaseState } from '@/states/BaseState.svelte';
import type {
    AdaptiveRuleDiagnosis,
    AdaptiveStateDistribution,
    AdaptiveTriggerItem,
    AdaptiveRuleTriggerStat,
    AdaptiveFact,
    AdaptiveAction,
} from '@/types';

export class AdaptiveRuleState extends BaseState {
    total_rules = $state(0);
    total_facts = $state(0);
    total_actions = $state(0);
    rules_by_diagnosis = $state<AdaptiveRuleDiagnosis[]>([]);
    adaptive_state_distribution = $state<AdaptiveStateDistribution[]>([]);
    recent_triggers = $state<AdaptiveTriggerItem[]>([]);
    rule_triggers_stats = $state<AdaptiveRuleTriggerStat[]>([]);
    decision_tree = $state<any>(null);
    all_facts = $state<AdaptiveFact[]>([]);
    all_actions = $state<AdaptiveAction[]>([]);

    constructor(data: any) {
        super();
        this.hydrate(data);
    }

    maxTriggerCount = $derived(
        Math.max(1, ...this.rule_triggers_stats.map((r) => r.trigger_count))
    );

    maxStateCount = $derived(Math.max(1, ...this.adaptive_state_distribution.map((s) => s.count)));
}
