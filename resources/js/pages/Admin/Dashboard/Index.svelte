<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import StatCard from "../../../components/ui/StatCard.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Badge from "../../../components/ui/Badge.svelte";
    import ProgressBar from "../../../components/ui/ProgressBar.svelte";
    import Chart from "../../../components/ui/Chart.svelte";
    import { onMount } from "svelte";
    import { relativeTime } from "../../../utils/formatters";
    import {
        Users,
        Signal,
        FolderTree,
        Cpu,
        ScanEye,
        Activity,
        ArrowRight,
        Search,
        Layers,
        Check,
        Clock,
        Zap,
    } from "lucide-svelte";

    export let totalStudents;
    export let totalMaterials;
    export let totalQuestions;
    export let activeStudents;
    export let recentProgress;
    export let studentProgress;
    export let popularMaterials;
    export let studentAnalytics;

    // process studentAnalytics for charts
    let distributionOptions = {};
    let distributionSeries = [];
    let radarOptions = {};
    let radarSeries = [];

    $: {
        if (studentAnalytics && studentAnalytics.distribution) {
            const distributionData = studentAnalytics.distribution;
            distributionSeries = [
                {
                    name: "Jumlah Mahasiswa",
                    data: Object.values(distributionData),
                },
            ];
            distributionOptions = {
                chart: {
                    toolbar: { show: false },
                    fontFamily: "Poppins, sans-serif",
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        columnWidth: "60%",
                        distributed: true,
                        dataLabels: { position: "top" },
                    },
                },
                colors: [
                    "#004e98",
                    "#10b981",
                    "#f59e0b",
                    "#004e98CC",
                    "#8b5cf6",
                ],
                legend: { show: false },
                grid: {
                    borderColor: "#f1f5f9",
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } },
                },
                xaxis: {
                    categories: Object.keys(distributionData),
                    labels: {
                        style: {
                            colors: "#94a3b8",
                            fontSize: "12px",
                            fontWeight: 600,
                        },
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: "#94a3b8",
                            fontSize: "12px",
                            fontWeight: 600,
                        },
                    },
                },
            };
        }

        if (studentAnalytics && studentAnalytics.modulePerformance) {
            const modulePerformance = studentAnalytics.modulePerformance;
            radarSeries = [
                {
                    name: "Completion Rate",
                    data: modulePerformance.data,
                },
            ];
            radarOptions = {
                chart: {
                    toolbar: { show: false },
                    fontFamily: "Poppins, sans-serif",
                    dropShadow: { enabled: true, blur: 8, opacity: 0.1 },
                },
                colors: ["#004e98"],
                fill: { opacity: 0.4 },
                stroke: { show: true, width: 3, colors: ["#004e98"] },
                markers: {
                    size: 6,
                    colors: ["#fff"],
                    strokeColors: "#004e98",
                    strokeWidth: 3,
                },
                xaxis: {
                    categories: modulePerformance.labels || [],
                    labels: {
                        show: true,
                        style: {
                            colors: ["#94a3b8"],
                            fontSize: "11px",
                            fontWeight: 700,
                        },
                    },
                },
                yaxis: { show: false, max: 100 },
                plotOptions: {
                    radar: {
                        size: 140,
                        polygons: {
                            strokeColors: "#f1f5f9",
                            connectorColors: "#f1f5f9",
                            fill: { colors: ["#f8fafc", "#fff"] },
                        },
                    },
                },
            };
        }
    }
</script>

