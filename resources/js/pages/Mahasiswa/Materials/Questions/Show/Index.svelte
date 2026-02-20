<script>
    import App from "@/layouts/App.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import Button from "@/components/ui/Button.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import MultipleChoice from "@/components/quiz/MultipleChoice.svelte";
    import FillInTheBlank from "@/components/quiz/FillInTheBlank.svelte";
    import DragAndDrop from "@/components/quiz/DragAndDrop.svelte";
    import AdaptiveFeedbackModal from "@/components/adaptive/AdaptiveFeedbackModal.svelte";
    import AdaptiveIndicator from "@/components/adaptive/AdaptiveIndicator.svelte";
    import {
        Terminal,
        UserCheck,
        Star,
        Flame,
        Lightbulb,
        HelpCircle,
        Loader2,
        CheckCircle2,
        Trophy,
        Check,
        Home,
        X,
        Target,
        BarChart3,
    } from "lucide-svelte";
    import GuestBanner from "@/components/ui/GuestBanner.svelte";
    import { QuestionShowState } from "@/states/Mahasiswa/QuestionShowState.svelte";

    // Props from controller
    export let material = {};
    export let currentQuestion = null;
    export let currentQuestionNumber = 1;
    export let totalQuestions = 0;
    export let answeredCount = 0;
    export let difficulty = "beginner";
    export let isGuest = false;
    export let studentState = {};

    const state = new QuestionShowState(
        material,
        currentQuestion,
        difficulty,
        studentState,
        isGuest,
    );

    // Calculate Progress using controller-provided data (pure calculation, not state dependent)
    $: progressPercentage =
        totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;
</script>

