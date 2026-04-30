import { router } from '@inertiajs/svelte';
import { debounce } from 'lodash-es';
import { confirmDelete } from '@/utils/confirmDelete';
import { BaseState } from '@/states/BaseState.svelte';
import { FormState } from '@/states/FormState.svelte';
import { ROUTES } from '@/utils/route';
import type { Answer, Question, Material, Pagination } from '@/types';

interface AnswerField {
    answer_text?: string | null;
    is_correct?: number;
    explanation?: string | null;
    drag_source?: string | null;
    drag_target?: string | null;
    blank_position?: number | null;
}

/**
 * Question List Admin State
 */
export class QuestionListAdminState extends BaseState {
    questions = $state<Pagination<Question>>({
        data: [],
        links: [],
        current_page: 1,
        from: null,
        last_page: 1,
        path: '',
        per_page: 10,
        to: null,
        total: 0,
        first_page_url: '',
        last_page_url: '',
        next_page_url: null,
        prev_page_url: null,
    });
    material = $state<Material | null>(null);
    search = $state('');
    difficulty = $state('');

    constructor(
        questions: Pagination<Question>,
        material: Material | null,
        search: string,
        difficulty: string
    ) {
        super();
        this.hydrate({ questions, material, search, difficulty });
    }

    handleSearch = debounce(() => {
        router.get(
            this.material
                ? ROUTES.ADMIN.MATERIALS.QUESTIONS.INDEX(this.material.id)
                : ROUTES.ADMIN.QUESTIONS.INDEX,
            {
                search: this.search,
                difficulty: this.difficulty,
            },
            { preserveState: true, preserveScroll: true }
        );
    }, 300);

    handleDelete(id: number) {
        confirmDelete(ROUTES.ADMIN.QUESTIONS.DELETE(id), 'Hapus soal ini?');
    }

    setDifficulty(diff: string) {
        this.difficulty = diff;
        this.handleSearch();
    }
}

/**
 * Question Form State (Create/Edit)
 */
export class QuestionFormState extends FormState<{
    question_text: string;
    question_type: string;
    difficulty: string;
    material_id: number | string;
    answers: AnswerField[];
    correct_answer: number | null;
}> {
    materials = $state<Material[]>([]);
    material = $state<Material | null>(null);
    question = $state<Question | null>(null);

    constructor(
        materials: Material[],
        material: Material | null,
        question: Question | null
    ) {
        super(
            {
                question_text: question ? question.question_text : '',
                question_type: question ? question.question_type : 'radio_button',
                difficulty: question ? question.difficulty : 'beginner',
                material_id: question ? question.material_id : material ? material.id : '',
                answers:
                    question && question.answers
                        ? question.answers.map((a: Answer) => ({
                              answer_text: a.answer_text || '',
                              is_correct: a.is_correct ? 1 : 0,
                              explanation: a.explanation || '',
                              drag_source: a.drag_source || '',
                              drag_target: a.drag_target || '',
                          }))
                        : [
                              {
                                  answer_text: '',
                                  is_correct: 0,
                                  explanation: '',
                                  drag_source: '',
                                  drag_target: '',
                              },
                              {
                                  answer_text: '',
                                  is_correct: 0,
                                  explanation: '',
                                  drag_source: '',
                                  drag_target: '',
                              },
                          ],
                correct_answer:
                    question && question.answers
                        ? question.answers.findIndex((a: Answer) => a.is_correct)
                        : null,
            },
            {
                isEdit: !!question,
                showSuccessToast: 'Soal berhasil disimpan!',
                showErrorToast: true,
            }
        );
        this.hydrate({
            materials,
            material,
            question,
        });
    }

    addAnswer() {
        this.form.answers = [
            ...(this.form.answers || []),
            { answer_text: '', is_correct: 0, explanation: '', drag_source: '', drag_target: '' },
        ];
    }

    removeAnswer(index: number) {
        this.form.answers = (this.form.answers || []).filter(
            (_: unknown, i: number) => i !== index
        );
    }

    setType(type: string) {
        this.form.question_type = type;
    }

    setDifficulty(diff: string) {
        this.form.difficulty = diff;
    }

    async submit() {
        if (['radio_button', 'multiple_choice'].includes(this.form.question_type)) {
            const answers = [...(this.form.answers || [])];
            answers.forEach((ans: AnswerField, i: number) => {
                ans.is_correct = i == this.form.correct_answer ? 1 : 0;
            });

            this.form.answers = answers;
        }

        if (this.form.question_type === 'drag_and_drop') {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.form.question_text;
            const dropzones = tempDiv.querySelectorAll('.dnd-dropzone');

            // Map blanks to index
            dropzones.forEach((dz, idx) => {
                const answerText = dz.getAttribute('data-answer');
                dz.outerHTML = `[blank_${idx + 1}]`;

                // Find answer
                const ans = this.form.answers?.find((a) => a.answer_text === answerText);
                if (ans) {
                    ans.drag_target = String(idx + 1);
                    ans.drag_source = String(idx + 1);
                }
            });
            this.form.question_text = tempDiv.innerHTML;
        }

        const url = this.question
            ? ROUTES.ADMIN.QUESTIONS.UPDATE(this.question.id)
            : ROUTES.ADMIN.QUESTIONS.INDEX;

        await this.submitForm(this.question ? 'put' : 'post', url);
    }
}

/**
 * Question Edit State
 */
export class QuestionEditState extends QuestionFormState {
    constructor(question: Question) {
        super(
            [],
            (question as unknown as { material: Material | null }).material || null,
            question
        );

        if (question && question.question_type === 'drag_and_drop') {
            let qt = question.question_text || '';
            (this.form.answers || []).forEach((ans) => {
                if (ans.drag_target) {
                    const html = `<span class="dnd-dropzone inline-block rounded border border-primary-200 bg-primary-50 px-2 py-1 mx-1 text-xs font-bold text-primary-700 shadow-sm" contenteditable="false" data-answer="${ans.answer_text}">[${ans.answer_text}]</span>`;
                    qt = qt.replace(`[blank_${ans.drag_target}]`, html);
                }
            });
            this.form.question_text = qt;
        }
    }
}
