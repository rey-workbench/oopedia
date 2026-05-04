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
    averages = $state<Partial<UeqAverages>>({});
    types = $state<string[]>([]);
    activeType = $state('');

    constructor(
        surveys: UeqSurvey[],
        averages: Partial<UeqAverages>,
        types: string[],
        activeType: string
    ) {
        super();
        this.hydrate({ surveys, averages, types, activeType });
    }

    public handleFilterChange(value: string | number) {
        router.get(
            ROUTES.ADMIN.UEQ.INDEX,
            { type: String(value) },
            { preserveState: true, replace: true }
        );
    }

    public exportResults() {
        const url = this.activeType
            ? `${ROUTES.ADMIN.UEQ.EXPORT}?type=${this.activeType}`
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

    dimensions = $derived(this.calculateDimensions());

    constructor(user: User, survey: UeqSurvey) {
        super();
        this.targetUser = user;
        this.survey = survey;
    }

    private calculateDimensions(): Record<string, number> {
        if (!this.survey) return {};

        const results: Record<string, number> = {};

        for (const [dimension, fields] of Object.entries(UEQ_DIMENSIONS)) {
            results[dimension] = this.calculateAverageForDimension(fields as string[]);
        }

        return results;
    }

    private calculateAverageForDimension(fields: string[]): number {
        const sum = fields.reduce(
            (acc, field) => acc + (Number(this.survey[field as keyof UeqSurvey]) || 0),
            0
        );
        return sum / fields.length;
    }
}
