import { router } from '@inertiajs/svelte';
import { BaseState } from '@/states/BaseState.svelte';
import { ROUTES } from '@/utils/route';
import type {
    Material,
    Question,
    QuestionDifficulty,
    QuestionWithAttempt,
    AdaptiveResult,
    LevelItem,
    StudentSessionState,
    CheckAnswerResponse,
    HydratedAction,
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
export class QuizState extends BaseState {
    // --- State Properties ---
    material = $state<Material>({} as Material);
    currentQuestion = $state<Question | null>(null);
    difficulty = $state<string>('beginner');
    studentState = $state<StudentSessionState | null>(null);

    // --- User Answers ---
    fillInTheBlankAnswer = $state('');
    selectedMultipleChoiceAnswer = $state<string | null>(null);
    dragAndDropAnswers = $state<Record<string, string>>({});

    // --- UI State ---
    isSubmitting = $state(false);
    show_feedback = $state(false);
    showHint = $state(false);
    usedHint = $state(false);
    isNavigating = $state(false);
    startTime = $state(Date.now());

    feedbackData = $state<CheckAnswerResponse>({
        status: 'success',
        message: '',
        next_url: '',
        is_correct: true,
        xp_earned: 0,
        adaptive_result: null,
    });

    // --- Adaptive UI States ---
    showAdaptiveIndicator = $state(false);
    adaptiveFacts = $state<string[]>([]);
    adaptiveTriggeredRule = $state<AdaptiveResult['triggered_rule']>(null);
    adaptiveTriggeredRules = $state<string[]>([]);

    // --- Derived Properties ---
    isProcessing = $derived(this.isSubmitting);
    showGuidance = $derived(
        this.studentState?.adaptive_engine?.adaptive_state?.['show_guidance'] === true
    );
    xp = $derived(this.studentState?.gamification?.xp ?? 0);
    streak = $derived(this.studentState?.gamification?.streak ?? 0);
    level = $derived(this.studentState?.gamification?.level ?? 'Pemula');
    hintsAvailable = $derived((this.studentState as any)?.hints_available ?? 0);

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

    // --- Methods ---

    async useHint() {
        if (!this.canUseHint()) return;

        if (this.usedHint) {
            this.showHint = true;
            return;
        }

        router.post(
            ROUTES.MAHASISWA.MATERIALS.QUESTIONS.HINT(this.material.id, this.currentQuestion!.id),
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

    private canUseHint(): boolean {
        return !!(this.currentQuestion && this.hintsAvailable > 0 && this.currentQuestion.hint);
    }

    closeHint() {
        this.showHint = false;
    }

    async submitAnswer() {
        if (this.isSubmitting || this.show_feedback || !this.currentQuestion) return;

        const payload = this.preparePayload();
        if (!this.validateAnswer()) {
            this.handleValidationError();
            return;
        }

        this.isSubmitting = true;
        router.post(
            ROUTES.MAHASISWA.MATERIALS.QUESTIONS.CHECK(this.material.id, this.currentQuestion.id),
            payload,
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => (this.isSubmitting = false),
            }
        );
    }

    private preparePayload(): Record<string, any> {
        const timeSpent = Math.max(0, Math.floor((Date.now() - this.startTime) / 1000));
        const payload: Record<string, any> = {
            time_spent: timeSpent,
            used_hint: this.usedHint,
            difficulty: this.difficulty,
        };

        if (!this.currentQuestion) return payload;

        const type = this.currentQuestion.question_type;
        if (type === 'fill_in_the_blank') {
            payload['fill_in_the_blank_answer'] = this.fillInTheBlankAnswer;
        } else if (type === 'drag_and_drop') {
            payload['drag_and_drop_answers'] = $state.snapshot(this.dragAndDropAnswers);
        } else if (this.selectedMultipleChoiceAnswer) {
            payload['answer'] = this.selectedMultipleChoiceAnswer;
        }

        return payload;
    }

    public validateAnswer(): boolean {
        if (!this.currentQuestion) return false;

        const type = this.currentQuestion.question_type;
        if (type === 'fill_in_the_blank') {
            return (this.fillInTheBlankAnswer?.trim() ?? '').length > 0;
        } else if (type === 'drag_and_drop') {
            return Object.keys(this.dragAndDropAnswers).length > 0;
        } else {
            return Boolean(this.selectedMultipleChoiceAnswer);
        }
    }

    private handleValidationError() {
        console.warn('[QuizState] Submission blocked: No answer provided.');
        this.feedbackData = {
            status: 'error',
            message: 'Jawaban wajib diisi.',
            next_url: '',
            is_correct: false,
            xp_earned: 0,
            adaptive_result: null,
        };
        this.show_feedback = true;
    }

    public handleResponseSound(status: string, adaptiveResult: AdaptiveResult | null) {
        if (status !== 'success') {
            playSound('wrong');
            return;
        }

        const isCertificate = this.checkForCertificateAction(adaptiveResult);
        playSound(isCertificate ? 'completed' : 'correct');
    }

    private checkForCertificateAction(adaptiveResult: AdaptiveResult | null): boolean {
        const actions = adaptiveResult?.triggered_rule?.actions || [];
        return actions.some(
            (a: HydratedAction) => a.id === 'CERTIFICATION' || a.variant === 'certificate'
        );
    }

    handleNext() {
        if (this.isNavigating) return;
        this.isNavigating = true;

        const nextUrl = this.feedbackData.next_url;
        this.show_feedback = false;
        this.showHint = false;

        if (nextUrl) {
            router.visit(nextUrl, {
                preserveState: false,
                preserveScroll: true,
                showProgress: false,
                onFinish: () => (this.isNavigating = false),
                onError: () => (this.isNavigating = false),
            });
        } else {
            router.reload({
                showProgress: false,
                onFinish: () => (this.isNavigating = false),
                onError: () => (this.isNavigating = false),
            });
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
    difficulty = $state<QuestionDifficulty | 'all'>('all');

    constructor(
        material: Material,
        materials: Material[],
        questions: QuestionWithAttempt[],
        difficulty: QuestionDifficulty | 'all'
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
