import { BaseState } from '@/states/BaseState.svelte';
import type {
    AdaptiveRuleDomain,
    AdaptiveStateDistribution,
    AdaptiveTriggerItem,
    AdaptiveRuleTriggerStat,
} from '@/types';

export class AdaptiveAnalyticsState extends BaseState {
    totalRules = $state(0);
    totalFacts = $state(0);
    totalActions = $state(0);
    rulesByDomain = $state<AdaptiveRuleDomain[]>([]);
    adaptiveStateDistribution = $state<AdaptiveStateDistribution[]>([]);
    recentTriggers = $state<AdaptiveTriggerItem[]>([]);
    ruleTriggersStats = $state<AdaptiveRuleTriggerStat[]>([]);
    decisionTree = $state<any>(null);

    constructor(data: {
        totalRules: number;
        totalFacts: number;
        totalActions: number;
        rulesByDomain: AdaptiveRuleDomain[];
        adaptiveStateDistribution: AdaptiveStateDistribution[];
        recentTriggers: AdaptiveTriggerItem[];
        ruleTriggersStats: AdaptiveRuleTriggerStat[];
        decisionTree: any;
    }) {
        super();
        this.totalRules = data.totalRules;
        this.totalFacts = data.totalFacts;
        this.totalActions = data.totalActions;
        this.rulesByDomain = data.rulesByDomain;
        this.adaptiveStateDistribution = data.adaptiveStateDistribution;
        this.recentTriggers = data.recentTriggers;
        this.ruleTriggersStats = data.ruleTriggersStats;
        this.decisionTree = data.decisionTree;
    }

    domainIcons: Record<string, string> = {
        Safety: 'Shield',
        Project: 'Target',
        Achievement: 'Trophy',
        Recovery: 'RefreshCcw',
        Progression: 'TrendingUp',
        Interaction: 'Activity',
    };

    domainColors: Record<string, string> = {
        Safety: 'rose',
        Project: 'blue',
        Achievement: 'amber',
        Recovery: 'emerald',
        Progression: 'purple',
        Interaction: 'cyan',
    };

    maxTriggerCount = $derived(
        Math.max(1, ...this.ruleTriggersStats.map((r) => r.trigger_count))
    );

    maxStateCount = $derived(
        Math.max(1, ...this.adaptiveStateDistribution.map((s) => s.count))
    );
}
