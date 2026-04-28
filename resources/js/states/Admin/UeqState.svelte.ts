import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';
import type { User, UeqSurvey, UeqAverages } from '@/types';
import { UEQ_ASPECTS, UEQ_DIMENSIONS } from '@/constants/survey';

/**
 * UEQ List State
 */
export class UeqListState extends BaseState {
    surveys = $state<UeqSurvey[]>([]);
    averages = $state<UeqAverages>({});
    classes = $state<string[]>([]);
    activeClass = $state('');

    constructor(
        surveys: UeqSurvey[],
        averages: UeqAverages,
        classes: string[],
        activeClass: string
    ) {
        super();
        this.hydrate({ surveys, averages, classes, activeClass });
    }

    handleFilterChange(e: Event) {
        const select = e.target as HTMLSelectElement;
        router.get(
            ROUTES.ADMIN.UEQ.INDEX,
            { class: select.value },
            { preserveState: true, replace: true }
        );
    }

    exportResults() {
        const url = this.activeClass
            ? `${ROUTES.ADMIN.UEQ.EXPORT}?class=${this.activeClass}`
            : ROUTES.ADMIN.UEQ.EXPORT;
        window.location.href = url;
    }
}

/**
 * UEQ Detail State
 */
export class UeqDetailState extends BaseState {
    targetUser = $state<User>({} as User);
    survey = $state<UeqSurvey>({} as UeqSurvey);

    aspects = UEQ_ASPECTS;

    dimensions = $derived.by(() => {
        if (!this.survey) return {};
        const results: Record<string, number> = {};
        
        for (const [dimension, fields] of Object.entries(UEQ_DIMENSIONS)) {
            const sum = fields.reduce((acc, field) => acc + (Number(this.survey[field as keyof UeqSurvey]) || 0), 0);
            results[dimension] = sum / fields.length;
        }
        
        return results;
    });

    constructor(user: User, survey: UeqSurvey) {
        super();
        this.targetUser = user;
        this.survey = survey;
    }
}
