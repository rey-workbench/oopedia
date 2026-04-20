import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';
import type { User, SusResult } from '@/types';

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
        acceptability: ''
    });
    classes = $state<string[]>([]);
    activeClass = $state('');

    constructor(
        results: SusResult[],
        averages: { total: number; items: Record<string, number> },
        grading: { score: number; adjective: string; grade: string; acceptability: string },
        classes: string[],
        activeClass: string
    ) {
        super();
        this.results = results;
        this.averages = averages;
        this.grading = grading;
        this.classes = classes;
        this.activeClass = activeClass;
    }

    handleFilterChange(e: Event) {
        const select = e.target as HTMLSelectElement;
        router.get(
            ROUTES.ADMIN.SUS.INDEX,
            { class: select.value },
            { preserveState: true, replace: true }
        );
    }

    exportResults() {
        const url = this.activeClass
            ? `${ROUTES.ADMIN.SUS.EXPORT}?class=${this.activeClass}`
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
        total_score: 0
    });

    questions = [
        { id: 1, text: 'Saya rasa saya akan sering menggunakan sistem ini.' },
        { id: 2, text: 'Saya merasa sistem ini tidak perlu rumit.' },
        { id: 3, text: 'Saya rasa sistem ini mudah digunakan.' },
        { id: 4, text: 'Saya rasa saya membutuhkan bantuan orang teknis untuk dapat menggunakan sistem ini.' },
        { id: 5, text: 'Saya merasa berbagai fungsi dalam sistem ini terintegrasi dengan baik.' },
        { id: 6, text: 'Saya rasa terlalu banyak ketidakkonsistenan dalam sistem ini.' },
        { id: 7, text: 'Saya rasa kebanyakan orang akan belajar menggunakan sistem ini dengan sangat cepat.' },
        { id: 8, text: 'Saya merasa sistem ini sangat membosankan untuk digunakan.' },
        { id: 9, text: 'Saya merasa sangat percaya diri menggunakan sistem ini.' },
        { id: 10, text: 'Saya perlu belajar banyak hal sebelum saya dapat menggunakan sistem ini.' },
    ];

    constructor(user: User, result: SusResult, calculation: { item_scores: Record<string, number>; total_score: number }) {
        super();
        this.targetUser = user;
        this.result = result;
        this.calculation = calculation;
    }
}
