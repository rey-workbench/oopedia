import { useForm } from "@inertiajs/svelte";

export class QuestionFormState {
    form;
    materials = $state<any[]>([]);
    material = $state<any>(null);
    subMaterials = $state<any[]>([]);
    question = $state<any>(null);
    isEdit = $state(false);
    availableSubMaterials = $state<any[]>([]);

    constructor(materials: any, material: any, subMaterials: any, question: any) {
        this.materials = materials;
        this.material = material;
        this.subMaterials = subMaterials;
        this.question = question;
        this.isEdit = !!question;
        this.availableSubMaterials = subMaterials;

        this.form = useForm({
            question_text: question ? question.question_text : "",
            question_type: question ? question.question_type : "radio_button",
            difficulty: question ? question.difficulty : "beginner",
            material_id: question
                ? question.material_id
                : material
                    ? material.id
                    : "",
            sub_material_id: question ? question.sub_material_id : "",
            answers: question
                ? question.answers
                : [
                    { answer_text: "", is_correct: 0, explanation: "" },
                    { answer_text: "", is_correct: 0, explanation: "" },
                ],
            correct_answer: question
                ? question.answers.findIndex((a: any) => a.is_correct)
                : null,
        });

        // Initial load logic
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
        const response = await fetch(
            `/admin/materials/${this.form.material_id}/submaterials/json`
        );
        this.availableSubMaterials = await response.json();
        if (!this.question) {
            this.form.sub_material_id = "";
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

    setType(type: any) {
        this.form.question_type = type;
    }

    setDifficulty(diff: any) {
        this.form.difficulty = diff;
    }

    submit() {
        // Process is_correct based on correct_answer index for radio_button/fill_in_the_blank
        if (
            ["radio_button", "fill_in_the_blank"].includes(this.form.question_type)
        ) {
            this.form.answers.forEach((ans: any, i: any) => {
                ans.is_correct = i == this.form.correct_answer ? 1 : 0;
            });
        }

        if (this.question) {
            this.form.put(`/admin/questions/${this.question.id}`);
        } else {
            this.form.post("/admin/questions");
        }
    }
}
