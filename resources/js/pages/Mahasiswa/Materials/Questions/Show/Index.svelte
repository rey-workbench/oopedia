<script lang="ts">
    import App from '@/layouts/App.svelte';
    import GuestBanner from '@/components/layout/GuestBanner.svelte';
    import {
        Terminal,
        UserCheck,
        AlertTriangle,
        Lightbulb,
        CheckCircle2,
        Loader2,
        Star,
        Flame,
        X,
        Clock,
    } from '@lucide/svelte';
    import { QuizState } from '@/states/Mahasiswa/QuizState.svelte';
    import QuestionSessionCard from '@/components/layout/QuestionSessionCard.svelte';
    import FinishStateCard from '@/components/layout/FinishStateCard.svelte';
    import { FeedbackModal } from '@/components/feedback';
    import AdaptiveDebugPanel from '@/components/layout/AdaptiveDebugPanel.svelte';
    import Modal from '@/components/ui/Modal.svelte';
    import { activateExamProtection, deactivateExamProtection, type ViolationType } from '@/utils';
    import { untrack, onMount } from 'svelte';
    import { fly } from 'svelte/transition';
    import type { Question, QuestionShowProps } from '@/types';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import XPGainEffect from '@/components/ui/XPGainEffect.svelte';
    import { Button, DragAndDrop, FillInTheBlank, MultipleChoice } from '@/components';

    const {
        material,
        current_question = null,
        current_question_number = 1,
        total_questions = 0,
        answered_count = 0,
        material_answered_count = 0,
        difficulty = 'beginner',
        is_guest: _isGuest = false,
        student_state,
        feedback = null,
    }: QuestionShowProps = $props();

    let quizState = untrack(
        () => new QuizState(material, current_question as Question, difficulty, student_state)
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

    // Full Backend-Driven Sync: Just mirror props to quizState
    $effect(() => {
        const newMaterial = material;
        const newQuestion = current_question;
        const newDifficulty = difficulty;
        const newStudentState = student_state;
        const newFeedback = feedback;

        untrack(() => {
            // 1. If we have feedback, we are in the "Result" phase of the current question.
            // We show the feedback modal but KEEP the current question displayed in the background.
            if (newFeedback) {
                quizState.feedbackData = newFeedback;
                quizState.show_feedback = true;

                // Update student state (XP, accuracy, etc.) even during feedback
                quizState.studentState = newStudentState;
                quizState.material = newMaterial;
                quizState.difficulty = newDifficulty;

                // Handle Adaptive Metadata
                const adaptiveResult = newFeedback.adaptive_result;
                if (adaptiveResult) {
                    quizState.adaptiveFacts = adaptiveResult.facts ?? [];
                    quizState.adaptiveTriggeredRule = adaptiveResult.triggered_rule ?? null;
                    quizState.showAdaptiveIndicator = true;
                }

                // Handle Audio Feedback
                if (typeof (quizState as any).handleResponseSound === 'function') {
                    (quizState as any).handleResponseSound(newFeedback.status, adaptiveResult);
                }
            } else {
                // 2. No feedback means we are either starting or have navigated to a new question.
                // Reset local UI state only if the question has actually changed
                if (quizState.currentQuestion?.id !== newQuestion?.id) {
                    quizState.selectedMultipleChoiceAnswer = null;
                    quizState.fillInTheBlankAnswer = '';
                    quizState.dragAndDropAnswers = {};
                    quizState.startTime = Date.now();
                    quizState.usedHint = false;
                    quizState.isNavigating = false;
                    quizState.show_feedback = false;
                }

                // Sync the actual question displayed
                quizState.material = newMaterial;
                quizState.currentQuestion = newQuestion;
                quizState.difficulty = newDifficulty;
                quizState.studentState = newStudentState;
                quizState.showAdaptiveIndicator = false;
            }
        });
    });

    const progressPercentage = $derived(
        total_questions > 0 ? (answered_count / total_questions) * 100 : 0
    );

    const DEBUG_MODE = import.meta.env['VITE_ADAPTIVE_DEBUG'] === 'true';
    const showDebug = $derived(quizState.showAdaptiveIndicator && DEBUG_MODE);

    // Countdown Timer logic
    const TIME_LIMIT = 60; // 60 seconds per question
    let timeLeft = $state(TIME_LIMIT);

    $effect(() => {
        // Reset timer when question changes
        quizState.startTime; // Dependency
        timeLeft = TIME_LIMIT;
    });

    $effect(() => {
        if (!quizState.currentQuestion || quizState.show_feedback || quizState.isSubmitting) {
            return;
        }

        const timer = setInterval(() => {
            if (timeLeft > 0) {
                timeLeft -= 1;
            }
        }, 1000);

        return () => clearInterval(timer);
    });

    const formattedTime = $derived(
        `${Math.floor(timeLeft / 60)}:${(timeLeft % 60).toString().padStart(2, '0')}`
    );
</script>

<App
    title={`Latihan Soal - ${material.title}`}
    showSidebar={false}
    showNavbar={false}
    fullWidth={true}
>
    <div class="py-8">
        <div
            class="mx-auto max-w-4xl px-4 transition-all duration-500 sm:px-6 lg:px-8"
            class:pb-40={true}
            class:pointer-events-none={quizState.show_feedback}
        >
            <div id="quiz-session-header" class="mb-12">
                <div class="flex items-start gap-4">
                    <a
                        href="/mahasiswa/dashboard"
                        class="flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl border-2 border-slate-200 px-3 text-slate-400 transition-all hover:border-rose-300 hover:bg-rose-50 hover:text-rose-600 active:scale-95"
                        title="Keluar"
                    >
                        <X size={20} />
                        <span class="hidden sm:inline text-xs font-black tracking-widest uppercase">KEMBALI</span>
                    </a>
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
                                        ? current_question_number
                                        : total_questions} /
                                    {total_questions}
                                </span>
                            </div>
                        </div>
                        <div id="quiz-progress" class="relative">
                            <ProgressBar value={progressPercentage} height="h-4" color="blue" />
                        </div>
                    </div>
                    <!-- Timer, XP & Streak Display -->
                    <div class="flex flex-col items-end gap-2 shrink-0">
                        {#if quizState.currentQuestion && !quizState.show_feedback}
                            <div
                                class={`flex items-center gap-2 rounded-2xl border-2 px-3 py-1.5 shadow-sm transition-all duration-300 ${
                                    timeLeft <= 10
                                        ? 'border-rose-200 bg-rose-50 text-rose-600 animate-pulse'
                                        : 'border-slate-100 bg-white text-slate-500'
                                }`}
                            >
                                <Clock size={16} />
                                <span class="text-sm font-black tracking-widest"
                                    >{formattedTime}</span
                                >
                            </div>
                        {/if}
                        <div class="flex items-center gap-2">
                            <div
                                id="xp-badge"
                                class="flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-white px-3 py-1.5 shadow-sm transition-all duration-300"
                            >
                                <Star size={16} class="fill-amber-400 text-amber-400" />
                                <span class="text-sm font-black text-slate-700">{quizState.xp}</span
                                >
                            </div>
                            <div
                                class="flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-white px-3 py-1.5 shadow-sm"
                            >
                                <Flame size={16} class="fill-orange-500 text-orange-500" />
                                <span class="text-sm font-black text-slate-700"
                                    >{quizState.streak}</span
                                >
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
                <QuestionSessionCard state={quizState}>
                    {#if quizState.currentQuestion.question_type === 'radio_button'}
                        <MultipleChoice
                            question={quizState.currentQuestion}
                            bind:selectedAnswerId={quizState.selectedMultipleChoiceAnswer}
                            disabled={quizState.isSubmitting}
                            showGuidance={quizState.showGuidance}
                            guidanceData={quizState.guidanceData}
                        />
                    {:else if quizState.currentQuestion.question_type === 'fill_in_the_blank'}
                        <FillInTheBlank
                            question={quizState.currentQuestion}
                            bind:answerText={quizState.fillInTheBlankAnswer}
                            disabled={quizState.isSubmitting}
                            showGuidance={quizState.showGuidance}
                            guidanceData={quizState.guidanceData}
                        />
                    {:else if quizState.currentQuestion.question_type === 'drag_and_drop'}
                        <DragAndDrop
                            question={quizState.currentQuestion}
                            bind:dragAndDropAnswers={quizState.dragAndDropAnswers}
                            disabled={quizState.isSubmitting}
                            showGuidance={quizState.showGuidance}
                            guidanceData={quizState.guidanceData}
                        />
                    {/if}
                </QuestionSessionCard>
            {:else}
                <FinishStateCard
                    state={quizState}
                    {material}
                    answered_count={material_answered_count}
                />
            {/if}
        </div>
    </div>

    <!-- Duolingo-style Bottom Action Bar -->
    {#if quizState.currentQuestion && !quizState.show_feedback}
        <div
            class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-100 bg-white px-6 py-4 transition-all duration-500 md:px-12 md:py-6"
            transition:fly={{ y: 100, duration: 500 }}
        >
            <div class="mx-auto flex max-w-4xl items-center justify-between gap-6">
                <!-- Hint Button -->
                <button
                    onclick={() => quizState.useHint()}
                    disabled={quizState.isSubmitting ||
                        quizState.hintsAvailable <= 0 ||
                        !quizState.currentQuestion?.hint}
                    class="group flex flex-col items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 px-6 py-3 font-black text-slate-400 transition-all hover:bg-slate-50 active:translate-y-0.5 active:border-b-2 disabled:opacity-50 disabled:grayscale"
                >
                    <div class="flex items-center gap-3">
                        <Lightbulb
                            size={20}
                            class={quizState.hintsAvailable > 0
                                ? 'text-amber-500'
                                : 'text-slate-300'}
                        />
                        <span class="text-xs tracking-widest uppercase">
                            Hint ({quizState.hintsAvailable})
                        </span>
                    </div>
                </button>

                <!-- Check Button -->
                <div class="flex-1 md:min-w-[240px] md:flex-initial">
                    <Button
                        variant={quizState.validateAnswer() ? 'primary' : 'secondary'}
                        size="lg"
                        disabled={quizState.isSubmitting || !quizState.validateAnswer()}
                        class="w-full rounded-2xl border-b-4 py-4 text-sm font-black tracking-widest uppercase transition-all active:translate-y-1 active:border-b-0"
                        onclick={() => quizState.submitAnswer()}
                    >
                        <div class="flex items-center justify-center gap-3">
                            {#if quizState.isSubmitting}
                                <Loader2 size={20} class="animate-spin" />
                                <span>Memproses...</span>
                            {:else}
                                <span>Periksa Jawaban</span>
                                <CheckCircle2 size={20} />
                            {/if}
                        </div>
                    </Button>
                </div>
            </div>
        </div>
    {/if}

    <FeedbackModal state={quizState} />
    <XPGainEffect />
    {#if showDebug}
        <AdaptiveDebugPanel {quizState} />
    {/if}

    <Modal show={showWarning} maxWidth="sm" onclose={() => (showWarning = false)}>
        <div class="p-6">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100">
                    <AlertTriangle class="h-5 w-5 text-rose-600" />
                </div>
                <h2 class="text-lg font-black text-rose-800">Peringatan!</h2>
            </div>
            <p class="mb-6 text-center text-base font-medium text-slate-700">
                {warningMessage}
            </p>
            <div class="flex flex-col gap-3">
                <Button
                    variant="primary"
                    size="md"
                    class="w-full font-black tracking-widest uppercase"
                    onclick={() => (showWarning = false)}
                >
                    Saya Mengerti
                </Button>
                <p class="text-center text-xs font-bold tracking-tighter text-slate-400 uppercase">
                    Pelanggaran akan dicatat oleh sistem
                </p>
            </div>
        </div>
    </Modal>
</App>
