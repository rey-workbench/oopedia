<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import MultipleChoice from '@/components/quiz/MultipleChoice.svelte';
    import FillInTheBlank from '@/components/quiz/FillInTheBlank.svelte';
    import DragAndDrop from '@/components/quiz/DragAndDrop.svelte';
    import { Star, Target, Lightbulb, Loader2, CheckCircle2, X } from 'lucide-svelte';
    import { fade, slide } from 'svelte/transition';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte.ts';
    import { getDifficultyLabel } from '@/utils';

    interface Props {
        state: QuestionShowState;
    }

    let { state = $bindable() }: Props = $props();
    let studentState = $derived(state.studentState);

    // Accuracy color intensity: >80% = emerald, 60-80% = orange, 40-60% = red-400, <40% = red-600
    const accuracyClass = $derived(() => {
        const acc = studentState?.accuracy ?? 100;
        if (acc >= 80) return { border: 'border-emerald-100', icon: 'text-emerald-500', text: 'text-slate-700', shake: false };
        if (acc >= 60) return { border: 'border-orange-200', icon: 'text-orange-500', text: 'text-orange-600', shake: false };
        if (acc >= 40) return { border: 'border-red-300', icon: 'text-red-500', text: 'text-red-600', shake: true };
        return { border: 'border-red-500', icon: 'text-red-600', text: 'text-red-700', shake: true };
    });
</script>

<Card variant="none" padding="p-0" class="border-duo-lg overflow-hidden rounded-3xl bg-white">
    {#if !state.isGuest}
        <div class="border-b-4 border-slate-50 bg-slate-50/30 p-4">
            <div class="flex flex-col items-center justify-between gap-6 px-4 py-2 sm:flex-row">
                <div
                    id="quiz-stats"
                    class="flex flex-wrap items-center justify-center gap-4 sm:gap-6"
                >
                    <!-- Difficulty Badge -->
                    <div
                        class="flex items-center gap-3 rounded-2xl border-2 border-b-4 border-slate-100 bg-white px-4 py-2 shadow-sm"
                    >
                        <span class="text-xs font-black tracking-widest text-slate-400 uppercase"
                            >Sulit</span
                        >
                        <Badge
                            variant={state.difficulty === 'beginner'
                                ? 'success'
                                : state.difficulty === 'medium'
                                  ? 'warning'
                                  : 'danger'}
                            size="sm"
                            class="border-none font-black"
                        >
                            {getDifficultyLabel(state.difficulty)}
                        </Badge>
                    </div>

                    <!-- XP Badge -->
                    <div
                        class="flex items-center gap-3 rounded-2xl border-2 border-b-4 border-amber-100 bg-white px-4 py-2 shadow-sm"
                    >
                        <Star size={18} class="fill-amber-400 text-amber-400" />
                        <span class="text-lg font-black tracking-tight text-slate-700"
                            >{state.xp}</span
                        >
                    </div>

                    <!-- Accuracy Badge -->
                    <div
                        class="flex items-center gap-3 rounded-2xl border-2 border-b-4 bg-white px-4 py-2 shadow-sm transition-colors duration-500 {accuracyClass().border}"
                        class:accuracy-shake={accuracyClass().shake}
                    >
                        <Target size={18} class="transition-colors duration-500 {accuracyClass().icon}" />
                        <span class="text-lg font-black tracking-tight transition-colors duration-500 {accuracyClass().text}"
                            >{studentState?.accuracy ?? 0}%</span
                        >
                    </div>
                </div>

                {#if (studentState?.adaptive_state?.scaffold_mode !== 'minimal')}
                <button
                    type="button"
                    id="quiz-hint-btn"
                    onclick={() => state.useHint()}
                    disabled={state.hintsAvailable() <= 0 || !state.currentQuestion?.hint}
                    class="group press-active border-primary-200 text-primary-600 hover:bg-primary-50 flex items-center gap-3 rounded-2xl border-2 border-b-4 bg-white px-5 py-2.5 font-black shadow-sm transition-all disabled:pointer-events-none disabled:opacity-50"
                >
                    <Lightbulb
                        size={18}
                        class="text-primary-500 transition-transform group-hover:rotate-12"
                    />
                    <span class="text-sm">Hint ({state.hintsAvailable})</span>
                </button>
                {/if}
            </div>
        </div>
    {/if}

    <!-- R02 Motivational Banner -->
    {#if studentState?.adaptive_state?.show_motivation}
        <div
            transition:slide={{ duration: 400 }}
            class="mx-6 mt-4 rounded-2xl border-2 border-blue-200 bg-linear-to-r from-blue-50 to-indigo-50 p-5 text-center shadow-sm"
        >
            <p class="text-lg font-bold text-blue-700">💪 Kamu Pasti Bisa!</p>
            <p class="mt-1 text-sm text-blue-500">
                Sistem menyiapkan soal yang lebih mudah untukmu. Jawab dengan tenang dan percaya diri!
            </p>
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
                    class="absolute top-4 right-4 flex h-8 w-8 items-center justify-center rounded-full bg-white text-amber-500 shadow-sm transition-all hover:bg-amber-100/50 hover:text-amber-600 active:translate-y-[2px]"
                    aria-label="Tutup petunjuk"
                >
                    <X size={18} />
                </button>
                <div class="flex items-start gap-4 pr-8">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-400 shadow-lg shadow-amber-200"
                    >
                        <Lightbulb size={24} class="fill-white text-white" />
                    </div>
                    <div>
                        <h4
                            class="mb-1 text-xs font-black tracking-widest text-amber-700 uppercase"
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

        <div id="quiz-question-area" class="space-y-0">
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
                        selectedAnswerId={state.selectedMultipleChoiceAnswer}
                        onselect={(answerId) => (state.selectedMultipleChoiceAnswer = answerId)}
                    />
                </div>
            {/if}
        </div>

        <div id="quiz-submit-btn" class="mt-8 border-t border-slate-50 pt-8">
            <Button
                id="quiz-main-submit-btn"
                variant="primary"
                size="lg"
                class="w-full py-5 text-base font-black tracking-widest uppercase"
                disabled={state.isSubmitting || state.showFeedback}
                onclick={() => state.submitAnswer()}
            >
                {#if state.isSubmitting}
                    <Loader2 size={20} class="mr-2.5 animate-spin" /> Memeriksa...
                {:else}
                    <CheckCircle2 size={20} class="mr-2.5" /> Periksa Jawaban
                {/if}
            </Button>
            <p class="mt-4 text-center text-xs font-bold tracking-[0.2em] text-slate-300 uppercase">
                Sistem Adaptif Oopedia • v2.0
            </p>
        </div>
    </div>
</Card>

<style>
    @keyframes accuracy-vibrate {
        0%,
        100% {
            transform: translateX(0);
        }
        15% {
            transform: translateX(-3px) rotate(-1deg);
        }
        30% {
            transform: translateX(3px) rotate(1deg);
        }
        45% {
            transform: translateX(-2px);
        }
        60% {
            transform: translateX(2px);
        }
        75% {
            transform: translateX(-1px);
        }
    }

    .accuracy-shake {
        animation: accuracy-vibrate 1.4s ease-in-out infinite;
        background-color: rgb(254 226 226 / 0.6);
    }
</style>
