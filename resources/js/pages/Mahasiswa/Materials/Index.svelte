<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Code, Puzzle, BookOpen, Ghost, ArrowRight } from 'lucide-svelte';
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
        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                Kurikulum PBO
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                Kuasai konsep fondasi hingga tingkat lanjut Pemrograman Berorientasi Objek.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-3">
            {#each state.materials as material (material.id)}
                <Card
                    padding="p-0 flex flex-col flex-1"
                    class="group flex h-full flex-col overflow-hidden"
                >
                    <!-- Image Section -->
                    <div class="relative h-60 shrink-0 overflow-hidden">
                        {#if material.media && material.media.length > 0}
                            <img
                                src={material.media[0]?.full_url}
                                alt={material.title}
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                        {:else}
                            <div
                                class="bg-primary-600 flex h-full w-full items-center justify-center"
                            >
                                <Code
                                    size={96}
                                    class="text-white/10 transition-transform group-hover:rotate-12"
                                />
                            </div>
                        {/if}
                        <div class="absolute inset-0 bg-slate-900/40"></div>

                        <div
                            class="absolute right-6 bottom-6 left-6 flex items-center justify-between"
                        >
                            <div
                                class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2 text-[10px] font-bold tracking-widest text-white uppercase backdrop-blur-md"
                            >
                                {formatDate(material.updated_at, {
                                    year: 'numeric',
                                    month: 'short',
                                })}
                            </div>
                            <div
                                class="bg-primary-600 shadow-primary-500/20 flex items-center gap-2 rounded-2xl px-4 py-2 text-[10px] font-bold tracking-widest text-white uppercase shadow-xl"
                            >
                                <Puzzle size={14} />
                                {material.total_questions} Tantangan
                            </div>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="flex flex-1 flex-col p-8">
                        <div class="mb-4 flex min-h-[4.5rem] items-start">
                            <h2
                                class="group-hover:text-primary-600 text-2xl leading-tight font-bold tracking-widest text-slate-900 uppercase transition-colors"
                            >
                                {material.title}
                            </h2>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center gap-3 text-slate-400">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-xs text-slate-500 shadow-inner"
                                >
                                    <BookOpen size={14} />
                                </div>
                                <span class="text-[10px] font-bold tracking-widest uppercase">
                                    {material.creator ? material.creator.name : 'Admin System'}
                                </span>
                            </div>
                        </div>

                        {#if state.isGuest}
                            <div
                                class="mb-8 flex min-h-[100px] items-start gap-4 rounded-3xl border border-amber-100 bg-amber-50 p-5 ring-4 ring-amber-50/50"
                            >
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200"
                                >
                                    <Ghost size={24} />
                                </div>
                                <div>
                                    <span
                                        class="mb-1 block text-[10px] font-bold tracking-widest text-amber-800 uppercase"
                                        >Mode Tamu</span
                                    >
                                    <p class="text-xs leading-relaxed font-medium text-amber-700">
                                        Akses terbatas ke materi & soal-soal pilihan.
                                    </p>
                                </div>
                            </div>
                        {:else}
                            <div class="mb-8 min-h-[100px]"></div>
                        {/if}

                        <div class="mt-auto pt-6">
                            <Button
                                href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)}
                                variant="primary"
                                class="w-full"
                                size="lg"
                                icon={ArrowRight}
                            >
                                MULAI BELAJAR
                            </Button>
                        </div>
                    </div>
                </Card>
            {/each}
        </div>
    </div>
</App>
