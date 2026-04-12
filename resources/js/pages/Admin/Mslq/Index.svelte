<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Chart from '@/components/ui/Chart.svelte';
    import Select from '@/components/ui/Select.svelte';
    import { Eye, FileSpreadsheet, Brain, Target, ClipboardList } from 'lucide-svelte';
    import { MslqListState } from '@/states/Admin/MslqListState.svelte';
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import type { Pagination, MslqResult } from '@/types';

    import { untrack } from 'svelte';

    let { 
        results, 
        averages = {}, 
        classes = [], 
        activeClass = '' 
    }: { 
        results: Pagination<MslqResult>, 
        averages: Record<string, number>, 
        classes: string[], 
        activeClass: string 
    } = $props();

    const state = untrack(() => new MslqListState(results.data, averages, classes, activeClass));

    // Removed unused scale helper variables

    const chartOptions = {
        chart: {
            toolbar: { show: false },
            dropShadow: { enabled: true, blur: 1, left: 1, top: 1 }
        },
        xaxis: {
            categories: [
                'Intrinsic Goal', 'Extrinsic Goal', 'Task Value', 'Control Beliefs', 'Self-Efficacy', 'Anxiety',
                'Rehearsal', 'Elaboration', 'Organization', 'Crit. Thinking', 'Metacognitive', 'Time/Study', 'Effort', 'Peer Learning', 'Help Seeking'
            ]
        },
        colors: ['#4f46e5'],
        fill: { opacity: 0.1 },
        stroke: { show: true, width: 2 },
        markers: { size: 4 }
    };

    const chartSeries = $derived([
        {
            name: 'Skor Rata-rata',
            data: [
                averages['mslq_intrinsic_goal_orientation'] || 0,
                averages['mslq_extrinsic_goal_orientation'] || 0,
                averages['mslq_task_value'] || 0,
                averages['mslq_control_of_learning_beliefs'] || 0,
                averages['mslq_self_efficacy_for_learning_performance'] || 0,
                averages['mslq_test_anxiety'] || 0,
                averages['mslq_rehearsal'] || 0,
                averages['mslq_elaboration'] || 0,
                averages['mslq_organization'] || 0,
                averages['mslq_critical_thinking'] || 0,
                averages['mslq_metacognitive_self_regulation'] || 0,
                averages['mslq_time_study_environment_management'] || 0,
                averages['mslq_effort_regulation'] || 0,
                averages['mslq_peer_learning'] || 0,
                averages['mslq_help_seeking'] || 0
            ]
        }
    ]);
</script>

