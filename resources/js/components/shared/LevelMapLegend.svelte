<script lang="ts">
    import Card from '@/components/ui/Card.svelte';
    import { Check, Lock, Trophy } from 'lucide-svelte';

    interface Props {
        class?: string;
    }

    let { class: className = '' }: Props = $props();
</script>

<Card padding="p-6" class={className}>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">
            Keterangan
        </span>
        <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
            <div class="flex items-center gap-2.5">
                <div
                    class="bg-primary-600 shadow-primary-900/20 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-white shadow-md"
                >
                    <span class="text-xs font-black">1</span>
                </div>
                <span class="text-xs font-semibold text-slate-600">Bisa dikerjakan</span>
            </div>
            <div class="flex items-center gap-2.5">
                <div
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white shadow-md shadow-emerald-200"
                >
                    <Check size={14} strokeWidth={3} />
                </div>
                <span class="text-xs font-semibold text-slate-600">Sudah benar</span>
            </div>
            <div class="flex items-center gap-2.5">
                <div
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-400"
                >
                    <Lock size={12} />
                </div>
                <span class="text-xs font-semibold text-slate-400">Terkunci</span>
            </div>
            <div class="flex items-center gap-2.5">
                <div
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-slate-300"
                >
                    <Trophy size={12} />
                </div>
                <span class="text-xs font-semibold text-slate-400">Penghargaan akhir</span>
            </div>
        </div>
    </div>
</Card>
