<script>
    import Badge from "@/components/ui/Badge.svelte";
    import Button from "@/components/ui/Button.svelte";
    import MultipleChoice from "@/components/quiz/MultipleChoice.svelte";
    import FillInTheBlank from "@/components/quiz/FillInTheBlank.svelte";
    import DragAndDrop from "@/components/quiz/DragAndDrop.svelte";
    import {
        Star,
        Flame,
        Lightbulb,
        HelpCircle,
        Loader2,
        CheckCircle2,
        X,
    } from "lucide-svelte";

    let { state = $bindable() } = $props();
</script>

<div class="bg-white rounded-xl shadow-md p-6">
    {#if !state.isGuest}
        <div
            class="mb-8 p-1 bg-slate-50 rounded-2xl flex items-center gap-1 shadow-inner"
        >
            <div
                class="flex-1 px-6 py-3 rounded-xl bg-white shadow-sm flex items-center justify-between"
            >
                <div class="flex items-center gap-6">
                    <div>
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5"
                            >Kesulitan</span
                        >
                        <span
                            class={`inline-flex items-center px-2.5 py-0.5 rounded-lg text-sm font-bold ${state.getDifficultyColor(state.difficulty)}`}
                        >
                            {state.getDifficultyLabel(state.difficulty)}
                        </span>
                    </div>

                    <div>
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5"
                            >Level</span
                        >
                        <h5 class="text-lg font-bold text-slate-700">
                            {state.level}
                        </h5>
                    </div>

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

                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    onclick={() => state.useHint()}
                    disabled={state.hintsAvailable <= 0 ||
                        !state.currentQuestion?.hint}
                    class="group bg-primary-50 text-primary-600 hover:bg-primary-100 hover:text-primary-700"
                >
                    <div
                        class="w-6 h-6 rounded-lg bg-primary-200 group-hover:bg-primary-300 flex items-center justify-center transition-colors mr-2"
                    >
                        <Lightbulb size={16} />
                    </div>
                    Hint ({state.hintsAvailable})
                </Button>
            </div>
        </div>
    {/if}

    {#if state.showHint && state.currentQuestion?.hint}
        <div
            class="mb-6 p-5 bg-amber-50 border-2 border-amber-200 rounded-2xl relative animate-fadeIn"
        >
            <Button
                type="button"
                variant="ghost"
                size="sm"
                icon={X}
                onclick={() => state.closeHint()}
                class="absolute top-3 right-3 w-6 h-6 rounded-full bg-amber-200 hover:bg-amber-300 text-amber-700 p-0"
            />
            <div class="flex items-start gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-amber-200 flex items-center justify-center flex-shrink-0"
                >
                    <Lightbulb size={20} class="text-amber-700" />
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-800 mb-1">
                        Petunjuk
                    </h4>
                    <p class="text-sm text-amber-700">
                        {state.currentQuestion.hint}
                    </p>
                </div>
            </div>
        </div>
    {/if}

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
                    {state.getDifficultyLabel(state.currentQuestion.difficulty)}
                </Badge>
            {/if}
        </div>

        {#if state.currentQuestion.question_type === "fill_in_the_blank"}
            <FillInTheBlank
                question={state.currentQuestion}
                bind:answerText={state.fillInTheBlankAnswer}
                oninput={(text) => (state.fillInTheBlankAnswer = text)}
            />
        {:else if state.currentQuestion.question_type === "drag_and_drop"}
            <DragAndDrop
                question={state.currentQuestion}
                bind:dragAndDropAnswers={state.dragAndDropAnswers}
            />
        {:else}
            <MultipleChoice
                question={state.currentQuestion}
                bind:selectedAnswerId={state.selectedMultipleChoiceAnswer}
                onselect={(answerId) =>
                    (state.selectedMultipleChoiceAnswer = answerId)}
            />
        {/if}
    </div>

    <div class="mt-6">
        <Button
            variant="primary"
            class="w-full py-3"
            disabled={state.isSubmitting}
            onclick={() => state.submitAnswer()}
        >
            {#if state.isSubmitting}
                <Loader2 size={18} class="mr-2 animate-spin" /> Memeriksa...
            {:else}
                <CheckCircle2 size={18} class="mr-2" /> Periksa Jawaban
            {/if}
        </Button>
    </div>
</div>

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
