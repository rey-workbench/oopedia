<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import EmptyState from "../../../components/ui/EmptyState.svelte"; // Assuming exists or fallback
    import { Link } from "@inertiajs/svelte";

    export let materialsWithStats = [];

    // Helper to calculate percentages safely
    function getPercent(correct, total) {
        return total > 0 ? Math.round((correct / total) * 100) : 0;
    }
</script>

<App title="Materi Sedang Dipelajari">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <PageHeader
                title="Materi Sedang Dipelajari"
                subtitle="Terus asah kemampuan Anda dan selesaikan tantangan yang ada."
            >
                <div slot="actions">
                    <Button
                        href="/mahasiswa/dashboard"
                        variant="ghost"
                        icon="fas fa-arrow-left"
                    >
                        Dashboard
                    </Button>
                </div>
            </PageHeader>

            <div class="mt-10">
                {#if materialsWithStats.length === 0}
                    <div
                        class="text-center py-24 bg-white rounded-[2.5rem] shadow-sm border border-slate-100"
                    >
                        <div
                            class="w-24 h-24 bg-blue-50 text-blue-500 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner"
                        >
                            <i class="fas fa-book-open text-5xl"></i>
                        </div>
                        <h3
                            class="text-3xl font-bold text-slate-900 mb-4 uppercase tracking-widest"
                        >
                            Belum Ada Progres
                        </h3>
                        <p
                            class="text-slate-500 mb-10 max-w-md mx-auto font-medium"
                        >
                            Anda belum memulai materi apapun. Pilih materi yang
                            Anda minati dan mulai petualangan belajar Anda
                            sekarang!
                        </p>
                        <Button
                            href="/mahasiswa/materials"
                            variant="primary"
                            class="px-10 py-4 rounded-2xl font-bold uppercase transition-all shadow-xl shadow-blue-100"
                            icon="fas fa-rocket"
                        >
                            Mulai Belajar
                        </Button>
                    </div>
                {:else}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        {#each materialsWithStats as materialData (materialData.material.id)}
                            <div class="group h-full">
                                <div
                                    class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 h-full flex flex-col"
                                >
                                    <!-- Header Card -->
                                    <div
                                        class="p-8 bg-gradient-to-br from-blue-600 to-indigo-700 relative overflow-hidden text-white"
                                    >
                                        <div
                                            class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity"
                                        >
                                            <i class="fas fa-running text-8xl"
                                            ></i>
                                        </div>
                                        <div
                                            class="relative z-10 flex items-center gap-6"
                                        >
                                            <div
                                                class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner border border-white/30 group-hover:scale-110 transition-transform duration-500"
                                            >
                                                <i
                                                    class="fas fa-spinner animate-spin-slow text-2xl"
                                                ></i>
                                            </div>
                                            <div
                                                class="min-h-[4.5rem] flex flex-col justify-center"
                                            >
                                                <div
                                                    class="text-[10px] font-bold uppercase tracking-widest text-blue-100 mb-1"
                                                >
                                                    Learning in Progress
                                                </div>
                                                <h4
                                                    class="text-2xl font-bold tracking-widest leading-tight"
                                                >
                                                    {materialData.material
                                                        .title}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-8 flex-1 flex flex-col">
                                        <!-- Overall Stats Header -->
                                        <div
                                            class="flex items-end justify-between mb-8"
                                        >
                                            <div>
                                                <div
                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1"
                                                >
                                                    Status Progres
                                                </div>
                                                <div
                                                    class="text-3xl font-bold text-slate-900"
                                                >
                                                    {materialData.stats.overall
                                                        .correct}
                                                    <span
                                                        class="text-sm text-slate-400 ml-2"
                                                        >/ {materialData.stats
                                                            .overall.total} SELESAI</span
                                                    >
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div
                                                    class="text-4xl font-bold text-blue-600"
                                                >
                                                    {materialData.stats.overall
                                                        .percentage}%
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-4 mb-10">
                                            <!-- Beginner -->
                                            <div
                                                class="p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-colors"
                                            >
                                                <div
                                                    class="flex justify-between items-center mb-2"
                                                >
                                                    <span
                                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                                        >Beginner Level</span
                                                    >
                                                    <span
                                                        class="text-xs font-bold text-slate-900"
                                                    >
                                                        {materialData.stats
                                                            .beginner
                                                            .correct}/{materialData
                                                            .stats.beginner
                                                            .configured_total}
                                                    </span>
                                                </div>
                                                <div
                                                    class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden"
                                                >
                                                    <div
                                                        class="h-full bg-emerald-500 rounded-full transition-all duration-1000"
                                                        style={`width: ${materialData.stats.beginner.percentage}%`}
                                                    ></div>
                                                </div>
                                            </div>

                                            <!-- Medium -->
                                            <div
                                                class="p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-colors"
                                            >
                                                <div
                                                    class="flex justify-between items-center mb-2"
                                                >
                                                    <span
                                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                                        >Medium Level</span
                                                    >
                                                    <span
                                                        class="text-xs font-bold text-slate-900"
                                                    >
                                                        {materialData.stats
                                                            .medium
                                                            .correct}/{materialData
                                                            .stats.medium
                                                            .configured_total}
                                                    </span>
                                                </div>
                                                <div
                                                    class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden"
                                                >
                                                    <div
                                                        class="h-full bg-amber-500 rounded-full transition-all duration-1000"
                                                        style={`width: ${materialData.stats.medium.percentage}%`}
                                                    ></div>
                                                </div>
                                            </div>

                                            <!-- Hard -->
                                            <div
                                                class="p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-colors"
                                            >
                                                <div
                                                    class="flex justify-between items-center mb-2"
                                                >
                                                    <span
                                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                                        >Hard Level</span
                                                    >
                                                    <span
                                                        class="text-xs font-bold text-slate-900"
                                                    >
                                                        {materialData.stats.hard
                                                            .correct}/{materialData
                                                            .stats.hard
                                                            .configured_total}
                                                    </span>
                                                </div>
                                                <div
                                                    class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden"
                                                >
                                                    <div
                                                        class="h-full bg-rose-500 rounded-full transition-all duration-1000"
                                                        style={`width: ${materialData.stats.hard.percentage}%`}
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="grid grid-cols-2 gap-4 mt-auto"
                                        >
                                            <Link
                                                href={`/mahasiswa/materials/${materialData.material.id}`}
                                                class="flex items-center justify-center gap-2 py-4 rounded-2xl font-bold uppercase text-xs border-2 border-slate-100 hover:bg-slate-50 transition-all text-slate-600"
                                            >
                                                <i class="fas fa-book"></i>
                                                Materi
                                            </Link>
                                            <Link
                                                href={`/mahasiswa/materials/${materialData.material.id}/questions/levels`}
                                                class="flex items-center justify-center gap-2 py-4 rounded-2xl font-bold uppercase text-xs bg-slate-900 text-white hover:bg-blue-600 transition-all shadow-lg shadow-slate-200 hover:shadow-blue-200"
                                            >
                                                <i class="fas fa-play"></i>
                                                Lanjut
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/each}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</App>
