<script lang="ts">
    import { CheckSquare, Terminal } from 'lucide-svelte';
    import type { Question } from '@/types';

    interface Props {
        question: Question;
        selectedAnswerId?: number | null;
        onselect?: (answerId: number) => void;
    }

    let { question, selectedAnswerId = $bindable(null), onselect = () => {} }: Props = $props();

    function handleSelect(answerId: number) {
        selectedAnswerId = answerId;
        onselect(answerId);
    }
</script>

<div class="space-y-6">
    <!-- Question block: consistent dark terminal style -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-8 shadow-xl">
        <!-- Subtle top accent line -->
        <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-primary-500/60 to-transparent"></div>
        <!-- Decorative icon -->
        <div class="pointer-events-none absolute -right-4 -top-4 text-white/4">
            <Terminal size={120} />
        </div>

        <!-- Header bar -->
        <div class="mb-5 flex items-center gap-3 border-b border-white/10 pb-4">
            <div class="flex gap-1.5">
                <div class="h-2 w-2 rounded-full bg-rose-500/60"></div>
                <div class="h-2 w-2 rounded-full bg-amber-500/60"></div>
                <div class="h-2 w-2 rounded-full bg-emerald-500/60"></div>
            </div>
            <span class="ml-1 font-mono text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                soal.txt
            </span>
        </div>

        <div class="relative z-10 font-semibold text-lg leading-relaxed text-slate-100 selection:bg-primary-500/30">
            {@html question.question_text}
        </div>
    </div>

    <!-- Answer options -->
    <div class="space-y-3">
        <div class="flex items-center gap-2 px-1">
            <CheckSquare size={13} class="text-primary-500" />
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">
                Pilih Jawaban Yang Tepat
            </span>
            <div class="ml-2 h-px flex-1 bg-slate-100"></div>
        </div>

        <div class="grid grid-cols-1 gap-3">
            {#each question.answers as answer (answer.id)}
                {@const isSelected = selectedAnswerId === answer.id}
                <label class="group relative block cursor-pointer">
                    <input
                        type="radio"
                        name="answer"
                        value={answer.id}
                        class="peer sr-only"
                        checked={isSelected}
                        onchange={() => handleSelect(answer.id)}
                    />
                    <div
                        class="flex items-center gap-5 rounded-2xl border-2 px-6 py-4 transition-all duration-200
                        {isSelected
                            ? 'border-primary-600 bg-primary-50 ring-4 ring-primary-50/80 shadow-md'
                            : 'border-slate-100 bg-white hover:border-primary-200 hover:bg-slate-50/50 shadow-sm hover:shadow-md'}"
                    >
                        <!-- Custom radio circle -->
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border-2 transition-all duration-200
                            {isSelected
                                ? 'border-primary-600 bg-primary-600 shadow-lg shadow-primary-200'
                                : 'border-slate-200 group-hover:border-primary-300'}"
                        >
                            {#if isSelected}
                                <div class="h-3 w-3 rounded-full bg-white shadow-sm"></div>
                            {/if}
                        </div>
                        <span
                            class="flex-1 font-semibold text-base tracking-tight transition-colors duration-200
                            {isSelected ? 'text-primary-900' : 'text-slate-700 group-hover:text-slate-900'}"
                        >
                            {answer.answer_text}
                        </span>
                    </div>
                </label>
            {/each}
        </div>
    </div>
</div>
