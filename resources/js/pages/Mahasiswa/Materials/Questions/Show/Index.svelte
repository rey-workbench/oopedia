<script>
    import App from "../../../../../layouts/App.svelte";
    import Badge from "../../../../../components/ui/Badge.svelte";
    import Button from "../../../../../components/ui/Button.svelte";
    import ProgressBar from "../../../../../components/ui/ProgressBar.svelte";
    import MultipleChoice from "../../../../../components/quiz/MultipleChoice.svelte";
    import FillInTheBlank from "../../../../../components/quiz/FillInTheBlank.svelte";
    import DragAndDrop from "../../../../../components/quiz/DragAndDrop.svelte";
    import AdaptiveFeedbackModal from "../../../../../components/adaptive/AdaptiveFeedbackModal.svelte";
    import AdaptiveIndicator from "../../../../../components/adaptive/AdaptiveIndicator.svelte";
    import { router, page } from "@inertiajs/svelte";
    import { onMount } from "svelte";
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
        ListOrdered,
        Book,
        Home,
        X,
        Target,
        BarChart3,
        Clock,
        Zap,
    } from "lucide-svelte";
    import axios from "axios";
    import GuestBanner from "../../../../../components/ui/GuestBanner.svelte";

    // Props from controller
    export let material = {};
    export let currentQuestion = null;
    export let currentQuestionNumber = 1;
    export let totalQuestions = 0;
    export let answeredCount = 0;
    export let difficulty = "beginner";
    export let isGuest = false;
    export let studentState = {};

    // Derive stats from studentState reactively
    $: xp = studentState?.gamification?.global_xp || 0;
    $: streak = studentState?.gamification?.current_streak || 0;
    $: level = studentState?.gamification?.current_level || "Pemula";
    $: hintsAvailable = studentState?.performance?.hints_available ?? 3;

    // Question State
    let fillInTheBlankAnswer = "";
    let selectedMultipleChoiceAnswer = null;
    let dragAndDropAnswers = {};

    // UI State
    let isSubmitting = false;
    let showFeedback = false;
    let showHint = false;
    let feedbackData = {
        status: "success",
        message: "",
        nextUrl: "",
        adaptiveResult: {},
    };

    let usedHint = false;
    let startTime = Date.now();

    // Adaptive UI State
    let showAdaptiveIndicator = false;
    let adaptiveFacts = [];
    let adaptiveTriggeredRule = null;

    // Calculate Progress using controller-provided data
    $: progressPercentage =
        totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;

    // Difficulty display helpers
    function getDifficultyLabel(diff) {
        const labels = {
            beginner: "Pemula",
            medium: "Menengah",
            hard: "Sulit",
        };
        return labels[diff] || diff;
    }

    function getDifficultyColor(diff) {
        const colors = {
            beginner: "text-emerald-600 bg-emerald-50",
            medium: "text-amber-600 bg-amber-50",
            hard: "text-rose-600 bg-rose-50",
        };
        return colors[diff] || "text-slate-600 bg-slate-50";
    }

    // Hint Logic
    function useHint() {
        if (hintsAvailable > 0 && currentQuestion?.hint) {
            usedHint = true;
            showHint = true;
            hintsAvailable--;
        }
    }

    function closeHint() {
        showHint = false;
    }

    // Answer Submission
    async function submitAnswer() {
        if (isSubmitting) return;
        isSubmitting = true;

        const timeSpent = Math.max(
            0,
            Math.floor((Date.now() - startTime) / 1000),
        );

        let payload = {
            question_id: currentQuestion.id,
            material_id: material.id,
            used_hint: usedHint,
            time_spent: timeSpent,
            difficulty: difficulty, // Send current difficulty for guest XP matching
        };

        if (currentQuestion.question_type === "fill_in_the_blank") {
            payload.fill_in_the_blank_answer = fillInTheBlankAnswer;
            payload.answer = fillInTheBlankAnswer;
        } else if (currentQuestion.question_type === "drag_and_drop") {
            payload.drag_and_drop_answers = JSON.stringify(dragAndDropAnswers);
        } else {
            payload.answer = selectedMultipleChoiceAnswer;
        }

        try {
            const response = await axios.post(
                `/mahasiswa/materials/${material.id}/questions/${currentQuestion.id}/check`,
                payload,
            );

            const data = response.data;

            feedbackData = {
                status: data.status,
                message: data.message,
                nextUrl: data.nextUrl,
                adaptiveResult: data.adaptiveResult,
                score: data.score,
            };

            // Update adaptive indicator
            if (data.adaptiveResult) {
                adaptiveFacts = data.adaptiveResult.facts || [];
                adaptiveTriggeredRule =
                    data.adaptiveResult.triggered_rule || null;
                showAdaptiveIndicator = true;
            }

            // Update local stats by updating studentState
            if (data.adaptiveResult?.new_state) {
                studentState = data.adaptiveResult.new_state;
            }

            showHint = false;
            showFeedback = true;
        } catch (error) {
            console.error("Error submitting answer:", error);
            feedbackData = {
                status: "error",
                message:
                    error.response?.data?.message ||
                    "Terjadi kesalahan saat memeriksa jawaban.",
                nextUrl: "",
                adaptiveResult: null,
            };
            showFeedback = true;
        } finally {
            isSubmitting = false;
        }
    }

    function handleNext() {
        showFeedback = false;
        showHint = false;
        if (feedbackData.nextUrl) {
            router.visit(feedbackData.nextUrl);
        }
    }

    function handleTryAgain() {
        showFeedback = false;
        showHint = false;
        fillInTheBlankAnswer = "";
        selectedMultipleChoiceAnswer = null;
        dragAndDropAnswers = {};
        startTime = Date.now();
    }
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

            {#if currentQuestion}
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
                                            class={`inline-flex items-center px-2.5 py-0.5 rounded-lg text-sm font-bold ${getDifficultyColor(difficulty)}`}
                                        >
                                            {getDifficultyLabel(difficulty)}
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
                                            {level}
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
                                            <span>{xp}</span>
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
                                            <span>{streak}</span>
                                        </h5>
                                    </div>
                                </div>

                                <!-- Hint Button -->
                                <button
                                    type="button"
                                    class="group flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-50 text-primary-600 hover:bg-primary-100 hover:text-primary-700 transition-all font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    on:click={useHint}
                                    disabled={hintsAvailable <= 0 ||
                                        !currentQuestion?.hint}
                                >
                                    <div
                                        class="w-6 h-6 rounded-lg bg-primary-200 group-hover:bg-primary-300 flex items-center justify-center transition-colors"
                                    >
                                        <Lightbulb size={16} />
                                    </div>
                                    <span>Hint ({hintsAvailable})</span>
                                </button>
                            </div>
                        </div>
                    {/if}

                    <!-- Hint Display -->
                    {#if showHint && currentQuestion?.hint}
                        <div
                            class="mb-6 p-5 bg-amber-50 border-2 border-amber-200 rounded-2xl relative animate-fadeIn"
                        >
                            <button
                                type="button"
                                class="absolute top-3 right-3 w-6 h-6 rounded-full bg-amber-200 hover:bg-amber-300 flex items-center justify-center transition-colors"
                                on:click={closeHint}
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
                                        {currentQuestion.hint}
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
                            {#if currentQuestion.difficulty}
                                <Badge
                                    variant="outline"
                                    size="sm"
                                    class={getDifficultyColor(
                                        currentQuestion.difficulty,
                                    )}
                                >
                                    {getDifficultyLabel(
                                        currentQuestion.difficulty,
                                    )}
                                </Badge>
                            {/if}
                        </div>

                        {#if currentQuestion.question_type === "fill_in_the_blank"}
                            <FillInTheBlank
                                question={currentQuestion}
                                bind:answerText={fillInTheBlankAnswer}
                                on:input={(e) =>
                                    (fillInTheBlankAnswer = e.detail.text)}
                            />
                        {:else if currentQuestion.question_type === "drag_and_drop"}
                            <DragAndDrop
                                question={currentQuestion}
                                bind:dragAndDropAnswers
                            />
                        {:else}
                            <MultipleChoice
                                question={currentQuestion}
                                bind:selectedAnswerId={
                                    selectedMultipleChoiceAnswer
                                }
                                on:select={(e) =>
                                    (selectedMultipleChoiceAnswer =
                                        e.detail.answerId)}
                            />
                        {/if}
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <Button
                            variant="primary"
                            class="w-full py-3"
                            disabled={isSubmitting}
                            on:click={submitAnswer}
                        >
                            {#if isSubmitting}
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
                                        {xp}
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
                                        {streak}
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
        show={showFeedback}
        {feedbackData}
        on:next={handleNext}
        on:tryAgain={handleTryAgain}
    />

    <!-- Adaptive Indicator (Debug/Info Panel) -->
    {#if !isGuest}
        <AdaptiveIndicator
            show={showAdaptiveIndicator}
            facts={adaptiveFacts}
            triggeredRule={adaptiveTriggeredRule}
            isProcessing={isSubmitting}
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
