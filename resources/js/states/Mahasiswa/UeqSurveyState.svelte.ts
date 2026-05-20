import { FormState } from '@/states/FormState.svelte';
import type { UeqSurveyForm } from '@/types';
import { UEQ_ASPECTS } from '@/constants/survey';
import { ROUTES } from '@/utils/route';

export class UeqSurveyState extends FormState<UeqSurveyForm> {
    aspects = $state<{ name: string }[]>([]);
    questionnaireAspects = UEQ_ASPECTS;

    constructor(aspects: { name: string }[], assessmentType: string = 'pre', user?: any) {
        super(UeqSurveyState.createInitialFields(aspects, assessmentType, user));
        this.aspects = aspects;
    }

    private static createInitialFields(
        aspects: { name: string }[],
        assessmentType: string,
        user?: any
    ): UeqSurveyForm {
        const fields: UeqSurveyForm = {
            assessment_type: assessmentType,
            nim: user?.nim || '',
            class: user?.class || '',
            answers: aspects.map((a) => ({
                question_id: a.name,
                value: null,
            })),
            comments: '',
            suggestions: '',
            errors: {},
            processing: false,
        };

        return fields;
    }

    submit() {
        this.submitForm('post', ROUTES.MAHASISWA.UEQ.STORE, {
            scrollToError: true,
        });
    }
}
