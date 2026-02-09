<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Badge from "../../../components/ui/Badge.svelte";
    import ProgressBar from "../../../components/ui/ProgressBar.svelte";
    import { page } from "@inertiajs/svelte";

    export let leaderboardData = [];
    export let currentUserRank = null;

    // Derive top 3
    $: top3 = [null, null, null];
    $: {
        if (leaderboardData.length > 0)
            top3[0] = leaderboardData.find((d) => d.rank === 1);
        if (leaderboardData.length > 0)
            top3[1] = leaderboardData.find((d) => d.rank === 2);
        if (leaderboardData.length > 0)
            top3[2] = leaderboardData.find((d) => d.rank === 3);
    }

    $: authUserId = $page.props.auth.user.id;
</script>

<App title="Leaderboard">
    <div class="space-y-12">
        <PageHeader
            title="Leaderboard"
            subtitle="Peringkat Terbaik Mahasiswa Berdasarkan Progres Pembelajaran"
        />

        <div class="space-y-12">
            <Card padding="p-0" hover={false} class="overflow-hidden">
                <!-- Podium Section -->
                <div
                    class="bg-gradient-to-b from-slate-50 to-white pt-20 pb-12 px-8"
                >
                    <div
                        class="flex justify-center items-end gap-4 md:gap-12 max-w-5xl mx-auto"
                    >
                        <!-- Rank 2 -->
                        <div class="flex-1 flex flex-col items-center order-1">
                            {#if top3[1]}
                                <div class="text-center mb-8 group">
                                    <div class="relative mb-6">
                                        <div
                                            class="w-20 h-20 md:w-24 md:h-24 bg-slate-200 rounded-[2rem] flex items-center justify-center text-4xl shadow-inner border-4 border-white mx-auto group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500"
                                        >
                                            🥈
                                        </div>
                                        <div
                                            class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg border-2 border-slate-100 text-[10px] font-bold"
                                        >
                                            2nd
                                        </div>
                                    </div>
                                    <h5
                                        class="font-bold text-slate-800 text-lg mb-1 truncate max-w-[140px] uppercase tracking-widest"
                                    >
                                        {top3[1].name}
                                    </h5>
                                    <div
                                        class="text-blue-600 font-bold tracking-widest text-sm"
                                    >
                                        {top3[1].formatted_score} PTS
                                    </div>
                                </div>
                                <div
                                    class="w-full h-40 md:h-48 bg-gradient-to-t from-slate-300 to-slate-200 rounded-t-[2rem] shadow-inner flex items-center justify-center relative overflow-hidden"
                                >
                                    <div
                                        class="absolute inset-x-0 top-0 h-1 bg-white/20"
                                    ></div>
                                    <div
                                        class="text-5xl md:text-7xl font-bold text-white/40 tracking-widest"
                                    >
                                        2
                                    </div>
                                </div>
                            {/if}
                        </div>

                        <!-- Rank 1 -->
                        <div class="flex-1 flex flex-col items-center order-2">
                            {#if top3[0]}
                                <div
                                    class="text-center mb-8 group relative z-10"
                                >
                                    <div
                                        class="absolute -top-14 left-1/2 -translate-x-1/2"
                                    >
                                        <i
                                            class="fas fa-crown text-amber-400 text-5xl animate-bounce drop-shadow-[0_0_15px_rgba(251,191,36,0.5)]"
                                        ></i>
                                    </div>
                                    <div class="relative mb-6">
                                        <div
                                            class="w-24 h-24 md:w-32 md:h-32 bg-amber-400 rounded-[2.5rem] flex items-center justify-center text-6xl shadow-2xl border-4 border-white mx-auto group-hover:scale-110 transition-all duration-500 ring-[12px] ring-amber-400/20"
                                        >
                                            🥇
                                        </div>
                                        <div
                                            class="absolute -bottom-2 -right-2 w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-xl border-2 border-amber-50 text-xs font-bold"
                                        >
                                            1st
                                        </div>
                                    </div>
                                    <h5
                                        class="font-bold text-slate-900 text-xl md:text-2xl mb-1 truncate max-w-[180px] uppercase tracking-widest"
                                    >
                                        {top3[0].name}
                                    </h5>
                                    <div
                                        class="text-amber-600 font-bold tracking-widest text-xl"
                                    >
                                        {top3[0].formatted_score} PTS
                                    </div>
                                </div>
                                <div
                                    class="w-full h-56 md:h-64 bg-gradient-to-t from-amber-500 to-amber-400 rounded-t-[3rem] shadow-2xl shadow-amber-200/50 flex items-center justify-center relative overflow-hidden"
                                >
                                    <div
                                        class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/30 to-transparent"
                                    ></div>
                                    <div
                                        class="absolute inset-x-0 top-0 h-1.5 bg-white/30"
                                    ></div>
                                    <div
                                        class="text-8xl md:text-9xl font-bold text-white/40 tracking-widest"
                                    >
                                        1
                                    </div>
                                </div>
                            {/if}
                        </div>

                        <!-- Rank 3 -->
                        <div class="flex-1 flex flex-col items-center order-3">
                            {#if top3[2]}
                                <div class="text-center mb-8 group">
                                    <div class="relative mb-6">
                                        <div
                                            class="w-20 h-20 md:w-24 md:h-24 bg-rose-200 rounded-[2rem] flex items-center justify-center text-4xl shadow-inner border-4 border-white mx-auto group-hover:scale-110 group-hover:rotate-3 transition-all duration-500"
                                        >
                                            🥉
                                        </div>
                                        <div
                                            class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg border-2 border-rose-50 text-[10px] font-bold"
                                        >
                                            3rd
                                        </div>
                                    </div>
                                    <h5
                                        class="font-bold text-slate-800 text-lg mb-1 truncate max-w-[140px] uppercase tracking-widest"
                                    >
                                        {top3[2].name}
                                    </h5>
                                    <div
                                        class="text-blue-600 font-bold tracking-widest text-sm"
                                    >
                                        {top3[2].formatted_score} PTS
                                    </div>
                                </div>
                                <div
                                    class="w-full h-32 md:h-40 bg-gradient-to-t from-rose-400 to-rose-300 rounded-t-[2rem] shadow-inner flex items-center justify-center relative overflow-hidden"
                                >
                                    <div
                                        class="absolute inset-x-0 top-0 h-1 bg-white/20"
                                    ></div>
                                    <div
                                        class="text-4xl md:text-6xl font-bold text-white/40 tracking-widest"
                                    >
                                        3
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="bg-white">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th
                                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest"
                                        >Peringkat</th
                                    >
                                    <th
                                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest"
                                        >Mahasiswa</th
                                    >
                                    <th
                                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest"
                                        >Kategori</th
                                    >
                                    <th
                                        class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest"
                                        >Progress</th
                                    >
                                    <th
                                        class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest"
                                        >Total Skor</th
                                    >
                                </tr>
                            </thead>
                            <tbody>
                                {#each leaderboardData as data (data.id)}
                                    <tr
                                        class={`group hover:bg-slate-50 transition-all ${data.id === authUserId ? "bg-blue-50/50" : ""}`}
                                    >
                                        <td
                                            class="px-6 py-6 border-b border-slate-50"
                                        >
                                            {#if data.rank <= 3}
                                                <div
                                                    class={`w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white shadow-lg
                          ${
                              data.rank === 1
                                  ? "bg-amber-400 shadow-amber-100"
                                  : data.rank === 2
                                    ? "bg-slate-300 shadow-slate-100"
                                    : "bg-rose-400 shadow-rose-100"
                          }`}
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
                                        <td
                                            class="px-6 py-6 border-b border-slate-50"
                                        >
                                            <div
                                                class="flex items-center gap-4"
                                            >
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center uppercase font-bold text-xs shadow-lg shadow-slate-200"
                                                >
                                                    {data.name.charAt(0)}
                                                </div>
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
                                        <td
                                            class="px-6 py-6 border-b border-slate-50"
                                        >
                                            <Badge
                                                variant={data.badge_color}
                                                size="xs"
                                            >
                                                {data.badge}
                                            </Badge>
                                        </td>
                                        <td
                                            class="px-6 py-6 border-b border-slate-50"
                                        >
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
                                                class="font-bold text-blue-600 text-xl tracking-widest"
                                            >
                                                {data.formatted_score}
                                                <span
                                                    class="text-[10px] text-slate-300 uppercase font-bold tracking-widest ml-1"
                                                    >Pts</span
                                                >
                                            </div>
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</App>
