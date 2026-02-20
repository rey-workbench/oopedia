<script>
    import Badge from "@/components/ui/Badge.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";

    export let leaderboardData = [];
    export let authUserId;
</script>

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
                        class={`group hover:bg-slate-50 transition-all ${data.id === authUserId ? "bg-primary-50/50" : ""}`}
                    >
                        <td class="px-6 py-6 border-b border-slate-50">
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
                        <td class="px-6 py-6 border-b border-slate-50">
                            <div class="flex items-center gap-4">
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
                        <td class="px-6 py-6 border-b border-slate-50">
                            <Badge variant={data.badge_color} size="xs">
                                {data.badge}
                            </Badge>
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
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
</div>
