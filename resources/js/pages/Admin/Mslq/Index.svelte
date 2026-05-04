<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Chart from '@/components/ui/Chart.svelte';
    import Select from '@/components/ui/Select.svelte';
    import { Eye, FileSpreadsheet, Brain, Target, ClipboardList, ChevronRight, Calculator } from 'lucide-svelte';
    import { MslqState } from '@/states/Admin/MslqState.svelte';
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import type { AdminMslqIndexProps, MslqResult } from '@/types';
    import { untrack } from 'svelte';
    import StatisticalAnalysis from '@/components/Admin/StatisticalAnalysis.svelte';
    import { router } from '@inertiajs/svelte';

    let {
        results,
        metrics = { averages: {}, total_responses: 0, avg_motivation: 0, avg_strategy: 0 },
        types = [],
        activeType = '',
        analysis = {},
    }: AdminMslqIndexProps & { analysis: any } = $props();

    let activeTab = $state('overview');
    let type1 = $state(activeType);
    let type2 = $state('');

    $effect(() => {
        type1 = activeType;
    });

    function handleComparison() {
        router.visit(window.location.pathname, {
            data: { type: type1, type1, type2 },
            preserveState: true,
            only: ['analysis'],
        });
    }

    const mslqState = untrack(() => new MslqState(results.data, metrics, types, activeType));

    // Removed unused scale helper variables

    const chartOptions = {
        chart: {
            toolbar: { show: false },
            dropShadow: { enabled: true, blur: 1, left: 1, top: 1, color: '#000' },
        },
        xaxis: {
            categories: [
                'Intrinsic Goal',
                'Extrinsic Goal',
                'Task Value',
                'Control Beliefs',
                'Self-Efficacy',
                'Anxiety',
                'Rehearsal',
                'Elaboration',
                'Organization',
                'Crit. Thinking',
                'Metacognitive',
                'Time/Study',
                'Effort',
                'Peer Learning',
                'Help Seeking',
            ],
            labels: {
                style: {
                    colors: '#64748b',
                    fontSize: '10px',
                    fontWeight: 700,
                },
            },
        },
        colors: ['#ff5242'], // accent-500
        fill: {
            opacity: 0.2,
            colors: ['#ff5242'],
        },
        stroke: { show: true, width: 3, colors: ['#ff5242'] },
        markers: {
            size: 4,
            colors: ['#fff'],
            strokeColors: '#ff5242',
            strokeWidth: 2,
        },
        tooltip: {
            theme: 'dark',
            marker: { show: true },
            x: { show: true },
        },
    };

    const chartSeries = $derived([
        {
            name: 'Skor Rata-rata',
            data: [
                metrics.averages['mslq_intrinsic_goal_orientation'] || 0,
                metrics.averages['mslq_extrinsic_goal_orientation'] || 0,
                metrics.averages['mslq_task_value'] || 0,
                metrics.averages['mslq_control_of_learning_beliefs'] || 0,
                metrics.averages['mslq_self_efficacy_for_learning_performance'] || 0,
                metrics.averages['mslq_test_anxiety'] || 0,
                metrics.averages['mslq_rehearsal'] || 0,
                metrics.averages['mslq_elaboration'] || 0,
                metrics.averages['mslq_organization'] || 0,
                metrics.averages['mslq_critical_thinking'] || 0,
                metrics.averages['mslq_metacognitive_self_regulation'] || 0,
                metrics.averages['mslq_time_study_environment_management'] || 0,
                metrics.averages['mslq_effort_regulation'] || 0,
                metrics.averages['mslq_peer_learning'] || 0,
                metrics.averages['mslq_help_seeking'] || 0,
            ],
        },
    ]);
</script>

