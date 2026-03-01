<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/shared/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import StatsGrid from '@/components/shared/StatsGrid.svelte';
    import DataTable from '@/components/shared/DataTable.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import { ArrowLeft, LineChart, CheckCheck, Zap } from 'lucide-svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { StudentProgressState } from '@/states/Admin/StudentState.svelte';

    let {
        student,
        materials = [],
        missingQuestionsByMaterial = [],
    }: { student: any; materials: any[]; missingQuestionsByMaterial: any[] } = $props();

    const state = untrack(
        () => new StudentProgressState(student, materials, missingQuestionsByMaterial)
    );

    const progressStats = $derived([
        {
            title: 'Lintasan Pembelajaran',
            value: `${state.avgProgress}%`,
            icon: LineChart,
            variant: 'primary',
            footer: 'Rata-rata penyelesaian modul',
        },
        {
            title: 'Modul Berhasil Diselesaikan',
            value: `${state.completedModules} / ${state.totalModules}`,
            icon: CheckCheck,
            variant: 'success',
            footer: 'Penyelesaian 100% tercapai',
        },
        {
            title: 'Sisa Unit Tantangan',
            value: state.missingQuestions,
            icon: Zap,
            variant: 'danger',
            footer: 'Jawaban benar tertunda',
        },
    ]);

    const matrixColumns = $derived([
        { key: 'module', label: 'Skema Modul', align: 'left' },
        { key: 'mastery', label: 'Tingkat Penguasaan', align: 'left' },
        { key: 'status', label: 'Status Protokol', align: 'center' },
        { key: 'last_accessed', label: 'Interaksi Terakhir', align: 'right' },
    ]);

    const challengeColumns = $derived([
        { key: 'module', label: 'Modul Kritis', align: 'left' },
        { key: 'anomaly', label: 'Jumlah Anomali', align: 'right' },
    ]);
</script>

<App title="Progress Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Wawasan Performa Siswa"
            subtitle={`Analisis trajectory pembelajaran untuk entitas ${state.student.name}.`}
        >
            {#snippet actions()}
                <Button href={ROUTES.ADMIN.STUDENTS.INDEX} variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            {/snippet}
        </PageHeader>

        <!-- Summary Cards -->
        <StatsGrid stats={progressStats} gridClass="grid-cols-1 md:grid-cols-3" />

        <!-- Tables -->
        <div class="space-y-12">
            <!-- Mastery Matrix -->
            <DataTable
                title="Matriks Penguasaan Konten"
                items={state.materials}
                columns={matrixColumns}
                hideSearch={true}
            >
                {#snippet empty()}
                    <EmptyState
                        title="Tidak Ada Log Interaksi"
                        description="Subjek belum melakukan interaksi dengan modul instruksional apapun."
                    />
                {/snippet}

                {#snippet row(material)}
                    <td class="border-b border-slate-50 px-6 py-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-primary-600 h-10 w-1 rounded-full"></div>
                            <span class="text-sm font-bold tracking-widest text-slate-900 uppercase"
                                >{material.material_title}</span
                            >
                        </div>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        <div class="w-40 space-y-1">
                            <div class="flex items-center justify-between px-0.5">
                                <span
                                    class="text-[9px] font-bold tracking-widest text-slate-400 uppercase"
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
                    <td class="border-b border-slate-50 px-6 py-6">
                        <Badge
                            variant={material.status === 'STABIL'
                                ? 'success'
                                : material.status === 'PROSES'
                                  ? 'warning'
                                  : 'secondary'}
                            size="xs"
                        >
                            {material.status}
                        </Badge>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        <span class="text-xs font-medium text-slate-400">
                            {material.last_accessed
                                ? formatDate(material.last_accessed)
                                : 'Belum diakses'}
                        </span>
                    </td>
                {/snippet}
            </DataTable>

            <!-- Missing Questions (Anomalies) -->
            {#if state.missingQuestionsByMaterial.length > 0}
                <DataTable
                    title="Unit Tantangan Belum Terpecahkan"
                    items={state.missingQuestionsByMaterial}
                    columns={challengeColumns}
                    hideSearch={true}
                >
                    {#snippet row(item)}
                        <td class="border-b border-slate-50 px-6 py-6">
                            <span class="text-sm font-bold tracking-widest text-slate-900 uppercase"
                                >{item.material_title}</span
                            >
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6">
                            <div
                                class="inline-flex items-center rounded-xl border border-rose-100 bg-rose-50 px-3 py-1.5 text-rose-600"
                            >
                                <span class="text-[10px] font-bold tracking-widest uppercase"
                                    >{item.missing_count} MENUNGGU</span
                                >
                            </div>
                        </td>
                    {/snippet}
                </DataTable>
            {/if}
        </div>
    </div>
</App>
