import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';

export class MslqState extends BaseState {
    results = $state<any[]>([]);
    averages = $state<Record<string, number>>({});
    avgMotivation = $state(0);
    avgStrategy = $state(0);
    classes = $state<string[]>([]);
    activeClass = $state('');

    constructor(
        results: any[],
        metrics: { averages: Record<string, number>; avg_motivation: number; avg_strategy: number },
        classes: string[],
        activeClass: string
    ) {
        super();
        this.hydrate({
            results,
            averages: metrics.averages,
            avgMotivation: metrics.avg_motivation,
            avgStrategy: metrics.avg_strategy,
            classes,
            activeClass,
        });
    }

    handleFilterChange(value: string | number) {
        router.get(
            ROUTES.ADMIN.MSLQ.INDEX,
            { class: String(value) },
            { preserveState: true, replace: true }
        );
    }

    exportResults() {
        window.location.href =
            ROUTES.ADMIN.MSLQ.EXPORT + (this.activeClass ? `?class=${this.activeClass}` : '');
    }
}
