<script lang="ts">
    import App from "@/layouts/App.svelte";
        import StatsGrid from "@/components/shared/StatsGrid.svelte";
    import Card from "@/components/ui/Card.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import { AdminDashboardState } from "@/states/Admin/DashboardState.svelte";
    import {
        Users,
        Signal,
        FolderTree,
        Cpu,
        BarChart3,
        Radar,
        Trophy,
        Activity,
    } from "lucide-svelte";
    import { formatDate } from "@/utils/formatters";

    let { totalStudents, totalMaterials, totalQuestions, activeStudents, recentProgress, studentProgress, popularMaterials, studentAnalytics }: { totalStudents: any; totalMaterials: any; totalQuestions: any; activeStudents: any; recentProgress: any; studentProgress: any; popularMaterials: any; studentAnalytics: any } = $props();

    const state = new AdminDashboardState({
        totalStudents,
        totalMaterials,
        totalQuestions,
        activeStudents,
        recentProgress,
        studentProgress,
        popularMaterials,
        studentAnalytics,
    } as any);

    const distribution = $derived(state.studentAnalytics?.distribution ?? {});
    const radar = $derived(state.studentAnalytics?.radar ?? {});
    const distributionMax = $derived(
        Math.max(
            1,
            ...Object.values(distribution).map(Number),
        )
    );
    const radarMax = $derived(Math.max(1, ...Object.values(radar).map(Number)));
    const radarColors = ["blue", "emerald", "amber", "rose", "gray"];

    const maxAttempts = $derived(
        Math.max(
            1,
            ...(state.popularMaterials || []).map((m: any) => m.total_attempts ?? 0),
        )
    );

    const dashboardStats = $derived([
        {
            title: "Total Mahasiswa",
            value: state.totalStudents,
            icon: Users,
            variant: "primary",
            footer: "Entitas terdaftar",
        },
        {
            title: "Node Aktif",
            value: state.activeStudents,
            icon: Signal,
            variant: "success",
            footer: "Sesi aktif hari ini",
        },
        {
            title: "Modul Instruksional",
            value: state.totalMaterials,
            icon: FolderTree,
            variant: "primary",
            footer: "Konten aktif",
        },
        {
            title: "Korpus Evaluasi",
            value: state.totalQuestions,
            icon: Cpu,
            variant: "success",
            footer: "Total butir evaluasi",
        },
    ]);
</script>

<App title="Admin Dashboard">
    <div class="space-y-12">
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Dashboard
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        Pusat kendali operasional dan visualisasi data sistem OOPedia.
    </p>
