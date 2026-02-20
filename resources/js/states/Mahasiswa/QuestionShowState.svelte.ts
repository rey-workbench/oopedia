import { router } from "@inertiajs/svelte";
import axios from "axios";

export class QuestionShowState {
    // Props
    material = $state<any>({});
    currentQuestion = $state<any>(null);
    difficulty = $state("beginner");
    studentState = $state<any>({});
    isGuest = $state(false);

    // Question State
    fillInTheBlankAnswer = $state("");
    selectedMultipleChoiceAnswer = $state(null);
    dragAndDropAnswers = $state({});

    // UI State
    isSubmitting = $state(false);
    showFeedback = $state(false);
    showHint = $state(false);
    feedbackData = $state<any>({
        status: "success",
        message: "",
        nextUrl: "",
        adaptiveResult: {},
        score: 0,
    });
    usedHint = $state(false);
    startTime = $state(Date.now());

    // Adaptive UI State
    showAdaptiveIndicator = $state(false);
    adaptiveFacts = $state<any[]>([]);
    adaptiveTriggeredRule = $state(null);

    // Derived State
    xp = $derived(this.studentState?.gamification?.global_xp || 0);
    streak = $derived(this.studentState?.gamification?.current_streak || 0);
    level = $derived(this.studentState?.gamification?.current_level || "Pemula");
    hintsAvailable = $derived(this.studentState?.performance?.hints_available ?? 3);

    constructor(material: any, currentQuestion: any, difficulty: any, studentState: any, isGuest: any) {
        this.material = material;
        this.currentQuestion = currentQuestion;
        this.difficulty = difficulty;
        this.studentState = studentState;
        this.isGuest = isGuest;
        this.startTime = Date.now();
    }

    getDifficultyLabel(diff: any) {
        const labels: any = {
            beginner: "Pemula",
            medium: "Menengah",
            hard: "Sulit",
        };
        return labels[diff] || diff;
    }

    getDifficultyColor(diff: any) {
        const colors: any = {
            beginner: "text-emerald-600 bg-emerald-50",
            medium: "text-amber-600 bg-amber-50",
            hard: "text-rose-600 bg-rose-50",
        };
        return colors[diff] || "text-slate-600 bg-slate-50";
    }

    useHint() {
        if (this.hintsAvailable > 0 && this.currentQuestion?.hint) {
            this.usedHint = true;
            this.showHint = true;
            // Optimistically decrease hints, though real state comes from DB
            if (this.studentState.performance) {
                this.studentState.performance.hints_available--;
            }
        }
    }

    closeHint() {
        this.showHint = false;
    }

    async submitAnswer() {
        if (this.isSubmitting) return;
        this.isSubmitting = true;

        const timeSpent = Math.max(
            0,
            Math.floor((Date.now() - this.startTime) / 1000)
        );

        let payload: any = {
            question_id: this.currentQuestion.id,
            material_id: this.material.id,
            used_hint: this.usedHint,
            time_spent: timeSpent,
            difficulty: this.difficulty,
        };

        if (this.currentQuestion.question_type === "fill_in_the_blank") {
            payload.fill_in_the_blank_answer = this.fillInTheBlankAnswer;
            payload.answer = this.fillInTheBlankAnswer;
        } else if (this.currentQuestion.question_type === "drag_and_drop") {
            payload.drag_and_drop_answers = JSON.stringify(this.dragAndDropAnswers);
        } else {
            payload.answer = this.selectedMultipleChoiceAnswer;
        }

        try {
            const response = await axios.post(
                `/mahasiswa/materials/${this.material.id}/questions/${this.currentQuestion.id}/check`,
                payload
            );

            const data = response.data;

            this.feedbackData = {
                status: data.status,
                message: data.message,
                nextUrl: data.nextUrl,
                adaptiveResult: data.adaptiveResult,
                score: data.score,
            };

            if (data.adaptiveResult) {
                this.adaptiveFacts = data.adaptiveResult.facts || [];
                this.adaptiveTriggeredRule = data.adaptiveResult.triggered_rule || null;
                this.showAdaptiveIndicator = true;
            }

            if (data.adaptiveResult?.new_state) {
                this.studentState = data.adaptiveResult.new_state;
            }

            this.showHint = false;
            this.showFeedback = true;
        } catch (error: any) {
            console.error("Error submitting answer:", error);
            this.feedbackData = {
                status: "error",
                message:
                    error.response?.data?.message ||
                    "Terjadi kesalahan saat memeriksa jawaban.",
                nextUrl: "",
                adaptiveResult: null,
                score: 0,
            };
            this.showFeedback = true;
        } finally {
            this.isSubmitting = false;
        }
    }

    handleNext() {
        this.showFeedback = false;
        this.showHint = false;
        if (this.feedbackData.nextUrl) {
            router.visit(this.feedbackData.nextUrl);
        }
    }

    handleTryAgain() {
        this.showFeedback = false;
        this.showHint = false;
        this.fillInTheBlankAnswer = "";
        this.selectedMultipleChoiceAnswer = null;
        this.dragAndDropAnswers = {};
        this.startTime = Date.now();
    }
}
