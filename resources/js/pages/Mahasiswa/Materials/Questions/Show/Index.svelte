<script lang="ts">
    import App from '@/layouts/App.svelte';
    import GuestBanner from '@/components/layout/GuestBanner.svelte';
    import { Terminal, UserCheck } from 'lucide-svelte';
    import { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';
    import QuestionSessionCard from '@/components/layout/QuestionSessionCard.svelte';
    import FinishStateCard from '@/components/layout/FinishStateCard.svelte';
    import FeedbackModal from '@/components/feedback/FeedbackModal.svelte';
    import AdaptiveDebugPanel from '@/components/layout/AdaptiveDebugPanel.svelte';
    import { untrack } from 'svelte';
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

    let state = untrack(
        () => new QuestionShowState(material, currentQuestion as Question, difficulty, studentState)
    );

    $effect(() => {
        const newMaterial = material;
        const newQuestion = currentQuestion;
        const newDifficulty = difficulty;
        const newStudentState = studentState;

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
    <div class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 transition-all duration-500" class:pb-40={state.showFeedback}>
            <!-- Duolingo-style Header -->
            <div class="mb-12">
                <div class="flex items-center gap-6">
                    <!-- Progress Section -->
                    <div class="flex-1">
                        <div class="mb-3 flex items-center justify-between px-2">
                            <div class="flex items-center gap-3">
                                <div class="bg-primary-100 flex h-8 w-8 items-center justify-center rounded-xl text-primary-600 shadow-inner">
                                    <Terminal size={14} />
                                </div>
                                <span class="text-xs font-black tracking-widest text-slate-500 uppercase">
                                    {material.title}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-slate-400">
                                    {state.currentQuestion ? currentQuestionNumber : totalQuestions} / {totalQuestions}
                                </span>
                            </div>
                        </div>
                        <div class="relative">
                           <!-- Background Bar -->
                           <div class="h-4 w-full rounded-full bg-slate-100 shadow-inner"></div>
                           <!-- Active Progress -->
                           <div 
                                class="absolute inset-y-0 left-0 rounded-full bg-primary-500 transition-all duration-500 ease-out border-b-4 border-primary-700" 
                                style="width: {progressPercentage}%"
                           >
                                <!-- Shine highlight -->
                                <div class="absolute inset-x-2 top-1 h-1 rounded-full bg-white/20"></div>
                           </div>
                        </div>
                    </div>
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
                <FinishStateCard {state} {material} answeredCount={materialAnsweredCount} />
            {/if}
        </div>
    </div>

    <FeedbackModal {state} />
    <AdaptiveDebugPanel quizState={state} {showDebug} />
</App>
