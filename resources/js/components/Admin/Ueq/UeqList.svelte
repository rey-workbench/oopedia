<script>
    import Card from "@/ui/Card.svelte";
    import Button from "@/ui/Button.svelte";
    import Badge from "@/ui/Badge.svelte";
    import { Eye } from "lucide-svelte";

    export let state;
</script>

<Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
    <div
        class="flex flex-col md:flex-row justify-between items-center gap-6 w-full px-8 py-6 border-b border-slate-50"
    >
        <p
            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
        >
            Log Responden Survey
        </p>
        <div class="flex items-center gap-4 w-full md:w-auto">
            <select
                on:change={(e) => state.handleFilterChange(e)}
                class="pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all appearance-none outline-none cursor-pointer"
                value={state.activeClass}
            >
                <option value="">Semua Kelas</option>
                {#each state.classes as c}
                    <option value={c}>{c}</option>
                {/each}
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest"
                        >Responden</th
                    >
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest text-center"
                        >Kelas</th
                    >
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest text-center"
                        >Tanggal Input</th
                    >
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest text-right"
                        >Aksi</th
                    >
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                {#each state.surveys as survey (survey.id)}
                    <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-6 font-bold text-slate-900">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold shadow-lg shadow-slate-200 uppercase text-xs"
                                >
                                    {survey.user?.name.charAt(0) || "U"}
                                </div>
                                <div>
                                    <div
                                        class="font-bold text-slate-900 tracking-widest uppercase text-xs"
                                    >
                                        {survey.user?.name || "User Terhapus"}
                                    </div>
                                    <div
                                        class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]"
                                    >
                                        {survey.nim || "N/A"}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <Badge variant="primary" size="xs"
                                >{survey.class || "EXT"}</Badge
                            >
                        </td>
                        <td
                            class="px-6 py-6 text-center text-xs font-bold text-slate-400"
                        >
                            {new Date(survey.created_at).toLocaleDateString(
                                "id-ID",
                                {
                                    day: "numeric",
                                    month: "short",
                                    year: "numeric",
                                },
                            )}
                        </td>
                        <td class="px-6 py-6 text-right">
                            <Button
                                variant="ghost"
                                size="sm"
                                href={`/admin/ueq-survey/${survey.user_id}`}
                                icon={Eye}
                            />
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
</Card>
