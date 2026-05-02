<script lang="ts">
    import { Terminal } from 'lucide-svelte';
    import type { Question } from '@/types';

    let {
        question,
        answerText = $bindable(''),
        disabled = false,
        showResult = false,
        showGuidance = false,
        isOverallCorrect = false,
    }: {
        question?: Question;
        answerText: string;
        disabled?: boolean;
        showResult?: boolean;
        showGuidance?: boolean;
        isOverallCorrect?: boolean;
    } = $props();

    let wordBank = $derived.by(() => {
        if (!showGuidance || !question?.answers) return [];

        const corrects = (question.answers || [])
            .filter((a) => a.is_correct)
            .map((a) => a.answer_text)
            .filter(Boolean);
        const wrongs = (question.answers || [])
            .filter((a) => !a.is_correct)
            .map((a) => a.answer_text)
            .filter(Boolean);

        // Return unique list of correct answers + 1 wrong if exists
        const list = [...new Set([...corrects, ...(wrongs.length > 0 ? [wrongs[0]] : [])])];
        return list.sort((a, b) => (a ?? '').localeCompare(b ?? ''));
    });
</script>

<div class="space-y-6">
    <!-- Question block: dark terminal style, same as MultipleChoice -->
    {#if question?.question_text}
        <div
            class="relative overflow-hidden rounded-3xl bg-slate-900 p-8 shadow-xl select-none"
            draggable="false"
        >
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

            <div class="relative z-10 text-lg leading-relaxed font-semibold text-slate-100">
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
                {disabled}
                placeholder={showResult ? 'Hasil Jawaban' : 'Ketik jawaban Anda di sini...'}
                class="w-full rounded-2xl border-2 border-b-6 px-6 py-5 text-lg font-bold shadow-sm transition-all duration-150 outline-none placeholder:text-slate-300
                {showResult
                    ? isOverallCorrect
                        ? 'border-emerald-600 border-b-emerald-700 bg-emerald-50 text-emerald-900'
                        : 'border-rose-600 border-b-rose-700 bg-rose-50 text-rose-900'
                    : 'focus:border-primary-500 focus:bg-primary-50/10 border-slate-200 bg-white text-slate-900 focus:translate-y-[2px] focus:border-b-4'}"
            />
            <!-- No icon when result, as requested -->
            {#if !showResult && answerText?.length > 0}
                <div
                    class="bg-primary-500 shadow-primary-200 absolute top-1/2 right-6 h-2.5 w-2.5 -translate-y-1/2 animate-pulse rounded-full shadow-lg"
                ></div>
            {/if}
        </div>

        {#if showGuidance && wordBank.length > 0}
            <div
                class="animate-in fade-in slide-in-from-top-2 mt-4 flex flex-wrap gap-2 duration-300"
            >
                <span
                    class="mr-1 self-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                    >Bank Kata:</span
                >
                {#each wordBank as word}
                    <button
                        type="button"
                        onclick={() => !disabled && (answerText = word ?? '')}
                        {disabled}
                        class="press-active hover:border-primary-300 hover:bg-primary-50 rounded-xl border-2 border-slate-200 bg-white px-3 py-1.5 text-xs font-black text-slate-700 shadow-sm transition-all disabled:opacity-50"
                    >
                        {word}
                    </button>
                {/each}
            </div>
        {/if}

        <p class="px-2 text-xs font-bold tracking-widest text-slate-300 uppercase">
            Masukkan teks jawaban dengan tepat
        </p>
    </div>
</div>
