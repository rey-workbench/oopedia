<script>
    import { Link } from "@inertiajs/svelte";
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Card from "@/components/ui/Card.svelte";
    import {
        ArrowLeft,
        BookOpen,
        Rocket,
        Activity,
        Loader2,
        Book,
        Play,
    } from "lucide-svelte";
    import { InProgressState } from "@/states/Mahasiswa/MaterialState.svelte";
    import { ROUTES } from "@/utils/route";

    export let materialsWithStats = [];

    const state = new InProgressState(materialsWithStats);
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
                        href={ROUTES.MAHASISWA.DASHBOARD}
                        variant="ghost"
                        icon={ArrowLeft}
                    >
                        Dashboard
                    </Button>
                </div>
            </PageHeader>

            <div class="mt-10">
                {#if state.materialsWithStats.length === 0}
                    <div
                        class="text-center py-24 bg-white rounded-[2.5rem] shadow-sm border border-slate-100"
                    >
                        <div
                            class="w-24 h-24 bg-primary-50 text-primary-500 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner"
                        >
                            <BookOpen size={48} strokeWidth={2} />
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
                            href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                            variant="primary"
                            class="px-10 py-4 rounded-2xl font-bold uppercase transition-all shadow-xl shadow-primary-900/10"
                            icon={Rocket}
                        >
                            Mulai Belajar
                        </Button>
                    </div>
                {:else}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        {#each state.materialsWithStats as materialData (materialData.material.id)}
                            <Link
                                href={ROUTES.MAHASISWA.MATERIALS.SHOW(
                                    materialData.material.id,
                                )}
                                class="group h-full block"
                            >
                                <Card
                                    padding="p-0"
                                    class="overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 h-full flex flex-col"
                                >
                                    <div
                                        slot="header"
                                        class="p-8 bg-primary-600 relative overflow-hidden text-white border-0 rounded-0"
                                    >
                                        <Activity
                                            size={96}
                                            strokeWidth={1}
                                            class="absolute -right-8 -top-8 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-700"
                                        />
                                        <div
                                            class="relative z-10 flex items-center gap-6"
                                        >
                                            <div
                                                class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner border border-white/30 group-hover:scale-110 transition-transform duration-500"
                                            >
                                                <Loader2
                                                    size={32}
                                                    strokeWidth={2.5}
                                                    class="animate-spin"
                                                />
                                            </div>
                                            <div
                                                class="min-h-[4.5rem] flex flex-col justify-center"
                                            >
                                                <div
                                                    class="text-[10px] font-bold uppercase tracking-widest text-primary-100 mb-1"
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
                                                    >
                                                        / {materialData.stats
                                                            .overall.total} SELESAI
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div
                                                    class="text-4xl font-bold text-primary-600"
                                                >
                                                    {materialData.stats.overall
                                                        .percentage}%
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-4 mb-10">
                                            {#each [{ label: "Beginner Level", stats: materialData.stats.beginner, color: "bg-emerald-500" }, { label: "Medium Level", stats: materialData.stats.medium, color: "bg-amber-500" }, { label: "Hard Level", stats: materialData.stats.hard, color: "bg-rose-500" }] as diff}
                                                <div
                                                    class="p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-colors"
                                                >
                                                    <div
                                                        class="flex justify-between items-center mb-2"
                                                    >
                                                        <span
                                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                                        >
                                                            {diff.label}
                                                        </span>
                                                        <span
                                                            class="text-xs font-bold text-slate-900"
                                                        >
                                                            {diff.stats
                                                                .correct}/{diff
                                                                .stats
                                                                .configured_total}
                                                        </span>
                                                    </div>
                                                    <div
                                                        class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden"
                                                    >
                                                        <div
                                                            class="h-full {diff.color} rounded-full transition-all duration-1000"
                                                            style={`width: ${diff.stats.percentage}%`}
                                                        ></div>
                                                    </div>
                                                </div>
                                            {/each}
                                        </div>

                                        <div
                                            class="grid grid-cols-2 gap-4 mt-auto"
                                        >
                                            <div
                                                class="flex items-center justify-center gap-2 py-4 rounded-2xl font-bold uppercase text-xs border-2 border-slate-100 hover:border-primary-600 transition-all text-slate-600"
                                            >
                                                <Book
                                                    size={14}
                                                    strokeWidth={2.5}
                                                /> Materi
                                            </div>
                                            <Link
                                                href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.LEVELS(
                                                    materialData.material.id,
                                                )}
                                                class="flex items-center justify-center gap-2 py-4 rounded-2xl font-bold uppercase text-xs bg-slate-900 text-white hover:bg-primary-600 transition-all shadow-lg shadow-slate-200 hover:shadow-primary-900/20"
                                            >
                                                <Play
                                                    size={14}
                                                    strokeWidth={2.5}
                                                /> Lanjut
                                            </Link>
                                        </div>
                                    </div>
                                </Card>
                            </Link>
                        {/each}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</App>
