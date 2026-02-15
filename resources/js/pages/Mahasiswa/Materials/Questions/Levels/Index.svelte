<script>
    import App from "../../../../../layouts/App.svelte";
    import PageHeader from "../../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../../components/ui/Card.svelte";
    import Button from "../../../../../components/ui/Button.svelte";
    import GuestBanner from "../../../../../components/ui/GuestBanner.svelte";
    import { Link } from "@inertiajs/svelte";
    import {
        ChevronLeft,
        Map,
        Bolt,
        CheckCheck,
        Lock,
        Crown,
        Play,
        Trophy,
        ArrowLeft,
        AlertCircle,
        BookOpen,
    } from "lucide-svelte";

    export let material = {};
    export let levels = [];
    export let isGuest = false;

    $: allCompleted =
        levels.length > 0 && levels.filter((l) => l.status !== "completed").length === 0;
    $: hasLevels = levels && levels.length > 0;
</script>

<App title={`Tantangan - ${material.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Peta Tantangan"
            subtitle={`Modul: ${material.title || 'Loading...'}`}
        >
            <div slot="actions">
                <Button
                    href="/mahasiswa/materials"
                    variant="ghost"
                    icon={ChevronLeft}>Daftar Materi</Button
                >
            </div>
        </PageHeader>

        <div class="max-w-6xl mx-auto space-y-12">
            <!-- Guest Banner -->
            {#if isGuest}
                <GuestBanner
                    show={isGuest}
                    variant="inline"
                    title="Mode Tamu"
                    message="Fitur peta tantangan hanya menampilkan preview. Daftar untuk akses penuh!"
                />
            {/if}

            {#if !hasLevels}
                <!-- Empty State: No Levels -->
                <Card class="p-20 text-center">
                    <div
                        class="w-20 h-20 bg-amber-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6"
                    >
                        <AlertCircle size={48} class="text-amber-400" />
                    </div>
                    <h3
                        class="text-2xl font-bold tracking-widest text-slate-900 mb-3"
                    >
                        Belum Ada Tantangan
                    </h3>
                    <p class="text-slate-500 text-base max-w-md mx-auto mb-8">
                        Materi ini belum memiliki soal latihan. Silakan kembali ke daftar materi atau baca materi terlebih dahulu.
                    </p>
                    <div class="flex justify-center gap-4">
                        <Button
                            href={`/mahasiswa/materials/${material.id}`}
                            variant="primary"
                            icon={BookOpen}
                        >
                            Baca Materi
                        </Button>
                        <Button
                            href="/mahasiswa/materials"
                            variant="outline"
                            icon={ArrowLeft}
                        >
                            Kembali ke Daftar
                        </Button>
                    </div>
                </Card>
            {:else}
                <!-- Navigation Legend -->
                <Card
                    padding="p-8"
                    class="bg-slate-900 border-slate-800 shadow-2xl relative overflow-hidden"
                >
                <div class="absolute -right-10 -top-10 opacity-10">
                    <Map size={150} class="text-white" />
                </div>
                <div
                    class="grid grid-cols-2 md:grid-cols-4 gap-8 relative z-10"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20"
                        >
                            <Bolt size={20} class="text-white fill-current" />
                        </div>
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                            >Tersedia</span
                        >
                    </div>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/20"
                        >
                            <CheckCheck size={20} class="text-white" />
                        </div>
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                            >Tuntas</span
                        >
                    </div>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center"
                        >
                            <Lock size={20} class="text-slate-600" />
                        </div>
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                            >Terkunci</span
                        >
                    </div>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/20"
                        >
                            <Crown size={20} class="text-white fill-current" />
                        </div>
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                            >Mastery</span
                        >
                    </div>
                </div>
            </Card>

            <!-- The Path -->
            <div
                class="relative py-20 overflow-hidden bg-slate-50/50 rounded-[3rem] border border-dashed border-slate-200"
            >
                <div
                    class="absolute inset-0 opacity-[0.03] pattern-grid-lg"
                ></div>

                <div class="max-w-xl mx-auto relative px-10">
                    <!-- The Wire -->
                    <div
                        class="absolute left-1/2 top-0 bottom-0 w-1 bg-slate-200 -translate-x-1/2 rounded-full hidden md:block"
                    ></div>

                    <div class="flex justify-center mb-16 relative">
                        <div
                            class="px-6 py-2 bg-slate-900 text-white rounded-full text-[10px] font-bold uppercase tracking-widest shadow-xl z-20"
                        >
                            Episode Start
                        </div>
                    </div>

                    {#each levels as level, index (level.level)}
                        <div
                            class={`flex ${index % 2 == 0 ? "justify-start" : "justify-end"} mb-20 relative`}
                        >
                            <!-- Step connector -->
                            <div
                                class={`absolute top-1/2 ${index % 2 == 0 ? "left-1/2" : "right-1/2"} h-0.5 bg-slate-200 w-16 -translate-y-1/2 hidden md:block -z-0`}
                            ></div>

                            <div class="relative z-10 group">
                                {#if level.status === "locked"}
                                    <div
                                        class="w-20 h-20 rounded-[2rem] bg-slate-100 border-4 border-white flex items-center justify-center text-slate-300 font-bold text-2xl shadow-inner"
                                    >
                                        {level.level}
                                    </div>
                                {:else if level.status === "completed"}
                                    <div class="relative">
                                        <div
                                            class="w-20 h-20 rounded-[2rem] bg-emerald-500 border-4 border-white flex items-center justify-center text-white font-bold text-2xl shadow-xl shadow-emerald-200 transition-transform group-hover:scale-110"
                                        >
                                            {level.level}
                                        </div>
                                        <div
                                            class="absolute -top-3 -right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg border-2 border-emerald-50 text-emerald-500"
                                        >
                                            <CheckCheck
                                                size={14}
                                                class="text-emerald-500"
                                            />
                                        </div>
                                    </div>
                                {:else}
                                    {#if level.question_id}
                                        <Link
                                            href={`/mahasiswa/materials/${material.id}/questions?question=${level.question_id}`}
                                            class="relative block transform hover:scale-110 transition-all duration-500 group"
                                        >
                                            <div
                                                class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-blue-600 to-indigo-700 border-4 border-white flex items-center justify-center text-white font-bold text-2xl shadow-2xl shadow-blue-500/40"
                                            >
                                                {level.level}
                                            </div>
                                            <div
                                                class="absolute inset-0 rounded-[2rem] bg-blue-500 animate-ping opacity-20 -z-10"
                                            ></div>
                                            <div
                                                class="absolute -top-3 -right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg border-2 border-blue-50 text-blue-600"
                                            >
                                                <Play
                                                    size={12}
                                                    class="ml-0.5 fill-current"
                                                />
                                            </div>
                                        </Link>
                                    {:else}
                                        <!-- Available but no question_id (shouldn't happen, but defensive) -->
                                        <div
                                            class="w-20 h-20 rounded-[2rem] bg-slate-200 border-4 border-white flex items-center justify-center text-slate-400 font-bold text-2xl shadow-inner"
                                        >
                                            {level.level}
                                        </div>
                                    {/if}
                                {/if}

                                <div
                                    class="absolute -bottom-8 left-1/2 -translate-x-1/2 whitespace-nowrap text-[9px] font-bold text-slate-400 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all"
                                >
                                    TANTANGAN {level.level}
                                </div>
                            </div>
                        </div>
                    {/each}

                    <div class="flex justify-center mt-24">
                        <div class="relative">
                            <div
                                class={`w-32 h-32 rounded-[3rem] ${allCompleted ? "bg-gradient-to-br from-amber-400 to-orange-500 shadow-2xl shadow-amber-200" : "bg-slate-100 border-4 border-white"} flex items-center justify-center transition-all duration-1000 group`}
                            >
                                <Trophy
                                    size={48}
                                    class={`${allCompleted ? "text-white animate-bounce" : "text-slate-200"}`}
                                />
                                {#if allCompleted}
                                    <div
                                        class="absolute inset-0 rounded-[3rem] bg-amber-400 animate-pulse opacity-20"
                                    ></div>
                                {/if}
                            </div>
                            <div
                                class={`absolute -bottom-10 left-1/2 -translate-x-1/2 whitespace-nowrap text-[10px] font-bold text-amber-600 uppercase tracking-[0.4em] ${allCompleted ? "opacity-100 animate-pulse" : "opacity-20"}`}
                            >
                                MASTERY ZONE
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Summary -->
            {#if hasLevels}
                <Card class="bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-100">
                    <div class="text-center py-6">
                        <div class="text-sm font-bold text-slate-600 uppercase tracking-widest mb-2">
                            Progress Keseluruhan
                        </div>
                        <div class="flex items-center justify-center gap-3">
                            <div class="text-4xl font-bold text-blue-600">
                                {levels.filter((l) => l.status === 'completed').length}
                            </div>
                            <div class="text-2xl text-slate-400 font-bold">/</div>
                            <div class="text-4xl font-bold text-slate-400">
                                {levels.length}
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 uppercase tracking-wider">
                            Tantangan Selesai
                        </p>
                        {#if allCompleted}
                            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-700 rounded-full text-xs font-bold uppercase tracking-wider">
                                <Crown size={14} class="fill-current" />
                                Mastery Achieved!
                            </div>
                        {/if}
                    </div>
                </Card>
            {/if}

            <div class="flex justify-center">
                <Button
                    href="/mahasiswa/materials"
                    variant="ghost"
                    size="lg"
                    icon={ArrowLeft}
                    class="text-slate-400"
                >
                    Kembali ke Katalog
                </Button>
            </div>
            {/if}
            <!-- End of hasLevels check -->
        </div>
    </div>
</App>
