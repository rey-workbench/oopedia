/**
 * UEQ (User Experience Questionnaire) Aspects
 * Standardized mapping of 26 semantic differential scales.
 */
export const UEQ_ASPECTS = [
    { name: 'annoying_enjoyable', left: 'Menyebalkan', right: 'Menyenangkan' },
    { name: 'not_understandable_understandable', left: 'Tidak dipahami', right: 'Dapat dipahami' },
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

/**
 * UEQ Dimensions Mapping
 */
export const UEQ_DIMENSIONS = {
    'Daya Tarik': ['annoying_enjoyable', 'good_bad', 'unlikable_pleasing', 'unpleasant_pleasant', 'attractive_unattractive', 'friendly_unfriendly'],
    'Kejelasan': ['not_understandable_understandable', 'easy_difficult', 'complicated_easy', 'clear_confusing'],
    'Efisiensi': ['fast_slow', 'inefficient_efficient', 'impractical_practical', 'organized_cluttered'],
    'Ketepatan': ['unpredictable_predictable', 'secure_not_secure', 'meets_expectations_does_not'],
    'Stimulasi': ['valuable_inferior', 'boring_exciting', 'not_interesting_interesting', 'motivating_demotivating'],
    'Kebaruan': ['creative_dull', 'inventive_conventional', 'usual_leading_edge', 'conservative_innovative'],
};

/**
 * SUS (System Usability Scale) Questions
 * Standardized 10-item questionnaire.
 */
export const SUS_QUESTIONS = [
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

