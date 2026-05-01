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
    import type { Question, QuestionShowProps } from '@/types';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';

    const {
        material,
        current_question = null,
        current_question_number = 1,
        total_questions = 0,
        answered_count = 0,
        material_answered_count = 0,
        difficulty = 'beginner' as const,
        is_guest: _isGuest = false,
        student_state,
        feedback = null,
    }: QuestionShowProps & { material_answered_count: number; feedback: any } = $props();

    let quizState = untrack(
        () =>
            new QuestionShowState(material, current_question as Question, difficulty, student_state)
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
</script>

<App
    title={`Latihan Soal - ${material.title}`}
    showSidebar={false}
    showNavbar={false}
    fullWidth={true}
>
    <div class="py-12">
        <div
            class="mx-auto max-w-5xl px-4 transition-all duration-500 sm:px-6 lg:px-8"
            class:pb-40={quizState.show_feedback}
            class:pointer-events-none={quizState.show_feedback}
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
                                        ? current_question_number
                                        : total_questions} /
                                    {total_questions}
                                </span>
                            </div>
                        </div>
                        <div id="quiz-progress" class="relative">
                            <ProgressBar 
                                value={progressPercentage} 
                                height="h-4" 
                                color="blue" 
                            />
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
                    answered_count={material_answered_count}
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
