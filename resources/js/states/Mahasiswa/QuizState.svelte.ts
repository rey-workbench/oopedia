import { router } from '@inertiajs/svelte';
import axios, { isAxiosError } from 'axios';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';
import { getDifficultyLabel, getDifficultyColor } from '@/utils/quizUtils';
import type {
    Material,
    Question,
    DifficultyLevel,
    QuestionWithAttempt,
    QuizSessionState,
    CheckAnswerResponse,
    AdaptiveResult,
    LevelItem,
    AnswerPayload,
} from '@/types';
import { playSound } from '@/utils';

/**
 * Question List State (Selection/Catalog)
 */
export class QuestionListState extends BaseState {
    materials = $state<Material[]>([]);

    constructor(materials: Material[]) {
        super();
        this.materials = materials;
    }
}

/**
 * Level Map State (Adaptive Path Visualization)
 */
export class LevelMapState extends BaseState {
    material = $state<Material>({} as Material);
    levels = $state<LevelItem[]>([]);

    sortedLevels = $derived([...this.levels].sort((a, b) => a.level - b.level));
    allCompleted = $derived(
        this.levels.length > 0 && this.levels.every((l) => l.status === 'completed')
    );

    constructor(material: Material, levels: LevelItem[]) {
        super();
        this.material = material;
        this.levels = levels ?? [];
    }
}

/**
 * Question Show State (Quiz Controller)
 */
export class QuestionShowState extends BaseState {
    material = $state<Material>({} as Material);
    currentQuestion = $state<Question | null>(null);
    difficulty = $state<string>('beginner');
    studentState = $state<QuizSessionState>({} as QuizSessionState);

    fillInTheBlankAnswer = $state('');
    selectedMultipleChoiceAnswer = $state<string | null>(null);
    dragAndDropAnswers = $state<Record<string, string>>({});

    isSubmitting = $state(false);
    showFeedback = $state(false);
    showHint = $state(false);
    feedbackData = $state<{
        status: string;
        message: string;
        nextUrl: string;
        adaptiveResult: AdaptiveResult | null;
        score: number;
        ui?: {
            label?: string;
            title?: string;
            type?: string;
            url?: string;
            message?: string;
        } | null;
    }>({
        status: 'success',
        message: '',
        nextUrl: '',
        adaptiveResult: null,
        score: 0,
        ui: null,
    });
    usedHint = $state(false);
    startTime = $state(Date.now());

    showAdaptiveIndicator = $state(false);
    adaptiveFacts = $state<string[]>([]);
    adaptiveTriggeredRule = $state<{
        id?: string;
        name?: string;
        action?: string | null;
        priority?: number;
        variant?: string;
    } | null>(null);
    adaptiveTriggeredRules = $state<
        Array<{
            id?: string;
            name?: string;
            action?: string | null;
            priority?: number;
            variant?: string;
        }>
    >([]);

    isProcessing = $derived(this.isSubmitting);

    xp = $derived(this.studentState?.gamification?.global_xp || 0);
    streak = $derived(this.studentState?.gamification?.current_streak || 0);
    level = $derived(this.studentState?.gamification?.current_level || 'Pemula');
    hintsAvailable = $derived(this.studentState?.performance?.hints_available ?? 3);

    constructor(
        material: Material,
        currentQuestion: Question,
        difficulty: string,
        studentState: QuizSessionState
    ) {
        super();
        this.material = material;
        this.currentQuestion = currentQuestion;
        this.difficulty = difficulty;
        this.studentState = studentState;
        this.startTime = Date.now();
    }

    getDifficultyLabel(diff: string): string {
        return getDifficultyLabel(diff);
    }

