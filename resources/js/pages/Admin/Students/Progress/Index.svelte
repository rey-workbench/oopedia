<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import { ArrowLeft, LineChart, CheckCheck, Zap, Award } from 'lucide-svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { StudentProgressState } from '@/states/Admin/StudentState.svelte';
    import type { User, MaterialWithProgress, MissingQuestionsItem } from '@/types';

    let {
        student,
        materials = [],
        missingQuestionsByMaterial = [],
        certifications = {},
    }: {
        student: User;
        materials: MaterialWithProgress[];
        missingQuestionsByMaterial: MissingQuestionsItem[];
        certifications: Record<number, string>;
    } = $props();

    const state = untrack(
        () => new StudentProgressState(student, materials, missingQuestionsByMaterial, certifications)
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
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            {#each progressStats as stat}
                <Card hover={true} class="relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 text-slate-400">
                        {#if typeof stat.icon !== 'string'}
                            {@const IconComponent = stat.icon}
                            <div class="scale-[4] transition-transform duration-500 group-hover:scale-[4.5]">
                                <IconComponent size={24} strokeWidth={2.5} />
                            </div>
                        {/if}
                    </div>

                    <div class="relative z-10">
                        <div
                            class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm
                            {stat.variant === 'success' ? 'bg-emerald-100 text-emerald-600' : 
                             stat.variant === 'danger' ? 'bg-rose-100 text-rose-600' :
                             'bg-primary-100 text-primary-600'}"
                        >
                            {#if typeof stat.icon === 'string'}
                                <i class={stat.icon}></i>
                            {:else}
                                {@const IconComponent = stat.icon}
                                <IconComponent size={24} strokeWidth={2.5} />
                            {/if}
                        </div>

                        <h3 class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase">
                            {stat.title}
                        </h3>
                        <div class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900">
                            {stat.value}
                        </div>

                        {#if stat.footer}
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-1.5 w-1.5 rounded-full {stat.variant === 'success'
                                        ? 'bg-emerald-500'
                                        : stat.variant === 'danger'
                                        ? 'bg-rose-500'
                                        : 'bg-primary-500'}"
                                ></div>
                                <p class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                                    {stat.footer}
                                </p>
                            </div>
                        {/if}
                    </div>
                </Card>
            {/each}
        </div>

        <!-- Certifications Section -->
        {#if Object.keys(state.certifications).length > 0}
            <div class="space-y-6">
                <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                    Pencapaian Sertifikasi
                </h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {#each Object.entries(state.certifications) as [materialId, type]}
                        {@const material = state.materials.find(m => m.id === Number(materialId))}
                        <Card class="relative overflow-hidden border-2 {type === 'gold' ? 'border-amber-400 bg-amber-50/10' : type === 'silver' ? 'border-slate-300 bg-slate-50/10' : 'border-orange-300 bg-orange-50/10'} px-6 py-8">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl {type === 'gold' ? 'bg-amber-100 text-amber-600' : type === 'silver' ? 'bg-slate-200 text-slate-600' : 'bg-orange-100 text-orange-600'} shadow-md">
                                    <Award size={24} strokeWidth={2.5} />
                                </div>
                                <div class="flex-1">
                                    <span class="text-[9px] font-black tracking-widest uppercase {type === 'gold' ? 'text-amber-600' : type === 'silver' ? 'text-slate-500' : 'text-orange-600'}">
                                        CERTIFIED ARCHITECT {type.toUpperCase()}
                                    </span>
                                    <h4 class="text-sm font-bold text-slate-900 uppercase truncate">
                                        {material?.title || 'Project'}
                                    </h4>
                                </div>
                            </div>
                        </Card>
                    {/each}
                </div>
            </div>
        {/if}

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
