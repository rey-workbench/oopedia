<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import ContentDisplay from '@/components/ui/ContentDisplay.svelte';
    import { page } from '@inertiajs/svelte';
    import { ArrowLeft, BookOpen, Info, Puzzle } from 'lucide-svelte';
    import { onMount, tick, untrack } from 'svelte';
    import { enhanceCodeBlocks } from '@/utils/codeBlockEnhancer';
    import { MaterialShowState } from '@/states/Mahasiswa/MaterialState.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { ROUTES } from '@/utils/route';
    import { getBgClass, getTextClass, getIcon, getBadgeLabel } from '@/utils/contentTypeStyles';
    import type { Material } from '@/types';

    const { material }: { material: Material } = $props();

    let contentContainer: HTMLElement | undefined;

    const stripHtml = (html: string | undefined) => {
        if (!html) return '';
        const doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent || '';
    };

    onMount(async () => {
        await tick();
        if (contentContainer) enhanceCodeBlocks(contentContainer);
    });

    $effect(() => {
        if (material && contentContainer) {
            tick().then(() => enhanceCodeBlocks(contentContainer!));
        }
    });

    // Initialize State
    const fromAdaptive = (page.props as any)?.flash?.from_adaptive || false;
    const state = untrack(() => new MaterialShowState(material, fromAdaptive));
</script>

<App title={state.material?.title || 'Material'}>
    <div class="space-y-12">
        <div id="material-header">
            <PageHeader title={state.material?.title || material.title} />
        </div>

        <!-- Adaptive System Alert -->
        {#if state.fromAdaptive}
            <div id="adaptive-recommendation">
                <Card class="border-primary-500 bg-primary-50 border-l-4">
                <div class="flex items-start gap-4">
                    <div
                        class="bg-primary-100 flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                    >
                        <Info size={24} class="text-primary-600" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-primary-900 mb-1 text-lg font-bold">
                            Rekomendasi Sistem Adaptif
                        </h3>
                        <p class="text-primary-700 text-sm leading-relaxed">
                            Sistem merekomendasikan Anda untuk mengulas kembali materi ini. Pilih
                            sub-materi yang ingin dipelajari untuk memperkuat pemahaman.
                        </p>
                    </div>
                </div>
            </Card>
            </div>
        {/if}

        <!-- Sub-Materials Grid -->
        <div id="sub-material-section">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="mb-2 text-3xl font-bold tracking-widest text-slate-900">
                        Daftar Sub-Materi
                    </h2>
                    <p class="font-medium text-slate-500">
                        Pilih sub-materi untuk memulai pembelajaran
                    </p>
                </div>
            </div>

            {#if state.subMaterials.length === 0}
                <Card class="p-20 text-center">
                    <div
                        class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-[2rem] bg-slate-50"
                    >
                        <BookOpen size={48} class="text-slate-200" />
                    </div>
                    <h3 class="mb-2 text-xl font-bold tracking-widest text-slate-900">
                        Belum Ada Sub-Materi
                    </h3>
                    <p class="mx-auto mb-6 max-w-xs text-sm text-slate-400">
                        Sub-materi untuk topik ini sedang dalam pengembangan.
                    </p>
                    <Button
                        href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                        variant="outline"
                        icon={ArrowLeft}
                        class="mx-auto"
                    >
                        Kembali ke Daftar Materi
                    </Button>
                </Card>
            {:else}
                <div id="sub-material-grid" class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    {#each state.subMaterials as subMaterial (subMaterial.id)}
                        {@const SubIcon = getIcon(subMaterial.jenis_konten)}
                        <Card
                            padding="p-0"
                            class="border-duo-lg overflow-hidden rounded-3xl bg-white"
                        >
                            <!-- Header with Icon -->
                            <div
                                class={`relative h-44 ${getBgClass(subMaterial.jenis_konten)} flex shrink-0 items-center justify-center`}
                            >
                                <div
                                    class="absolute inset-0 bg-black/5 opacity-0 transition-opacity group-hover:opacity-100"
                                ></div>
                                <div
                                    class="relative z-10 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3"
                                >
                                    <SubIcon size={56} class="text-white drop-shadow-lg" />
                                </div>

                                <!-- Floating Index Badge -->
                                <div
                                    class="absolute top-4 left-4 flex h-10 w-10 items-center justify-center rounded-2xl border-2 border-slate-100 bg-white shadow-lg"
                                >
                                    <span
                                        class={`text-lg font-black tracking-tight ${getTextClass(subMaterial.jenis_konten)}`}
                                        >{subMaterial.order}</span
                                    >
                                </div>

                                <!-- Floating Status/Questions -->
                                <div
                                    class="absolute right-4 bottom-4 flex items-center gap-2 rounded-2xl border-2 border-white/30 bg-white/20 px-3 py-1.5 backdrop-blur-md"
                                >
                                    <Puzzle size={14} class="text-white" />
                                    <span
                                        class="text-[10px] font-black tracking-widest text-white uppercase"
                                    >
                                        {subMaterial.questions ? subMaterial.questions.length : 0} Soal
                                    </span>
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="flex flex-1 flex-col p-6">
                                <div class="mb-4">
                                    <div
                                        class={`mb-3 inline-block rounded-full px-3 py-1 text-[9px] font-black tracking-[0.15em] text-white uppercase ${getBgClass(subMaterial.jenis_konten)} shadow-sm`}
                                    >
                                        {getBadgeLabel(subMaterial.jenis_konten)}
                                    </div>
                                    <h3
                                        class="line-clamp-2 text-xl leading-tight font-black tracking-tight text-slate-900 transition-colors"
                                    >
                                        {subMaterial.title}
                                    </h3>
                                </div>

                                <div class="mb-6 flex-1">
                                    <p
                                        class="line-clamp-2 text-sm leading-relaxed font-medium text-slate-500"
                                    >
                                        {stripHtml(subMaterial.content)}
                                    </p>
                                </div>

                                <div class="mt-auto">
                                    <Button
                                        href={ROUTES.MAHASISWA.SUBMATERIALS.SHOW(
                                            state.material.id,
                                            subMaterial.id
                                        )}
                                        variant="primary"
                                        size="md"
                                        class="w-full"
                                        icon={BookOpen}
                                    >
                                        Mulai Belajar
                                    </Button>
                                </div>
                            </div>
                        </Card>
                    {/each}
                </div>
            {/if}
        </div>

        <!-- Material Content Section (Optional) -->
        <div id="material-content">
            {#if state.material.content}
                <Card>
                    <div class="prose max-w-none">
                        <h3 class="mb-4 text-2xl font-bold text-slate-900">Tentang Materi Ini</h3>
                        <div class="leading-relaxed">
                            <ContentDisplay content={state.material.content} />
                        </div>
                    </div>
                </Card>
            {/if}
        </div>
    </div>
</App>
