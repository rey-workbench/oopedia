<script lang="ts">
    import App from "@/layouts/App.svelte";
        import Button from "@/components/ui/Button.svelte";
    import DataTable from "@/components/shared/DataTable.svelte";
    import Pagination from "@/components/ui/Pagination.svelte";
    import StatsGrid from "@/components/shared/StatsGrid.svelte";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import { BarChart3, FileDown, Eye } from "lucide-svelte";
    import { UeqListState } from "@/states/Admin/UeqState.svelte";
    import { formatDate } from "@/utils/formatters";
    import { ROUTES } from "@/utils/route";

    let { surveys = [], averages = {}, classes = [], activeClass = "" }: { surveys: any[]; averages: Record<string, number>; classes: string[]; activeClass: string } = $props();

    const state = new UeqListState(surveys, averages, classes, activeClass);

    const columns = $derived([
        { key: "respondent", label: "Responden", align: "left" },
        { key: "class", label: "Kelas", align: "center" },
        { key: "date", label: "Tanggal Input", align: "center" },
        { key: "actions", label: "Aksi", align: "right" },
    ]);

    const statsData = $derived(
        Object.entries(state.averages).map(([dimension, score]) => ({
            title: dimension,
            value: (score as number).toFixed(2),
            icon: BarChart3,
            variant: (score as number) >= 1.5 ? "success" : (score as number) >= 0.8 ? "warning" : "danger",
            footer:
                (score as number) >= 1.5
                    ? "Sangat Baik"
                    : (score as number) >= 0.8
                      ? "Rata-rata"
                      : "Perlu Perbaikan",
        }))
    );
</script>

<App title="Hasil Survey UEQ">
    <div class="space-y-12 pb-20">
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Analitik User Experience
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        Metrik komprehensif kepuasan pengguna menggunakan kuesioner UEQ (User Experience Questionnaire).
    </p>
    <div class="mt-6 flex flex-wrap gap-4">
        <div>
                <Button
                    onclick={() => state.exportResults()}
                    variant="success"
                    icon={FileDown}>EKSPOR CSV</Button
                >
            </div>
    </div>
</div>

        <!-- Averages Overview -->
        <StatsGrid
            stats={statsData}
            gridClass="grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
        />

        <div class="flex justify-end mb-4">
            <select
                onchange={(e) => state.handleFilterChange(e)}
                class="pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all appearance-none outline-none cursor-pointer"
                value={state.activeClass}
            >
                <option value="">Semua Kelas</option>
                {#each state.classes as c}
                    <option value={c}>{c}</option>
                {/each}
            </select>
        </div>

        <DataTable
            title="Log Responden Survey"
            items={state.surveys}
            {columns}
            hideSearch={true}
        >
            {#snippet row(survey)}
                <td class="px-6 py-6 border-b border-slate-50">
                    <div class="flex items-center gap-4">
                        <UserAvatar
                            name={survey.user ? survey.user.name : "?"}
                        />
                        <div>
                            <div
                                class="font-bold text-slate-900 uppercase tracking-widest text-sm"
                            >
                                {survey.user ? survey.user.name : "Tamu"}
                            </div>
                            <div
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"
                            >
                                {survey.nim || "-"}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-6 border-b border-slate-50">
                    <span
                        class="px-3 py-1.5 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-widest rounded-xl"
                    >
                        {survey.class || "-"}
                    </span>
                </td>
                <td class="px-6 py-6 border-b border-slate-50">
                    <span class="text-xs font-medium text-slate-500">
                        {survey.created_at
                            ? formatDate(survey.created_at)
                            : "-"}
                    </span>
                </td>
                <td class="px-6 py-6 border-b border-slate-50">
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

        <Pagination links={(state.surveys as any).links || []} />
    </div>
</App>