</div>

        <!-- Main Stats -->
        <StatsGrid stats={dashboardStats} />

        <!-- Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Distribution -->
            <Card hover={false}>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-9 h-9 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center"
                        >
                            <BarChart3 size={18} />
                        </div>
                        <h3
                            class="text-sm font-bold uppercase tracking-widest text-slate-900"
                        >
                            Distribusi Level
                        </h3>
                    </div>
                    {#if Object.keys(distribution).length > 0}
                        <div class="space-y-3">
                            {#each Object.entries(distribution) as [label, value]}
                                <div class="space-y-1">
                                    <div
                                        class="flex justify-between items-center px-0.5"
                                    >
                                        <span
                                            class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"
                                            >{label}</span
                                        >
                                        <span
                                            class="text-[10px] font-bold text-slate-700"
                                            >{value}</span
                                        >
                                    </div>
                                    <ProgressBar
                                        value={Number(value)}
                                        max={distributionMax}
                                        color="blue"
                                        height="h-2"
                                    />
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-xs text-slate-400 font-medium">
                            Data distribusi tidak tersedia.
                        </p>
                    {/if}
                </div>
            </Card>

            <!-- Radar / Kompetensi -->
            <Card hover={false}>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"
                        >
                            <Radar size={18} />
                        </div>
                        <h3
                            class="text-sm font-bold uppercase tracking-widest text-slate-900"
                        >
                            Kompetensi Materi
                        </h3>
                    </div>
                    {#if Object.keys(radar).length > 0}
                        <div class="space-y-3">
                            {#each Object.entries(radar) as [label, value], i}
                                <div class="space-y-1">
                                    <div
                                        class="flex justify-between items-center px-0.5"
                                    >
                                        <span
                                            class="text-[10px] font-bold text-slate-500 uppercase tracking-widest line-clamp-1 max-w-[70%]"
                                            >{label}</span
                                        >
                                        <span
                                            class="text-[10px] font-bold text-slate-700"
                                            >{Number(value).toFixed(1)}%</span
                                        >
                                    </div>
                                    <ProgressBar
                                        value={Number(value)}
                                        max={radarMax}
                                        color={(radarColors[
                                            i % radarColors.length
                                        ] ?? "blue") as "emerald" | "amber" | "rose" | "blue" | "gray"}
                                        height="h-2"
                                    />
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-xs text-slate-400 font-medium">
                            Data kompetensi tidak tersedia.
                        </p>
                    {/if}
                </div>
            </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Students -->
            <Card hover={false} class="lg:col-span-2">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center"
                        >
                            <Trophy size={18} />
                        </div>
                        <h3
                            class="text-sm font-bold uppercase tracking-widest text-slate-900"
                        >
                            Top Mahasiswa
                        </h3>
                    </div>

                    {#if state.studentProgress && state.studentProgress.length > 0}
                        <div class="space-y-3">
                            {#each state.studentProgress as s, i}
                                <div class="flex items-center gap-4">
                                    <span
                                        class="text-[10px] font-bold text-slate-400 w-5 text-center"
                                        >{i + 1}</span
                                    >
                                    <UserAvatar
                                        name={s.user?.name ?? "?"}
                                        size="sm"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="flex justify-between items-center mb-1"
                                        >
                                            <span
                                                class="text-xs font-bold text-slate-900 uppercase tracking-widest truncate"
                                                >{s.user?.name ?? "-"}</span
                                            >
                                            <span
                                                class="text-[10px] font-bold text-slate-400 ml-2 shrink-0"
                                                >{s.accuracy ?? 0}%</span
                                            >
                                        </div>
                                        <ProgressBar
                                            value={s.accuracy ?? 0}
                                            color="amber"
                                            height="h-1.5"
                                        />
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div
                                            class="text-xs font-bold text-slate-700"
                                        >
                                            {s.correct_count ?? 0}
                                        </div>
                                        <div
                                            class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                        >
                                            Benar
                                        </div>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-xs text-slate-400 font-medium">
                            Tidak ada data mahasiswa.
                        </p>
                    {/if}
                </div>
            </Card>

            <!-- Popular Materials -->
            <Card hover={false}>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"
                        >
                            <FolderTree size={18} />
                        </div>
                        <h3
                            class="text-sm font-bold uppercase tracking-widest text-slate-900"
                        >
                            Materi Populer
                        </h3>
                    </div>

                    {#if state.popularMaterials && state.popularMaterials.length > 0}
                        <div class="space-y-4">
                            {#each state.popularMaterials as m}
                                <div class="space-y-1.5">
                                    <div
                                        class="flex justify-between items-start"
                                    >
                                        <span
                                            class="text-xs font-bold text-slate-900 uppercase tracking-widest line-clamp-1 max-w-[70%]"
                                            >{m.title}</span
                                        >
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 ml-2 shrink-0"
                                            >{m.total_attempts ?? 0} percobaan</span
                                        >
                                    </div>
                                    <ProgressBar
                                        value={m.total_attempts ?? 0}
                                        max={maxAttempts}
                                        color="emerald"
                                        height="h-1.5"
                                    />
                                    <div
                                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                    >
                                        {m.unique_students ?? 0} mahasiswa unik
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-xs text-slate-400 font-medium">
                            Belum ada data materi.
                        </p>
                    {/if}
                </div>
            </Card>
        </div>

        <!-- Recent Activity Timeline -->
        <Card hover={false}>
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <div
                        class="w-9 h-9 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center"
                    >
                        <Activity size={18} />
                    </div>
                    <h3
                        class="text-sm font-bold uppercase tracking-widest text-slate-900"
                    >
                        Aktivitas Terbaru
                    </h3>
                </div>

                {#if state.recentProgress && state.recentProgress.length > 0}
                    <div class="space-y-4">
                        {#each state.recentProgress as item}
                            <div class="flex items-start gap-3">
                                <UserAvatar
                                    name={item.user?.name ?? "?"}
                                    size="sm"
                                />
                                <div class="flex-1 min-w-0 space-y-1.5">
                                    <div
                                        class="flex justify-between items-start"
                                    >
                                        <div>
                                            <span
                                                class="text-xs font-bold text-slate-900 uppercase tracking-widest"
                                                >{item.user?.name ?? "-"}</span
                                            >
                                            <span
                                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block"
                                                >{item.material?.title ??
                                                    "-"}</span
                                            >
                                        </div>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 ml-2 shrink-0"
                                        >
                                            {item.updated_at
                                                ? formatDate(item.updated_at)
                                                : "-"}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <ProgressBar
                                            value={item.progress ?? 0}
                                            color="blue"
                                            height="h-1.5"
                                        />
                                        <span
                                            class="text-[10px] font-bold text-slate-500 shrink-0"
                                            >{item.progress ?? 0}%</span
                                        >
                                    </div>
                                </div>
                            </div>
                        {/each}
                    </div>
                {:else}
                    <p class="text-xs text-slate-400 font-medium">
                        Belum ada aktivitas terbaru.
                    </p>
                {/if}
            </div>
        </Card>
    </div>
</App>
