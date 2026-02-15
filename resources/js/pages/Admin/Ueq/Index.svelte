<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Badge from "../../../components/ui/Badge.svelte";
    import ProgressBar from "../../../components/ui/ProgressBar.svelte";
    import { router } from "@inertiajs/svelte";
    import { FileDown, Eye } from "lucide-svelte";

    export let surveys = [];
    export let averages = {};
    export let classes = [];
    export let activeClass = "";

    function handleFilterChange(e) {
        router.get(
            "/admin/ueq-survey",
            { class: e.target.value },
            { preserveState: true, replace: true },
        );
    }

    function exportResults() {
        const url = activeClass
            ? `/admin/ueq-survey/export?class=${activeClass}`
            : "/admin/ueq-survey/export";
        window.location.href = url;
    }
</script>

<App title="Hasil Survey UEQ">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Analitik User Experience"
            subtitle="Metrik komprehensif kepuasan pengguna menggunakan kuesioner UEQ (User Experience Questionnaire)."
        >
            <div slot="actions">
                <Button
                    on:click={exportResults}
                    variant="success"
                    icon={FileDown}>EKSPOR CSV</Button
                >
            </div>
        </PageHeader>

        <!-- Averages Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {#each Object.entries(averages) as [dimension, score]}
                <Card
                    class="border-slate-100 shadow-xl hover:shadow-2xl transition-all group overflow-hidden relative"
                >
                    <div class="flex flex-col gap-4 relative z-10">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                >{dimension}</span
                            >
                            <span class="text-xl font-bold text-slate-900"
                                >{score.toFixed(2)}</span
                            >
                        </div>
                        <ProgressBar
                            value={(score / 3) * 100}
                            size="sm"
                            showPercentage={false}
                            variant={score >= 1.5
                                ? "success"
                                : score >= 0.8
                                  ? "warning"
                                  : "danger"}
                        />
                        <p
                            class="text-[9px] font-bold text-slate-400 uppercase"
                        >
                            Status: <span
                                class={score >= 1.5
                                    ? "text-emerald-500"
                                    : score >= 0.8
                                      ? "text-amber-500"
                                      : "text-rose-500"}
                            >
                                {score >= 1.5
                                    ? "Sangat Baik"
                                    : score >= 0.8
                                      ? "Rata-rata"
                                      : "Perlu Perbaikan"}
                            </span>
                        </p>
                    </div>
                    <div
                        class="absolute -right-8 -bottom-8 w-24 h-24 bg-slate-50 rounded-full group-hover:scale-150 transition-transform -z-0 opacity-50"
                    ></div>
                </Card>
            {/each}
        </div>

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
                        on:change={handleFilterChange}
                        class="pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all appearance-none outline-none cursor-pointer"
                        value={activeClass}
                    >
                        <option value="">Semua Kelas</option>
                        {#each classes as c}
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
                        {#each surveys as survey (survey.id)}
                            <tr
                                class="group hover:bg-slate-50 transition-colors"
                            >
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
                                                {survey.user?.name ||
                                                    "User Terhapus"}
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
                                    {new Date(
                                        survey.created_at,
                                    ).toLocaleDateString("id-ID", {
                                        day: "numeric",
                                        month: "short",
                                        year: "numeric",
                                    })}
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        href={`/admin/ueq/${survey.user_id}/detail`}
                                        icon={Eye}
                                    />
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </Card>
    </div>
</App>
