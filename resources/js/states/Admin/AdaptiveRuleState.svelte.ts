import { BaseState } from '@/states/BaseState.svelte';
import type {
    AdaptiveRuleDiagnosis,
    AdaptiveStateDistribution,
    AdaptiveTriggerItem,
    AdaptiveRuleTriggerStat,
} from '@/types';
import type { AdaptiveFact, AdaptiveAction } from '@/types/models';

export class AdaptiveRuleState extends BaseState {
    totalRules = $state(0);
    totalFacts = $state(0);
    totalActions = $state(0);
    rulesByDiagnosis = $state<AdaptiveRuleDiagnosis[]>([]);
    adaptiveStateDistribution = $state<AdaptiveStateDistribution[]>([]);
    recentTriggers = $state<AdaptiveTriggerItem[]>([]);
    ruleTriggersStats = $state<AdaptiveRuleTriggerStat[]>([]);
    decisionTree = $state<any>(null);
    allFacts = $state<AdaptiveFact[]>([]);
    allActions = $state<AdaptiveAction[]>([]);

    constructor(data: any) {
        super();
        this.hydrate(data);
    }

    maxTriggerCount = $derived(Math.max(1, ...this.ruleTriggersStats.map((r) => r.trigger_count)));

    maxStateCount = $derived(Math.max(1, ...this.adaptiveStateDistribution.map((s) => s.count)));
}
