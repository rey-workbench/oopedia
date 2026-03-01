<script lang="ts">
    import App from '@/layouts/App.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import GuestBanner from '@/components/shared/GuestBanner.svelte';
    import { Terminal, UserCheck } from 'lucide-svelte';
    import { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';
    import QuestionSessionCard from '@/components/quiz/QuestionSessionCard.svelte';
    import FinishStateCard from '@/components/quiz/FinishStateCard.svelte';
    import FeedbackModal from '@/components/quiz/FeedbackModal.svelte';
    import AdaptiveDebugPanel from '@/components/quiz/AdaptiveDebugPanel.svelte';
    import { untrack } from 'svelte';
    import type { Material, Question, DifficultyLevel, QuizSessionState } from '@/types';

    const {
        material,
        currentQuestion = null,
        currentQuestionNumber = 1,
        totalQuestions = 0,
        answeredCount = 0,
        difficulty = 'beginner' as const,
        isGuest: _isGuest = false,
        studentState,
    }: {
        material: Material;
        currentQuestion: Question | null;
        currentQuestionNumber: number;
        totalQuestions: number;
        answeredCount: number;
        difficulty: DifficultyLevel;
        isGuest: boolean;
        studentState: QuizSessionState;
    } = $props();

    let state = untrack(
        () => new QuestionShowState(material, currentQuestion as Question, difficulty, studentState)
    );

    $effect(() => {
        // Track the incoming props — this block re-runs only when Inertia
        // delivers new props (e.g. navigating to the next question).
        const newMaterial = material;
        const newQuestion = currentQuestion;
        const newDifficulty = difficulty;
        const newStudentState = studentState;

        // All reads & writes to $state happen inside untrack so they don't
        // register as dependencies and cause another loop iteration.
        untrack(() => {
            if (state.currentQuestion?.id !== newQuestion?.id) {
                state.selectedMultipleChoiceAnswer = null;
                state.fillInTheBlankAnswer = '';
                state.dragAndDropAnswers = {};
                state.startTime = Date.now();
            }
            state.material = newMaterial;
            state.currentQuestion = newQuestion;
            state.difficulty = newDifficulty;
            state.studentState = newStudentState;
        });
    });

    const progressPercentage = $derived(
        totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0
    );

    const DEBUG_MODE = import.meta.env['VITE_ADAPTIVE_DEBUG'] === 'true';
    const showDebug = $derived(state.showAdaptiveIndicator && DEBUG_MODE);
</script>

<App title={`Latihan Soal - ${material.title}`}>
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h1
                    class="flex items-center justify-center gap-4 text-4xl font-bold tracking-widest text-slate-900 uppercase"
                >
                    <Terminal size={32} class="text-primary-600" />
                    Evaluasi: {material.title}
                </h1>
                <p class="mt-3 text-xs font-bold tracking-widest text-slate-400 uppercase">
                    Mode Ujian Terkendali & Aman
                </p>
                <div class="mx-auto mt-6 max-w-xl">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500"
                            >Soal {currentQuestionNumber} / {totalQuestions}</span
                        >
                        <span class="text-primary-600 text-xs font-bold"
                            >{Math.round(progressPercentage)}%</span
                        >
                    </div>
                    <ProgressBar value={progressPercentage} color="blue" height="h-1.5" />
                </div>
            </div>

            {#if state.isGuest}
                <GuestBanner
                    show={state.isGuest}
                    variant="inline"
                    title="Mode Tamu Aktif!"
                    message="Anda hanya dapat melihat sebagian dari soal latihan ini."
                >
                    {#snippet icon()}
                        <UserCheck size={24} class="text-amber-600" />
                    {/snippet}
                </GuestBanner>
            {/if}

            {#if state.currentQuestion}
                <QuestionSessionCard {state} />
            {:else}
                <FinishStateCard {state} {material} {answeredCount} />
            {/if}
        </div>
    </div>

    <FeedbackModal {state} />
    <AdaptiveDebugPanel quizState={state} {showDebug} />
</App>
