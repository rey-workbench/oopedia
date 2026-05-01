<script lang="ts">
    import { CheckSquare, Terminal } from 'lucide-svelte';
    import type { Question } from '@/types';

    interface Props {
        question: Question;
        selectedAnswerId?: string | null;
        disabled?: boolean;
        showResult?: boolean;
        onselect?: (answerId: string) => void;
    }

    let {
        question,
        selectedAnswerId = $bindable(null),
        disabled = false,
        showResult = false,
        onselect = () => {},
    }: Props = $props();

    function handleSelect(answerId: string) {
        if (disabled) return;
        selectedAnswerId = answerId;
        onselect(answerId);
    }
</script>

<div class="space-y-6">
    <!-- Question block: consistent dark terminal style -->
    <div class="relative select-none overflow-hidden rounded-3xl bg-slate-900 p-8 shadow-xl" draggable="false">
        <!-- Subtle top accent line -->
        <div
            class="via-primary-500/60 absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent to-transparent"
        ></div>
        <!-- Decorative icon -->
        <div class="pointer-events-none absolute -top-4 -right-4 text-white/4">
            <Terminal size={120} />
        </div>

        <!-- Header bar -->
        <div class="mb-5 flex items-center gap-3 border-b border-white/10 pb-4">
            <div class="flex gap-1.5">
                <div class="h-2 w-2 rounded-full bg-rose-500/60"></div>
                <div class="h-2 w-2 rounded-full bg-amber-500/60"></div>
                <div class="h-2 w-2 rounded-full bg-emerald-500/60"></div>
            </div>
            <span class="ml-1 font-mono text-xs font-bold tracking-widest text-slate-500 uppercase">
                soal.txt
            </span>
        </div>

        <div
            class="relative z-10 text-lg leading-relaxed font-semibold text-slate-100"
        >
            {@html question.question_text}
        </div>
    </div>

    <!-- Answer options -->
    <div class="space-y-4">
        <div class="flex items-center gap-2 px-1">
            <CheckSquare size={13} class="text-primary-500" />
            <span class="text-xs font-black tracking-widest text-slate-400 uppercase">
                Pilih Jawaban Yang Tepat
            </span>
            <div class="ml-2 h-px flex-1 bg-slate-100"></div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            {#each question.answers as answer, i (answer.id)}
                {@const isSelected = selectedAnswerId === answer.id}
                {@const label = String.fromCharCode(65 + i)}
                {@const isCorrect = answer.is_correct}
                <label
                    class="group relative block select-none {disabled
                        ? 'cursor-default'
                        : 'cursor-pointer'}"
                >
                    <input
                        type="radio"
                        name="answer"
                        value={answer.id}
                        class="peer sr-only"
                        checked={isSelected}
                        disabled={disabled}
                        onchange={() => handleSelect(answer.id)}
                    />
                    <div
                        class="press-active-lg flex items-center gap-5 rounded-3xl border-2 border-b-6 px-6 py-5 transition-all
                        {showResult
                            ? isSelected
                                ? isCorrect
                                    ? 'border-emerald-600 border-b-emerald-700 bg-emerald-50 shadow-emerald-200'
                                    : 'border-rose-600 border-b-rose-700 bg-rose-50 shadow-rose-200'
                                : 'border-slate-100 border-b-slate-200 bg-white opacity-50'
                            : isSelected
                              ? 'border-primary-600 border-b-primary-700 bg-primary-100/50 shadow-primary-200 shadow-sm'
                              : 'border-slate-100 border-b-slate-300 bg-white shadow-sm hover:border-slate-300 hover:bg-slate-50'}"
                    >
                        <!-- Letter Coin -->
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border-2 border-b-4 transition-colors
                            {showResult
                                ? isSelected
                                    ? isCorrect
                                        ? 'border-emerald-600 text-emerald-600 bg-white'
                                        : 'border-rose-600 text-rose-600 bg-white'
                                    : 'border-slate-200 bg-white text-slate-300'
                                : isSelected
                                  ? 'border-primary-600 text-primary-600 bg-white shadow-sm'
                                  : 'border-slate-200 bg-white text-slate-400 group-hover:border-slate-300'}"
                        >
                            <span class="text-lg font-black">{label}</span>
                        </div>

                        <span
                            class="flex-1 text-lg font-bold tracking-tight transition-colors
                            {showResult
                                ? isSelected
                                    ? isCorrect
                                        ? 'text-emerald-900'
                                        : 'text-rose-900'
                                    : 'text-slate-400'
                                : isSelected
                                  ? 'text-primary-950'
                                  : 'text-slate-700 group-hover:text-slate-900'}"
                        >
                            {answer.answer_text}
                        </span>

                        {#if showResult && isSelected}
                            <!-- No icon, just color-coding as requested -->
                        {:else if !showResult && isSelected}
                            <div
                                class="bg-primary-600 flex h-6 w-6 items-center justify-center rounded-full shadow-lg"
                            >
                                <div class="h-2 w-2 rounded-full bg-white"></div>
                            </div>
                        {/if}
                    </div>
                </label>
            {/each}
        </div>
    </div>
</div>
