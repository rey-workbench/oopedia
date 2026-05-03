<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import Pagination from '@/components/ui/Pagination.svelte';
    import StatCard from '@/components/ui/StatCard.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Select from '@/components/ui/Select.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import StatisticalAnalysis from '@/components/Admin/StatisticalAnalysis.svelte';
    import { BarChart3, FileDown, Eye, Calculator } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { UeqListState } from '@/states/Admin/UeqState.svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import { router } from '@inertiajs/svelte';

    let {
        surveys = [],
        averages = {},
        classes = [],
        activeClass = '',
        analysis = {},
    }: {
        surveys: any[];
        averages: Record<string, number>;
        classes: string[];
        activeClass: string;    
        analysis: any;
    } = $props();

    const state = untrack(() => new UeqListState(surveys, averages, classes, activeClass));

    let activeTab = $state('overview');
    let class1 = $state(activeClass);
    let class2 = $state('');

    const columns = $derived([
        { key: 'respondent', label: 'Responden', align: 'left' },
        { key: 'class', label: 'Kelas', align: 'center' },
        { key: 'date', label: 'Tanggal Input', align: 'center' },
        { key: 'actions', label: 'Aksi', align: 'right' },
    ]);

    const statsData = $derived(
        Object.entries(state.averages).map(([dimension, score]) => ({
            title: dimension,
            value: (score as number).toFixed(2),
            icon: BarChart3,
            variant:
                (score as number) >= 1.5
                    ? ('success' as const)
                    : (score as number) >= 0.8
                      ? ('warning' as const)
                      : ('danger' as const),
            footer:
                (score as number) >= 1.5
                    ? 'Sangat Baik'
                    : (score as number) >= 0.8
                      ? 'Rata-rata'
                      : 'Perlu Perbaikan',
        }))
    );

    function runAnalysis() {
        router.get(
            ROUTES.ADMIN.UEQ.INDEX,
            { class: activeClass, class1, class2 },
            { preserveState: true, only: ['analysis'] }
        );
    }
</script>

<App title="Hasil Survey UEQ">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Analitik UEQ"
            subtitle="User Experience Questionnaire - Metrik komprehensif kepuasan pengguna."
            id="ueq-header"
        >
            {#snippet actions()}
                <div class="flex items-center gap-4">
                    <Select
                        placeholder="Filter Kelas"
                        value={state.activeClass}
                        onchange={(v) => state.handleFilterChange({ target: { value: v } } as any)}
                        options={state.classes.map((c) => ({ label: c, value: c }))}
                        class="border-duo w-48 rounded-xl"
                    />
                    <Button
                        variant="success"
                        size="md"
                        icon={FileDown}
                        class="border-duo"
                        onclick={() => state.exportResults()}>EKSPOR CSV</Button
                    >
                </div>
            {/snippet}
        </PageHeader>

        <!-- Tab Navigation -->
        <div class="border-b border-slate-100">
            <nav class="flex gap-8" aria-label="Tabs" role="tablist">
                <button
                    onclick={() => activeTab = 'overview'}
                    aria-selected={activeTab === 'overview'}
                    role="tab"
                    class="px-8 py-4 text-xs font-black tracking-widest uppercase transition-all {activeTab === 'overview' ? 'border-primary-500 text-primary-500 border-b-4' : 'text-slate-400 hover:text-slate-600'}"
                >
                    Ikhtisar Data
                </button>
                <button
                    onclick={() => activeTab = 'analysis'}
                    aria-selected={activeTab === 'analysis'}
                    role="tab"
                    class="px-8 py-4 text-xs font-black tracking-widest uppercase transition-all {activeTab === 'analysis' ? 'border-accent-500 text-accent-500 border-b-4' : 'text-slate-400 hover:text-slate-600'}"
                >
                    Analisis Statistik (Skripsi)
                </button>
            </nav>
        </div>

        {#if activeTab === 'overview'}
            <!-- Averages Overview -->
            <div id="ueq-summary-metrics" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                {#each statsData as stat (stat.title)}
                    <StatCard 
                        title={stat.title}
                        value={stat.value}
                        icon={stat.icon}
                        variant={stat.variant}
                        footer={stat.footer}
                    />
                {/each}
            </div>

            <div id="ueq-respondents-table" class="mt-8">
                <DataTable
                    title="Log Responden Survey"
                    items={state.surveys}
                    {columns}
                    hideSearch={true}
                >
                    {#snippet row(survey)}
                        <td class="border-b border-slate-50 px-6 py-6">
                            <div class="flex items-center gap-4">
                                <UserAvatar name={survey.user ? survey.user.name : '?'} />
                                <div>
                                    <div
                                        class="text-sm font-bold tracking-widest text-slate-900 uppercase"
                                    >
                                        {survey.user ? survey.user.name : 'Tamu'}
                                    </div>
                                    <div
                                        class="mt-0.5 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                                    >
                                        {survey.nim || '-'}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6 text-center">
                            <span
                                class="rounded-xl bg-slate-100 px-3 py-1.5 text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                            >
                                {survey.class || '-'}
                            </span>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6 text-center">
                            <span class="text-xs font-medium text-slate-500">
                                {survey.created_at ? formatDate(survey.created_at) : '-'}
                            </span>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6">
                            <div class="flex justify-end">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    href={ROUTES.ADMIN.UEQ.SHOW(survey.id)}
                                    icon={Eye}
                                />
                            </div>
                        </td>
                    {/snippet}
                </DataTable>
            </div>

            <Pagination links={(state.surveys as any).links || []} />
        {:else}
            <!-- Statistical Analysis Section -->
            <div class="space-y-6">
                <Card class="border-duo overflow-hidden rounded-3xl border-slate-100 shadow-xl">
                    <div class="flex flex-wrap items-center justify-between gap-6 p-8">
                        <div class="space-y-1">
                            <h3 class="text-accent-500 font-display text-lg font-black tracking-widest uppercase">Komparasi Kelompok UEQ</h3>
                            <p class="text-xs font-medium text-slate-500 uppercase">Pilih dua kelas untuk membandingkan skor UEQ (Attractiveness) secara statistik.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="space-y-2">
                                <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">Kelompok 1</span>
                                <Select 
                                    options={state.classes.map(c => ({ label: c, value: c }))} 
                                    bind:value={class1} 
                                    placeholder="Kelas 1"
                                    class="w-40 rounded-xl"
                                />
                            </div>
                            <div class="space-y-2">
                                <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">Kelompok 2</span>
                                <Select 
                                    options={state.classes.map(c => ({ label: c, value: c }))} 
                                    bind:value={class2} 
                                    placeholder="Kelas 2"
                                    class="w-40 rounded-xl"
                                />
                            </div>
                            <Button 
                                variant="primary" 
                                size="md" 
                                class="mt-6"
                                icon={Calculator}
                                onclick={runAnalysis}
                            >
                                ANALISIS
                            </Button>
                        </div>
                    </div>
                </Card>

                <StatisticalAnalysis analysis={analysis} />
            </div>
        {/if}
    </div>
</App>
