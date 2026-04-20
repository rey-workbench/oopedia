import { FormState } from '@/states/FormState.svelte';
import type { SusSurveyForm } from '@/types';

export class SusSurveyState extends FormState<SusSurveyForm> {
    questions = [
        { id: 1, text: 'Saya rasa saya akan sering menggunakan sistem ini.' },
        { id: 2, text: 'Saya merasa sistem ini tidak perlu rumit.' },
        { id: 3, text: 'Saya rasa sistem ini mudah digunakan.' },
        { id: 4, text: 'Saya rasa saya membutuhkan bantuan orang teknis untuk dapat menggunakan sistem ini.' },
        { id: 5, text: 'Saya merasa berbagai fungsi dalam sistem ini terintegrasi dengan baik.' },
        { id: 6, text: 'Saya rasa terlalu banyak ketidakkonsistenan dalam sistem ini.' },
        { id: 7, text: 'Saya rasa kebanyakan orang akan belajar menggunakan sistem ini dengan sangat cepat.' },
        { id: 8, text: 'Saya merasa sistem ini sangat membosankan untuk digunakan.' },
        { id: 9, text: 'Saya merasa sangat percaya diri menggunakan sistem ini.' },
        { id: 10, text: 'Saya perlu belajar banyak hal sebelum saya dapat menggunakan sistem ini.' },
    ];

    constructor() {
        const initialFields: SusSurveyForm = {
            nim: '',
            class: '',
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
        this.submitForm('post', '/mahasiswa/sus-survey', {
            scrollToError: true,
        });
    }
}
