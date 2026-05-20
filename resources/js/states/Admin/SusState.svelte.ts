import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';
import type { User, SusResult } from '@/types';
import { SUS_QUESTIONS } from '@/constants/survey';

/**
 * SUS List State
 */
export class SusListState extends BaseState {
    results = $state<SusResult[]>([]);
    averages = $state<{ total: number; items: Record<string, number> }>({ total: 0, items: {} });
    grading = $state<{ score: number; adjective: string; grade: string; acceptability: string }>({
        score: 0,
        adjective: '',
        grade: '',
        acceptability: '',
    });
    types = $state<string[]>([]);
    activeType = $state('');

    constructor(
        results: SusResult[],
        averages: { total: number; items: Record<string, number> },
        grading: { score: number; adjective: string; grade: string; acceptability: string },
        types: string[],
        activeType: string
    ) {
        super();
        this.hydrate({ results, averages, grading, types, activeType });
    }

    handleFilterChange(value: string) {
        router.get(ROUTES.ADMIN.SUS.INDEX, { type: value }, { preserveState: true, replace: true });
    }

    exportResults() {
        const url = this.activeType
            ? `${ROUTES.ADMIN.SUS.EXPORT}?type=${this.activeType}`
            : ROUTES.ADMIN.SUS.EXPORT;
        window.location.href = url;
    }
}

/**
 * SUS Detail State
 */
export class SusDetailState extends BaseState {
    targetUser = $state<User>({} as User);
    result = $state<SusResult>({} as SusResult);
    calculation = $state<{ item_scores: Record<string, number>; total_score: number }>({
        item_scores: {},
        total_score: 0,
    });

    questions = SUS_QUESTIONS;

    constructor(
        user: User,
        result: SusResult,
        calculation: { item_scores: Record<string, number>; total_score: number }
    ) {
        super();
        this.hydrate({ targetUser: user, result, calculation });
    }
}
