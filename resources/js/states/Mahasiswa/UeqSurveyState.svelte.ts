import { FormState } from '@/states/FormState.svelte';
import type { UeqSurveyForm } from '@/types';
import { UEQ_ASPECTS } from '@/constants/survey';
import { ROUTES } from '@/utils/route';

export class UeqSurveyState extends FormState<UeqSurveyForm> {
    aspects = $state<{ name: string }[]>([]);
    questionnaireAspects = UEQ_ASPECTS;

    constructor(aspects: { name: string }[]) {
        super(UeqSurveyState.createInitialFields(aspects));
        this.aspects = aspects;
    }

    private static createInitialFields(aspects: { name: string }[]): UeqSurveyForm {
        const fields: UeqSurveyForm = {
            nim: '',
            class: '',
            comments: '',
            suggestions: '',
        };

        for (const aspect of aspects) {
            if (aspect.name) {
                fields[aspect.name] = 0;
            }
        }

        return fields;
    }

    submit() {
        this.submitForm('post', ROUTES.MAHASISWA.UEQ.STORE, {
            scrollToError: true,
        });
    }
}
