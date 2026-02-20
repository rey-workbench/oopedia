import { useForm } from "@inertiajs/svelte";

export class QuestionEditState {
    form;
    availableSubMaterials = $state([]);
    materialId = $state("");

    constructor(question: any) {
        this.form = useForm({
            question_text: question.question_text,
            question_type: question.question_type,
            difficulty: question.difficulty,
            material_id: question.material_id,
            sub_material_id: question.sub_material_id || "",
            answers: question.answers.map((a: any) => ({
                id: a.id,
                answer_text: a.answer_text,
                is_correct: a.is_correct,
                explanation: a.explanation || "",
            })),
            correct_answer: question.answers.findIndex((a: any) => a.is_correct == 1),
        });

        this.materialId = question.material_id;
    }

    setSubMaterials(subMaterials: any) {
        this.availableSubMaterials = subMaterials;
    }

    async handleMaterialChange() {
        if (!this.form.material_id) {
            this.availableSubMaterials = [];
            return;
        }

        try {
            const response = await fetch(
                `/admin/materials/${this.form.material_id}/submaterials/json`
            );
            this.availableSubMaterials = await response.json();
            this.form.sub_material_id = "";
        } catch (error) {
            console.error("Failed to fetch submaterials", error);
            this.availableSubMaterials = [];
        }
    }

    addAnswer() {
        this.form.answers = [
            ...this.form.answers,
            { answer_text: "", is_correct: 0, explanation: "" },
        ];
    }

    removeAnswer(index: any) {
        this.form.answers = this.form.answers.filter((_: any, i: any) => i !== index);
    }

    setQuestionType(type: any) {
        this.form.question_type = type;
    }

    setDifficulty(diff: any) {
        this.form.difficulty = diff;
    }

    submit(questionId: any) {
        if (
            ["radio_button", "fill_in_the_blank"].includes(this.form.question_type)
        ) {
            this.form.answers.forEach((ans: any, i: any) => {
                ans.is_correct = i == this.form.correct_answer ? 1 : 0;
            });
        }
        this.form.put(`/admin/questions/${questionId}`);
    }
}
