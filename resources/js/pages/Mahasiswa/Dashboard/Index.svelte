<script>
    import { Link } from "@inertiajs/svelte";
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import StatsGrid from "@/components/ui/StatsGrid.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import DarkHeroPanel from "@/components/ui/DarkHeroPanel.svelte";
    import {
        BookOpen,
        Brain,
        Flame,
        Trophy,
        Star,
        ClipboardList,
        Ghost,
        Code2,
        Plus,
    } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { DashboardState } from "@/states/Mahasiswa/DashboardState.svelte";

    export let totalMaterials = 0;
    export let totalQuestions = 0;
    export let hardQuestions = 0;
    export let recentActivities = [];

    const state = new DashboardState({
        totalMaterials,
        totalQuestions,
        hardQuestions,
        recentActivities,
    });

    $: dashboardStats = [
        {
            title: "Materi Tersedia",
            value: state.totalMaterials,
            icon: BookOpen,
            variant: "primary",
            footer: "Konsep PBO dari Dasar",
        },
        {
            title: "Total Soal",
            value: state.totalQuestions,
            icon: Brain,
            variant: "success",
            footer: "Latihan & Tantangan",
        },
        {
            title: "Level Hard",
            value: state.hardQuestions,
            icon: Flame,
            variant: "danger",
            footer: "Tingkat Kesulitan Tinggi",
        },
        {
            title: "Peringkat",
            value: "#12",
            icon: Trophy,
            variant: "warning",
            footer: "Peringkat global Anda",
        },
    ];
</script>

