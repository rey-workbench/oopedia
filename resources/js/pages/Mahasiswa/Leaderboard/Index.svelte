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

    let { leaderboard_data = [] }: { leaderboard_data: LeaderboardEntry[] } = $props();

    const state = untrack(() => new LeaderboardState(leaderboard_data));

    const columns = [
        { key: 'rank', label: 'Peringkat', align: 'left' },
        { key: 'student', label: 'Mahasiswa', align: 'left' },
        { key: 'category', label: 'Kategori', align: 'left' },
        { key: 'progress', label: 'Progress', align: 'center' },
        { key: 'score', label: 'Total Skor', align: 'right' },
    ];
</script>

<App title="Leaderboard">
    <div class="space-y-8">
        <PageHeader
            id="page-header"
            title="Leaderboard"
            subtitle="Peringkat Terbaik Mahasiswa Berdasarkan Progres Pembelajaran"
        />

        <div class="space-y-8">
            <Card padding="p-0" hover={false} class="overflow-hidden border-b-6">
                <div id="leaderboard-podium">
                    <LeaderboardPodium top3={state.topThree} />
                </div>

                <div id="leaderboard-full-list">
                    <DataTable
                        title="Peringkat Menyeluruh"
                        items={state.leaderboard_data}
                        {columns}
                        hideSearch={true}
                        itemsPerPage={10}
                        rowClass={(item: LeaderboardEntry) =>
                            item.id === state.user?.id ? 'bg-primary-50/50' : ''}
                    >
                        {#snippet row(data: LeaderboardEntry)}
                            <td class="border-b border-slate-50 px-6 py-4">
                                {#if data.rank <= 3}
                                    <div
                                        class={`flex h-8 w-8 items-center justify-center rounded-lg border-2 border-b-4 border-slate-900 font-bold text-white text-xs
                                    ${data.rank === 1 ? 'bg-amber-400' : data.rank === 2 ? 'bg-slate-300' : 'bg-rose-400'}`}
                                    >
                                        {data.rank}
                                    </div>
                                {:else}
                                    <span class="block w-8 text-center font-bold text-slate-300 text-xs"
                                        >#{data.rank}</span
                                    >
                                {/if}
                            </td>
                            <td class="border-b border-slate-50 px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <UserAvatar name={data.name} size="sm" />
                                    <div>
                                        <div
                                            class="font-bold tracking-widest text-slate-900 uppercase text-sm"
                                        >
                                            {data.name}
                                        </div>
                                        <div
                                            class="mt-0.5 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                                        >
                                            {'completion_date' in (data as any)
                                                ? (data as any).completion_date
                                                : 'Aktif Belajar'}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="border-b border-slate-50 px-6 py-6">
                                <Badge variant={data.badge_color as any} size="xs"
                                    >{data.badge}</Badge
                                >
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
                                        value={data.percentage ?? 0}
                                        height="h-2"
                                        color={data.badge_color as any}
                                    />
                                </div>
                            </td>
                            <td class="border-b border-slate-50 px-6 py-6 text-right">
                                <div class="text-primary-600 text-lg font-bold tracking-widest">
                                    {data.formatted_score}
                                    <span
                                        class="ml-1 text-[10px] font-bold tracking-widest text-slate-300 uppercase"
                                        >Pts</span
                                    >
                                </div>
                            </td>
                        {/snippet}
                    </DataTable>
                </div>
            </Card>
        </div>
    </div>
</App>
