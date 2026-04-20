<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import Pagination from '@/components/ui/Pagination.svelte';
    import Card from '@/components/ui/Card.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { FileDown, Eye } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { SusListState } from '@/states/Admin/SusState.svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import type { AdminSusIndexProps } from '@/types';

    let {
        results = [],
        averages = { total: 0, items: {} },
        grading = { score: 0, adjective: '', grade: '', acceptability: '' },
        classes = [],
        activeClass = '',
    }: AdminSusIndexProps = $props();

    const state = untrack(() => new SusListState(results, averages, grading, classes, activeClass));

    const columns = $derived([
        { key: 'respondent', label: 'Responden', align: 'left' },
        { key: 'class', label: 'Kelas', align: 'center' },
        { key: 'score', label: 'Skor SUS', align: 'center' },
        { key: 'date', label: 'Tanggal Input', align: 'center' },
        { key: 'actions', label: 'Aksi', align: 'right' },
    ]);
</script>

<App title="Hasil Survey SUS">
    <div class="space-y-12 pb-20">
        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                Analitik System Usability Scale
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                Data kuantitatif mengenai kebergunaan sistem berdasarkan standar kuesioner SUS
                internasional.
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                <Button
                    id="sus-export-btn"
                    onclick={() => state.exportResults()}
                    variant="success"
                    icon={FileDown}>EKSPOR CSV</Button
                >
            </div>
        </div>

        <!-- Average Overview -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            <Card hover={true} class="group relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase">
                        Rata-rata Skor
                    </h3>
                    <div
                        class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900"
                    >
                        {state.averages.total.toFixed(1)}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="bg-primary-500 h-1.5 w-1.5 rounded-full"></div>
                        <p class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                            Skala 0-100
                        </p>
                    </div>
                </div>
            </Card>

            <Card hover={true} class="group relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase">
                        Adjective Rating
                    </h3>
                    <div
                        class="font-display mb-2 text-2xl font-black tracking-tight text-slate-900"
                    >
                        {state.grading.adjective}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                        <p class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                            Qualitative
                        </p>
                    </div>
                </div>
            </Card>

            <Card hover={true} class="group relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase">
                        Grade Scale
                    </h3>
                    <div
                        class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900"
                    >
                        {state.grading.grade}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-1.5 rounded-full bg-amber-500"></div>
                        <p class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                            Letter Grade
                        </p>
                    </div>
                </div>
            </Card>

            <Card hover={true} class="group relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase">
                        Acceptability
                    </h3>
                    <div
                        class="font-display mb-2 text-2xl font-black tracking-tight text-slate-900"
                    >
                        {state.grading.acceptability}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-1.5 rounded-full bg-indigo-500"></div>
                        <p class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                            Conclusion
                        </p>
                    </div>
                </div>
            </Card>
        </div>

        <div id="sus-class-filter" class="mb-4 flex justify-end">
            <select
                onchange={(e) => state.handleFilterChange(e)}
                class="focus:ring-primary-100 focus:border-primary-600 cursor-pointer appearance-none rounded-2xl border border-slate-200 bg-slate-50 py-2.5 pr-10 pl-4 text-xs font-bold transition-all outline-none focus:ring-4"
                value={state.activeClass}
            >
                <option value="">Semua Kelas</option>
                {#each state.classes as c}
                    <option value={c}>{c}</option>
                {/each}
            </select>
        </div>

        <div id="sus-results-table">
            <DataTable title="Log Responden SUS" items={state.results} {columns} hideSearch={true}>
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
                                    {result.nim || '-'}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6 text-center">
                        <span
                            class="rounded-xl bg-slate-100 px-3 py-1.5 text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                        >
                            {result.class || '-'}
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

        <Pagination links={(state.results as any).links || []} />
    </div>
</App>
