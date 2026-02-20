<script>
    import Card from "@/components/ui/Card.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import { relativeTime } from "@/utils/formatters";

    export let materials = [];
    export let missingQuestionsByMaterial = [];
</script>

<div class="space-y-12">
    <!-- Mastery Matrix -->
    <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
        <div slot="header" class="flex items-center gap-4">
            <h6
                class="mb-0 font-bold uppercase tracking-widest text-xs text-slate-400"
            >
                Matriks Penguasaan Konten
            </h6>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th
                            class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Skema Modul</th
                        >
                        <th
                            class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Tingkat Penguasaan</th
                        >
                        <th
                            class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Status Protokol</th
                        >
                        <th
                            class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Interaksi Terakhir</th
                        >
                    </tr>
                </thead>
                <tbody>
                    {#if materials.length === 0}
                        <tr>
                            <td colspan="4" class="p-0">
                                <EmptyState
                                    title="Tidak Ada Log Interaksi"
                                    description="Subjek belum melakukan interaksi dengan modul instruksional apapun."
                                />
                            </td>
                        </tr>
                    {:else}
                        {#each materials as material}
                            <tr
                                class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                            >
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-1.5 h-8 bg-slate-900 rounded-full"
                                        ></div>
                                        <span
                                            class="text-[10px] font-bold text-slate-900 uppercase tracking-widest"
                                            >{material.title}</span
                                        >
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div
                                        class="flex flex-col gap-2 min-w-[150px]"
                                    >
                                        <div
                                            class="flex justify-between text-[8px] font-bold uppercase tracking-widest text-slate-400"
                                        >
                                            <span>Penguasaan</span>
                                            <span
                                                >{Number(
                                                    material.progress || 0,
                                                ).toFixed(0)}%</span
                                            >
                                        </div>
                                        <ProgressBar
                                            value={Number(
                                                material.progress || 0,
                                            )}
                                            size="xs"
                                            color="bg-primary-600"
                                        />
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    {#if Number(material.progress) === 100}
                                        <Badge variant="success" size="xs"
                                            >STABIL</Badge
                                        >
                                    {:else if Number(material.progress) > 0}
                                        <Badge variant="warning" size="xs"
                                            >PROSES</Badge
                                        >
                                    {:else}
                                        <Badge variant="secondary" size="xs"
                                            >INAKTIF</Badge
                                        >
                                    {/if}
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                    >
                                        {relativeTime(material.last_accessed)}
                                    </span>
                                </td>
                            </tr>
                        {/each}
                    {/if}
                </tbody>
            </table>
        </div>
    </Card>

    <!-- Missing Questions (Anomalies) -->
    {#if missingQuestionsByMaterial.length > 0}
        <Card
            padding="p-0"
            class="overflow-hidden border-rose-100 shadow-2xl bg-rose-50/5"
        >
            <div slot="header" class="flex items-center gap-4">
                <h6
                    class="mb-0 font-bold uppercase tracking-widest text-xs text-rose-500"
                >
                    Unit Tantangan Belum Terpecahkan
                </h6>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th
                                class="p-6 text-xs font-bold text-rose-400 uppercase tracking-widest bg-rose-50/50"
                                >Modul Kritis</th
                            >
                            <th
                                class="p-6 text-right text-xs font-bold text-rose-400 uppercase tracking-widest bg-rose-50/50"
                                >Jumlah Anomali</th
                            >
                        </tr>
                    </thead>
                    <tbody>
                        {#each missingQuestionsByMaterial as item}
                            <tr
                                class="group hover:bg-rose-50/50 transition-colors border-b border-rose-100 last:border-0"
                            >
                                <td
                                    class="px-6 py-6 font-bold text-rose-900 text-xs tracking-widest uppercase"
                                    >{item.material_title}</td
                                >
                                <td class="px-6 py-6 text-right">
                                    <Badge variant="danger" size="xs"
                                        >{item.missing_count} MENUNGGU</Badge
                                    >
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </Card>
    {/if}
</div>
