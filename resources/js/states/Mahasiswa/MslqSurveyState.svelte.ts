import { FormState } from '@/states/FormState.svelte';
import { router } from '@inertiajs/svelte';
import { ROUTES } from '@/utils/route';

export interface MslqAnswerInput {
    question_id: string;
    value: number | null;
}

export interface MslqForm {
    nim: string;
    class: string;
    answers: MslqAnswerInput[];
}

export class MslqSurveyState extends FormState<MslqForm> {
    questions = $state<any[]>([]);

    constructor(questions: any[]) {
        const initialAnswers = questions.map((q) => ({
            question_id: q.id,
            value: null,
        }));

        super({
            nim: '',
            class: '',
            answers: initialAnswers,
        });

        this.questions = questions;
    }

    get progress() {
        const total = this.form.answers.length;
        const filled = this.form.answers.filter((a) => a.value !== null).length;
        return (filled / total) * 100;
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
