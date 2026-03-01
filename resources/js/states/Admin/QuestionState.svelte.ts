import { router } from "@inertiajs/svelte";
import { debounce } from "lodash";
import axios from "axios";
import { confirmDelete } from "@/utils/confirmDelete";
import { BaseState } from "@/states/BaseState.svelte";
import { FormState } from "@/states/FormState.svelte";
import { ROUTES } from "@/utils/route";
import type { Answer, Question, Material, SubMaterial, Pagination } from "@/types";

interface AnswerField {
    answer_text: string;
    is_correct: number;
    explanation: string;
}

/**
 * Question List Admin State
 */
export class QuestionListAdminState extends BaseState {
    questions = $state<Pagination<Question>>({ data: [], links: [], current_page: 1, from: null, last_page: 1, path: "", per_page: 10, to: null, total: 0 });
    material = $state<Material | null>(null);
    search = $state("");
    difficulty = $state("");

    constructor(questions: Pagination<Question>, material: Material | null, search: string, difficulty: string) {
        super();
        this.questions = questions;
        this.material = material;
        this.search = search;
        this.difficulty = difficulty;
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
        confirmDelete(
            ROUTES.ADMIN.QUESTIONS.DELETE(id),
            "Hapus soal ini?"
        );
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
    sub_material_id: number | string | null;
    answers: AnswerField[];
    correct_answer: number | null;
}> {
    materials = $state<Material[]>([]);
    material = $state<Material | null>(null);
    subMaterials = $state<SubMaterial[]>([]);
    question = $state<Question | null>(null);
    availableSubMaterials = $state<SubMaterial[]>([]);

    constructor(materials: Material[], material: Material | null, subMaterials: SubMaterial[], question: Question | null) {
        super({
            question_text: question ? question.question_text : "",
            question_type: question ? question.question_type : "radio_button",
            difficulty: question ? question.difficulty : "beginner",
            material_id: question
                ? question.material_id
                : material
                    ? material.id
                    : "",
            sub_material_id: question ? question.sub_material_id : "",
            answers: question && question.answers
                ? question.answers.map((a: Answer) => ({
                    answer_text: a.answer_text || "",
                    is_correct: a.is_correct ? 1 : 0,
                    explanation: a.explanation || ""
                }))
                : [
                    { answer_text: "", is_correct: 0, explanation: "" },
                    { answer_text: "", is_correct: 0, explanation: "" },
                ],
            correct_answer: question && question.answers
                ? question.answers.findIndex((a: Answer) => a.is_correct)
                : null,
        }, !!question);

        this.materials = materials;
        this.material = material;
        this.subMaterials = subMaterials;
        this.question = question;
        this.availableSubMaterials = subMaterials;

        if (this.form.material_id && this.subMaterials.length === 0) {
            this.handleMaterialChange();
        } else if (this.subMaterials.length > 0) {
            this.availableSubMaterials = this.subMaterials;
        }
    }

    async handleMaterialChange() {
        if (!this.form.material_id) {
            this.availableSubMaterials = [];
            return;
        }
        try {
            const response = await axios.get(
                ROUTES.ADMIN.MATERIALS.SUBMATERIALS.JSON(this.form.material_id)
            );
            this.availableSubMaterials = response.data;
            if (!this.question) {
                this.form.sub_material_id = "";
            }
        } catch (error) {
            console.error("Failed to fetch submaterials", error);
            this.availableSubMaterials = [];
        }
    }

    addAnswer() {
        this.form.answers = [
            ...(this.form.answers || []),
            { answer_text: "", is_correct: 0, explanation: "" },
        ];
    }

    removeAnswer(index: number) {
        this.form.answers = (this.form.answers || []).filter((_: unknown, i: number) => i !== index);
    }

    setType(type: string) {
        this.form.question_type = type;
    }

    setDifficulty(diff: string) {
        this.form.difficulty = diff;
    }

    async submit() {
        if (
            ["radio_button", "fill_in_the_blank", "multiple_choice"].includes(this.form.question_type)
        ) {
            const answers = [...(this.form.answers || [])];
            answers.forEach((ans: AnswerField, i: number) => {
                ans.is_correct = i == this.form.correct_answer ? 1 : 0;
            });

            this.form.answers = answers;
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
    constructor(question: Question, subMaterials: SubMaterial[] = []) {
        super([], (question as any).material || null, subMaterials, question);
    }
}
