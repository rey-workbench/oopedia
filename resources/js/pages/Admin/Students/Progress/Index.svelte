<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import StatsGrid from "@/components/ui/StatsGrid.svelte";
    import DataTable from "@/components/ui/DataTable.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import { ArrowLeft, LineChart, CheckCheck, Zap } from "lucide-svelte";
    import { relativeTime, formatDate } from "@/utils/formatters";
    import { ROUTES } from "@/utils/route";
    import { StudentProgressState } from "@/states/Admin/StudentState.svelte";

    export let student;
    export let materials = [];
    export let missingQuestionsByMaterial = [];

    const state = new StudentProgressState(
        student,
        materials,
        missingQuestionsByMaterial,
    );

    $: progressStats = [
        {
            title: "Lintasan Pembelajaran",
            value: `${state.avgProgress}%`,
            icon: LineChart,
            variant: "primary",
            footer: "Rata-rata penyelesaian modul",
        },
        {
            title: "Modul Berhasil Diselesaikan",
            value: `${state.completedModules} / ${state.totalModules}`,
            icon: CheckCheck,
            variant: "success",
            footer: "Penyelesaian 100% tercapai",
        },
        {
            title: "Sisa Unit Tantangan",
            value: state.missingQuestions,
            icon: Zap,
            variant: "danger",
            footer: "Jawaban benar tertunda",
        },
    ];

    $: matrixColumns = [
        { key: "module", label: "Skema Modul", align: "left" },
        { key: "mastery", label: "Tingkat Penguasaan", align: "left" },
        { key: "status", label: "Status Protokol", align: "center" },
        { key: "last_accessed", label: "Interaksi Terakhir", align: "right" },
    ];

    $: challengeColumns = [
        { key: "module", label: "Modul Kritis", align: "left" },
        { key: "anomaly", label: "Jumlah Anomali", align: "right" },
    ];
</script>

<App title="Progress Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Wawasan Performa Siswa"
            subtitle={`Analisis trajectory pembelajaran untuk entitas ${state.student.name}.`}
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.STUDENTS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <!-- Summary Cards -->
        <StatsGrid
            stats={progressStats}
            gridClass="grid-cols-1 md:grid-cols-3"
        />

        <!-- Tables -->
        <div class="space-y-12">
            <!-- Mastery Matrix -->
            <DataTable
                title="Matriks Penguasaan Konten"
                items={state.materials}
                columns={matrixColumns}
                hideSearch={true}
            >
                <svelte:fragment slot="empty">
                    <EmptyState
                        title="Tidak Ada Log Interaksi"
                        description="Subjek belum melakukan interaksi dengan modul instruksional apapun."
                    />
                </svelte:fragment>

                <svelte:fragment slot="row" let:item={material}>
                    <td class="px-6 py-6 border-b border-slate-50">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-1 h-10 rounded-full bg-primary-600"
                            ></div>
                            <span
                                class="font-bold text-slate-900 uppercase tracking-widest text-sm"
                                >{material.material_title}</span
                            >
                        </div>
                    </td>
                    <td class="px-6 py-6 border-b border-slate-50">
                        <div class="w-40 space-y-1">
                            <div
                                class="flex justify-between items-center px-0.5"
                            >
                                <span
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                    >{material.mastery_percentage}%</span
                                >
                            </div>
                            <ProgressBar
                                value={material.mastery_percentage}
                                height="h-2"
                                color="blue"
                            />
                        </div>
                    </td>
                    <td class="px-6 py-6 border-b border-slate-50">
                        <Badge
                            variant={material.status === "STABIL"
                                ? "success"
                                : material.status === "PROSES"
                                  ? "warning"
                                  : "secondary"}
                            size="xs"
                        >
                            {material.status}
                        </Badge>
                    </td>
                    <td class="px-6 py-6 border-b border-slate-50">
                        <span class="text-xs font-medium text-slate-400">
                            {material.last_accessed
                                ? formatDate(material.last_accessed)
                                : "Belum diakses"}
                        </span>
                    </td>
                </svelte:fragment>
            </DataTable>

            <!-- Missing Questions (Anomalies) -->
            {#if state.missingQuestionsByMaterial.length > 0}
                <DataTable
                    title="Unit Tantangan Belum Terpecahkan"
                    items={state.missingQuestionsByMaterial}
                    columns={challengeColumns}
                    hideSearch={true}
                >
                    <svelte:fragment slot="row" let:item>
                        <td class="px-6 py-6 border-b border-slate-50">
                            <span
                                class="font-bold text-slate-900 uppercase tracking-widest text-sm"
                                >{item.material_title}</span
                            >
                        </td>
                        <td class="px-6 py-6 border-b border-slate-50">
                            <div
                                class="inline-flex items-center px-3 py-1.5 bg-rose-50 text-rose-600 rounded-xl border border-rose-100"
                            >
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest"
                                    >{item.missing_count} MENUNGGU</span
                                >
                            </div>
                        </td>
                    </svelte:fragment>
                </DataTable>
            {/if}
        </div>
    </div>
</App>
