<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { Code, Puzzle, BookOpen, Ghost, ArrowRight, Trophy, Lock } from 'lucide-svelte';
    import { Link } from '@inertiajs/svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { MaterialCatalogState } from '@/states/Mahasiswa/MaterialState.svelte';

    import type { Material } from '@/types';

    const { materials = [] }: { materials: Material[] } = $props();

    const state = untrack(() => new MaterialCatalogState(materials));
</script>

<App title="Materi Pembelajaran">
    <div class="space-y-12">
        <PageHeader
            id="page-header"
            title="Kurikulum PBO"
            subtitle="Kuasai konsep fondasi hingga tingkat lanjut Pemrograman Berorientasi Objek."
        />

        <div id="material-exploration-grid" class="grid grid-cols-1 gap-10">
            {#each state.materials as material, i (material.id)}
                <div id={i === 0 ? 'material-item-0' : undefined}>
                    <Card
                        padding="p-0"
                        hover={!material.is_locked}
                        interactive={!material.is_locked}
                        class="group overflow-hidden {material.is_locked
                            ? 'opacity-70 grayscale'
                            : ''}"
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
                                        <Code size={96} class="text-white/10" />
                                    </div>
                                {/if}
                                <div class="absolute top-6 left-6">
                                    {#if material.is_locked}
                                        <Badge variant="warning" size="sm" class="shadow-xl"
                                            >TERKUNCI</Badge
                                        >
                                    {:else if material.is_final_project}
                                        <div
                                            class="flex items-center gap-2 rounded-2xl bg-amber-400 px-4 py-2 text-[10px] font-bold tracking-widest text-amber-950 uppercase shadow-xl ring-4 ring-amber-400/20"
                                        >
                                            <Trophy size={14} strokeWidth={3} />
                                            PROYEK AKHIR
                                        </div>
                                    {:else}
                                        <Badge variant="primary" size="sm" class="shadow-xl"
                                            >MODUL AKTIF</Badge
                                        >
                                    {/if}
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
                                                        <BookOpen size={14} />
                                                    </div>
                                                    <span
                                                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                    >
                                                        {material.creator
                                                            ? material.creator.name
                                                            : 'Admin System'}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2.5">
                                                    <div
                                                        class="bg-primary-50 text-primary-600 flex h-8 w-8 items-center justify-center rounded-xl text-xs shadow-inner"
                                                    >
                                                        <Puzzle size={14} />
                                                    </div>
                                                    <span
                                                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                    >
                                                        {material.total_questions || 0} Tantangan
                                                    </span>
                                                </div>
                                                <div class="hidden items-center gap-2.5 md:flex">
                                                    <span
                                                        class="text-[10px] font-bold tracking-widest text-slate-300 uppercase"
                                                    >
                                                        Diperbarui: {formatDate(
                                                            material.updated_at,
                                                            {
                                                                year: 'numeric',
                                                                month: 'long',
                                                                day: 'numeric',
                                                            }
                                                        )}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="{material.is_locked
                                                ? 'bg-slate-100 text-slate-400'
                                                : 'group-hover:bg-primary-600 bg-slate-50 text-slate-900 group-hover:text-white group-active:translate-y-px'} hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl shadow-inner transition-all duration-150 sm:flex"
                                        >
                                            {#if material.is_locked}
                                                <Lock size={20} />
                                            {:else}
                                                <ArrowRight size={20} />
                                            {/if}
                                        </div>
                                    </div>

                                    {#if state.isGuest}
                                        <div
                                            class="mt-8 flex items-center gap-6 rounded-[2rem] border border-amber-100 bg-amber-50 p-6 ring-8 ring-amber-50/50"
                                        >
                                            <div
                                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200"
                                            >
                                                <Ghost size={20} />
                                            </div>
                                            <div>
                                                <span
                                                    class="mb-1 block text-[10px] font-bold tracking-widest text-amber-800 uppercase"
                                                    >Mode Tamu</span
                                                >
                                                <p class="text-xs font-medium text-amber-700">
                                                    Akses terbatas ke materi & soal-soal pilihan.
                                                </p>
                                            </div>
                                        </div>
                                    {:else if material.is_locked}
                                        <div
                                            class="mt-8 flex items-center gap-6 rounded-[2rem] border border-slate-200 bg-slate-100 p-6"
                                        >
                                            <div
                                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-400 text-white shadow-lg"
                                            >
                                                <Lock size={20} />
                                            </div>
                                            <div>
                                                <span
                                                    class="mb-1 block text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                                                    >Modul Terkunci</span
                                                >
                                                <p class="text-xs font-medium text-slate-500">
                                                    Selesaikan modul sebelumnya untuk membuka modul
                                                    ini.
                                                </p>
                                            </div>
                                        </div>
                                    {/if}
                                </div>

                                {#if !material.is_locked && !state.isGuest}
                                    <div class="mt-10 md:hidden">
                                        <Button
                                            id={i === 0 ? 'btn-start-learning' : undefined}
                                            variant="primary"
                                            class="shadow-primary-900/20 w-full shadow-lg"
                                            icon={ArrowRight}
                                        >
                                            Mulai Belajar
                                        </Button>
                                    </div>
                                {/if}
                            </div>
                        {/snippet}

                        <!-- Render as link if unlocked, plain div if locked -->
                        {#if material.is_locked}
                            <div class="flex h-full flex-col md:flex-row">
                                {@render cardInner()}
                            </div>
                        {:else}
                            <Link
                                href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)}
                                class="flex h-full flex-col md:flex-row"
                            >
                                {@render cardInner()}
                            </Link>
                        {/if}
                    </Card>
                </div>
            {/each}
        </div>
    </div>
</App>
