<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Card from '@/components/ui/Card.svelte';
    import { Link } from '@inertiajs/svelte';
    import {
        ArrowLeft,
        Medal,
        GraduationCap,
        CheckCircle2,
        RotateCcw,
        Trophy,
    } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { CompletedState } from '@/states/Mahasiswa/MaterialState.svelte';
    import { ROUTES } from '@/utils/route';
    import type { MaterialWithStats } from '@/types';

    const { materialsWithStats = [] }: { materialsWithStats: MaterialWithStats[] } = $props();

    const state = untrack(() => new CompletedState(materialsWithStats));
</script>

<App title="Materi Selesai">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="Materi Selesai"
            subtitle="Kumpulan modul pembelajaran yang telah berhasil Anda kuasai sepenuhnya."
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
                    class="font-black tracking-widest uppercase"
                >
                    Kembali
                </Button>
            {/snippet}
        </PageHeader>

        <div id="completed-materials-grid">
            {#if state.materialsWithStats.length === 0}
                <div
                    class="rounded-3xl border-2 border-dashed border-slate-200 bg-white/50 py-24 text-center shadow-sm backdrop-blur-sm"
                >
                    <div
                        class="bg-emerald-50 text-emerald-500 mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl shadow-inner"
                    >
                        <Medal size={48} strokeWidth={2} />
                    </div>
                    <h3 class="mb-4 text-3xl font-black tracking-widest text-slate-900 uppercase">
                        Belum Ada Koleksi
                    </h3>
                    <p class="mx-auto mb-10 max-w-md font-bold text-slate-400">
                        Selesaikan setidaknya satu modul pembelajaran untuk melihat prestasi luar
                        biasa Anda di sini.
                    </p>
                    <Button
                        href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                        variant="primary"
                        class="shadow-primary-900/10 rounded-2xl px-10 py-4 font-black tracking-widest uppercase shadow-xl transition-all"
                        icon={GraduationCap}
                    >
                        Mulai Belajar
                    </Button>
                </div>
            {:else}
                <div
                    id="completed-materials-grid"
                    class="grid grid-cols-1 gap-10"
                >
                    {#each state.materialsWithStats as { material }}
                        <Card
                            id="completed-card-{material.id}"
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
                                            class="bg-emerald-600 flex h-60 items-center justify-center md:h-full"
                                        >
                                            <Trophy
                                                size={96}
                                                class="text-white/10 transition-transform group-hover:rotate-6"
                                            />
                                        </div>
                                    {/if}
                                    <div class="absolute inset-0 bg-slate-900/20 md:hidden"></div>
                                    <div class="absolute top-6 left-6 z-20">
                                        <div
                                            class="bg-emerald-500 flex items-center gap-2 rounded-2xl px-4 py-2 text-[10px] font-black tracking-widest text-white uppercase shadow-xl ring-4 ring-emerald-500/20"
                                        >
                                            <CheckCircle2 size={14} strokeWidth={3} />
                                            DIKUASAI
                                        </div>
                                    </div>
                                    
                                    <div class="absolute right-6 bottom-6 left-6 flex items-center justify-between md:hidden">
                                        <div
                                            class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2 text-[10px] font-bold tracking-widest text-white uppercase backdrop-blur-md"
                                        >
                                            100% SELESAI
                                        </div>
                                    </div>

                                    <!-- Full Progress Bar at the bottom of image for mobile -->
                                    <div class="absolute right-0 bottom-0 left-0 h-1.5 bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] md:hidden"></div>
                                </div>

                                <!-- Content Section -->
                                <div class="flex flex-1 flex-col justify-between p-10">
                                    <div>
                                        <div class="flex items-start justify-between gap-6">
                                            <div>
                                                <h2
                                                    class="group-hover:text-emerald-600 mb-3 text-3xl leading-tight font-bold tracking-widest text-slate-900 uppercase transition-colors"
                                                >
                                                    {material.title}
                                                </h2>
                                                <div class="flex flex-wrap items-center gap-6">
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-xs text-emerald-600 shadow-inner"
                                                        >
                                                            <Trophy size={14} />
                                                        </div>
                                                        <span
                                                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                            >Lulus dengan Sempurna</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="group-hover:bg-emerald-600 bg-emerald-50 text-emerald-600 group-hover:text-white group-active:scale-95 hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl shadow-inner transition-all duration-150 sm:flex"
                                            >
                                                <RotateCcw size={20} />
                                            </div>
                                        </div>

                                        <div class="mt-8 hidden space-y-4 md:block">
                                            <div class="flex items-center justify-between px-1">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                        >PROGRESS MASTERY</span
                                                    >
                                                    <div
                                                        class="rounded-xl bg-emerald-500 px-2 py-1 text-[10px] font-bold tracking-widest text-white"
                                                    >
                                                        100%
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="text-[10px] font-bold text-slate-300 uppercase"
                                                        >LULUS</span
                                                    >
                                                    <CheckCircle2
                                                        size={14}
                                                        class="text-emerald-500"
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                class="h-2 w-full overflow-hidden rounded-full bg-slate-100"
                                            >
                                                <div class="h-full bg-emerald-500 w-full rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-10 md:hidden">
                                        <Button
                                            variant="outline"
                                            class="w-full text-emerald-600 border-emerald-200 hover:bg-emerald-50 font-black tracking-widest uppercase"
                                            icon={RotateCcw}
                                        >
                                            ULAS KEMBALI
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
