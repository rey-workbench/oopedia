<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import { Link } from '@inertiajs/svelte';
    import {
        ArrowLeft,
        BookOpen,
        Rocket,
        Activity,
        Play,
        Users,
    } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { InProgressState } from '@/states/Mahasiswa/MaterialState.svelte';
    import { ROUTES } from '@/utils/route';

    import type { MaterialWithStats } from '@/types';

    const { materialsWithStats = [] }: { materialsWithStats: MaterialWithStats[] } = $props();

    const state = untrack(() => new InProgressState(materialsWithStats));
</script>

<App title="Materi Sedang Dipelajari">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="Materi Progres"
            subtitle="Terus asah kemampuan Anda dan selesaikan tantangan yang ada."
            breadcrumbs={[
                { label: 'DASHBOARD', href: ROUTES.MAHASISWA.DASHBOARD },
                { label: 'PROGRES BELAJAR' },
            ]}
        >
            {#snippet actions()}
                <Button
                    href={ROUTES.MAHASISWA.DASHBOARD}
                    variant="ghost"
                    icon={ArrowLeft}
                    class="font-black tracking-widest"
                >
                    KEMBALI
                </Button>
            {/snippet}
        </PageHeader>

        <div id="inprogress-materials-grid">
            {#if state.materialsWithStats.length === 0}
                <div
                    class="rounded-3xl border-2 border-dashed border-slate-200 bg-white/50 py-24 text-center shadow-sm backdrop-blur-sm"
                >
                    <div
                        class="bg-primary-50 text-primary-500 mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl shadow-inner"
                    >
                        <BookOpen size={48} strokeWidth={2} />
                    </div>
                    <h3 class="mb-4 text-3xl font-black tracking-widest text-slate-900 uppercase">
                        Belum Ada Progres
                    </h3>
                    <p class="mx-auto mb-10 max-w-md font-bold text-slate-400">
                        Anda belum memulai materi apapun. Pilih materi yang Anda minati dan mulai
                        petualangan belajar Anda sekarang!
                    </p>
                    <Button
                        href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                        variant="primary"
                        class="shadow-primary-900/10 rounded-2xl px-10 py-4 font-black tracking-widest uppercase shadow-xl transition-all"
                        icon={Rocket}
                    >
                        Mulai Belajar
                    </Button>
                </div>
            {:else}
                <div
                    id="inprogress-materials-grid"
                    class="grid grid-cols-1 gap-10"
                >
                    {#each state.materialsWithStats as materialData (materialData.material.id)}
                        {@const { material, stats } = materialData}
                        <Card
                            id="inprogress-card-{material.id}"
                            padding="p-0"
                            hover
                            class="group overflow-hidden"
                        >
                            {#snippet cardInner()}
                                <!-- Graphic Section -->
                                <div class="relative shrink-0 md:w-72 lg:w-96">
                                    {#if material.media && material.media.length > 0}
                                        <div class="h-60 md:h-full">
                                            <img
                                                src={material.media[0]?.full_url}
                                                alt={material.title}
                                                class="h-full w-full object-cover"
                                            />
                                            <div
                                                class="absolute inset-0 bg-slate-900/10 transition-colors group-hover:bg-transparent"
                                            ></div>
                                        </div>
                                    {:else}
                                        <div
                                            class="bg-primary-600 flex h-60 items-center justify-center md:h-full"
                                        >
                                            <Activity
                                                size={96}
                                                class="text-white/10 transition-transform group-hover:rotate-6"
                                            />
                                        </div>
                                    {/if}
                                    <div class="absolute top-6 left-6">
                                        <Badge variant="primary" size="sm" class="shadow-xl"
                                            >MODUL AKTIF</Badge
                                        >
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="flex flex-1 flex-col justify-between p-10">
                                    <div>
                                        <div class="flex items-start justify-between gap-6">
                                            <div>
                                                <h2
                                                    class="group-hover:text-primary-600 mb-3 text-3xl leading-tight font-bold tracking-widest text-slate-900 transition-colors"
                                                >
                                                    {material.title}
                                                </h2>
                                                <div class="flex flex-wrap items-center gap-6">
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="bg-primary-50 text-primary-600 flex h-8 w-8 items-center justify-center rounded-xl text-xs shadow-inner"
                                                        >
                                                            <Users size={14} />
                                                        </div>
                                                        <span
                                                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                            >{material.student_count || 0} Mahasiswa</span
                                                        >
                                                    </div>
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="bg-primary-50 text-primary-600 flex h-8 w-8 items-center justify-center rounded-xl text-xs shadow-inner"
                                                        >
                                                            <Activity size={14} />
                                                        </div>
                                                        <span
                                                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                            >{stats.overall.total} Unit Belajar</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="group-hover:bg-primary-600 bg-slate-50 text-slate-900 group-hover:text-white group-active:translate-y-[2px] hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl shadow-inner transition-all duration-150 sm:flex"
                                            >
                                                <Play size={20} class="ml-1 fill-current" />
                                            </div>
                                        </div>

                                        <div class="mt-8 space-y-4">
                                            <div class="flex items-center justify-between px-1">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                        >PROGRESS MASTERY</span
                                                    >
                                                    <Badge variant="success" size="xs"
                                                        >{stats.overall.percentage}%</Badge
                                                    >
                                                </div>
                                                <span
                                                    class="text-[10px] font-bold text-slate-300 uppercase"
                                                    >{stats.overall.correct}/{stats.overall.total} UNIT</span
                                                >
                                            </div>
                                            <ProgressBar
                                                value={stats.overall.percentage}
                                                height="h-2"
                                                color="emerald"
                                            />
                                        </div>
                                    </div>

                                    <div class="mt-10 md:hidden">
                                        <Button
                                            variant="primary"
                                            class="shadow-primary-900/20 w-full shadow-lg"
                                            icon={Play}
                                        >
                                            Lanjutkan
                                        </Button>
                                    </div>
                                </div>
                            {/snippet}

                            <Link
                                href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)}
                                class="flex h-full flex-col md:flex-row"
                            >
                                {@render cardInner()}
                            </Link>
                        </Card>
                    {/each}
                </div>
            {/if}
        </div>
    </div>
</App>
