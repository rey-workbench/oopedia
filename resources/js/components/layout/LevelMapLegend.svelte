<script lang="ts">
    import Panel from '@/components/ui/Panel.svelte';
    import { Check, Lock, Trophy, Info } from 'lucide-svelte';

    interface Props {
        class?: string;
    }

    let { class: className = '' }: Props = $props();
</script>

<Panel
    variant="none"
    padding="p-6"
    class={`rounded-[2rem] border border-slate-100 bg-white/50 shadow-sm backdrop-blur-sm transition-shadow duration-300 hover:shadow-md ${className}`}
>
    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <Info size={14} class="text-primary-500" />
            <span class="text-xs font-black tracking-widest text-slate-400 uppercase">
                Panduan Peta Belajar
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-x-10 gap-y-4">
            <div class="group flex items-center gap-3">
                <div
                    class="bg-primary-600 shadow-primary-200 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-white shadow-lg transition-transform group-hover:scale-110"
                >
                    <span class="text-sm font-black italic">1</span>
                </div>
                <div class="flex flex-col">
                    <span
                        class="mb-0.5 text-sm leading-none font-black tracking-tight text-slate-800"
                        >Aktif</span
                    >
                    <span class="text-xs font-bold tracking-tighter text-slate-400 uppercase"
                        >Bisa dikerjakan</span
                    >
                </div>
            </div>

            <div class="group flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-lg shadow-emerald-100 transition-transform group-hover:scale-110"
                >
                    <Check size={20} strokeWidth={3} />
                </div>
                <div class="flex flex-col">
                    <span
                        class="mb-0.5 text-sm leading-none font-black tracking-tight text-emerald-700"
                        >Selesai</span
                    >
                    <span class="text-xs font-bold tracking-tighter text-slate-400 uppercase"
                        >Sudah benar</span
                    >
                </div>
            </div>

            <div class="group flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-slate-100 text-slate-400 shadow-sm transition-transform group-hover:scale-105"
                >
                    <Lock size={16} />
                </div>
                <div class="flex flex-col">
                    <span
                        class="mb-0.5 text-sm leading-none font-black tracking-tight text-slate-500"
                        >Terkunci</span
                    >
                    <span class="text-xs font-bold tracking-tighter text-slate-400 uppercase"
                        >Butuh prasyarat</span
                    >
                </div>
            </div>

            <div class="group flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border-2 border-amber-100 bg-amber-50 text-amber-500 shadow-sm shadow-amber-50 transition-transform group-hover:scale-110"
                >
                    <Trophy size={18} />
                </div>
                <div class="flex flex-col">
                    <span
                        class="mb-0.5 text-sm leading-none font-black tracking-tight text-amber-600"
                        >Tujuan</span
                    >
                    <span class="text-xs font-bold tracking-tighter text-slate-400 uppercase"
                        >Hasil akhir</span
                    >
                </div>
            </div>
        </div>
    </div>
</Panel>
