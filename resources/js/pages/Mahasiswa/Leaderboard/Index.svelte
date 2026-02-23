<script>
    import { page } from "@inertiajs/svelte";
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Card from "@/components/ui/Card.svelte";
    import DataTable from "@/components/ui/DataTable.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import LeaderboardPodium from "./components/LeaderboardPodium.svelte";
    import { LeaderboardState } from "@/states/Mahasiswa/LeaderboardState.svelte";

    export let leaderboardData = [];

    const state = new LeaderboardState(leaderboardData);

    $: columns = [
        { key: "rank", label: "Peringkat", align: "left" },
        { key: "student", label: "Mahasiswa", align: "left" },
        { key: "category", label: "Kategori", align: "left" },
        { key: "progress", label: "Progress", align: "center" },
        { key: "score", label: "Total Skor", align: "right" },
    ];
</script>

<App title="Leaderboard">
    <div class="space-y-12">
        <PageHeader
            title="Leaderboard"
            subtitle="Peringkat Terbaik Mahasiswa Berdasarkan Progres Pembelajaran"
        />

        <div class="space-y-12">
            <Card
                padding="p-0"
                hover={false}
                class="overflow-hidden shadow-2xl"
            >
                <LeaderboardPodium top3={state.topThree} />

                <DataTable
                    title="Peringkat Menyeluruh"
                    items={state.leaderboardData}
                    {columns}
                    hideSearch={true}
                    rowClass={(item) =>
                        item.id === state.user?.id ? "bg-primary-50/50" : ""}
                >
                    <svelte:fragment slot="row" let:item={data}>
                        <td class="px-6 py-6 border-b border-slate-50">
                            {#if data.rank <= 3}
                                <div
                                    class={`w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white shadow-lg
                                    ${data.rank === 1 ? "bg-amber-400 shadow-amber-100" : data.rank === 2 ? "bg-slate-300 shadow-slate-100" : "bg-rose-400 shadow-rose-100"}`}
                                >
                                    {data.rank}
                                </div>
                            {:else}
                                <span
                                    class="w-10 text-center block font-bold text-slate-300"
                                    >#{data.rank}</span
                                >
                            {/if}
                        </td>
                        <td class="px-6 py-6 border-b border-slate-50">
                            <div class="flex items-center gap-4">
                                <UserAvatar name={data.name} />
                                <div>
                                    <div
                                        class="font-bold text-slate-900 tracking-widest uppercase"
                                    >
                                        {data.name}
                                    </div>
                                    <div
                                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"
                                    >
                                        {data.completion_date ||
                                            "Aktif Belajar"}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 border-b border-slate-50">
                            <Badge variant={data.badge_color} size="xs"
                                >{data.badge}</Badge
                            >
                        </td>
                        <td class="px-6 py-6 border-b border-slate-50">
                            <div class="w-32 mx-auto">
                                <div
                                    class="flex justify-between items-center mb-1.5 px-0.5"
                                >
                                    <span
                                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
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
                        <td
                            class="px-6 py-6 border-b border-slate-50 text-right"
                        >
                            <div
                                class="font-bold text-primary-600 text-xl tracking-widest"
                            >
                                {data.formatted_score}
                                <span
                                    class="text-[10px] text-slate-300 uppercase font-bold tracking-widest ml-1"
                                    >Pts</span
                                >
                            </div>
                        </td>
                    </svelte:fragment>
                </DataTable>
            </Card>
        </div>
    </div>
</App>
