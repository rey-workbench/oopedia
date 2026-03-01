import { FormState } from '@/states/FormState.svelte';

type UeqSurveyForm = {
    nim: string;
    class: string;
    comments: string;
    suggestions: string;
    [key: string]: any;
};

export class UeqSurveyState extends FormState<UeqSurveyForm> {
    aspects = $state<{ name: string }[]>([]);
    questionnaireAspects = [
        { name: 'annoying_enjoyable', left: 'Menyebalkan', right: 'Menyenangkan' },
        {
            name: 'not_understandable_understandable',
            left: 'Tidak dipahami',
            right: 'Dapat dipahami',
        },
        { name: 'creative_dull', left: 'Kreatif', right: 'Monoton' },
        { name: 'easy_difficult', left: 'Mudah', right: 'Sulit' },
        { name: 'valuable_inferior', left: 'Bermanfaat', right: 'Inferior' },
        { name: 'boring_exciting', left: 'Membosankan', right: 'Menarik' },
        { name: 'not_interesting_interesting', left: 'Tidak menarik', right: 'Menarik' },
        { name: 'unpredictable_predictable', left: 'Unpredictable', right: 'Predictable' },
        { name: 'fast_slow', left: 'Cepat', right: 'Lambat' },
        { name: 'inventive_conventional', left: 'Inovatif', right: 'Konvensional' },
        { name: 'obstructive_supportive', left: 'Menghambat', right: 'Mendukung' },
        { name: 'good_bad', left: 'Baik', right: 'Buruk' },
        { name: 'complicated_easy', left: 'Rumit', right: 'Sederhana' },
        { name: 'unlikable_pleasing', left: 'Unlikable', right: 'Pleasing' },
        { name: 'usual_leading_edge', left: 'Biasa saja', right: 'Terdepan' },
        { name: 'unpleasant_pleasant', left: 'Unpleasant', right: 'Pleasant' },
        { name: 'secure_not_secure', left: 'Aman', right: 'Tidak aman' },
        { name: 'motivating_demotivating', left: 'Memotivasi', right: 'Demotivating' },
        { name: 'meets_expectations_does_not', left: 'Meets Expect.', right: "Doesn't Meet" },
        { name: 'inefficient_efficient', left: 'Tidak efisien', right: 'Efisien' },
        { name: 'clear_confusing', left: 'Jelas', right: 'Membingungkan' },
        { name: 'impractical_practical', left: 'Tidak praktis', right: 'Praktis' },
        { name: 'organized_cluttered', left: 'Terorganisir', right: 'Berantakan' },
        { name: 'attractive_unattractive', left: 'Menarik', right: 'Tidak menarik' },
        { name: 'friendly_unfriendly', left: 'Ramah', right: 'Tidak ramah' },
        { name: 'conservative_innovative', left: 'Konservatif', right: 'Inovatif' },
    ];

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
        this.submitForm('post', '/mahasiswa/ueq-survey', {
            scrollToError: true,
        });
    }
}
