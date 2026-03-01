<script lang="ts">
    import App from "@/layouts/App.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import GuestBanner from "@/components/shared/GuestBanner.svelte";
    import { Terminal, UserCheck } from "lucide-svelte";
    import { QuestionShowState } from "@/states/Mahasiswa/QuizState.svelte";
    import QuestionSessionCard from "@/components/quiz/QuestionSessionCard.svelte";
    import FinishStateCard from "@/components/quiz/FinishStateCard.svelte";
    import FeedbackModal from "@/components/quiz/FeedbackModal.svelte";
    import AdaptiveDebugPanel from "@/components/quiz/AdaptiveDebugPanel.svelte";
    import type { Material, Question, DifficultyLevel, QuizSessionState } from "@/types";

    const {
        material,
        currentQuestion = null,
        currentQuestionNumber = 1,
        totalQuestions = 0,
        answeredCount = 0,
        difficulty = "beginner" as const,
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

    let state = new QuestionShowState(
        material,
        currentQuestion as Question,
        difficulty,
        studentState,
    );

    $effect(() => {
        if (state.currentQuestion?.id !== currentQuestion?.id) {
            state.selectedMultipleChoiceAnswer = null;
            state.fillInTheBlankAnswer = "";
            state.dragAndDropAnswers = {};
            state.startTime = Date.now();
        }
        state.material = material;
        state.currentQuestion = currentQuestion;
        state.difficulty = difficulty;
        state.studentState = studentState;
    });

    const progressPercentage = $derived(
        totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0
    );

    const DEBUG_MODE = import.meta.env['VITE_ADAPTIVE_DEBUG'] === "true";
    const showDebug = $derived(state.showAdaptiveIndicator && DEBUG_MODE);
</script>

<App title={`Latihan Soal - ${material.title}`}>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h1
                    class="text-4xl font-bold text-slate-900 tracking-widest uppercase flex items-center justify-center gap-4"
                >
                    <Terminal size={32} class="text-primary-600" />
                    Evaluasi: {material.title}
                </h1>
                <p
                    class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-3"
                >
                    Mode Ujian Terkendali & Aman
                </p>
                <div class="mt-6 max-w-xl mx-auto">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-500"
                            >Soal {currentQuestionNumber} / {totalQuestions}</span
                        >
                        <span class="text-xs font-bold text-primary-600"
                            >{Math.round(progressPercentage)}%</span
                        >
                    </div>
                    <ProgressBar
                        value={progressPercentage}
                        color="blue"
                        height="h-1.5"
                    />
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
