<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import Badge from "../../../../components/ui/Badge.svelte";
    import StatCard from "../../../../components/ui/StatCard.svelte";
    import ProgressBar from "../../../../components/ui/ProgressBar.svelte";
    import { ArrowLeft, LineChart, CheckCheck, Zap } from "lucide-svelte";

    export let student;
    export let materials = []; // Collection of materials with calculated stats
    export let missingQuestionsByMaterial = [];

    // Derive stats
    // Use client-side calculation or backend props. Backend passed:
    // materials->avg('progress'), materials->where('progress', 100)->count(), array_sum(column)

    // Let's recalculate simply or trust props if we pass them.
    // The controller passes 'materials'. We can compute these easily here.

    $: avgProgress =
        materials.length > 0
            ? (
                  materials.reduce(
                      (acc, m) => acc + (Number(m.progress) || 0),
                      0,
                  ) / materials.length
              ).toFixed(1)
            : "0.0";
    $: completedModules = materials.filter(
        (m) => Number(m.progress) === 100,
    ).length;
    $: totalModules = materials.length;
    $: missingQuestions = missingQuestionsByMaterial.reduce(
        (acc, item) => acc + item.missing_count,
        0,
    );

    import { relativeTime } from "../../../../utils/formatters";
</script>

<App title="Progress Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Wawasan Performa Siswa"
            subtitle={`Analisis trajectory pembelajaran untuk entitas ${student.name}.`}
        >
            <div slot="actions">
                <Button href="/admin/students" variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <StatCard
                title="Lintasan Pembelajaran"
                value={`${avgProgress}%`}
                icon={LineChart}
                variant="primary"
                footer="Rata-rata penyelesaian modul"
            />
            <StatCard
                title="Modul Berhasil Diselesaikan"
                value={`${completedModules} / ${totalModules}`}
                icon={CheckCheck}
                variant="success"
                footer="Penyelesaian 100% tercapai"
            />
            <StatCard
                title="Sisa Unit Tantangan"
                value={missingQuestions}
                icon={Zap}
                variant="danger"
                footer="Jawaban benar tertunda"
            />
        </div>

        <!-- Mastery Matrix -->
        <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <div
                slot="header"
                class="px-6 py-4 border-b border-slate-50 flex items-center gap-4"
            >
                <div class="w-1.5 h-8 bg-primary-600 rounded-full"></div>
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
                                <td
                                    colspan="4"
                                    class="p-20 text-center text-slate-400 text-xs uppercase font-bold tracking-widest"
                                    >Tidak Ada Log Interaksi Ditemukan</td
                                >
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
                                            {relativeTime(
                                                material.last_accessed,
                                            )}
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
                <div
                    slot="header"
                    class="px-6 py-4 border-b border-rose-100 flex items-center gap-4"
                >
                    <div class="w-1.5 h-8 bg-rose-500 rounded-full"></div>
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
</App>
