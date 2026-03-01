<script lang="ts">
    import Card from "@/components/ui/Card.svelte";
    import { Check, Lock, Trophy } from "lucide-svelte";

    interface Props {
        class?: string;
    }

    let { class: className = "" }: Props = $props();
</script>

<Card padding="p-6" class={className}>
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
    >
        <span
            class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
        >
            Keterangan
        </span>
        <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
            <div class="flex items-center gap-2.5">
                <div
                    class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white shadow-md shadow-primary-900/20 flex-shrink-0"
                >
                    <span class="text-xs font-black">1</span>
                </div>
                <span class="text-xs font-semibold text-slate-600"
                    >Bisa dikerjakan</span
                >
            </div>
            <div class="flex items-center gap-2.5">
                <div
                    class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white shadow-md shadow-emerald-200 flex-shrink-0"
                >
                    <Check size={14} strokeWidth={3} />
                </div>
                <span class="text-xs font-semibold text-slate-600"
                    >Sudah benar</span
                >
            </div>
            <div class="flex items-center gap-2.5">
                <div
                    class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-400 flex-shrink-0"
                >
                    <Lock size={12} />
                </div>
                <span class="text-xs font-semibold text-slate-400"
                    >Terkunci</span
                >
            </div>
            <div class="flex items-center gap-2.5">
                <div
                    class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-300 flex-shrink-0"
                >
                    <Trophy size={12} />
                </div>
                <span class="text-xs font-semibold text-slate-400"
                    >Penghargaan akhir</span
                >
            </div>
        </div>
    </div>
</Card>
