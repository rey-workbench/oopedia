import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';
import type { User, UeqSurvey, UeqAverages } from '@/types';

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
        this.surveys = surveys;
        this.averages = averages;
        this.classes = classes;
        this.activeClass = activeClass;
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

    aspects = [
        { name: 'annoying_enjoyable', left: 'Menyebalkan', right: 'Menyenangkan' },
        {
            name: 'not_understandable_understandable',
            left: 'Tidak dipahami',
            right: 'Dapat dipahami',
        },
        { name: 'creative_dull', left: 'Kreatif', right: 'Monoton' },
        { name: 'easy_difficult', left: 'Mudah', right: 'Sulit' },
        { name: 'valuable_inferior', left: 'Bermanfaat', right: 'Inferior' },
        { name: 'boring_exciting', left: 'Membosankan', right: 'Menarik' },
        { name: 'not_interesting_interesting', left: 'Tidak menarik', right: 'Menarik' },
        { name: 'unpredictable_predictable', left: 'Unpredictable', right: 'Predictable' },
        { name: 'fast_slow', left: 'Cepat', right: 'Lambat' },
        { name: 'inventive_conventional', left: 'Inovatif', right: 'Konvensional' },
        { name: 'obstructive_supportive', left: 'Menghambat', right: 'Mendukung' },
        { name: 'good_bad', left: 'Baik', right: 'Buruk' },
        { name: 'complicated_easy', left: 'Rumit', right: 'Sederhana' },
        { name: 'unlikable_pleasing', left: 'Unlikable', right: 'Pleasing' },
        { name: 'usual_leading_edge', left: 'Biasa saja', right: 'Terdepan' },
        { name: 'unpleasant_pleasant', left: 'Unpleasant', right: 'Pleasant' },
        { name: 'secure_not_secure', left: 'Aman', right: 'Tidak aman' },
        { name: 'motivating_demotivating', left: 'Memotivasi', right: 'Demotivating' },
        { name: 'meets_expectations_does_not_meet', left: 'Meets Expect.', right: "Doesn't Meet" },
        { name: 'inefficient_efficient', left: 'Tidak efisien', right: 'Efisien' },
        { name: 'clear_confusing', left: 'Jelas', right: 'Membingungkan' },
        { name: 'impractical_practical', left: 'Tidak praktis', right: 'Praktis' },
        { name: 'organized_cluttered', left: 'Terorganisir', right: 'Berantakan' },
        { name: 'attractive_unattractive', left: 'Menarik', right: 'Tidak menarik' },
        { name: 'friendly_unfriendly', left: 'Ramah', right: 'Tidak ramah' },
        { name: 'conservative_innovative', left: 'Konservatif', right: 'Inovatif' },
    ];

    dimensions = $derived.by(() => {
        if (!this.survey) return {};
        return {
            'Daya Tarik':
                (this.survey.annoying_enjoyable +
                    this.survey.good_bad +
                    this.survey.unlikable_pleasing +
                    this.survey.unpleasant_pleasant +
                    this.survey.attractive_unattractive +
                    this.survey.friendly_unfriendly) /
                6,
            Kejelasan:
                (this.survey.not_understandable_understandable +
                    this.survey.easy_difficult +
                    this.survey.complicated_easy +
                    this.survey.clear_confusing) /
                4,
            Efisiensi:
                (this.survey.fast_slow +
                    this.survey.inefficient_efficient +
                    this.survey.impractical_practical +
                    this.survey.organized_cluttered) /
                4,
            Ketepatan:
                (this.survey.unpredictable_predictable +
                    this.survey.secure_not_secure +
                    this.survey.meets_expectations_does_not_meet) /
                3,
            Stimulasi:
                (this.survey.valuable_inferior +
                    this.survey.boring_exciting +
                    this.survey.not_interesting_interesting +
                    this.survey.motivating_demotivating) /
                4,
            Kebaruan:
                (this.survey.creative_dull +
                    this.survey.inventive_conventional +
                    this.survey.usual_leading_edge +
                    this.survey.conservative_innovative) /
                4,
        };
    });

    constructor(user: User, survey: UeqSurvey) {
        super();
        this.targetUser = user;
        this.survey = survey;
    }
}
