import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';

export class MslqListState extends BaseState {
    results = $state<any[]>([]);
    averages = $state<Record<string, number>>({});
    classes = $state<string[]>([]);
    activeClass = $state('');

    constructor(
        results: any[],
        averages: Record<string, number>,
        classes: string[],
        activeClass: string
    ) {
        super();
        this.results = results;
        this.averages = averages;
        this.classes = classes;
        this.activeClass = activeClass;
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
