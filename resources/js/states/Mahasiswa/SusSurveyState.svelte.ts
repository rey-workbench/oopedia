import { FormState } from '@/states/FormState.svelte';
import type { SusSurveyForm } from '@/types';
import { SUS_QUESTIONS } from '@/constants/survey';
import { ROUTES } from '@/utils/route';

export class SusSurveyState extends FormState<SusSurveyForm> {
    questions = SUS_QUESTIONS;

    constructor(type: string = 'pre') {
        const initialFields: SusSurveyForm = {
            assessment_type: type,
            q1: 3,
            q2: 3,
            q3: 3,
            q4: 3,
            q5: 3,
            q6: 3,
            q7: 3,
            q8: 3,
            q9: 3,
            q10: 3,
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