<App title="Admin Dashboard">
    <div class="space-y-12">
        <PageHeader
            title="Dashboard"
            subtitle="Pusat kendali operasional dan visualisasi data sistem OOPedia."
        />

        <!-- Main Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
                title="Total Mahasiswa"
                value={totalStudents}
                icon={Users}
                variant="primary"
                footer="Entitas terdaftar"
            />
            <StatCard
                title="Node Aktif"
                value={activeStudents}
                icon={Signal}
                variant="success"
                footer="Sesi aktif hari ini"
            />
            <StatCard
                title="Modul Instruksional"
                value={totalMaterials}
                icon={FolderTree}
                variant="primary"
                footer="Konten aktif"
            />
            <StatCard
                title="Korpus Evaluasi"
                value={totalQuestions}
                icon={Cpu}
                variant="success"
                footer="Total butir evaluasi"
            />
        </div>

        <!-- Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Progress Distribution Chart -->
            <div
                class="bg-white/50 backdrop-blur-xl rounded-2xl p-0 shadow-xl border border-slate-100 relative overflow-hidden"
            >
                <div
                    class="flex items-center justify-between w-full px-8 py-6 bg-white/80 border-b border-slate-50"
                >
                    <div class="flex items-center gap-5">
                        <div
                            class="w-12 h-12 rounded-2xl bg-primary-50 flex items-center justify-center text-primary-600 shadow-inner"
                        >
                            <ScanEye size={24} strokeWidth={2.5} />
                        </div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0"
                        >
                            Distribusi Progres Cohort
                        </p>
                    </div>
                </div>
                <div class="p-8">
                    <Chart
                        type="bar"
                        options={distributionOptions}
                        series={distributionSeries}
                        height={350}
                    />
                </div>
                <div
                    class="absolute -bottom-24 -right-24 w-80 h-80 bg-primary-50/40 rounded-full blur-[100px] -z-10"
                ></div>
            </div>

            <!-- Module Performance Radar Chart -->
            <div
                class="bg-white/50 backdrop-blur-xl rounded-2xl p-0 shadow-xl border border-slate-100 relative overflow-hidden"
            >
                <div
                    class="flex items-center justify-between w-full px-8 py-6 bg-white/80 border-b border-slate-50"
                >
                    <div class="flex items-center gap-5">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-inner"
                        >
                            <Activity size={24} strokeWidth={2.5} />
                        </div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0"
                        >
                            Balance Mastering Modul
                        </p>
                    </div>
                </div>
                <div class="p-8">
                    <Chart
                        type="radar"
                        options={radarOptions}
                        series={radarSeries}
                        height={350}
                    />
                </div>
                <div
                    class="absolute -top-24 -left-24 w-80 h-80 bg-emerald-50/40 rounded-full blur-[100px] -z-10"
                ></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Students Table -->
            <div class="lg:col-span-2">
                <Card
                    padding="p-0"
                    class="overflow-hidden border-slate-100 shadow-2xl"
                >
                    <div
                        class="flex items-center justify-between w-full px-8 py-6 border-b border-slate-50"
                    >
                        <p
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                        >
                            Matriks Performa Utama
                        </p>
                        <Button
                            variant="ghost"
                            size="sm"
                            href="/admin/students"
                            icon={ArrowRight}>DATA GLOBAL</Button
                        >
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr>
                                    <th
                                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                        >Identitas Subjek</th
                                    >
                                    <th
                                        class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                        >Jumlah Evaluasi</th
                                    >
                                    <th
                                        class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                        >Progres Sinkronisasi</th
                                    >
                                    <th
                                        class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                        >Aksi</th
                                    >
                                </tr>
                            </thead>
                            <tbody>
                                {#each studentProgress as student (student.id)}
                                    <tr
                                        class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                                    >
                                        <td class="px-6 py-4">
                                            <div
                                                class="flex items-center gap-4"
                                            >
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold shadow-lg shadow-slate-200 uppercase text-xs"
                                                >
                                                    {student.name.charAt(0)}
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-bold text-slate-900 tracking-widest leading-none mb-1"
                                                    >
                                                        {student.name}
                                                    </div>
                                                    <div
                                                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                                    >
                                                        {student.email}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="text-sm font-bold text-slate-900"
                                                >{student.completed_questions}</span
                                            >
                                        </td>
                                        <td class="px-6 py-4">
                                            <div
                                                class="flex flex-col gap-2 w-32 mx-auto"
                                            >
                                                <div
                                                    class="flex justify-between text-[8px] font-bold uppercase tracking-widest text-slate-400"
                                                >
                                                    <span>Progres</span>
                                                    <span
                                                        >{student.materials_progress}%</span
                                                    >
                                                </div>
                                                <ProgressBar
                                                    value={student.materials_progress}
                                                    size="xs"
                                                    color="bg-primary-600"
                                                />
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                href={`/admin/students/${student.id}`}
                                                icon={Search}
                                            />
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </div>

            <!-- Popular Materials -->
            <div>
                <Card
                    padding="p-0"
                    class="overflow-hidden border-slate-100 shadow-2xl h-full"
                >
                    <div class="px-8 py-6 border-b border-slate-50">
                        <p
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                        >
                            Heatmap Konten
                        </p>
                    </div>
                    <div class="space-y-4 p-6 bg-slate-50/50 h-full">
                        {#each popularMaterials as material}
                            <div
                                class="flex items-center gap-4 p-4 rounded-3xl bg-white border border-slate-100 group hover:border-primary-200 transition-all shadow-sm"
                            >
                                <div
                                    class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center shadow-lg text-white transition-transform group-hover:scale-110"
                                >
                                    <Layers size={20} strokeWidth={2.5} />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5
                                        class="text-xs font-bold tracking-widest text-slate-900 truncate mb-1"
                                    >
                                        {material.title}
                                    </h5>
                                    <p
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                    >
                                        {material.students_count} Subjek
                                    </p>
                                </div>
                                <span class="text-xs font-bold text-primary-600"
                                    >{material.completion_rate}%</span
                                >
                            </div>
                        {/each}
                    </div>
                </Card>
            </div>
        </div>

        <!-- Recent Activity Timeline -->
        <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <div class="px-8 py-6 border-b border-slate-50 bg-white">
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                >
                    Log Operasi (Langsung)
                </p>
            </div>
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 p-8 bg-white"
            >
                {#each recentProgress as progress}
                    <div
                        class="relative p-6 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white transition-colors"
                    >
                        <div class="absolute top-6 right-6">
                            <Badge
                                variant={progress.is_correct
                                    ? "success"
                                    : "warning"}
                                size="xs"
                            >
                                {progress.question?.complexity?.toUpperCase() ||
                                    "LVL"}
                            </Badge>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class={`w-8 h-8 rounded-lg ${progress.is_correct ? "bg-emerald-500" : "bg-amber-500"} text-white flex items-center justify-center text-[10px] shadow-lg shadow-emerald-500/20`}
                                >
                                    <svelte:component
                                        this={progress.is_correct
                                            ? Check
                                            : Clock}
                                        size={14}
                                        strokeWidth={3}
                                    />
                                </div>
                                <div
                                    class="font-bold text-slate-900 uppercase tracking-widest text-xs"
                                >
                                    {progress.user?.name ||
                                        "ENT-TIDAK DIKETAHUI"}
                                </div>
                            </div>

                            <p
                                class="text-[11px] font-bold text-slate-500 leading-relaxed"
                            >
                                {progress.is_correct
                                    ? "Berhasil mendekripsi"
                                    : "Menganalisis"} modul
                                <span
                                    class="text-slate-900 underline decoration-primary-200 underline-offset-4"
                                >
                                    {progress.question?.material?.title || "-"}
                                </span>
                            </p>

                            <div
                                class="pt-4 border-t border-slate-200 flex justify-between items-center text-[9px] font-bold text-slate-300 uppercase tracking-widest"
                            >
                                <span>{relativeTime(progress.created_at)}</span>
                                <Zap
                                    size={12}
                                    strokeWidth={3}
                                    class="text-primary-500 opacity-20"
                                />
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        </Card>
    </div>
</App>
