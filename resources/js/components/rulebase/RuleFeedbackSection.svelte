<script lang="ts">
    import { MessageSquareQuote } from 'lucide-svelte';

    let { form } = $props<{ form: any }>();
</script>

<div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white p-7 shadow-sm">
    <div class="flex items-center gap-4 mb-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600 border-2 border-b-4 border-rose-100 shadow-sm">
            <MessageSquareQuote size={24} />
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-black uppercase tracking-[0.15em] text-primary-500">SAY (Feedback)</span>
            <span class="text-[10px] font-bold text-rose-400 mt-0.5">Pesan untuk mahasiswa</span>
        </div>
    </div>
    <textarea bind:value={form.recommendation} class="w-full rounded-2xl bg-slate-50 border-2 border-slate-100 p-5 text-sm font-bold text-primary-500 focus:bg-white focus:border-rose-400 focus:outline-none transition-all min-h-[100px]" placeholder="Pesan motivasi atau instruksi perbaikan..."></textarea>
</div>
