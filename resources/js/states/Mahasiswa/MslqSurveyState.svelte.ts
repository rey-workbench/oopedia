import { FormState } from '@/states/FormState.svelte';
import { router } from '@inertiajs/svelte';
import { ROUTES } from '@/utils/route';
import type { MslqForm, MslqQuestion } from '@/types';
import type { User } from '../../types/models/User';

export class MslqSurveyState extends FormState<MslqForm> {
    questions = $state<MslqQuestion[]>([]);

    constructor(questions: MslqQuestion[], type: string = 'pre', user?: User | null) {
        super(MslqSurveyState.createInitialFields(questions, type, user));
        this.questions = questions;
        this.hydrate({ questions });
    }

    private static createInitialFields(
        questions: MslqQuestion[],
        assessmentType: string,
        user?: User | null
    ): MslqForm {
        return {
            assessment_type: assessmentType,
            nim: user?.nim || '',
            class: user?.class || '',
            answers: questions.map((q) => ({
                question_id: q.id,
                value: null,
            })),
        };
    }

    get progress() {
        const total = this.form.answers.length;
        if (total === 0) return 0;

        const filledCount = this.form.answers.filter(
            (a: { value: number | null }) => a.value !== null
        ).length;
        return (filledCount / total) * 100;
    }

    submit() {
        this.submitForm('post', ROUTES.MAHASISWA.MSLQ.STORE, {
            scrollToError: true,
            onSuccess: () => {
                router.visit(ROUTES.MAHASISWA.MSLQ.THANK_YOU);
            },
        });
    }
}
