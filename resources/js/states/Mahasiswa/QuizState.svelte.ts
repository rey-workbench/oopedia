import { router } from "@inertiajs/svelte";
import axios from "axios";
import { BaseState } from "@/states/BaseState.svelte";
import { ROUTES } from "@/utils/route";
import type { Material, Question, DifficultyLevel, StudentStateViewModel, CheckAnswerResponse } from "@/types";

/**
 * Question List State (Selection/Catalog)
 */
export class QuestionListState extends BaseState {
    materials = $state<any[]>([]);

    constructor(materials: any) {
        super();
        this.materials = materials;
    }
}

/**
 * Level Map State (Adaptive Path Visualization)
 */
export class LevelMapState extends BaseState {
    material = $state<any>({});
    levels = $state<any[]>([]);

    beginnerLevels = $derived(this.levels.filter(l => l.level === 'beginner'));
    mediumLevels = $derived(this.levels.filter(l => l.level === 'medium'));
    hardLevels = $derived(this.levels.filter(l => l.level === 'hard'));

    constructor(material: any, levels: any) {
        super();
        this.material = material;
        this.levels = levels;
    }
}

/**
 * Question Show State (Quiz Controller)
 */
export class QuestionShowState extends BaseState {
    material = $state<Material>({} as Material);
    currentQuestion = $state<Question | null>(null);
    difficulty = $state<DifficultyLevel>('beginner');
    studentState = $state<StudentStateViewModel>({} as StudentStateViewModel);

    fillInTheBlankAnswer = $state("");
    selectedMultipleChoiceAnswer = $state<any>(null);
    dragAndDropAnswers = $state<Record<string, string>>({});

    isSubmitting = $state(false);
    showFeedback = $state(false);
    showHint = $state(false);
    feedbackData = $state<{
        status: string;
        message: string;
        nextUrl: string;
        adaptiveResult: any;
        score: number;
    }>({
        status: "success",
        message: "",
        nextUrl: "",
        adaptiveResult: {},
        score: 0,
    });
    usedHint = $state(false);
    startTime = $state(Date.now());

    showAdaptiveIndicator = $state(false);
    adaptiveFacts = $state<any[]>([]);
    adaptiveTriggeredRule = $state<string | null>(null);

    xp = $derived(this.studentState?.gamification?.global_xp || 0);
    streak = $derived(this.studentState?.gamification?.current_streak || 0);
    level = $derived(this.studentState?.gamification?.current_level || "Pemula");
    hintsAvailable = $derived(this.studentState?.performance?.hints_available ?? 3);

    constructor(material: Material, currentQuestion: Question, difficulty: DifficultyLevel, studentState: StudentStateViewModel) {
        super();
        this.material = material;
        this.currentQuestion = currentQuestion;
        this.difficulty = difficulty;
        this.studentState = studentState;
        this.startTime = Date.now();
    }

    getDifficultyLabel(diff: DifficultyLevel) {
        const labels: Record<string, string> = {
            beginner: "Pemula",
            medium: "Menengah",
            hard: "Sulit",
        };
        return labels[diff] || diff;
    }

    getDifficultyColor(diff: DifficultyLevel) {
        const colors: Record<string, string> = {
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
            if (this.studentState.performance) {
                this.studentState.performance.hints_available--;
            }
        }
    }

    closeHint() {
        this.showHint = false;
    }

    async submitAnswer() {
        if (this.isSubmitting || !this.currentQuestion) return;
        this.isSubmitting = true;

        const timeSpent = Math.max(0, Math.floor((Date.now() - this.startTime) / 1000));

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
            const response = await axios.post<CheckAnswerResponse>(
                ROUTES.MAHASISWA.MATERIALS.QUESTIONS.CHECK(this.material.id, this.currentQuestion.id),
                payload
            );

            const data = response.data;
            this.feedbackData = {
                status: data.status,
                message: data.message,
                nextUrl: data.nextUrl,
                adaptiveResult: data.adaptiveResult,
                score: data.xpEarned || 0,
            };

            if (data.adaptiveResult) {
                this.adaptiveFacts = data.adaptiveResult.facts || [];
                this.adaptiveTriggeredRule = data.adaptiveResult.triggeredRule || null;
                this.showAdaptiveIndicator = true;
            }

            this.showHint = false;
            this.showFeedback = true;
        } catch (error: any) {
            console.error("Error submitting answer:", error);
            this.feedbackData = {
                status: "error",
                message: error.response?.data?.message || "Terjadi kesalahan saat memeriksa jawaban.",
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

/**
 * Review State (Quiz Summary & Filter)
 */
export class ReviewState extends BaseState {
    material = $state<any>({});
    materials = $state<any[]>([]);
    questions = $state<any[]>([]);
    difficulty = $state("");

    constructor(material: any, materials: any, questions: any, difficulty: any) {
        super();
        this.material = material;
        this.materials = materials;
        this.questions = questions;
        this.difficulty = difficulty;
    }

    getDifficultyLabel(d: any) {
        return d === 'beginner' ? 'Pemula' : d === 'medium' ? 'Menengah' : 'Sulit';
    }

    getDifficultyColor(d: any) {
        return d === 'beginner' ? 'text-emerald-600 bg-emerald-50' : d === 'medium' ? 'text-amber-600 bg-amber-50' : 'text-rose-600 bg-rose-50';
    }

    filterDifficulty(d: any) {
        router.get(
            ROUTES.MAHASISWA.MATERIALS.QUESTIONS.REVIEW(this.material.id),
            { difficulty: d },
            {
                preserveState: true,
                preserveScroll: true,
                only: ["questions", "difficulty"],
            }
        );
    }
}