<App title="Dashboard">
    <div class="space-y-12">
        <PageHeader
            title="Dashboard"
            subtitle="Selamat datang di pusat kendali belajar Anda."
        />

        <DarkHeroPanel class="p-12 shadow-2xl shadow-slate-200">
            <div class="flex flex-col md:flex-row items-center gap-10">
                <div
                    class="w-32 h-32 bg-white rounded-[2.5rem] flex items-center justify-center shadow-2xl rotate-3"
                >
                    <img
                        src="/images/logo.png"
                        alt="Oopedia"
                        class="w-20 h-auto"
                    />
                </div>
                <div class="text-center md:text-left">
                    <p
                        class="text-[10px] font-bold uppercase tracking-widest text-primary-400 mb-3"
                    >
                        Selamat Datang Kembali
                    </p>
                    <h2
                        class="text-5xl font-bold tracking-widest mb-4 text-white uppercase"
                    >
                        {state.user?.name}
                    </h2>
                    <p class="text-slate-400 font-medium text-lg max-w-xl">
                        Lanjutkan perjalanan belajar Anda hari ini dan kuasai
                        konsep
                        <span class="text-white"
                            >Object-Oriented Programming</span
                        > dengan cara yang menyenangkan!
                    </p>
                </div>
            </div>
        </DarkHeroPanel>

        <StatsGrid
            stats={dashboardStats}
            gridClass="grid-cols-1 md:grid-cols-2 lg:grid-cols-4"
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-8">
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <h3
                            class="text-xl font-bold tracking-widest text-slate-900 uppercase"
                        >
                            Aktivitas Terbaru
                        </h3>
                        <Button variant="ghost" size="sm">Lihat Semua</Button>
                    </div>

                    <!-- Activity Feed Inline -->
                    <div class="space-y-6">
                        {#each state.recentActivities as activity}
                            <Card
                                padding="p-0"
                                class="hover:border-primary-400 transition-all border-slate-100 shadow-xl overflow-hidden group"
                            >
                                <div class="p-8 flex gap-8 items-center">
                                    <div
                                        class={`w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 border border-black/5 shadow-inner transition-colors
                                    ${activity.type === "achievement" ? "bg-emerald-50 text-emerald-500" : activity.type === "milestone" ? "bg-amber-50 text-amber-500" : "bg-primary-50 text-primary-500"}`}
                                    >
                                        <svelte:component
                                            this={activity.type ===
                                            "achievement"
                                                ? Trophy
                                                : activity.type === "milestone"
                                                  ? Star
                                                  : ClipboardList}
                                            size={24}
                                            strokeWidth={2.5}
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="flex justify-between items-start"
                                        >
                                            <div>
                                                <h4
                                                    class="font-bold text-slate-900 tracking-widest text-sm uppercase"
                                                >
                                                    {activity.type ===
                                                    "achievement"
                                                        ? "Pencapaian Baru!"
                                                        : activity.type ===
                                                            "milestone"
                                                          ? "Milestone Tercapai!"
                                                          : "Progres Belajar"}
                                                </h4>
                                                <p
                                                    class="text-slate-500 text-sm font-medium mt-1 leading-relaxed"
                                                >
                                                    {#if activity.type === "achievement"}
                                                        Menyelesaikan <span
                                                            class="text-emerald-500 font-bold"
                                                            >{activity.total_correct}
                                                            soal</span
                                                        >
                                                        di materi
                                                        <span
                                                            class="text-slate-900 font-bold uppercase"
                                                            >{activity.material_title}</span
                                                        >
                                                    {:else if activity.type === "milestone"}
                                                        Berhasil menyelesaikan
                                                        soal <span
                                                            class="text-amber-500 font-bold"
                                                            >level hard</span
                                                        >
                                                        di materi
                                                        <span
                                                            class="text-slate-900 font-bold uppercase"
                                                            >{activity.material_title}</span
                                                        >
                                                    {:else}
                                                        Mengerjakan soal <span
                                                            class="capitalize font-bold text-primary-500"
                                                            >{activity.difficulty}</span
                                                        >
                                                        di materi
                                                        <span
                                                            class="text-slate-900 font-bold uppercase"
                                                            >{activity.material_title}</span
                                                        >
                                                    {/if}
                                                </p>
                                            </div>
                                            <span
                                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                                >{activity.time_ago ||
                                                    "Baru saja"}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </Card>
                        {:else}
                            <EmptyState
                                title="Aktivitas Kosong"
                                description="Belum ada aktivitas tercatat untuk akun ini."
                                icon={Ghost}
                            />
                        {/each}
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="space-y-8">
                    <h3
                        class="text-xl font-bold tracking-widest text-slate-900 uppercase"
                    >
                        Materi Unggulan
                    </h3>

                    <div class="space-y-6">
                        <Card
                            padding="p-8"
                            class="bg-white border border-slate-100 shadow-2xl relative overflow-hidden group"
                        >
                            <div
                                class="absolute -top-10 -right-10 w-32 h-32 bg-primary-100/50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
                            ></div>
                            <div class="mb-6 text-primary-600">
                                <svelte:component
                                    this={Code2}
                                    size={32}
                                    strokeWidth={2.5}
                                />
                            </div>
                            <h4
                                class="text-lg font-bold tracking-widest mb-2 uppercase text-slate-900"
                            >
                                Dasar PBO: Class & Object
                            </h4>
                            <p
                                class="text-slate-500 text-sm font-medium mb-8 leading-relaxed"
                            >
                                Fundamental utama pemrograman berorientasi objek
                                yang harus dikuasai.
                            </p>
                            <Button
                                variant="primary"
                                size="sm"
                                class="w-full py-4 uppercase font-bold tracking-widest"
                                href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                                >PELAJARI SEKARANG</Button
                            >
                        </Card>

                        <Card
                            padding="p-8"
                            class="border-2 border-dashed border-slate-200 shadow-none hover:border-primary-400 hover:bg-primary-50/20 transition-all cursor-pointer group"
                        >
                            <Link
                                href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                                class="flex flex-col items-center text-center"
                            >
                                <div
                                    class="w-12 h-12 rounded-xl bg-slate-100/50 text-slate-500 flex items-center justify-center mb-4 group-hover:bg-primary-600 group-hover:text-white transition-all"
                                >
                                    <svelte:component
                                        this={Plus}
                                        size={24}
                                        strokeWidth={3}
                                    />
                                </div>
                                <h4
                                    class="text-[10px] font-bold text-slate-600 uppercase tracking-widest group-hover:text-primary-600"
                                >
                                    Eksplorasi Katalog Materi
                                </h4>
                                <p
                                    class="mt-2 text-[9px] font-bold text-primary-600 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    LIHAT SEMUA
                                </p>
                            </Link>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </div>
</App>
