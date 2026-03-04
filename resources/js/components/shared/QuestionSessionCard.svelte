<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import Card from '@/components/ui/Card.svelte';
    import MultipleChoice from '@/components/shared/MultipleChoice.svelte';
    import FillInTheBlank from '@/components/shared/FillInTheBlank.svelte';
    import DragAndDrop from '@/components/shared/DragAndDrop.svelte';
    import { Star, Flame, Lightbulb, Loader2, CheckCircle2, X } from 'lucide-svelte';
    import { fade, slide } from 'svelte/transition';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte.ts';

    interface Props {
        state: QuestionShowState;
    }

    let { state = $bindable() }: Props = $props();
</script>

<Card variant="none" padding="p-0" class="overflow-hidden border-none bg-white shadow-2xl rounded-3xl">
    {#if !state.isGuest}
        <div class="border-b border-slate-100 bg-slate-50/50 p-2">
            <Panel variant="none" rounded="2xl" padding="p-1" class="bg-white shadow-sm ring-1 ring-slate-100">
                <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-3 gap-4">
                    <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-8">
                        <div class="text-center sm:text-left">
                            <span class="mb-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">Kesulitan</span>
                            <Badge
                                variant={state.difficulty === 'beginner' ? 'success' : state.difficulty === 'medium' ? 'warning' : 'danger'}
                                size="sm"
                                class="font-bold border-none"
                            >
                                {state.getDifficultyLabel(state.difficulty)}
                            </Badge>
                        </div>

                        <div class="text-center sm:text-left">
                            <span class="mb-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">Level</span>
                            <h5 class="text-xl font-black text-slate-800 tabular-nums tracking-tighter">
                                {state.level}
                            </h5>
                        </div>

                        <div class="text-center sm:text-left">
                            <span class="mb-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">XP</span>
                            <h5 class="text-primary-600 flex items-center justify-center sm:justify-start gap-1.5 text-xl font-black tracking-tighter">
                                <Star size={18} class="fill-current text-amber-400" />
                                <span>{state.xp}</span>
                            </h5>
                        </div>

                        <div class="text-center sm:text-left">
                            <span class="mb-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">Streak</span>
                            <h5 class="flex items-center justify-center sm:justify-start gap-1.5 text-xl font-black tracking-tighter text-orange-600">
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
                        class="group flex items-center gap-2 border-primary-100 bg-primary-50 text-primary-700 hover:bg-primary-100 hover:text-primary-800 font-bold tracking-wide rounded-xl px-4"
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
                    class="absolute top-4 right-4 flex h-8 w-8 items-center justify-center rounded-full bg-white text-amber-500 hover:text-amber-600 shadow-sm transition-all hover:scale-110 active:scale-95"
                    aria-label="Tutup petunjuk"
                >
                    <X size={18} />
                </button>
                <div class="flex items-start gap-4 pr-8">
                    <div
                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-amber-400 shadow-lg shadow-amber-200"
                    >
                        <Lightbulb size={24} class="text-white fill-white" />
                    </div>
                    <div>
                        <h4 class="mb-1 text-[11px] font-black tracking-widest text-amber-700 uppercase">Wawasan Adaptif</h4>
                        <p class="text-base font-medium text-amber-900 leading-relaxed">
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
                        onselect={(answerId) => (state.selectedMultipleChoiceAnswer = answerId as any)}
                    />
                </div>
            {/if}
        </div>

        <div class="mt-8 pt-8 border-t border-slate-50">
            <Button
                variant="primary"
                size="lg"
                class="w-full py-5 text-base font-black tracking-widest uppercase shadow-xl shadow-primary-100 hover:shadow-primary-200 hover:scale-[1.01] active:scale-[0.99] transition-all"
                disabled={state.isSubmitting}
                onclick={() => state.submitAnswer()}
            >
                {#if state.isSubmitting}
                    <Loader2 size={20} class="mr-2.5 animate-spin" /> Memeriksa...
                {:else}
                    <CheckCircle2 size={20} class="mr-2.5" /> Periksa Jawaban
                {/if}
            </Button>
            <p class="mt-4 text-center text-[10px] font-bold tracking-[0.2em] text-slate-300 uppercase">
                Sistem Adaptif Oopedia • v2.0
            </p>
        </div>
    </div>
</Card>
