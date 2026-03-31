<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import Card from '@/components/ui/Card.svelte';
    import MultipleChoice from '@/components/quiz/MultipleChoice.svelte';
    import FillInTheBlank from '@/components/quiz/FillInTheBlank.svelte';
    import DragAndDrop from '@/components/quiz/DragAndDrop.svelte';
    import { Star, Flame, Lightbulb, Loader2, CheckCircle2, X } from 'lucide-svelte';
    import { fade, slide } from 'svelte/transition';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte.ts';

    interface Props {
        state: QuestionShowState;
    }

    let { state = $bindable() }: Props = $props();
</script>

<Card
    variant="none"
    padding="p-0"
    class="overflow-hidden rounded-3xl border-none bg-white shadow-2xl"
>
    {#if !state.isGuest}
        <div class="border-b border-slate-100 bg-slate-50/50 p-2">
            <Panel
                variant="none"
                rounded="2xl"
                padding="p-1"
                class="bg-white shadow-sm ring-1 ring-slate-100"
            >
                <div class="flex flex-col items-center justify-between gap-4 px-6 py-3 sm:flex-row">
                    <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-8">
                        <div class="text-center sm:text-left">
                            <span
                                class="mb-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >Kesulitan</span
                            >
                            <Badge
                                variant={state.difficulty === 'beginner'
                                    ? 'success'
                                    : state.difficulty === 'medium'
                                      ? 'warning'
                                      : 'danger'}
                                size="sm"
                                class="border-none font-bold"
                            >
                                {state.getDifficultyLabel(state.difficulty)}
                            </Badge>
                        </div>

                        <div class="text-center sm:text-left">
                            <span
                                class="mb-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >Level</span
                            >
                            <h5
                                class="text-xl font-black tracking-tighter text-slate-800 tabular-nums"
                            >
                                {state.level}
                            </h5>
                        </div>

                        <div class="text-center sm:text-left">
                            <span
                                class="mb-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >XP</span
                            >
                            <h5
                                class="text-primary-600 flex items-center justify-center gap-1.5 text-xl font-black tracking-tighter sm:justify-start"
                            >
                                <Star size={18} class="fill-current text-amber-400" />
                                <span>{state.xp}</span>
                            </h5>
                        </div>

                        <div class="text-center sm:text-left">
                            <span
                                class="mb-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >Streak</span
                            >
                            <h5
                                class="flex items-center justify-center gap-1.5 text-xl font-black tracking-tighter text-orange-600 sm:justify-start"
                            >
                                <Flame size={18} class="fill-current text-orange-500" />
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
                        class="group border-primary-100 bg-primary-50 text-primary-700 hover:bg-primary-100 hover:text-primary-800 flex items-center gap-2 rounded-xl px-4 font-bold tracking-wide"
                    >
                        <div
                            class="bg-primary-200 group-hover:bg-primary-300 flex h-7 w-7 items-center justify-center rounded-lg transition-colors"
                        >
                            <Lightbulb size={16} class="fill-white" />
                        </div>
                        Hint ({state.hintsAvailable})
                    </Button>
                </div>
            </Panel>
        </div>
    {/if}

    <div class="p-8 sm:p-10">
        {#if state.showHint && state.currentQuestion?.hint}
            <div
                transition:slide={{ duration: 300 }}
                class="relative mb-8 rounded-3xl border-2 border-amber-200 bg-amber-50/50 p-6 shadow-sm"
            >
                <button
                    type="button"
                    onclick={() => state.closeHint()}
                    class="absolute top-4 right-4 flex h-8 w-8 items-center justify-center rounded-full bg-white text-amber-500 shadow-sm transition-all hover:scale-110 hover:text-amber-600 active:scale-95"
                    aria-label="Tutup petunjuk"
                >
                    <X size={18} />
                </button>
                <div class="flex items-start gap-4 pr-8">
                    <div
                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-amber-400 shadow-lg shadow-amber-200"
                    >
                        <Lightbulb size={24} class="fill-white text-white" />
                    </div>
                    <div>
                        <h4
                            class="mb-1 text-[11px] font-black tracking-widest text-amber-700 uppercase"
                        >
                            Wawasan Adaptif
                        </h4>
                        <p class="text-base leading-relaxed font-medium text-amber-900">
                            {state.currentQuestion.hint}
                        </p>
                    </div>
                </div>
            </div>
        {/if}

        <div class="space-y-0">
            <!-- Question type components already contain their own header & layout -->
            {#if state.currentQuestion?.question_type === 'fill_in_the_blank'}
                <div transition:fade>
                    <FillInTheBlank
                        question={state.currentQuestion}
                        bind:answerText={state.fillInTheBlankAnswer}
                    />
                </div>
            {:else if state.currentQuestion?.question_type === 'drag_and_drop'}
                <div transition:fade>
                    <DragAndDrop
                        question={state.currentQuestion}
                        bind:dragAndDropAnswers={state.dragAndDropAnswers}
                    />
                </div>
            {:else if state.currentQuestion}
                <div transition:fade>
                    <MultipleChoice
                        question={state.currentQuestion}
                        selectedAnswerId={state.selectedMultipleChoiceAnswer as any}
                        onselect={(answerId) =>
                            (state.selectedMultipleChoiceAnswer = answerId as any)}
                    />
                </div>
            {/if}
        </div>

        <div class="mt-8 border-t border-slate-50 pt-8">
            <Button
                variant="primary"
                size="lg"
                class="shadow-primary-100 hover:shadow-primary-200 w-full py-5 text-base font-black tracking-widest uppercase shadow-xl transition-all hover:scale-[1.01] active:scale-[0.99]"
                disabled={state.isSubmitting}
                onclick={() => state.submitAnswer()}
            >
                {#if state.isSubmitting}
                    <Loader2 size={20} class="mr-2.5 animate-spin" /> Memeriksa...
                {:else}
                    <CheckCircle2 size={20} class="mr-2.5" /> Periksa Jawaban
                {/if}
            </Button>
            <p
                class="mt-4 text-center text-[10px] font-bold tracking-[0.2em] text-slate-300 uppercase"
            >
                Sistem Adaptif Oopedia • v2.0
            </p>
        </div>
    </div>
</Card>
