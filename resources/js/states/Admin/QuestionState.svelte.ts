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

    public handleSearch = debounce(() => {
        const params = {
            search: this.search,
            difficulty: this.difficulty,
        };

        const url = this.material
            ? ROUTES.ADMIN.MATERIALS.QUESTIONS.INDEX(this.material.id)
            : ROUTES.ADMIN.QUESTIONS.INDEX;

        router.get(url, params, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);

    public handleDelete(id: number) {
        confirmDelete(ROUTES.ADMIN.QUESTIONS.DELETE(id), 'Hapus soal ini?');
    }

    public setDifficulty(difficulty: string) {
        this.difficulty = difficulty;
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

    constructor(materials: Material[], material: Material | null, question: Question | null) {
        super(QuestionFormState.prepareInitialFormValues(material, question), {
            isEdit: !!question,
            showSuccessToast: 'Soal berhasil disimpan!',
            showErrorToast: true,
        });
        this.hydrate({ materials, material, question });
    }

    private static prepareInitialFormValues(material: Material | null, question: Question | null) {
        return {
            question_text: question?.question_text ?? '',
            question_type: question?.question_type ?? 'radio_button',
            difficulty: question?.difficulty ?? 'beginner',
            material_id: question?.material_id ?? material?.id ?? '',
            answers: question?.answers?.map(this.mapAnswerToField) ?? this.defaultAnswers(),
            correct_answer: question?.answers?.findIndex((a) => a.is_correct) ?? null,
        };
    }

    private static mapAnswerToField(a: Answer): AnswerField {
        return {
            answer_text: a.answer_text || '',
            is_correct: a.is_correct ? 1 : 0,
            explanation: a.explanation || '',
            drag_source: a.drag_source || '',
            drag_target: a.drag_target || '',
        };
    }

    private static defaultAnswers(): AnswerField[] {
        return Array(2)
            .fill(null)
            .map(() => ({
                answer_text: '',
                is_correct: 0,
                explanation: '',
                drag_source: '',
                drag_target: '',
            }));
    }

    public addAnswer() {
        const newAnswer: AnswerField = {
            answer_text: '',
            is_correct: 0,
            explanation: '',
            drag_source: '',
            drag_target: '',
        };
        this.form.answers = [...(this.form.answers || []), newAnswer];
    }

    public removeAnswer(index: number) {
        this.form.answers = (this.form.answers || []).filter((_, i) => i !== index);
    }

    public setType(type: string) {
        this.form.question_type = type;
    }

    public setDifficulty(difficulty: string) {
        this.form.difficulty = difficulty;
    }

    public async submit() {
        this.prepareSubmissionData();

        const url =
            this.isEdit && this.question
                ? ROUTES.ADMIN.QUESTIONS.UPDATE(this.question.id)
                : ROUTES.ADMIN.QUESTIONS.INDEX;

        await this.submitForm(this.isEdit ? 'put' : 'post', url);
    }

    private prepareSubmissionData() {
        if (this.isMultipleChoiceType()) {
            this.syncCorrectAnswerFlag();
        }

        if (this.form.question_type === 'drag_and_drop') {
            this.processDragAndDropQuestion();
        }
    }

    private isMultipleChoiceType(): boolean {
        return ['radio_button', 'multiple_choice'].includes(this.form.question_type);
    }

    private syncCorrectAnswerFlag() {
        this.form.answers?.forEach((ans, i) => {
            ans.is_correct = i === this.form.correct_answer ? 1 : 0;
        });
    }

    private processDragAndDropQuestion() {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = this.form.question_text;
        const dropzones = tempDiv.querySelectorAll('.dnd-dropzone');

        dropzones.forEach((dz, idx) => {
            const answerText = dz.getAttribute('data-answer');
            dz.outerHTML = `[blank_${idx + 1}]`;

            const ans = this.form.answers?.find((a) => a.answer_text === answerText);
            if (ans) {
                ans.drag_target = String(idx + 1);
                ans.drag_source = String(idx + 1);
            }
        });

        this.form.question_text = tempDiv.innerHTML;
    }
}

/**
 * Question Edit State
 */
export class QuestionEditState extends QuestionFormState {
    constructor(question: Question) {
        const material = (question as any).material || null;
        super([], material, question);

        if (question.question_type === 'drag_and_drop') {
            this.restoreDragAndDropVisuals();
        }
    }

    private restoreDragAndDropVisuals() {
        let text = this.form.question_text;
        this.form.answers?.forEach((ans) => {
            if (ans.drag_target) {
                const html = this.createDropzoneHtml(ans.answer_text || '');
                text = text.replace(`[blank_${ans.drag_target}]`, html);
            }
        });
        this.form.question_text = text;
    }

    private createDropzoneHtml(answer: string): string {
        return `<span class="dnd-dropzone inline-block rounded border border-primary-200 bg-primary-50 px-2 py-1 mx-1 text-xs font-bold text-primary-700 shadow-sm" contenteditable="false" data-answer="${answer}">[${answer}]</span>`;
    }
}
