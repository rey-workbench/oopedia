<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Card from '@/components/ui/Card.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import StatCard from '@/components/ui/StatCard.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import { Target, ClipboardList } from 'lucide-svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import type { MslqResult } from '@/types';
    import { MslqDetailState } from '@/states/Admin/MslqState.svelte';
    import { untrack } from 'svelte';

    let { result }: { result: MslqResult } = $props();

    const state = untrack(() => new MslqDetailState(result));

    const breadcrumbs = [
        { label: 'Analitik MSLQ', href: ROUTES.ADMIN.MSLQ.INDEX },
        { label: 'Detail Responden' }
    ];
</script>

<App title={`Detail MSLQ - ${state.result.user.name}`}>
    <div class="space-y-12 pb-20">
        <!-- Page Header -->
        <PageHeader
            title="Analisis MSLQ"
            subtitle="Detail instrumen Motivated Strategies for Learning Questionnaire."
            {breadcrumbs}
        >
            {#snippet actions()}
                <div class="flex items-center gap-6 rounded-3xl bg-white p-4 shadow-xl shadow-slate-200/50 ring-1 ring-slate-100">
                    <UserAvatar name={state.result.user.name} size="lg" />
                    <div>
                        <div class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                            {state.result.user.name}
                        </div>
                        <div class="mt-0.5 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                            {state.result.nim || '-'} • {state.result.class || '-'}
                        </div>
                    </div>
                </div>
            {/snippet}
        </PageHeader>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <StatCard 
                title="Skor Motivasi"
                value={state.result.total_motivation.toFixed(2)}
                icon={Target}
                variant="accent"
                footer="Global Motivation"
            />
            <StatCard 
                title="Skor Strategi"
                value={state.result.total_strategy.toFixed(2)}
                icon={ClipboardList}
                variant="primary"
                footer="Learning Strategies"
            />
            
            <Card class="lg:col-span-2 rounded-[2rem] bg-slate-900 border-none p-6 text-white shadow-2xl flex items-center justify-between">
                <div class="flex items-center gap-6 px-4">
                    <div class="text-center">
                        <div class="text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase mb-1">MSLQ Status</div>
                        <div class="text-2xl font-black tracking-widest">COMPLETE</div>
                    </div>
                    <div class="h-10 w-px bg-white/10"></div>
                    <div class="text-center">
                        <div class="text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase mb-1">Submitted</div>
                        <div class="text-xs font-bold text-slate-300">{formatDate(state.result.created_at)}</div>
                    </div>
                </div>
                <Badge variant="success" size="sm" class="animate-pulse">
                    <div class="mr-2 h-1 w-1 rounded-full bg-white"></div>
                    Verified
                </Badge>
            </Card>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Info Alert -->
                <Alert variant="primary" class="shadow-lg">
                    <div class="space-y-2">
                        <h4 class="font-black uppercase tracking-widest text-[10px]">Panduan Interpretasi</h4>
                        <p class="font-medium text-slate-600 leading-relaxed text-[10px]">
                            Skala MSLQ menggunakan rentang 1-7. Skor yang lebih tinggi menunjukkan kecenderungan yang lebih kuat pada dimensi tersebut.
                        </p>
                    </div>
                </Alert>

                <!-- Detailed Breakdown Section -->
                <Card class="rounded-3xl border-slate-100 shadow-xl overflow-hidden" padding="p-0">
                    <div class="bg-slate-50/50 border-b border-slate-100 px-8 py-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="bg-accent-500 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg shadow-accent-100">
                                <Target size={20} />
                            </div>
                            <h3 class="text-sm font-black tracking-widest text-slate-900 uppercase">Motivasi</h3>
                        </div>
                    </div>
                    <div class="p-8 space-y-5">
                        {#each state.motivationScores as score}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black tracking-widest text-slate-500 uppercase">{score.label}</span>
                                    <span class="text-accent-500 text-[10px] font-black">{score.value.toFixed(2)}</span>
                                </div>
                                <ProgressBar
                                    value={(score.value / 7) * 100}
                                    color="accent"
                                    height="h-1.5"
                                />
                            </div>
                        {/each}
                    </div>
                </Card>

                <Card class="rounded-3xl border-slate-100 shadow-xl overflow-hidden" padding="p-0">
                    <div class="bg-slate-50/50 border-b border-slate-100 px-8 py-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="bg-primary-500 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg shadow-slate-100">
                                <ClipboardList size={20} />
                            </div>
                            <h3 class="text-sm font-black tracking-widest text-slate-900 uppercase">Strategi</h3>
                        </div>
                    </div>
                    <div class="p-8 space-y-5">
                        {#each state.strategyScores as score}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black tracking-widest text-slate-500 uppercase">{score.label}</span>
                                    <span class="text-primary-500 text-[10px] font-black">{score.value.toFixed(2)}</span>
                                </div>
                                <ProgressBar
                                    value={(score.value / 7) * 100}
                                    color="blue"
                                    height="h-1.5"
                                />
                            </div>
                        {/each}
                    </div>
                </Card>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-12">
                <DataTable
                    title="Respon Butir Instrumen"
                    description="Detail jawaban per butir kuesioner MSLQ"
                    items={state.result.answers || []}
                    columns={[
                        { key: 'order', label: 'Item', align: 'left' },
                        { key: 'text', label: 'Pernyataan', align: 'left' },
                        { key: 'value', label: 'Skor', align: 'center' }
                    ]}
                    itemsPerPage={10}
                    class="rounded-[3rem] border-slate-100 shadow-xl"
                >
                    {#snippet row(answer)}
                        <tr class="group transition-colors hover:bg-slate-50/50">
                            <td class="px-8 py-5">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[10px] font-black text-slate-400 group-hover:bg-primary-100 group-hover:text-primary-500 transition-colors">
                                    {answer.question.order}
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-xs font-bold text-slate-600 group-hover:text-slate-900 transition-colors leading-relaxed">
                                    {answer.question.text}
                                </p>
                                <div class="mt-2 flex gap-2">
                                    <Badge variant="outline" size="xs" class="lowercase font-medium tracking-tight text-[8px] border-slate-100 bg-slate-50">
                                        {answer.question.scale.split('_').join(' ')}
                                    </Badge>
                                    {#if answer.question.is_reverse}
                                        <Badge variant="warning" size="xs" class="text-[8px]">
                                            Reverse
                                        </Badge>
                                    {/if}
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-lg font-black text-primary-500 group-hover:text-accent-500 transition-colors">
                                    {answer.value}
                                </span>
                            </td>
                        </tr>
                    {/snippet}
                </DataTable>
            </div>
        </div>
    </div>
</App>
