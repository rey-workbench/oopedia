<script>
    import Card from "@/components/ui/Card.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";

    export let dimensions = {};
</script>

<Card
    padding="p-0"
    class="border-slate-100 shadow-2xl overflow-hidden rounded-[2.5rem]"
>
    <div
        slot="header"
        class="px-8 py-6 border-b border-slate-50 bg-slate-50/50"
    >
        <h6
            class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400"
        >
            Agregasi Dimensi
        </h6>
    </div>
    <div class="divide-y divide-slate-50">
        {#each Object.entries(dimensions) as [name, score]}
            <div
                class="px-8 py-6 flex flex-col gap-3 group hover:bg-slate-50 transition-colors"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"
                        >{name}</span
                    >
                    <span class="text-xs font-bold text-slate-900"
                        >{score.toFixed(2)}</span
                    >
                </div>
                <ProgressBar
                    value={(score / 7) * 100}
                    size="xs"
                    showPercentage={false}
                    variant={score >= 5
                        ? "success"
                        : score >= 3
                          ? "warning"
                          : "danger"}
                />
            </div>
        {/each}
    </div>
</Card>
