<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import MultipleChoice from '@/components/quiz/MultipleChoice.svelte';
    import FillInTheBlank from '@/components/quiz/FillInTheBlank.svelte';
    import DragAndDrop from '@/components/quiz/DragAndDrop.svelte';
    import { Star, Flame, Lightbulb, HelpCircle, Loader2, CheckCircle2, X } from 'lucide-svelte';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte.ts';

    interface Props {
        state: QuestionShowState;
    }

    let { state = $bindable() }: Props = $props();
</script>

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

<div class="rounded-xl bg-white p-6 shadow-md">
    {#if !state.isGuest}
        <div class="mb-8 flex items-center gap-1 rounded-2xl bg-slate-50 p-1 shadow-inner">
            <div
                class="flex flex-1 items-center justify-between rounded-xl bg-white px-6 py-3 shadow-sm"
            >
                <div class="flex items-center gap-6">
                    <div>
                        <span
                            class="mb-0.5 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Kesulitan</span
                        >
                        <span
                            class={`inline-flex items-center rounded-lg px-2.5 py-0.5 text-sm font-bold ${state.getDifficultyColor(state.difficulty)}`}
                        >
                            {state.getDifficultyLabel(state.difficulty)}
                        </span>
                    </div>

                    <div>
                        <span
                            class="mb-0.5 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Level</span
                        >
                        <h5 class="text-lg font-bold text-slate-700">
                            {state.level}
                        </h5>
                    </div>

                    <div>
                        <span
                            class="mb-0.5 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >XP</span
                        >
                        <h5 class="text-primary-600 flex items-center gap-1 text-lg font-bold">
                            <Star size={14} class="fill-current text-amber-400" />
                            <span>{state.xp}</span>
                        </h5>
                    </div>

                    <div>
                        <span
                            class="mb-0.5 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Streak</span
                        >
                        <h5 class="flex items-center gap-1 text-lg font-bold text-orange-600">
                            <Flame size={14} class="fill-current text-orange-500" />
                            <span>{state.streak}</span>
                        </h5>
                    </div>
                </div>

                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    onclick={() => state.useHint()}
                    disabled={state.hintsAvailable <= 0 || !state.currentQuestion?.hint}
                    class="group bg-primary-50 text-primary-600 hover:bg-primary-100 hover:text-primary-700"
                >
                    <div
                        class="bg-primary-200 group-hover:bg-primary-300 mr-2 flex h-6 w-6 items-center justify-center rounded-lg transition-colors"
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
            class="animate-fadeIn relative mb-6 rounded-2xl border-2 border-amber-200 bg-amber-50 p-5"
        >
            <Button
                type="button"
                variant="ghost"
                size="sm"
                icon={X}
                onclick={() => state.closeHint()}
                class="absolute top-3 right-3 h-6 w-6 rounded-full bg-amber-200 p-0 text-amber-700 hover:bg-amber-300"
            />
            <div class="flex items-start gap-3">
                <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-amber-200"
                >
                    <Lightbulb size={20} class="text-amber-700" />
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-bold text-amber-800">Petunjuk</h4>
                    <p class="text-sm text-amber-700">
                        {state.currentQuestion.hint}
                    </p>
                </div>
            </div>
        </div>
    {/if}

    <div class="mb-6">
        <div class="mb-6 flex items-center justify-between">
            <Badge variant="primary" size="lg"><HelpCircle size={18} class="mr-2" /> Soal</Badge>
            {#if state.currentQuestion?.difficulty}
                <Badge
                    variant="outline"
                    size="sm"
                    class={state.getDifficultyColor(state.currentQuestion.difficulty)}
                >
                    {state.getDifficultyLabel(state.currentQuestion.difficulty)}
                </Badge>
            {/if}
        </div>

        {#if state.currentQuestion?.question_type === 'fill_in_the_blank'}
            <FillInTheBlank
                question={state.currentQuestion}
                bind:answerText={state.fillInTheBlankAnswer}
                oninput={(text) => (state.fillInTheBlankAnswer = text)}
            />
        {:else if state.currentQuestion?.question_type === 'drag_and_drop'}
            <DragAndDrop
                question={state.currentQuestion}
                bind:dragAndDropAnswers={state.dragAndDropAnswers}
            />
        {:else if state.currentQuestion}
            <MultipleChoice
                question={state.currentQuestion}
                selectedAnswerId={state.selectedMultipleChoiceAnswer as any}
                onselect={(answerId) => (state.selectedMultipleChoiceAnswer = answerId as any)}
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
