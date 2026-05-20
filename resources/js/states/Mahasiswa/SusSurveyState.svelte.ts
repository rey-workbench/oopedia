import { FormState } from '@/states/FormState.svelte';
import type { SusSurveyForm } from '@/types';
import { SUS_QUESTIONS } from '@/constants/survey';
import { ROUTES } from '@/utils/route';

export class SusSurveyState extends FormState<SusSurveyForm> {
    questions: any[];

    constructor(type: string = 'post', user?: any, questions: any[] = []) {
        const initialFields: SusSurveyForm = {
            assessment_type: type,
            nim: user?.nim || '',
            class: user?.class || '',
            answers: questions.map((q) => ({
                question_id: q.id,
                value: null,
            })),
            comments: '',
            suggestions: '',
        };
        super(initialFields);
        this.questions = questions;
    }

    submit() {
        this.submitForm('post', ROUTES.MAHASISWA.SUS.STORE, {
            scrollToError: true,
        });
    }
}
