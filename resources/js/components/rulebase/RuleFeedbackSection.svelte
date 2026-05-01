<script lang="ts">
    import { MessageSquareQuote } from 'lucide-svelte';

    let { form } = $props<{ form: any }>();
</script>

<div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white p-7 shadow-sm">
    <div class="mb-6 flex items-center gap-4">
        <div
            class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-b-4 border-rose-100 bg-rose-50 text-rose-600 shadow-sm"
        >
            <MessageSquareQuote size={24} />
        </div>
        <div class="flex flex-col">
            <span class="text-primary-500 text-xs font-black tracking-[0.15em] uppercase"
                >SAY (Feedback)</span
            >
            <span class="mt-0.5 text-[10px] font-bold text-rose-400">Pesan untuk mahasiswa</span>
        </div>
    </div>
    <textarea
        bind:value={form.recommendation}
        class="text-primary-500 min-h-[100px] w-full rounded-2xl border-2 border-slate-100 bg-slate-50 p-5 text-sm font-bold transition-all focus:border-rose-400 focus:bg-white focus:outline-none"
        placeholder="Pesan motivasi atau instruksi perbaikan..."
    ></textarea>
</div>
