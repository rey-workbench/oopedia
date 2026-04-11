<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import Pagination from '@/components/ui/Pagination.svelte';
    import Card from '@/components/ui/Card.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { BarChart3, FileDown, Eye } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { UeqListState } from '@/states/Admin/UeqState.svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';

    let {
        surveys = [],
        averages = {},
        classes = [],
        activeClass = '',
    }: {
        surveys: any[];
        averages: Record<string, number>;
        classes: string[];
        activeClass: string;
    } = $props();

    const state = untrack(() => new UeqListState(surveys, averages, classes, activeClass));

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
                    ? 'success'
                    : (score as number) >= 0.8
                      ? 'warning'
                      : 'danger',
            footer:
                (score as number) >= 1.5
                    ? 'Sangat Baik'
                    : (score as number) >= 0.8
                      ? 'Rata-rata'
                      : 'Perlu Perbaikan',
        }))
    );
</script>

<App title="Hasil Survey UEQ">
    <div class="space-y-12 pb-20">
        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                Analitik User Experience
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                Metrik komprehensif kepuasan pengguna menggunakan kuesioner UEQ (User Experience
                Questionnaire).
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                <Button id="ueq-export-btn" onclick={() => state.exportResults()} variant="success" icon={FileDown}
                    >EKSPOR CSV</Button
                >
            </div>
        </div>

        <!-- Averages Overview -->
        <div id="ueq-stats-grid" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            {#each statsData as stat (stat.title)}
                <Card hover={true} class="group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 text-slate-400 opacity-10">
                        {#if typeof stat.icon !== 'string'}
                            {@const IconComponent = stat.icon}
                            <div
                                class="scale-[4] transition-transform duration-500 group-hover:scale-[4.5]"
                            >
                                <IconComponent size={24} strokeWidth={2.5} />
                            </div>
                        {/if}
                    </div>

                    <div class="relative z-10">
                        <div
                            class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm
                            {stat.variant === 'success'
                                ? 'bg-emerald-100 text-emerald-600'
                                : stat.variant === 'warning'
                                  ? 'bg-amber-100 text-amber-600'
                                  : stat.variant === 'danger'
                                    ? 'bg-rose-100 text-rose-600'
                                    : 'bg-primary-100 text-primary-600'}"
                        >
                            {#if typeof stat.icon === 'string'}
                                <i class={stat.icon}></i>
                            {:else}
                                {@const IconComponent = stat.icon}
                                <IconComponent size={24} strokeWidth={2.5} />
                            {/if}
                        </div>

                        <h3
                            class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase"
                        >
                            {stat.title}
                        </h3>
                        <div
                            class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900"
                        >
                            {stat.value}
                        </div>

                        {#if stat.footer}
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-1.5 w-1.5 rounded-full {stat.variant === 'success'
                                        ? 'bg-emerald-500'
                                        : stat.variant === 'warning'
                                          ? 'bg-amber-500'
                                          : stat.variant === 'danger'
                                            ? 'bg-rose-500'
                                            : 'bg-primary-500'}"
                                ></div>
                                <p
                                    class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                                >
                                    {stat.footer}
                                </p>
                            </div>
                        {/if}
                    </div>
                </Card>
            {/each}
        </div>

        <div id="ueq-class-filter" class="mb-4 flex justify-end">
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

        <div id="ueq-log-table">
        <DataTable title="Log Responden Survey" items={state.surveys} {columns} hideSearch={true}>
            {#snippet row(survey)}
                <td class="border-b border-slate-50 px-6 py-6">
                    <div class="flex items-center gap-4">
                        <UserAvatar name={survey.user ? survey.user.name : '?'} />
                        <div>
                            <div class="text-sm font-bold tracking-widest text-slate-900 uppercase">
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
                <td class="border-b border-slate-50 px-6 py-6">
                    <span
                        class="rounded-xl bg-slate-100 px-3 py-1.5 text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                    >
                        {survey.class || '-'}
                    </span>
                </td>
                <td class="border-b border-slate-50 px-6 py-6">
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
    </div>
</App>
