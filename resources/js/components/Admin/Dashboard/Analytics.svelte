<script>
    import Chart from "@/ui/Chart.svelte";
    import { ScanEye, Activity } from "lucide-svelte";

    export let studentAnalytics;

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
