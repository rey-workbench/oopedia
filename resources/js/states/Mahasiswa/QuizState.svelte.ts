import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';
import type {
    Material,
    Question,
    DifficultyLevel,
    QuestionWithAttempt,
    AdaptiveResult,
    LevelItem,
    StudentSessionState,
    CheckAnswerResponse,
} from '@/types';
import { playSound } from '@/utils';

/**
 * Question List State (Selection/Catalog)
 */
export class QuestionListState extends BaseState {
    materials = $state<Material[]>([]);

    constructor(materials: Material[]) {
        super();
        this.hydrate({ materials });
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
        this.hydrate({ material, levels: levels ?? [] });
    }
}

/**
 * Question Show State (Quiz Controller)
 */
export class QuestionShowState extends BaseState {
    material = $state<Material>({} as Material);
    currentQuestion = $state<Question | null>(null);
    difficulty = $state<string>('beginner');
    studentState = $state<StudentSessionState | null>(null);

    fillInTheBlankAnswer = $state('');
    selectedMultipleChoiceAnswer = $state<string | null>(null);
    dragAndDropAnswers = $state<Record<string, string>>({});

    isSubmitting = $state(false);
    show_feedback = $state(false);
    showHint = $state(false);
    feedbackData = $state<CheckAnswerResponse>({
        status: 'success',
        message: '',
        next_url: '',
        is_correct: true,
        xp_earned: 0,
        adaptive_result: null,
        student_state: null,
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

    xp = $derived(this.studentState?.xp ?? 0);
    streak = $derived(this.studentState?.streak ?? 0);
    level = $derived(this.studentState?.level ?? 'Pemula');
    hintsAvailable = $derived(this.studentState?.hints_available ?? 0);

    constructor(
        material: Material,
        currentQuestion: Question,
        difficulty: string,
        studentState: StudentSessionState | null
    ) {
        super();
        this.hydrate({ material, currentQuestion, difficulty, studentState });
        this.startTime = Date.now();
    }

    async useHint() {
        if (!this.currentQuestion) return;

        // If hint already used for this question, just show it
        if (this.usedHint) {
            this.showHint = true;
            return;
        }

        if (this.hintsAvailable > 0 && this.currentQuestion?.hint) {
            router.post(
                ROUTES.MAHASISWA.MATERIALS.QUESTIONS.HINT(
                    this.material.id,
                    this.currentQuestion.id
                ),
                {},
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.usedHint = true;
                        this.showHint = true;
                    },
                }
            );
        }
    }

    closeHint() {
        this.showHint = false;
    }

    async submitAnswer() {
        if (this.isSubmitting || this.show_feedback || !this.currentQuestion) return;
        this.isSubmitting = true;

        const timeSpent = Math.max(0, Math.floor((Date.now() - this.startTime) / 1000));

        const payload: Record<string, any> = {
            time_spent: timeSpent,
            used_hint: this.usedHint,
            difficulty: this.difficulty,
        };

        if (this.currentQuestion.question_type === 'fill_in_the_blank') {
            payload['fill_in_the_blank_answer'] = this.fillInTheBlankAnswer;
        } else if (this.currentQuestion.question_type === 'drag_and_drop') {
            payload['drag_and_drop_answers'] = this.dragAndDropAnswers;
        } else if (this.selectedMultipleChoiceAnswer) {
            payload['answer'] = this.selectedMultipleChoiceAnswer;
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
                next_url: '',
                is_correct: false,
                xp_earned: 0,
                adaptive_result: null,
                ui: null,
            };
            this.show_feedback = true;
            this.isSubmitting = false;
            return;
        }

        router.post(
            ROUTES.MAHASISWA.MATERIALS.QUESTIONS.CHECK(this.material.id, this.currentQuestion.id),
            payload,
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {},
                onFinish: () => {
                    this.isSubmitting = false;
                },
            }
        );
    }

    public handleResponseSound(status: string, adaptiveResult: AdaptiveResult | null) {
        if (status !== 'success') {
            playSound('wrong');
            return;
        }

        const flow = adaptiveResult?.triggered_rule?.action || 'NEXT';
        const isTerminal = ['FINISH', 'REVIEW'].includes(flow);

        if (isTerminal || adaptiveResult?.triggered_rule?.variant === 'certificate') {
            playSound('completed');
        } else {
            playSound('correct');
        }
    }

    isNavigating = $state(false);
    handleNext() {
        if (this.isNavigating) return;
        this.isNavigating = true;

        // Read URL BEFORE hiding feedback to avoid any reactive side-effects
        const nextUrl = this.feedbackData.next_url;
        console.debug('[QuizState] handleNext → nextUrl:', nextUrl);

        this.show_feedback = false;
        this.showHint = false;

        if (nextUrl) {
            router.visit(nextUrl, {
                preserveState: false,
                onFinish: () => {
                    this.isNavigating = false;
                },
                onError: () => {
                    this.isNavigating = false;
                },
            });
        } else {
            console.warn('[QuizState] handleNext → no nextUrl, skipping navigation');
            this.isNavigating = false;
        }
    }

    handleTryAgain() {
        this.show_feedback = false;
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
        this.hydrate({ material, materials, questions, difficulty });
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
