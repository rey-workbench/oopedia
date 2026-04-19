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
                class="selection:bg-primary-500/30 relative z-10 text-lg leading-relaxed font-semibold text-slate-100"
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
                placeholder="Ketik jawaban Anda di sini..."
                class="hover:border-primary-200 focus:border-primary-500 focus:ring-primary-50 w-full rounded-2xl border-2 border-slate-100 bg-white px-6 py-5 text-lg font-semibold text-slate-900 shadow-sm transition-all
                       duration-200 outline-none placeholder:text-slate-300 focus:ring-4"
            />
            <!-- Accent dot when typing -->
            {#if answerText?.length > 0}
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
