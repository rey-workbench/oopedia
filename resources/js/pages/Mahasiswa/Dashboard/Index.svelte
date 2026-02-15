<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import StatCard from "../../../components/ui/StatCard.svelte";
    import { page, Link } from "@inertiajs/svelte";

    export let totalMaterials = 0;
    export let totalQuestions = 0;
    export let hardQuestions = 0;
    export let recentActivities = [];

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
        ArrowRight,
    } from "lucide-svelte";
    $: user = $page.props.auth.user;
</script>

<App title="Dashboard">
    <div class="space-y-12">
        <PageHeader
            title="Dashboard"
            subtitle="Selamat datang di pusat kendali belajar Anda."
        />

        <div
            class="relative overflow-hidden rounded-[3rem] bg-slate-900 p-12 text-white shadow-2xl shadow-slate-200"
        >
            <div
                class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/20 to-transparent"
            ></div>
            <div
                class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px]"
            ></div>

            <div
                class="relative z-10 flex flex-col md:flex-row items-center gap-10"
            >
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
                        class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-3"
                    >
                        Selamat Datang Kembali
                    </p>
                    <h2
                        class="text-5xl font-bold tracking-widest mb-4 text-white uppercase"
                    >
                        {user.name}
                    </h2>
                    <p class="text-slate-400 font-medium text-lg max-w-xl">
                        Lanjutkan perjalanan belajar Anda hari ini dan kuasai
                        konsep <span class="text-white"
                            >Object-Oriented Programming</span
                        > dengan cara yang menyenangkan!
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <StatCard
                title="Materi Tersedia"
                value={totalMaterials}
                icon={BookOpen}
                variant="primary"
                footer="Konsep PBO dari Dasar"
            />
            <StatCard
                title="Total Soal"
                value={totalQuestions}
                icon={Brain}
                variant="success"
                footer="Latihan & Tantangan"
            />
            <StatCard
                title="Level Hard"
                value={hardQuestions}
                icon={Flame}
                variant="danger"
                footer="Tingkat Kesulitan Tinggi"
            />
            <StatCard
                title="Peringkat Anda"
                value="#12"
                icon={Trophy}
                variant="warning"
                footer="Terus tingkatkan skor!"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-8">
                <div class="flex items-center justify-between">
                    <h3
                        class="text-xl font-bold tracking-widest text-slate-900 uppercase"
                    >
                        Aktivitas Terbaru
                    </h3>
                    <Button variant="ghost" size="sm">Lihat Semua</Button>
                </div>

                <div class="space-y-6">
                    {#each recentActivities as activity}
                        <Card
                            padding="p-0"
                            class="hover:border-blue-400 transition-all border-slate-100 shadow-xl overflow-hidden group"
                        >
                            <div class="p-8 flex gap-8 items-center">
                                <div
                                    class={`w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 border border-black/5 shadow-inner transition-colors
                  ${
                      activity.type === "achievement"
                          ? "bg-emerald-50 text-emerald-500"
                          : activity.type === "milestone"
                            ? "bg-amber-50 text-amber-500"
                            : "bg-blue-50 text-blue-500"
                  }`}
                                >
                                    <svelte:component
                                        this={activity.type === "achievement"
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
                                                {activity.type === "achievement"
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
                                                    Berhasil menyelesaikan soal <span
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
                                                        class="capitalize font-bold text-blue-500"
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
                                            class="text-[10px] font-bold text-slate-300 uppercase tracking-widest"
                                            >{activity.time_ago ||
                                                "Baru saja"}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </Card>
                    {:else}
                        <Card
                            class="p-20 text-center border-dashed border-slate-200 shadow-none"
                        >
                            <div
                                class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-200"
                            >
                                <svelte:component
                                    this={Ghost}
                                    size={32}
                                    strokeWidth={1.5}
                                />
                            </div>
                            <p
                                class="text-xs font-bold uppercase tracking-widest text-slate-400"
                            >
                                Belum ada aktivitas tercatat untuk akun ini.
                            </p>
                        </Card>
                    {/each}
                </div>
            </div>

            <div class="space-y-8">
                <h3
                    class="text-xl font-bold tracking-widest text-slate-900 uppercase"
                >
                    Materi Unggulan
                </h3>

                <div class="space-y-6">
                    <Card
                        padding="p-8"
                        class="bg-gradient-to-br from-blue-600 to-blue-700 text-white border-none shadow-2xl relative overflow-hidden group"
                    >
                        <div
                            class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
                        ></div>
                        <div class="mb-6 opacity-30">
                            <svelte:component
                                this={Code2}
                                size={32}
                                strokeWidth={2.5}
                            />
                        </div>
                        <h4
                            class="text-lg font-bold tracking-widest mb-2 uppercase"
                        >
                            Dasar PBO: Class & Object
                        </h4>
                        <p
                            class="text-blue-100 text-sm font-medium mb-8 leading-relaxed"
                        >
                            Fundamental utama pemrograman berorientasi objek
                            yang harus dikuasai.
                        </p>
                        <Button
                            variant="secondary"
                            size="sm"
                            class="w-full py-4 uppercase font-bold tracking-widest"
                            href="/mahasiswa/materials"
                            >PELAJARI SEKARANG</Button
                        >
                    </Card>

                    <Card
                        padding="p-8"
                        class="border-2 border-dashed border-slate-200 shadow-none hover:border-blue-400 hover:bg-blue-50/20 transition-all cursor-pointer group"
                    >
                        <Link
                            href="/mahasiswa/materials"
                            class="flex flex-col items-center text-center"
                        >
                            <div
                                class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-all"
                            >
                                <svelte:component
                                    this={Plus}
                                    size={24}
                                    strokeWidth={3}
                                />
                            </div>
                            <h4
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-blue-600"
                            >
                                Eksplorasi Katalog Materi
                            </h4>
                            <p
                                class="mt-2 text-[9px] font-bold text-blue-600 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                LIHAT SEMUA
                            </p>
                        </Link>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</App>
