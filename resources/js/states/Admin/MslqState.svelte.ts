import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';
import type { MslqResult } from '@/types';

export class MslqState extends BaseState {
    results = $state<MslqResult[]>([]);
    averages = $state<Record<string, number>>({});
    avgMotivation = $state(0);
    avgStrategy = $state(0);
    types = $state<string[]>([]);
    activeType = $state('');

    constructor(
        results: MslqResult[],
        metrics: { averages: Record<string, number>; avg_motivation: number; avg_strategy: number },
        types: string[],
        activeType: string
    ) {
        super();
        this.hydrate({
            results,
            averages: metrics.averages,
            avgMotivation: metrics.avg_motivation,
            avgStrategy: metrics.avg_strategy,
            types,
            activeType,
        });
    }

    handleFilterChange(value: string | number) {
        router.get(
            ROUTES.ADMIN.MSLQ.INDEX,
            { type: String(value) },
            { preserveState: true, replace: true }
        );
    }

    exportResults() {
        window.location.href =
            ROUTES.ADMIN.MSLQ.EXPORT + (this.activeType ? `?type=${this.activeType}` : '');
    }
}

/**
 * MSLQ Detail State
 */
export class MslqDetailState extends BaseState {
    result = $state<MslqResult>({} as MslqResult);

    constructor(result: MslqResult) {
        super();
        this.result = result;
    }

    get motivationScores() {
        const labels: Record<string, string> = {
            mslq_intrinsic_goal_orientation: 'Intrinsic Goal Orientation',
            mslq_extrinsic_goal_orientation: 'Extrinsic Goal Orientation',
            mslq_task_value: 'Task Value',
            mslq_control_of_learning_beliefs: 'Control of Learning Beliefs',
            mslq_self_efficacy_for_learning_performance: 'Self-Efficacy for Learning & Performance',
            mslq_test_anxiety: 'Test Anxiety',
        };

        return Object.entries(labels).map(([key, label]) => ({
            key,
            label,
            value: this.result.scores_by_scale[key as keyof typeof this.result.scores_by_scale] ?? 0,
        }));
    }

    get strategyScores() {
        const labels: Record<string, string> = {
            mslq_rehearsal: 'Rehearsal',
            mslq_elaboration: 'Elaboration',
            mslq_organization: 'Organization',
            mslq_critical_thinking: 'Critical Thinking',
            mslq_metacognitive_self_regulation: 'Metacognitive Self-Regulation',
            mslq_time_study_environment_management: 'Time & Study Environment Management',
            mslq_effort_regulation: 'Effort Regulation',
            mslq_peer_learning: 'Peer Learning',
            mslq_help_seeking: 'Help Seeking',
        };

        return Object.entries(labels).map(([key, label]) => ({
            key,
            label,
            value: this.result.scores_by_scale[key as keyof typeof this.result.scores_by_scale] ?? 0,
        }));
    }
}
