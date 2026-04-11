<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import { ArrowLeft, BookOpen, Rocket, Activity, Loader2, Book, Play } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { InProgressState } from '@/states/Mahasiswa/MaterialState.svelte';
    import { ROUTES } from '@/utils/route';

    import type { MaterialWithStats } from '@/types';

    const { materialsWithStats = [] }: { materialsWithStats: MaterialWithStats[] } = $props();

    const state = untrack(() => new InProgressState(materialsWithStats));
</script>

<App title="Materi Sedang Dipelajari">
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div id="inprogress-header" class="mb-8">
                <h1
                    class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
                >
                    Materi Sedang Dipelajari
                </h1>
                <div class="mt-3 flex items-center gap-2" role="presentation">
                    <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                    <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                    <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
                </div>
                <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                    Terus asah kemampuan Anda dan selesaikan tantangan yang ada.
                </p>
                <div class="mt-6 flex flex-wrap gap-4">
                    <div>
                        <Button href={ROUTES.MAHASISWA.DASHBOARD} variant="ghost" icon={ArrowLeft}>
                            Dashboard
                        </Button>
                    </div>
                </div>
            </div>

            <div id="inprogress-materials-grid" class="mt-10">
                {#if state.materialsWithStats.length === 0}
                    <div
                        class="rounded-[2.5rem] border border-slate-100 bg-white py-24 text-center shadow-sm"
                    >
                        <div
                            class="bg-primary-50 text-primary-500 mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl shadow-inner"
                        >
                            <BookOpen size={48} strokeWidth={2} />
                        </div>
                        <h3
                            class="mb-4 text-3xl font-bold tracking-widest text-slate-900 uppercase"
                        >
                            Belum Ada Progres
                        </h3>
                        <p class="mx-auto mb-10 max-w-md font-medium text-slate-500">
                            Anda belum memulai materi apapun. Pilih materi yang Anda minati dan
                            mulai petualangan belajar Anda sekarang!
                        </p>
                        <Button
                            href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                            variant="primary"
                            class="shadow-primary-900/10 rounded-2xl px-10 py-4 font-bold uppercase shadow-xl transition-all"
                            icon={Rocket}
                        >
                            Mulai Belajar
                        </Button>
                    </div>
                {:else}
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
                        {#each state.materialsWithStats as materialData (materialData.material.id)}
                            <Link
                                href={ROUTES.MAHASISWA.MATERIALS.SHOW(materialData.material.id)}
                                class="group block h-full"
                            >
                                <Card
                                    padding="p-0"
                                    class="flex h-full flex-col overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl"
                                >
                                    {#snippet header()}
                                        <div
                                            class="bg-primary-600 rounded-0 relative overflow-hidden border-0 p-8 text-white"
                                        >
                                            <Activity
                                                size={96}
                                                strokeWidth={1}
                                                class="absolute -top-8 -right-8 opacity-10 transition-all duration-700 group-hover:scale-110 group-hover:opacity-20"
                                            />
                                            <div class="relative z-10 flex items-center gap-6">
                                                <div
                                                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/30 bg-white/20 shadow-inner backdrop-blur-md transition-transform duration-500 group-hover:scale-110"
                                                >
                                                    <Loader2
                                                        size={32}
                                                        strokeWidth={2.5}
                                                        class="animate-spin"
                                                    />
                                                </div>
                                                <div
                                                    class="flex min-h-[4.5rem] flex-col justify-center"
                                                >
                                                    <div
                                                        class="text-primary-100 mb-1 text-[10px] font-bold tracking-widest uppercase"
                                                    >
                                                        Learning in Progress
                                                    </div>
                                                    <h4
                                                        class="text-2xl leading-tight font-bold tracking-widest"
                                                    >
                                                        {materialData.material.title}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    {/snippet}

                                    <div class="flex flex-1 flex-col p-8">
                                        <div class="mb-8 flex items-end justify-between">
                                            <div>
                                                <div
                                                    class="mb-1 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                >
                                                    Status Progres
                                                </div>
                                                <div class="text-3xl font-bold text-slate-900">
                                                    {materialData.stats.overall.correct}
                                                    <span class="ml-2 text-sm text-slate-400">
                                                        / {materialData.stats.overall.total} SELESAI
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-primary-600 text-4xl font-bold">
                                                    {materialData.stats.overall.percentage}%
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-10 space-y-4">
                                            {#each [{ label: 'Beginner Level', stats: materialData.stats.beginner, color: 'bg-emerald-500' }, { label: 'Medium Level', stats: materialData.stats.medium, color: 'bg-amber-500' }, { label: 'Hard Level', stats: materialData.stats.hard, color: 'bg-rose-500' }] as diff}
                                                <div
                                                    class="rounded-2xl border border-slate-100 bg-slate-50 p-4 transition-colors"
                                                >
                                                    <div
                                                        class="mb-2 flex items-center justify-between"
                                                    >
                                                        <span
                                                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                        >
                                                            {diff.label}
                                                        </span>
                                                        <span
                                                            class="text-xs font-bold text-slate-900"
                                                        >
                                                            {diff.stats.correct}/{diff.stats
                                                                .configured_total}
                                                        </span>
                                                    </div>
                                                    <div
                                                        class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200"
                                                    >
                                                        <div
                                                            class="h-full {diff.color} rounded-full transition-all duration-1000"
                                                            style={`width: ${diff.stats.percentage}%`}
                                                        ></div>
                                                    </div>
                                                </div>
                                            {/each}
                                        </div>

                                        <div class="mt-auto grid grid-cols-2 gap-4">
                                            <div
                                                class="hover:border-primary-600 flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-100 py-4 text-xs font-bold text-slate-600 uppercase transition-all"
                                            >
                                                <Book size={14} strokeWidth={2.5} /> Materi
                                            </div>
                                            <Link
                                                href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.LEVELS(
                                                    materialData.material.id
                                                )}
                                                class="hover:bg-primary-600 hover:shadow-primary-900/20 flex items-center justify-center gap-2 rounded-2xl bg-slate-900 py-4 text-xs font-bold text-white uppercase shadow-lg shadow-slate-200 transition-all"
                                            >
                                                <Play size={14} strokeWidth={2.5} /> Lanjut
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
