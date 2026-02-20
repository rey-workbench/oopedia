<script>
    import Card from "@/components/ui/Card.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";

    export let averages = {};
</script>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    {#each Object.entries(averages) as [dimension, score]}
        <Card
            class="border-slate-100 shadow-xl hover:shadow-2xl transition-all group overflow-hidden relative"
        >
            <div class="flex flex-col gap-4 relative z-10">
                <div class="flex items-center justify-between">
                    <span
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >{dimension}</span
                    >
                    <span class="text-xl font-bold text-slate-900"
                        >{score.toFixed(2)}</span
                    >
                </div>
                <ProgressBar
                    value={(score / 3) * 100}
                    size="sm"
                    showPercentage={false}
                    variant={score >= 1.5
                        ? "success"
                        : score >= 0.8
                          ? "warning"
                          : "danger"}
                />
                <p class="text-[9px] font-bold text-slate-400 uppercase">
                    Status: <span
                        class={score >= 1.5
                            ? "text-emerald-500"
                            : score >= 0.8
                              ? "text-amber-500"
                              : "text-rose-500"}
                    >
                        {score >= 1.5
                            ? "Sangat Baik"
                            : score >= 0.8
                              ? "Rata-rata"
                              : "Perlu Perbaikan"}
                    </span>
                </p>
            </div>
            <div
                class="absolute -right-8 -bottom-8 w-24 h-24 bg-slate-50 rounded-full group-hover:scale-150 transition-transform -z-0 opacity-50"
            ></div>
        </Card>
    {/each}
</div>
