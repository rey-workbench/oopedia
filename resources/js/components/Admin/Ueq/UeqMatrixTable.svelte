<script>
    import Card from "@/components/ui/Card.svelte";

    export let survey;
    export let aspects = [];
</script>

<Card
    padding="p-0"
    class="border-slate-100 shadow-2xl overflow-hidden rounded-[2.5rem]"
>
    <div slot="header">
        <h6
            class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400"
        >
            Matriks Skor Psikometrik
        </h6>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th
                        class="px-8 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >Aspek Evaluasi</th
                    >
                    <th
                        class="px-8 py-6 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >Peta Skalar (1-7)</th
                    >
                    <th
                        class="px-8 py-6 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >Rentang Semantik</th
                    >
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                {#each aspects as aspect}
                    {@const val = survey[aspect.name]}
                    <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-8 py-6">
                            <span
                                class="text-[10px] font-bold text-slate-900 uppercase tracking-widest"
                                >{aspect.name.replace(/_/g, " ")}</span
                            >
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center justify-center gap-1">
                                {#each Array(7) as _, i}
                                    <div
                                        class={`w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold transition-all
                            ${val == i + 1 ? "bg-primary-600 text-white shadow-lg shadow-primary-900/20 scale-110" : "bg-slate-100 text-slate-300"}`}
                                    >
                                        {i + 1}
                                    </div>
                                {/each}
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex flex-col items-end">
                                <span
                                    class="text-[8px] font-bold uppercase text-slate-300 mb-1"
                                    >Semantik Map:</span
                                >
                                <div
                                    class="text-[10px] font-bold text-slate-600 flex items-center gap-2"
                                >
                                    <span
                                        class={val <= 3
                                            ? "text-primary-600"
                                            : ""}>{aspect.left}</span
                                    >
                                    <span class="w-px h-3 bg-slate-200"></span>
                                    <span
                                        class={val >= 5
                                            ? "text-primary-600"
                                            : ""}>{aspect.right}</span
                                    >
                                </div>
                            </div>
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
</Card>
