<script lang="ts">
    import { Terminal } from 'lucide-svelte';

    let {
        question,
        answerText = $bindable(''),
    }: {
        question?: { question_text?: string };
        answerText: string;
    } = $props();
</script>

<div class="space-y-6">
    <!-- Question block: dark terminal style, same as MultipleChoice -->
    {#if question?.question_text}
        <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-8 shadow-xl">
            <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-primary-500/60 to-transparent"></div>
            <div class="pointer-events-none absolute -right-4 -top-4 text-white/4">
                <Terminal size={120} />
            </div>

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
    {/if}

    <!-- Answer input: clean light style, consistent with options in MultipleChoice -->
    <div class="space-y-3">
        <div class="flex items-center gap-2 px-1">
            <span class="font-mono text-[10px] font-black tracking-widest text-slate-400 uppercase">&gt;_ Input Jawaban</span>
            <div class="ml-2 h-px flex-1 bg-slate-100"></div>
        </div>

        <div class="relative">
            <input
                id="fill_in_the_blank_answer"
                type="text"
                bind:value={answerText}
                placeholder="Ketik jawaban Anda di sini..."
                class="w-full rounded-2xl border-2 border-slate-100 bg-white px-6 py-5 text-lg font-semibold text-slate-900 shadow-sm outline-none transition-all duration-200 placeholder:text-slate-300
                       hover:border-primary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-50"
            />
            <!-- Accent dot when typing -->
            {#if answerText?.length > 0}
                <div class="absolute top-1/2 right-6 -translate-y-1/2 h-2.5 w-2.5 rounded-full bg-primary-500 shadow-lg shadow-primary-200 animate-pulse"></div>
            {/if}
        </div>

        <p class="px-2 text-[10px] font-bold tracking-widest text-slate-300 uppercase">
            Masukkan teks jawaban dengan tepat
        </p>
    </div>
</div>