<App title="Analitik MSLQ">
    <div class="space-y-8 pb-10">
        <PageHeader 
            title="Analitik MSLQ" 
            subtitle="Motivated Strategies for Learning Questionnaire"
            id="mslq-header"
        >
            {#snippet actions()}
                <div class="flex items-center gap-4">
                    <Select
                        placeholder="Filter Kelas"
                        value={state.activeClass}
                        onchange={(v) => state.handleFilterChange(v as any)}
                        options={state.classes.map(c => ({ label: c, value: c }))}
                        class="w-48"
                    />
                    <Button 
                        variant="outline" 
                        icon={FileSpreadsheet}
                        onclick={() => state.exportResults()}
                    >Export Data</Button>
                </div>
            {/snippet}
        </PageHeader>

        <!-- Chart Summary -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <Card class="lg:col-span-2 overflow-hidden rounded-3xl border-slate-100 shadow-xl" id="mslq-chart-card">
                <div class="p-8">
                    <div class="mb-6 flex items-center gap-4">
                        <div class="bg-indigo-50 text-indigo-600 flex h-10 w-10 items-center justify-center rounded-xl">
                            <Brain size={20} />
                        </div>
                        <h3 class="text-lg font-bold tracking-widest text-slate-900 uppercase">Profil Belajar Mahasiswa</h3>
                    </div>
                    <Chart type="radar" series={chartSeries} options={chartOptions} height={450} />
                </div>
            </Card>

            <div class="space-y-6">
                <Card class="rounded-3xl border-slate-100 shadow-xl bg-indigo-600 text-white" id="mslq-stat-motivation">
                    <div class="p-8">
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <div class="text-[10px] font-bold tracking-widest uppercase opacity-80">Rata-rata Motivasi</div>
                                <div class="text-4xl font-black">
                                    { (results.data.reduce((acc, r) => acc + r.total_motivation, 0) / (results.data.length || 1)).toFixed(2) }
                                </div>
                            </div>
                            <div class="bg-white/20 p-4 rounded-2xl">
                                <Target size={32} />
                            </div>
                        </div>
                    </div>
                </Card>

                <Card class="rounded-3xl border-slate-100 shadow-xl bg-emerald-600 text-white" id="mslq-stat-strategy">
                    <div class="p-8">
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <div class="text-[10px] font-bold tracking-widest uppercase opacity-80">Rata-rata Strategi</div>
                                <div class="text-4xl font-black">
                                    { (results.data.reduce((acc, r) => acc + r.total_strategy, 0) / (results.data.length || 1)).toFixed(2) }
                                </div>
                            </div>
                            <div class="bg-white/20 p-4 rounded-2xl">
                                <ClipboardList size={32} />
                            </div>
                        </div>
                    </div>
                </Card>

                <Card class="rounded-3xl border-slate-100 shadow-xl" id="mslq-info">
                    <div class="p-8 space-y-4">
                        <h4 class="text-sm font-bold tracking-widest text-slate-900 uppercase">Informasi</h4>
                        <p class="text-xs leading-relaxed text-slate-500">
                            Grafik radar di samping menunjukkan kekuatan dan kelemahan kolektif mahasiswa dalam motivasi dan strategi belajar. Skor berkisar antara 1 hingga 7.
                        </p>
                    </div>
                </Card>
            </div>
        </div>

        <!-- Data Table -->
        <Card class="overflow-hidden rounded-3xl border-slate-100 shadow-xl" padding="p-0" id="mslq-table-card">
            <div class="border-b border-slate-50 p-8">
                <h3 class="text-lg font-bold tracking-widest text-slate-900 uppercase">Hasil Kuesioner Mahasiswa</h3>
            </div>
            <DataTable
                items={results.data}
                columns={[
                    { key: 'user.name', label: 'Nama Mahasiswa', align: 'left' },
                    { key: 'nim', label: 'NIM', align: 'left' },
                    { key: 'class', label: 'Kelas', align: 'left' },
                    { key: 'total_motivation', label: 'Motivasi', align: 'center' },
                    { key: 'total_strategy', label: 'Strategi Belajar', align: 'center' },
                    { key: 'created_at', label: 'Tanggal Submit', align: 'left' },
                    { key: 'actions', label: 'Aksi', align: 'right' }
                ]}
            >
                {#snippet row(item: MslqResult)}
                    <td class="px-6 py-6 border-l-4 border-transparent group-hover:border-indigo-600 transition-all">
                        <span class="font-bold text-slate-900">{item.user.name}</span>
                    </td>
                    <td class="px-6 py-6 font-medium text-slate-500">{item.nim}</td>
                    <td class="px-6 py-6">
                        <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase bg-slate-100 px-3 py-1 rounded-full">{item.class}</span>
                    </td>
                    <td class="px-6 py-6 text-center">
                        <span class="font-bold text-indigo-600">{item.total_motivation}</span>
                    </td>
                    <td class="px-6 py-6 text-center">
                        <span class="font-bold text-emerald-600">{item.total_strategy}</span>
                    </td>
                    <td class="px-6 py-6">
                        <span class="text-xs text-slate-500">{new Date(item.created_at).toLocaleDateString()}</span>
                    </td>
                    <td class="px-6 py-6">
                        <div class="flex justify-end gap-2">
                            <Link href={ROUTES.ADMIN.MSLQ.SHOW(item.id)}>
                                <Button variant="ghost" size="sm" icon={Eye} color="primary" />
                            </Link>
                        </div>
                    </td>
                {/snippet}
            </DataTable>
        </Card>
    </div>
</App>