<App title={`Latihan Soal - ${material.title}`}>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Progress & Header -->
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

            <!-- Guest Warning -->
            {#if isGuest}
                <GuestBanner
                    show={isGuest}
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
                <div class="bg-white rounded-xl shadow-md p-6">
                    <!-- Stats Bar -->
                    {#if !isGuest}
                        <div
                            class="mb-8 p-1 bg-slate-50 rounded-2xl flex items-center gap-1 shadow-inner"
                        >
                            <div
                                class="flex-1 px-6 py-3 rounded-xl bg-white shadow-sm flex items-center justify-between"
                            >
                                <div class="flex items-center gap-6">
                                    <!-- Difficulty -->
                                    <div>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5"
                                            >Kesulitan</span
                                        >
                                        <span
                                            class={`inline-flex items-center px-2.5 py-0.5 rounded-lg text-sm font-bold ${state.getDifficultyColor(state.difficulty)}`}
                                        >
                                            {state.getDifficultyLabel(
                                                state.difficulty,
                                            )}
                                        </span>
                                    </div>

                                    <!-- Level -->
                                    <div>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5"
                                            >Level</span
                                        >
                                        <h5
                                            class="text-lg font-bold text-slate-700"
                                        >
                                            {state.level}
                                        </h5>
                                    </div>

                                    <!-- XP -->
                                    <div>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5"
                                            >XP</span
                                        >
                                        <h5
                                            class="text-lg font-bold text-primary-600 flex items-center gap-1"
                                        >
                                            <Star
                                                size={14}
                                                class="text-amber-400 fill-current"
                                            />
                                            <span>{state.xp}</span>
                                        </h5>
                                    </div>

                                    <!-- Streak -->
                                    <div>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5"
                                            >Streak</span
                                        >
                                        <h5
                                            class="text-lg font-bold text-orange-600 flex items-center gap-1"
                                        >
                                            <Flame
                                                size={14}
                                                class="text-orange-500 fill-current"
                                            />
                                            <span>{state.streak}</span>
                                        </h5>
                                    </div>
                                </div>

                                <!-- Hint Button -->
                                <button
                                    type="button"
                                    class="group flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-50 text-primary-600 hover:bg-primary-100 hover:text-primary-700 transition-all font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    on:click={() => state.useHint()}
                                    disabled={state.hintsAvailable <= 0 ||
                                        !state.currentQuestion?.hint}
                                >
                                    <div
                                        class="w-6 h-6 rounded-lg bg-primary-200 group-hover:bg-primary-300 flex items-center justify-center transition-colors"
                                    >
                                        <Lightbulb size={16} />
                                    </div>
                                    <span>Hint ({state.hintsAvailable})</span>
                                </button>
                            </div>
                        </div>
                    {/if}

                    <!-- Hint Display -->
                    {#if state.showHint && state.currentQuestion?.hint}
                        <div
                            class="mb-6 p-5 bg-amber-50 border-2 border-amber-200 rounded-2xl relative animate-fadeIn"
                        >
                            <button
                                type="button"
                                class="absolute top-3 right-3 w-6 h-6 rounded-full bg-amber-200 hover:bg-amber-300 flex items-center justify-center transition-colors"
                                on:click={() => state.closeHint()}
                            >
                                <X size={14} class="text-amber-700" />
                            </button>
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-amber-200 flex items-center justify-center flex-shrink-0"
                                >
                                    <Lightbulb
                                        size={20}
                                        class="text-amber-700"
                                    />
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-bold text-amber-800 mb-1"
                                    >
                                        Petunjuk
                                    </h4>
                                    <p class="text-sm text-amber-700">
                                        {state.currentQuestion.hint}
                                    </p>
                                </div>
                            </div>
                        </div>
                    {/if}

                    <!-- Question Content -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-6">
                            <Badge variant="primary" size="lg"
                                ><HelpCircle size={18} class="mr-2" /> Soal</Badge
                            >
                            {#if state.currentQuestion.difficulty}
                                <Badge
                                    variant="outline"
                                    size="sm"
                                    class={state.getDifficultyColor(
                                        state.currentQuestion.difficulty,
                                    )}
                                >
                                    {state.getDifficultyLabel(
                                        state.currentQuestion.difficulty,
                                    )}
                                </Badge>
                            {/if}
                        </div>

                        {#if state.currentQuestion.question_type === "fill_in_the_blank"}
                            <FillInTheBlank
                                question={state.currentQuestion}
                                bind:answerText={state.fillInTheBlankAnswer}
                                on:input={(e) =>
                                    (state.fillInTheBlankAnswer =
                                        e.detail.text)}
                            />
                        {:else if state.currentQuestion.question_type === "drag_and_drop"}
                            <DragAndDrop
                                question={state.currentQuestion}
                                bind:dragAndDropAnswers={
                                    state.dragAndDropAnswers
                                }
                            />
                        {:else}
                            <MultipleChoice
                                question={state.currentQuestion}
                                bind:selectedAnswerId={
                                    state.selectedMultipleChoiceAnswer
                                }
                                on:select={(e) =>
                                    (state.selectedMultipleChoiceAnswer =
                                        e.detail.answerId)}
                            />
                        {/if}
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <Button
                            variant="primary"
                            class="w-full py-3"
                            disabled={state.isSubmitting}
                            on:click={() => state.submitAnswer()}
                        >
                            {#if state.isSubmitting}
                                <Loader2 size={18} class="mr-2 animate-spin" /> Memeriksa...
                            {:else}
                                <CheckCircle2 size={18} class="mr-2" /> Periksa Jawaban
                            {/if}
                        </Button>
                    </div>
                </div>
            {:else}
                <!-- Finish State -->
                <div class="max-w-3xl mx-auto">
                    <div
                        class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100"
                    >
                        <div
                            class="bg-emerald-600 p-12 text-center text-white relative"
                        >
                            <div class="absolute top-0 right-0 p-8 opacity-10">
                                <Trophy size={96} class="text-white" />
                            </div>
                            <div class="relative z-10">
                                <div
                                    class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl border-4 border-white/30"
                                >
                                    <Check size={48} class="text-white" />
                                </div>
                                <h2
                                    class="text-4xl font-bold mb-3 tracking-widest"
                                >
                                    HEBAT!
                                </h2>
                                <p class="text-emerald-50 text-xl font-medium">
                                    Kamu sudah menjawab semua soal di materi ini
                                    dengan baik.
                                </p>
                            </div>
                        </div>

                        <!-- Stats Summary -->
                        {#if !isGuest}
                            <div
                                class="grid grid-cols-3 gap-0 border-b border-slate-100"
                            >
                                <div
                                    class="p-6 text-center border-r border-slate-100"
                                >
                                    <div
                                        class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center mx-auto mb-2"
                                    >
                                        <Target
                                            size={20}
                                            class="text-primary-600"
                                        />
                                    </div>
                                    <div
                                        class="text-2xl font-bold text-slate-800"
                                    >
                                        {answeredCount}
                                    </div>
                                    <div
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                                    >
                                        Soal Dijawab
                                    </div>
                                </div>
                                <div
                                    class="p-6 text-center border-r border-slate-100"
                                >
                                    <div
                                        class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-2"
                                    >
                                        <Star
                                            size={20}
                                            class="text-amber-500"
                                        />
                                    </div>
                                    <div
                                        class="text-2xl font-bold text-slate-800"
                                    >
                                        {state.xp}
                                    </div>
                                    <div
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                                    >
                                        Total XP
                                    </div>
                                </div>
                                <div class="p-6 text-center">
                                    <div
                                        class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center mx-auto mb-2"
                                    >
                                        <Flame
                                            size={20}
                                            class="text-orange-500"
                                        />
                                    </div>
                                    <div
                                        class="text-2xl font-bold text-slate-800"
                                    >
                                        {state.streak}
                                    </div>
                                    <div
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                                    >
                                        Streak
                                    </div>
                                </div>
                            </div>
                        {/if}

                        <div class="p-10 bg-white text-center space-y-4">
                            <a
                                href={`/mahasiswa/materials/${material.id}/questions/review`}
                                class="inline-flex items-center justify-center font-bold tracking-tight transition-all duration-300 active:scale-95 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 px-8 py-3.5 text-sm uppercase mr-3"
                            >
                                <BarChart3 size={18} class="mr-2" /> Review Jawaban
                            </a>
                            <a
                                href={`/mahasiswa/materials/${material.id}`}
                                class="inline-flex items-center justify-center font-bold tracking-tight transition-all duration-300 active:scale-95 rounded-xl bg-primary-600 text-white shadow-lg shadow-accent-950/20 hover:scale-[1.02] hover:shadow-accent-600/30 px-8 py-3.5 text-sm uppercase"
                            >
                                Kembali ke Materi <Home
                                    size={18}
                                    class="ml-2"
                                />
                            </a>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>

    <!-- Adaptive Feedback Modal -->
    <AdaptiveFeedbackModal
        show={state.showFeedback}
        feedbackData={state.feedbackData}
        on:next={() => state.handleNext()}
        on:tryAgain={() => state.handleTryAgain()}
    />

    <!-- Adaptive Indicator (Debug/Info Panel) -->
    {#if !isGuest}
        <AdaptiveIndicator
            show={state.showAdaptiveIndicator}
            facts={state.adaptiveFacts}
            triggeredRule={state.adaptiveTriggeredRule}
            isProcessing={state.isSubmitting}
        />
    {/if}
</App>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    :global(.animate-fadeIn) {
        animation: fadeIn 0.3s ease-out;
    }
</style>
