<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import LeaderboardPodium from './components/LeaderboardPodium.svelte';
    import { untrack } from 'svelte';
    import { LeaderboardState } from '@/states/Mahasiswa/LeaderboardState.svelte';

    import type { LeaderboardEntry } from '@/types';

    const { leaderboardData = [] }: { leaderboardData: LeaderboardEntry[] } = $props();

    const state = untrack(() => new LeaderboardState(leaderboardData));

    const columns = [
        { key: 'rank', label: 'Peringkat', align: 'left' },
        { key: 'student', label: 'Mahasiswa', align: 'left' },
        { key: 'category', label: 'Kategori', align: 'left' },
        { key: 'progress', label: 'Progress', align: 'center' },
        { key: 'score', label: 'Total Skor', align: 'right' },
    ];
</script>

<App title="Leaderboard">
    <div class="space-y-12">
        <PageHeader
            title="Leaderboard"
            subtitle="Peringkat Terbaik Mahasiswa Berdasarkan Progres Pembelajaran"
        />

        <div class="space-y-12">
            <Card padding="p-0" hover={false} class="overflow-hidden shadow-2xl">
                <LeaderboardPodium top3={state.topThree} />

                <DataTable
                    title="Peringkat Menyeluruh"
                    items={state.leaderboardData}
                    {columns}
                    hideSearch={true}
                    rowClass={(item) => (item.id === state.user?.id ? 'bg-primary-50/50' : '')}
                >
                    {#snippet row(data)}
                        <td class="border-b border-slate-50 px-6 py-6">
                            {#if data.rank <= 3}
                                <div
                                    class={`flex h-10 w-10 items-center justify-center rounded-xl font-bold text-white shadow-lg
                                    ${data.rank === 1 ? 'bg-amber-400 shadow-amber-100' : data.rank === 2 ? 'bg-slate-300 shadow-slate-100' : 'bg-rose-400 shadow-rose-100'}`}
                                >
                                    {data.rank}
                                </div>
                            {:else}
                                <span class="block w-10 text-center font-bold text-slate-300"
                                    >#{data.rank}</span
                                >
                            {/if}
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6">
                            <div class="flex items-center gap-4">
                                <UserAvatar name={data.name} />
                                <div>
                                    <div class="font-bold tracking-widest text-slate-900 uppercase">
                                        {data.name}
                                    </div>
                                    <div
                                        class="mt-0.5 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                                    >
                                        {data.completion_date || 'Aktif Belajar'}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6">
                            <Badge variant={data.badge_color} size="xs">{data.badge}</Badge>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6">
                            <div class="mx-auto w-32">
                                <div class="mb-1.5 flex items-center justify-between px-0.5">
                                    <span
                                        class="text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                                        >{data.percentage}%</span
                                    >
                                </div>
                                <ProgressBar
                                    value={data.percentage}
                                    height="h-2"
                                    color={data.badge_color}
                                />
                            </div>
                        </td>
                        <td class="border-b border-slate-50 px-6 py-6 text-right">
                            <div class="text-primary-600 text-xl font-bold tracking-widest">
                                {data.formatted_score}
                                <span
                                    class="ml-1 text-[10px] font-bold tracking-widest text-slate-300 uppercase"
                                    >Pts</span
                                >
                            </div>
                        </td>
                    {/snippet}
                </DataTable>
            </Card>
        </div>
    </div>
</App>
