<script lang="ts">
    import { Terminal } from 'lucide-svelte';
    import type { Question } from '@/types';

    let {
        question,
        answerText = $bindable(''),
        disabled = false,
        showResult = false,
        isOverallCorrect = false,
    }: {
        question?: Question;
        answerText: string;
        disabled?: boolean;
        showResult?: boolean;
        isOverallCorrect?: boolean;
    } = $props();
</script>

<div class="space-y-6">
    <!-- Question block: dark terminal style, same as MultipleChoice -->
    {#if question?.question_text}
        <div class="relative select-none overflow-hidden rounded-3xl bg-slate-900 p-8 shadow-xl" draggable="false">
            <div
                class="via-primary-500/60 absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent to-transparent"
            ></div>
            <div class="pointer-events-none absolute -top-4 -right-4 text-white/4">
                <Terminal size={120} />
            </div>

            <div class="mb-5 flex items-center gap-3 border-b border-white/10 pb-4">
                <div class="flex gap-1.5">
                    <div class="h-2 w-2 rounded-full bg-rose-500/60"></div>
                    <div class="h-2 w-2 rounded-full bg-amber-500/60"></div>
                    <div class="h-2 w-2 rounded-full bg-emerald-500/60"></div>
                </div>
                <span
                    class="ml-1 font-mono text-xs font-bold tracking-widest text-slate-500 uppercase"
                >
                    soal.txt
                </span>
            </div>

            <div
                class="relative z-10 text-lg leading-relaxed font-semibold text-slate-100"
            >
                {@html question.question_text}
            </div>
        </div>
    {/if}

    <!-- Answer input: clean light style, consistent with options in MultipleChoice -->
    <div class="space-y-3">
        <div class="flex items-center gap-2 px-1">
            <span class="font-mono text-xs font-black tracking-widest text-slate-400 uppercase"
                >&gt;_ Input Jawaban</span
            >
            <div class="ml-2 h-px flex-1 bg-slate-100"></div>
        </div>

        <div class="relative">
            <input
                id="fill_in_the_blank_answer"
                type="text"
                bind:value={answerText}
                disabled={disabled}
                placeholder={showResult ? "Hasil Jawaban" : "Ketik jawaban Anda di sini..."}
                class="w-full rounded-2xl border-2 border-b-6 px-6 py-5 text-lg font-bold shadow-sm transition-all duration-150 outline-none placeholder:text-slate-300
                {showResult 
                    ? isOverallCorrect
                        ? 'border-emerald-600 border-b-emerald-700 bg-emerald-50 text-emerald-900'
                        : 'border-rose-600 border-b-rose-700 bg-rose-50 text-rose-900'
                    : 'border-slate-200 bg-white text-slate-900 focus:border-primary-500 focus:bg-primary-50/10 focus:translate-y-[2px] focus:border-b-4'}"
            />
            <!-- No icon when result, as requested -->
            {#if !showResult && answerText?.length > 0}
                <div
                    class="bg-primary-500 shadow-primary-200 absolute top-1/2 right-6 h-2.5 w-2.5 -translate-y-1/2 animate-pulse rounded-full shadow-lg"
                ></div>
            {/if}
        </div>

        <p class="px-2 text-xs font-bold tracking-widest text-slate-300 uppercase">
            Masukkan teks jawaban dengan tepat
        </p>
    </div>
</div>
