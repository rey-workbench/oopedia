<script>
    import Card from "@/ui/Card.svelte";
    import { Layers } from "lucide-svelte";

    export let popularMaterials;
</script>

<div>
    <Card
        padding="p-0"
        class="overflow-hidden border-slate-100 shadow-2xl h-full"
    >
        <div class="px-8 py-6 border-b border-slate-50">
            <p
                class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
            >
                Heatmap Konten
            </p>
        </div>
        <div class="space-y-4 p-6 bg-slate-50/50 h-full">
            {#each popularMaterials as material}
                <div
                    class="flex items-center gap-4 p-4 rounded-3xl bg-white border border-slate-100 group hover:border-primary-200 transition-all shadow-sm"
                >
                    <div
                        class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center shadow-lg text-white transition-transform group-hover:scale-110"
                    >
                        <Layers size={20} strokeWidth={2.5} />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h5
                            class="text-xs font-bold tracking-widest text-slate-900 truncate mb-1"
                        >
                            {material.title}
                        </h5>
                        <p
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >
                            {material.students_count} Subjek
                        </p>
                    </div>
                    <span class="text-xs font-bold text-primary-600"
                        >{material.completion_rate}%</span
                    >
                </div>
            {/each}
        </div>
    </Card>
</div>