<App title="Analitik MSLQ">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Analitik MSLQ"
            subtitle="Motivated Strategies for Learning Questionnaire"
            id="mslq-header"
        >
            {#snippet actions()}
                <div class="flex items-center gap-4">
                    <Select
                        placeholder="Filter Tipe"
                        value={mslqState.activeType}
                        onchange={(v) => mslqState.handleFilterChange(v as any)}
                        options={mslqState.types.map((t) => ({ label: t === 'pre' ? 'Pre-Test (Awal)' : 'Post-Test (Akhir)', value: t }))}
                        class="border-duo w-48 rounded-xl"
                    />
                    <Button
                        variant="success"
                        size="md"
                        icon={FileSpreadsheet}
                        class="border-duo"
                        onclick={() => mslqState.exportResults()}>EKSPOR CSV</Button
                    >
                </div>
            {/snippet}
        </PageHeader>

        <!-- Tabs -->
        <div class="flex items-center gap-2 border-b-2 border-slate-50">
            <button 
                onclick={() => activeTab = 'overview'}
                aria-selected={activeTab === 'overview'}
                role="tab"
                class="px-8 py-4 text-xs font-black tracking-widest uppercase transition-all {activeTab === 'overview' ? 'border-primary-500 text-primary-500 border-b-4' : 'text-slate-400 hover:text-slate-600'}"
            >
                Ringkasan Data
            </button>
            <button 
                onclick={() => activeTab = 'analysis'}
                aria-selected={activeTab === 'analysis'}
                role="tab"
                class="px-8 py-4 text-xs font-black tracking-widest uppercase transition-all {activeTab === 'analysis' ? 'border-primary-500 text-primary-600 border-b-4' : 'text-slate-400 hover:text-slate-600'}"
            >
                Analisis Statistik (Skripsi)
            </button>
        </div>

        {#if activeTab === 'overview'}
            <!-- Chart Summary -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <Card
                    padding="p-0"
                    class="border-duo overflow-hidden rounded-3xl border-slate-100 shadow-xl lg:col-span-2"
                    id="mslq-chart-card"
                >
                    <div class="p-8">
                        <div class="mb-6 flex items-center gap-4">
                            <div
                                class="bg-primary-50 text-primary-500 border-primary-100 flex h-10 w-10 items-center justify-center rounded-xl border-2"
                            >
                                <Brain size={20} />
                            </div>
                            <h3
                                class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                            >
                                Profil Belajar Mahasiswa
                            </h3>
                        </div>
                        <Chart type="radar" series={chartSeries} options={chartOptions} height={450} />
                    </div>
                </Card>

                <div class="space-y-6">
                    <Card
                        padding="p-0"
                        class="border-duo-lg border-accent-700 bg-accent-500 shadow-accent-100 overflow-hidden rounded-3xl text-white shadow-xl"
                        id="mslq-stat-motivation"
                    >
                        <div class="relative p-8">
                            <div
                                class="absolute -top-6 -right-6 h-24 w-24 rounded-full bg-white/10 blur-2xl"
                            ></div>
                            <div class="relative z-10 flex items-center justify-between">
                                <div class="space-y-1">
                                    <div
                                        class="text-[10px] font-black tracking-widest uppercase opacity-80"
                                    >
                                        Rata-rata Motivasi
                                    </div>
                                    <div class="text-4xl font-black">
                                        {mslqState.avgMotivation.toFixed(2)}
                                    </div>
                                </div>
                                <div class="rounded-2xl border-2 border-white/20 bg-white/20 p-4">
                                    <Target size={32} />
                                </div>
                            </div>
                        </div>
                    </Card>
 
                    <Card
                        padding="p-0"
                        class="border-duo-lg border-primary-800 bg-primary-500 overflow-hidden rounded-3xl text-white shadow-xl shadow-slate-200"
                        id="mslq-stat-strategy"
                    >
                        <div class="relative p-8">
                            <div
                                class="absolute -top-6 -right-6 h-24 w-24 rounded-full bg-white/10 blur-2xl"
                            ></div>
                            <div class="relative z-10 flex items-center justify-between">
                                <div class="space-y-1">
                                    <div
                                        class="text-[10px] font-black tracking-widest uppercase opacity-80"
                                    >
                                        Rata-rata Strategi
                                    </div>
                                    <div class="text-4xl font-black">
                                        {mslqState.avgStrategy.toFixed(2)}
                                    </div>
                                </div>
                                <div class="rounded-2xl border-2 border-white/20 bg-white/20 p-4">
                                    <ClipboardList size={32} />
                                </div>
                            </div>
                        </div>
                    </Card>

                    <Card class="border-duo rounded-3xl border-slate-100 shadow-xl" id="mslq-info">
                        <div class="space-y-4 p-8">
                            <h4
                                class="text-primary-500 font-display text-sm font-black tracking-widest uppercase"
                            >
                                Informasi
                            </h4>
                            <p class="text-[11px] leading-relaxed font-medium text-slate-500 uppercase">
                                Grafik radar di samping menunjukkan kekuatan dan kelemahan kolektif
                                mahasiswa dalam motivasi dan strategi belajar. Skor berkisar antara 1
                                hingga 7.
                            </p>
                        </div>
                    </Card>
                </div>
            </div>
        {:else}
            <!-- Statistical Analysis Section -->
            <div class="space-y-6">
                <Card class="border-duo overflow-hidden rounded-3xl border-slate-100 shadow-xl">
                    <div class="flex flex-wrap items-center justify-between gap-6 p-8">
                        <div class="space-y-1">
                            <h3 class="text-primary-500 font-display text-lg font-black tracking-widest uppercase">Komparasi Kelompok</h3>
                            <p class="text-xs font-medium text-slate-500 uppercase">Pilih dua tipe asesmen untuk melakukan uji Independent T-Test & Mann-Whitney U.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="space-y-2">
                                <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">Kelompok 1</span>
                                <Select 
                                    options={types.map(t => ({ label: t === 'pre' ? 'Pre-Test (Awal)' : 'Post-Test (Akhir)', value: t }))} 
                                    bind:value={type1} 
                                    placeholder="Tipe 1"
                                    class="w-40 rounded-xl"
                                />
                            </div>
                            <div class="text-slate-300 mt-6">
                                <ChevronRight size={20} />
                            </div>
                            <div class="space-y-2">
                                <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">Kelompok 2</span>
                                <Select 
                                    options={types.map(t => ({ label: t === 'pre' ? 'Pre-Test (Awal)' : 'Post-Test (Akhir)', value: t }))} 
                                    bind:value={type2} 
                                    placeholder="Tipe 2"
                                    class="w-40 rounded-xl"
                                />
                            </div>
                            <Button variant="primary" class="mt-6 rounded-xl font-black" icon={Calculator} onclick={handleComparison}>PROSES UJI</Button>
                        </div>
                    </div>
                </Card>

                <StatisticalAnalysis {analysis} />
            </div>
        {/if}

        <!-- Data Table -->
        <Card
            class="border-duo overflow-hidden rounded-3xl border-slate-100 shadow-xl"
            padding="p-0"
            id="mslq-table-card"
        >
            <div class="border-b-2 border-slate-50 p-8">
                <h3
                    class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                >
                    Hasil Kuesioner Mahasiswa
                </h3>
            </div>
            <DataTable
                items={results.data}
                links={results.links}
                columns={[
                    { key: 'user.name', label: 'Nama Mahasiswa', align: 'left' },
                    { key: 'assessment_type', label: 'Tipe Asesmen', align: 'left' },
                    { key: 'total_motivation', label: 'Motivasi', align: 'center' },
                    { key: 'total_strategy', label: 'Strategi Belajar', align: 'center' },
                    { key: 'created_at', label: 'Tanggal Submit', align: 'left' },
                    { key: 'actions', label: 'Aksi', align: 'right' },
                ]}
            >
                {#snippet row(item: MslqResult)}
                    <td
                        class="group-hover:border-accent-500 border-l-4 border-transparent px-6 py-6 transition-all"
                    >
                        <span class="font-bold text-slate-900">{item.user?.name ?? 'Tamu'}</span>
                    </td>
                    <td class="px-6 py-6">
                        <span
                            class="text-primary-500 bg-primary-50 border-primary-100 rounded-full border px-3 py-1 text-[10px] font-black tracking-widest uppercase"
                            >{item.assessment_type === 'pre' ? 'Pre-Test' : 'Post-Test'}</span
                        >
                    </td>
                    <td class="px-6 py-6 text-center">
                        <span class="text-accent-500 font-black">{item.total_motivation}</span>
                    </td>
                    <td class="px-6 py-6 text-center">
                        <span class="text-primary-500 font-black">{item.total_strategy}</span>
                    </td>
                    <td class="px-6 py-6">
                        <span class="text-xs font-bold text-slate-400 uppercase"
                            >{new Date(item.created_at).toLocaleDateString()}</span
                        >
                    </td>
                    <td class="px-6 py-6">
                        <div class="flex justify-end gap-2">
                            <Link href={ROUTES.ADMIN.MSLQ.SHOW(item.id)}>
                                <Button variant="ghost" size="sm" icon={Eye} />
                            </Link>
                        </div>
                    </td>
                {/snippet}
            </DataTable>
        </Card>
    </div>
</App>
