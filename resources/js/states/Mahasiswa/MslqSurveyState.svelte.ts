import { FormState } from '@/states/FormState.svelte';
import { router } from '@inertiajs/svelte';
import { ROUTES } from '@/utils/route';
import type { MslqForm, MslqQuestion } from '@/types';

export class MslqSurveyState extends FormState<MslqForm> {
    questions = $state<MslqQuestion[]>([]);

    constructor(questions: MslqQuestion[], type: string = 'pre') {
        super(MslqSurveyState.createInitialFields(questions, type));
        this.questions = questions;
        this.hydrate({ questions });
    }

    private static createInitialFields(questions: MslqQuestion[], type: string): MslqForm {
        return {
            assessment_type: type,
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
