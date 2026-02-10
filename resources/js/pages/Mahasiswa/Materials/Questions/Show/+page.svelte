<script>
    import App from "../../../../../layouts/App.svelte";
    import Badge from "../../../../../components/ui/Badge.svelte";
    import Button from "../../../../../components/ui/Button.svelte";
    import ProgressBar from "../../../../../components/ui/ProgressBar.svelte";
    import MultipleChoice from "../../../../../components/quiz/MultipleChoice.svelte";
    import FillInTheBlank from "../../../../../components/quiz/FillInTheBlank.svelte";
    import DragAndDrop from "../../../../../components/quiz/DragAndDrop.svelte";
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
        XCircle,
        RotateCcw,
        ArrowRight,
    } from "lucide-svelte";
    import axios from "axios"; // Assuming axios is available, otherwise usage might need adjustment
    import GuestBanner from "../../../../../components/ui/GuestBanner.svelte";

    export let material = {};
    export let currentQuestion = null;
    export let currentQuestionNumber = 1;
    export let difficulty = "beginner";
    export let isGuest = false;
    // Stats passed from controller or computed
    // Note: Controller doesn't pass 'xp', 'streak', 'hintsAvailable' explicitly in the $data array
    // We might need to fetch them or pass them if they are in shared props.
    // The blade view fetched them using PHP directly in the view.
    // For now, we'll try to use $page.props.auth.user if available, or just defaults.
    // If needed we can update the controller to pass these.

    let xp = 0;
    let streak = 0;
    let hintsAvailable = 3;

    // Question State
    let fillInTheBlankAnswer = "";
    let selectedMultipleChoiceAnswer = null;
    let dragAndDropAnswers = {};

    // UI State
    let isSubmitting = false;
    let showFeedback = false;
    let feedbackData = {
        status: "success", // or 'error'
        message: "",
        explanation: "", // Not in JSON response currently, maybe need to add?
        nextUrl: "",
        adaptiveResult: {},
    };

    let usedHint = false;
    let startTime = Date.now();

    // Calculate Progress
    $: progressPercentage =
        material.questions_count > 0
            ? (currentQuestionNumber / material.questions_count) * 100
            : 0;

    // Hint Logic
    function useHint() {
        if (hintsAvailable > 0 && currentQuestion.hint) {
            alert(currentQuestion.hint); // Simple alert for now, could be a modal or UI element
            usedHint = true;
            hintsAvailable--;
            // In a real app we might want to sync this decrement with backend or just trust the next backend response
        }
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
        };

        if (currentQuestion.question_type === "fill_in_the_blank") {
            payload.fill_in_the_blank_answer = fillInTheBlankAnswer;
            payload.answer = fillInTheBlankAnswer; // Backup/Alias
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
            };

            // Update local stats from adaptive result if available
            if (data.adaptiveResult?.new_state?.gamification_data) {
                const gData = data.adaptiveResult.new_state.gamification_data;
                xp = gData.global_xp || xp;
                // Streak updates might need parsing logic or just rely on page reload next time
            }

            showFeedback = true;
        } catch (error) {
            console.error("Error submitting answer:", error);
            alert(
                "Terjadi kesalahan saat memeriksa jawaban. Silakan coba lagi.",
            );
        } finally {
            isSubmitting = false;
        }
    }

    function handleNext() {
        showFeedback = false;
        if (feedbackData.nextUrl) {
            router.visit(feedbackData.nextUrl);
        }
    }

    function handleTryAgain() {
        showFeedback = false;
        // Reset inputs?
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
                    <Terminal size={32} class="text-blue-600" />
                    Evaluasi: {material.title}
                </h1>
                <p
                    class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-3"
                >
                    Mode Ujian Terkendali & Aman
                </p>
                <div class="mt-6 max-w-xl mx-auto">
                    <ProgressBar
                        value={progressPercentage}
                        color="blue"
                        height="h-1"
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
                                        <h5
                                            class={`text-lg font-bold ${difficulty === "hard" ? "text-rose-600" : difficulty === "medium" ? "text-amber-600" : "text-emerald-600"}`}
                                        >
                                            {difficulty
                                                .charAt(0)
                                                .toUpperCase() +
                                                difficulty.slice(1)}
                                        </h5>
                                    </div>

                                    <!-- XP -->
                                    <div>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5"
                                            >XP</span
                                        >
                                        <h5
                                            class="text-lg font-bold text-blue-600 flex items-center gap-1"
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
                                    class="group flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 transition-all font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    on:click={useHint}
                                    disabled={hintsAvailable <= 0}
                                >
                                    <div
                                        class="w-6 h-6 rounded-lg bg-indigo-200 group-hover:bg-indigo-300 flex items-center justify-center transition-colors"
                                    >
                                        <Lightbulb size={16} />
                                    </div>
                                    <span>Hint ({hintsAvailable})</span>
                                </button>
                            </div>
                        </div>
                    {/if}

                    <!-- Question Content -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-6">
                            <Badge variant="primary" size="lg"
                                ><HelpCircle size={18} class="mr-2" /> Soal</Badge
                            >
                            <Badge variant="secondary" size="lg"
                                >{currentQuestion.difficulty
                                    .charAt(0)
                                    .toUpperCase() +
                                    currentQuestion.difficulty.slice(1)} Question</Badge
                            >
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
                            class="bg-gradient-to-br from-emerald-500 to-teal-600 p-12 text-center text-white relative"
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
                                    LUAR BIASA!
                                </h2>
                                <p class="text-emerald-50 text-xl font-medium">
                                    Anda telah menyelesaikan semua soal pada
                                    materi ini.
                                </p>
                            </div>
                        </div>

                        <div class="p-10 bg-white">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <a
                                    href={`/mahasiswa/materials/${material.id}/questions/levels?difficulty=${difficulty}`}
                                    class="group p-6 rounded-2xl bg-slate-50 border-2 border-transparent hover:border-emerald-200 hover:bg-emerald-50 transition-all text-center"
                                >
                                    <div
                                        class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform"
                                    >
                                        <ListOrdered size={24} />
                                    </div>
                                    <span class="font-bold text-slate-700 block"
                                        >Pilih Level</span
                                    >
                                </a>

                                <a
                                    href={`/mahasiswa/materials/${material.id}`}
                                    class="group p-6 rounded-2xl bg-slate-50 border-2 border-transparent hover:border-blue-200 hover:bg-blue-50 transition-all text-center"
                                >
                                    <div
                                        class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform"
                                    >
                                        <Book size={24} />
                                    </div>
                                    <span class="font-bold text-slate-700 block"
                                        >Baca Materi</span
                                    >
                                </a>

                                <a
                                    href="/mahasiswa/dashboard"
                                    class="group p-6 rounded-2xl bg-slate-50 border-2 border-transparent hover:border-indigo-200 hover:bg-indigo-50 transition-all text-center"
                                >
                                    <div
                                        class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform"
                                    >
                                        <Home size={24} />
                                    </div>
                                    <span class="font-bold text-slate-700 block"
                                        >Dashboard</span
                                    >
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>

    <!-- Feedback Modal -->
    {#if showFeedback}
        <div
            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-all duration-300"
        >
            <div
                class="bg-white rounded-3xl p-12 text-center shadow-2xl max-w-lg w-full mx-4 transform scale-100 transition-all"
            >
                <div class="text-8xl mb-6">
                    {#if feedbackData.status === "success"}
                        <CheckCircle2
                            size={96}
                            class="text-emerald-500 mx-auto"
                        />
                    {:else}
                        <XCircle size={96} class="text-rose-500 mx-auto" />
                    {/if}
                </div>

                <h2
                    class={`text-4xl font-bold mb-4 uppercase tracking-widest ${feedbackData.status === "success" ? "text-emerald-600" : "text-rose-600"}`}
                >
                    {feedbackData.status === "success" ? "BENAR!" : "SALAH!"}
                </h2>

                <p class="text-lg text-slate-600 mb-8">
                    {feedbackData.message}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    {#if feedbackData.status === "error"}
                        <Button
                            variant="outline"
                            on:click={handleTryAgain}
                            class="px-8 py-3 uppercase tracking-widest text-sm font-bold"
                        >
                            <RotateCcw size={18} class="mr-2" /> Coba Lagi
                        </Button>
                    {:else}
                        <Button
                            variant="primary"
                            on:click={handleNext}
                            class="px-8 py-3 uppercase tracking-widest text-sm font-bold"
                        >
                            Lanjut <ArrowRight size={18} class="ml-2" />
                        </Button>
                    {/if}
                </div>
            </div>
        </div>
    {/if}
</App>
