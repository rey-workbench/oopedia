<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import StatCard from '@/components/ui/StatCard.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Select from '@/components/ui/Select.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import {
        FileDown,
        Eye,
        ChevronRight,
        BarChart3,
        Star,
        Award,
        CheckCircle2,
        Calculator,
    } from '@lucide/svelte';
    import { untrack } from 'svelte';
    import { SusListState } from '@/states/Admin/SusState.svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import type { AdminSusIndexProps } from '@/types';
    import { router } from '@inertiajs/svelte';
    import StatisticalAnalysis from '@/components/Admin/StatisticalAnalysis.svelte';
    import { Button } from '@/components';

    let {
        results = [],
        averages = { total: 0, items: {} },
        grading = { score: 0, adjective: '', grade: '', acceptability: '' },
        types = [],
        activeType = '',
        analysis = {},
    }: AdminSusIndexProps & { analysis: any } = $props();

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

    const susState = untrack(() => new SusListState(results, averages, grading, types, activeType));

    const columns = $derived([
        { key: 'respondent', label: 'Responden', align: 'left' },
        { key: 'assessment_type', label: 'Tipe Asesmen', align: 'center' },
        { key: 'score', label: 'Skor SUS', align: 'center' },
        { key: 'date', label: 'Tanggal Input', align: 'center' },
        { key: 'actions', label: 'Aksi', align: 'right' },
    ]);
</script>

<App title="Hasil Survey SUS">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Analitik SUS"
            subtitle="System Usability Scale - Data kuantitatif mengenai kebergunaan sistem."
            id="sus-header"
        >
            {#snippet actions()}
                <div class="flex items-center gap-4">
                    <Select
                        placeholder="Filter Tipe"
                        value={susState.activeType}
                        onchange={(v) => susState.handleFilterChange(v as any)}
                        options={susState.types.map((t) => ({
                            label: t === 'pre' ? 'Pre-Test (Awal)' : 'Post-Test (Akhir)',
                            value: t,
                        }))}
                        class="border-duo w-48 rounded-xl"
                    />
                    <Button
                        variant="success"
                        size="md"
                        icon={FileDown}
                        class="border-duo"
                        onclick={() => susState.exportResults()}>EKSPOR CSV</Button
                    >
                </div>
            {/snippet}
        </PageHeader>

        <!-- Tabs -->
        <div class="flex items-center gap-2 border-b-2 border-slate-50">
            <button
                onclick={() => (activeTab = 'overview')}
                aria-selected={activeTab === 'overview'}
                role="tab"
                class="px-8 py-4 text-xs font-black tracking-widest uppercase transition-all {activeTab ===
                'overview'
                    ? 'border-primary-500 text-primary-500 border-b-4'
                    : 'text-slate-400 hover:text-slate-600'}"
            >
                Ringkasan Data
            </button>
            <button
                onclick={() => (activeTab = 'analysis')}
                aria-selected={activeTab === 'analysis'}
                role="tab"
                class="px-8 py-4 text-xs font-black tracking-widest uppercase transition-all {activeTab ===
                'analysis'
                    ? 'border-primary-500 text-primary-600 border-b-4'
                    : 'text-slate-400 hover:text-slate-600'}"
            >
                Analisis Statistik (Skripsi)
            </button>
        </div>

        {#if activeTab === 'overview'}
            <!-- Average Overview -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <StatCard
                    title="Rata-rata Skor"
                    value={susState.averages.total.toFixed(1)}
                    icon={BarChart3}
                    variant="accent"
                    footer="Skala 0-100"
                />

                <StatCard
                    title="Adjective Rating"
                    value={susState.grading.adjective}
                    icon={Star}
                    variant="success"
                    footer="Qualitative Scale"
                />

                <StatCard
                    title="Grade Scale"
                    value={susState.grading.grade}
                    icon={Award}
                    variant="warning"
                    footer="Letter Grade"
                />

                <StatCard
                    title="Acceptability"
                    value={susState.grading.acceptability}
                    icon={CheckCircle2}
                    variant="info"
                    footer="Usability Conclusion"
                />
            </div>

            <div id="sus-results-table" class="mt-8">
                <DataTable
                    title="Log Responden SUS"
                    items={susState.results}
                    {columns}
                    hideSearch={true}
                    itemsPerPage={10}
                >
                    {#snippet row(result)}
                        <td class="border-b border-slate-50 px-6 py-6">
                            <div class="flex items-center gap-4">
                                <UserAvatar name={result.user ? result.user.name : '?'} />
                                <div>
                                    <div
                                        class="text-sm font-bold tracking-widest text-slate-900 uppercase"
                                    >
                                        {result.user ? result.user.name : 'Tamu'}
                                    </div>
                                    <div
                                        class="mt-0.5 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                                    >
                                        ID: {result.id.substring(0, 8)}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6 text-center">
                            <span
                                class="rounded-xl bg-slate-100 px-3 py-1.5 text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                            >
                                {result.assessment_type === 'pre' ? 'Pre-Test' : 'Post-Test'}
                            </span>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6 text-center">
                            <div
                                class="font-display inline-flex h-10 w-16 items-center justify-center rounded-xl bg-slate-900 text-sm font-bold text-white"
                            >
                                {result.total_score}
                            </div>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6 text-center">
                            <span class="text-xs font-medium text-slate-500">
                                {result.created_at ? formatDate(result.created_at) : '-'}
                            </span>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6">
                            <div class="flex justify-end">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    href={ROUTES.ADMIN.SUS.SHOW(result.id)}
                                    icon={Eye}
                                />
                            </div>
                        </td>
                    {/snippet}
                </DataTable>
            </div>
        {:else}
            <!-- Statistical Analysis Section -->
            <div class="space-y-6">
                <Card class="border-duo overflow-hidden rounded-3xl border-slate-100 shadow-xl">
                    <div class="flex flex-wrap items-center justify-between gap-6 p-8">
                        <div class="space-y-1">
                            <h3
                                class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                            >
                                Komparasi Kelompok
                            </h3>
                            <p class="text-xs font-medium text-slate-500 uppercase">
                                Pilih dua tipe asesmen untuk membandingkan skor SUS secara
                                statistik.
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="space-y-2">
                                <span
                                    class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >Kelompok 1</span
                                >
                                <Select
                                    options={types.map((t) => ({
                                        label:
                                            t === 'pre' ? 'Pre-Test (Awal)' : 'Post-Test (Akhir)',
                                        value: t,
                                    }))}
                                    bind:value={type1}
                                    placeholder="Tipe 1"
                                    class="w-40 rounded-xl"
                                />
                            </div>
                            <div class="mt-6 text-slate-300">
                                <ChevronRight size={20} />
                            </div>
                            <div class="space-y-2">
                                <span
                                    class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >Kelompok 2</span
                                >
                                <Select
                                    options={types.map((t) => ({
                                        label:
                                            t === 'pre' ? 'Pre-Test (Awal)' : 'Post-Test (Akhir)',
                                        value: t,
                                    }))}
                                    bind:value={type2}
                                    placeholder="Tipe 2"
                                    class="w-40 rounded-xl"
                                />
                            </div>
                            <Button
                                variant="primary"
                                class="mt-6 rounded-xl font-black"
                                icon={Calculator}
                                onclick={handleComparison}>PROSES UJI</Button
                            >
                        </div>
                    </div>
                </Card>

                <StatisticalAnalysis {analysis} />
            </div>
        {/if}
    </div>
</App>
