<script lang="ts">
    import App from '@/layouts/App.svelte';
    import GuestBanner from '@/components/layout/GuestBanner.svelte';
    import { Terminal, UserCheck, AlertTriangle } from 'lucide-svelte';
    import { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';
    import QuestionSessionCard from '@/components/layout/QuestionSessionCard.svelte';
    import FinishStateCard from '@/components/layout/FinishStateCard.svelte';
    import { FeedbackModal } from '@/components/feedback';
    import AdaptiveDebugPanel from '@/components/layout/AdaptiveDebugPanel.svelte';
    import Modal from '@/components/ui/Modal.svelte';
    import { activateExamProtection, deactivateExamProtection, type ViolationType } from '@/utils';
    import { untrack, onMount } from 'svelte';
    import type { Material, Question, DifficultyLevel, QuizSessionState } from '@/types';

    const {
        material,
        currentQuestion = null,
        currentQuestionNumber = 1,
        totalQuestions = 0,
        answeredCount = 0,
        materialAnsweredCount = 0,
        difficulty = 'beginner' as const,
        isGuest: _isGuest = false,
        studentState,
    }: {
        material: Material;
        currentQuestion: Question | null;
        currentQuestionNumber: number;
        totalQuestions: number;
        answeredCount: number;
        materialAnsweredCount: number;
        difficulty: DifficultyLevel;
        isGuest: boolean;
        studentState: QuizSessionState;
    } = $props();

    let quizState = untrack(
        () => new QuestionShowState(material, currentQuestion as Question, difficulty, studentState)
    );

    let showWarning = $state(false);
    let warningMessage = $state('');

    function handleViolation(type: ViolationType, message: string) {
        console.warn('[ExamProtection] Violation detected:', type, message);

        warningMessage = message;
        showWarning = true;

        setTimeout(() => {
            showWarning = false;
        }, 3000);
    }

    onMount(() => {
        activateExamProtection({
            onViolation: handleViolation,
        } as any);
        return () => deactivateExamProtection();
    });

    $effect(() => {
        const newMaterial = material;
        const newQuestion = currentQuestion;
        const newDifficulty = difficulty;
        const newStudentState = studentState;

        untrack(() => {
            if (quizState.currentQuestion?.id !== newQuestion?.id) {
                quizState.selectedMultipleChoiceAnswer = null;
                quizState.fillInTheBlankAnswer = '';
                quizState.dragAndDropAnswers = {};
                quizState.startTime = Date.now();
            }
            quizState.material = newMaterial;
            quizState.currentQuestion = newQuestion;
            quizState.difficulty = newDifficulty;
            quizState.studentState = newStudentState;
        });
    });

    const progressPercentage = $derived(
        totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0
    );

    const DEBUG_MODE = import.meta.env['VITE_ADAPTIVE_DEBUG'] === 'true';
    const showDebug = $derived(quizState.showAdaptiveIndicator && DEBUG_MODE);
</script>

<App title={`Latihan Soal - ${material.title}`}>
    <div class="py-12">
        <div
            class="mx-auto max-w-5xl px-4 transition-all duration-500 sm:px-6 lg:px-8"
            class:pb-40={quizState.showFeedback}
        >
            <div id="quiz-session-header" class="mb-12">
                <div class="flex items-center gap-6">
                    <div class="flex-1">
                        <div class="mb-3 flex items-center justify-between px-2">
                            <div class="flex items-center gap-3">
                                <div
                                    class="bg-primary-100 text-primary-600 flex h-8 w-8 items-center justify-center rounded-xl shadow-inner"
                                >
                                    <Terminal size={14} />
                                </div>
                                <span
                                    class="text-xs font-black tracking-widest text-slate-500 uppercase"
                                >
                                    {material.title}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-slate-400">
                                    {quizState.currentQuestion
                                        ? currentQuestionNumber
                                        : totalQuestions} /
                                    {totalQuestions}
                                </span>
                            </div>
                        </div>
                        <div id="quiz-progress" class="relative">
                            <div class="h-4 w-full rounded-full bg-slate-100 shadow-inner"></div>
                            <div
                                class="bg-primary-500 border-primary-700 absolute inset-y-0 left-0 rounded-full border-b-4 transition-all duration-500 ease-out"
                                style="width: {progressPercentage}%"
                            >
                                <div
                                    class="absolute inset-x-2 top-1 h-1 rounded-full bg-white/20"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {#if quizState.isGuest}
                <GuestBanner
                    show={quizState.isGuest}
                    variant="inline"
                    title="Mode Tamu Aktif!"
                    message="Anda hanya dapat melihat sebagian dari soal latihan ini."
                >
                    {#snippet icon()}
                        <UserCheck size={24} class="text-amber-600" />
                    {/snippet}
                </GuestBanner>
            {/if}

            {#if quizState.currentQuestion}
                <QuestionSessionCard state={quizState} />
            {:else}
                <FinishStateCard
                    state={quizState}
                    {material}
                    answeredCount={materialAnsweredCount}
                />
            {/if}
        </div>
    </div>

    <FeedbackModal state={quizState} />
    <AdaptiveDebugPanel {quizState} {showDebug} />

    <Modal show={showWarning} maxWidth="sm" onclose={() => (showWarning = false)}>
        <div class="p-6">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100">
                    <AlertTriangle class="h-5 w-5 text-rose-600" />
                </div>
                <h2 class="text-lg font-black text-rose-800">Peringatan!</h2>
            </div>
            <p class="mb-2 text-center text-base font-medium text-slate-700">
                {warningMessage}
            </p>
            <p class="text-center text-sm text-slate-500">Pelanggaran akan dicatat.</p>
        </div>
    </Modal>
</App>
