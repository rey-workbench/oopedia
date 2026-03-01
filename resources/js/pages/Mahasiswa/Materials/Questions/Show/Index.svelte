<script>
    import App from "@/layouts/App.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import GuestBanner from "@/components/shared/GuestBanner.svelte";
    import { Terminal, UserCheck } from "lucide-svelte";
    import { QuestionShowState } from "@/states/Mahasiswa/QuizState.svelte";
    import QuestionSessionCard from "@/components/quiz/QuestionSessionCard.svelte";
    import FinishStateCard from "@/components/quiz/FinishStateCard.svelte";
    import FeedbackModal from "@/components/quiz/FeedbackModal.svelte";
    import AdaptiveDebugPanel from "@/components/quiz/AdaptiveDebugPanel.svelte";

    export let material = {};
    export let currentQuestion = null;
    export let currentQuestionNumber = 1;
    export let totalQuestions = 0;
    export let answeredCount = 0;
    export let difficulty = "beginner";
    export let studentState = {};

    let state = new QuestionShowState(
        material,
        currentQuestion,
        difficulty,
        studentState,
    );

    // Sync Svelte 4 props from Inertia navigations to Svelte 5 class instance
    $: {
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
    }

    $: progressPercentage =
        totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;

    const DEBUG_MODE = import.meta.env.VITE_ADAPTIVE_DEBUG === "true";
    $: showDebug = state.showAdaptiveIndicator && DEBUG_MODE;
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
                        color="primary"
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
                    <svelte:fragment slot="icon">
                        <UserCheck size={24} class="text-amber-600" />
                    </svelte:fragment>
                </GuestBanner>
            {/if}

            {#if state.currentQuestion}
                <QuestionSessionCard bind:state />
            {:else}
                <FinishStateCard bind:state {material} {answeredCount} />
            {/if}
        </div>
    </div>

    <FeedbackModal bind:state />
    <AdaptiveDebugPanel bind:state {showDebug} />
</App>
