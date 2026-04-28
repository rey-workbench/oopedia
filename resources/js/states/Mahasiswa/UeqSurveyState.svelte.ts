import { FormState } from '@/states/FormState.svelte';
import type { UeqSurveyForm } from '@/types';
import { UEQ_ASPECTS } from '@/constants/survey';

export class UeqSurveyState extends FormState<UeqSurveyForm> {
    aspects = $state<{ name: string }[]>([]);
    questionnaireAspects = UEQ_ASPECTS;

    constructor(aspects: { name: string }[]) {
        const initialFields: UeqSurveyForm = {
            nim: '',
            class: '',
            comments: '',
            suggestions: '',
        };
        for (const a of aspects) {
            if (a.name) initialFields[a.name as string] = null;
        }
        super(initialFields);
        this.aspects = aspects;
    }

    submit() {
        this.submitForm('post', '/mahasiswa/surveys/ueq', {
            scrollToError: true,
        });
    }
}
