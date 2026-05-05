import { FormState } from '@/states/FormState.svelte';
import type { SusSurveyForm } from '@/types';
import { SUS_QUESTIONS } from '@/constants/survey';
import { ROUTES } from '@/utils/route';

export class SusSurveyState extends FormState<SusSurveyForm> {
    questions = SUS_QUESTIONS;

    constructor(type: string = 'pre', user?: any) {
        const initialFields: SusSurveyForm = {
            assessment_type: type,
            nim: user?.nim || '',
            class: user?.class || '',
            answers: SUS_QUESTIONS.map(q => ({
                question_id: q.id,
                value: null,
            })),
            comments: '',
            suggestions: '',
        };
        super(initialFields);
    }

    submit() {
        this.submitForm('post', ROUTES.MAHASISWA.SUS.STORE, {
            scrollToError: true,
        });
    }
}
