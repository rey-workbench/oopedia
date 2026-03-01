<script lang="ts">
    import App from "@/layouts/App.svelte";
    import Card from "@/components/ui/Card.svelte";
    import DataTable from "@/components/shared/DataTable.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import LeaderboardPodium from "./components/LeaderboardPodium.svelte";
    import { LeaderboardState } from "@/states/Mahasiswa/LeaderboardState.svelte";

    const { leaderboardData = [] }: { leaderboardData: any[] } = $props();

    const state = new LeaderboardState(leaderboardData);

    const columns = [
        { key: "rank", label: "Peringkat", align: "left" },
        { key: "student", label: "Mahasiswa", align: "left" },
        { key: "category", label: "Kategori", align: "left" },
        { key: "progress", label: "Progress", align: "center" },
        { key: "score", label: "Total Skor", align: "right" },
    ];
</script>

<App title="Leaderboard">
    <div class="space-y-12">
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Leaderboard
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        Peringkat Terbaik Mahasiswa Berdasarkan Progres Pembelajaran
    </p>
</div>

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
                    {#snippet row(data)}
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
                    {/snippet}
                </DataTable>
            </Card>
        </div>
    </div>
</App>
