<script>
    import Card from "@/ui/Card.svelte";
    import Badge from "@/ui/Badge.svelte";
    import { Check, Clock, Zap } from "lucide-svelte";
    import { relativeTime } from "@/utils/formatters";

    export let recentProgress;
</script>

<Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
    <div class="px-8 py-6 border-b border-slate-50 bg-white">
        <p
            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
        >
            Log Operasi (Langsung)
        </p>
    </div>
    <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 p-8 bg-white"
    >
        {#each recentProgress as progress}
            <div
                class="relative p-6 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white transition-colors"
            >
                <div class="absolute top-6 right-6">
                    <Badge
                        variant={progress.is_correct ? "success" : "warning"}
                        size="xs"
                    >
                        {progress.question?.complexity?.toUpperCase() || "LVL"}
                    </Badge>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class={`w-8 h-8 rounded-lg ${progress.is_correct ? "bg-emerald-500" : "bg-amber-500"} text-white flex items-center justify-center text-[10px] shadow-lg shadow-emerald-500/20`}
                        >
                            <svelte:component
                                this={progress.is_correct ? Check : Clock}
                                size={14}
                                strokeWidth={3}
                            />
                        </div>
                        <div
                            class="font-bold text-slate-900 uppercase tracking-widest text-xs"
                        >
                            {progress.user?.name || "ENT-TIDAK DIKETAHUI"}
                        </div>
                    </div>

                    <p
                        class="text-[11px] font-bold text-slate-500 leading-relaxed"
                    >
                        {progress.is_correct
                            ? "Berhasil mendekripsi"
                            : "Menganalisis"} modul
                        <span
                            class="text-slate-900 underline decoration-primary-200 underline-offset-4"
                        >
                            {progress.question?.material?.title || "-"}
                        </span>
                    </p>

                    <div
                        class="pt-4 border-t border-slate-200 flex justify-between items-center text-[9px] font-bold text-slate-300 uppercase tracking-widest"
                    >
                        <span>{relativeTime(progress.created_at)}</span>
                        <Zap
                            size={12}
                            strokeWidth={3}
                            class="text-primary-500 opacity-20"
                        />
                    </div>
                </div>
            </div>
        {/each}
    </div>
</Card>