    getDifficultyColor(diff: string): string {
        return getDifficultyColor(diff);
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
        if (this.isSubmitting || this.showFeedback || !this.currentQuestion) return;
        this.isSubmitting = true;

        const timeSpent = Math.max(0, Math.floor((Date.now() - this.startTime) / 1000));

        const payload: AnswerPayload = {
            question_id: this.currentQuestion.id,
            material_id: this.material.id,
            used_hint: this.usedHint,
            time_spent: timeSpent,
            // Prefer the current question difficulty to avoid sending non-answer filters like "all".
            difficulty: this.currentQuestion.difficulty ?? this.difficulty,
        };

        if (this.currentQuestion.question_type === 'fill_in_the_blank') {
            payload.fill_in_the_blank_answer = this.fillInTheBlankAnswer;
            payload.answer = this.fillInTheBlankAnswer;
        } else if (this.currentQuestion.question_type === 'drag_and_drop') {
            payload.drag_and_drop_answers = JSON.stringify(this.dragAndDropAnswers);
        } else {
            payload.answer = this.selectedMultipleChoiceAnswer;
        }

        const hasAnswer =
            this.currentQuestion.question_type === 'fill_in_the_blank'
                ? this.fillInTheBlankAnswer.trim().length > 0
                : this.currentQuestion.question_type === 'drag_and_drop'
                  ? Object.keys(this.dragAndDropAnswers).length > 0
                  : Boolean(this.selectedMultipleChoiceAnswer);

        if (!hasAnswer) {
            this.feedbackData = {
                status: 'error',
                message: 'Jawaban wajib diisi.',
                nextUrl: '',
                adaptiveResult: null,
                score: 0,
            };
            this.showFeedback = true;
            this.isSubmitting = false;

            return;
        }

        console.debug('[QuizState] Submitting answer payload:', payload);

        try {
            const response = await axios.post<CheckAnswerResponse>(
                ROUTES.MAHASISWA.MATERIALS.QUESTIONS.CHECK(
                    this.material.id,
                    this.currentQuestion.id
                ),
                payload
            );

            console.debug('[QuizState] Received answer response:', response.data);
            const data = response.data;
            const adaptiveResult = (data.adaptiveResult as unknown as AdaptiveResult) ?? null;
            this.feedbackData = {
                status: data.status,
                message: data.message,
                nextUrl: data.nextUrl,
                adaptiveResult,
                score: data.xpEarned || 0,
                ui: (data as any).ui ?? null,
            };

            if (adaptiveResult) {
                this.adaptiveFacts = adaptiveResult.facts ?? [];
                this.adaptiveTriggeredRule = adaptiveResult.triggered_rule ?? null;
                this.adaptiveTriggeredRules = adaptiveResult.triggered_rules ?? [];
                this.showAdaptiveIndicator = true;
            }

            this.showHint = false;
            this.showFeedback = true;

            // --- Play Sound Effects ---
            if (data.status === 'success') {
                const action = adaptiveResult?.triggered_rule?.action;
                if (action === 'FINISH_MATERIAL' || action === 'NEXT_MATERIAL') {
                    playSound('completed');
                } else {
                    playSound('correct');
                }
            } else {
                playSound('wrong');
            }
        } catch (err: unknown) {
            const message = isAxiosError(err)
                ? ((err.response?.data as { message?: string })?.message ??
                  'Terjadi kesalahan saat memeriksa jawaban.')
                : 'Terjadi kesalahan tidak terduga.';
            console.error('[QuizState] Error submitting answer:', err);
            if (isAxiosError(err)) {
                console.error('[QuizState] Axios error details:', err.response?.data);
            }
            this.feedbackData = {
                status: 'error',
                message,
                nextUrl: '',
                adaptiveResult: null,
                score: 0,
            };
            this.showFeedback = true;
        } finally {
            this.isSubmitting = false;
        }
    }

    isNavigating = $state(false);
    handleNext() {
        if (this.isNavigating) return;
        this.isNavigating = true;

        this.showFeedback = false;
        this.showHint = false;
        if (this.feedbackData.nextUrl) {
            router.visit(this.feedbackData.nextUrl, {
                onFinish: () => {
                    this.isNavigating = false;
                },
            });
        } else {
            this.isNavigating = false;
        }
    }

    handleTryAgain() {
        this.showFeedback = false;
        this.showHint = false;
        this.fillInTheBlankAnswer = '';
        this.selectedMultipleChoiceAnswer = null;
        this.dragAndDropAnswers = {};
        this.startTime = Date.now();
    }
}

/**
 * Review State (Quiz Summary & Filter)
 */
export class ReviewState extends BaseState {
    material = $state<Material>({} as Material);
    materials = $state<Material[]>([]);
    questions = $state<QuestionWithAttempt[]>([]);
    difficulty = $state<DifficultyLevel | 'all'>('all');

    constructor(
        material: Material,
        materials: Material[],
        questions: QuestionWithAttempt[],
        difficulty: DifficultyLevel | 'all'
    ) {
        super();
        this.material = material;
        this.materials = materials;
        this.questions = questions;
        this.difficulty = difficulty;
    }

    getDifficultyLabel(d: DifficultyLevel | string): string {
        return getDifficultyLabel(d);
    }

    getDifficultyColor(d: DifficultyLevel | string): string {
        return getDifficultyColor(d);
    }

    filterDifficulty(d: string) {
        router.get(
            ROUTES.MAHASISWA.MATERIALS.QUESTIONS.REVIEW(this.material.id, d),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                only: ['questions', 'difficulty'],
            }
        );
    }
}
