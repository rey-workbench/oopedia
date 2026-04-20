<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import ContentDisplay from '@/components/ui/ContentDisplay.svelte';
    import { page, Link } from '@inertiajs/svelte';
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
        <PageHeader id="page-header" title={state.material?.title || material.title} />

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
                                Sistem merekomendasikan Anda untuk mengulas kembali materi ini.
                                Pilih sub-materi yang ingin dipelajari untuk memperkuat pemahaman.
                            </p>
                        </div>
                    </div>
                </Card>
            </div>
        {/if}

        <!-- Material Content Section (Optional) -->
        {#if state.material.content}
            <div id="material-content">
                <Card>
                    <div class="prose max-w-none">
                        <h3 class="mb-4 text-2xl font-bold tracking-widest text-slate-900">
                            Tentang Materi Ini
                        </h3>
                        <div class="leading-relaxed font-medium text-slate-600">
                            <ContentDisplay content={state.material.content} />
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
                <div id="sub-material-grid" class="grid grid-cols-1 gap-10">
                    {#each state.subMaterials as subMaterial (subMaterial.id)}
                        {@const SubIcon = getIcon(subMaterial.jenis_konten)}
                        <Card
                            id="sub-material-card-{subMaterial.id}"
                            padding="p-0"
                            hover={true}
                            class="group overflow-hidden"
                        >
                            {#snippet cardInner()}
                                <!-- Graphic Section -->
                                <div class="relative shrink-0 md:w-72 lg:w-96">
                                    <div
                                        class={`flex h-60 items-center justify-center md:h-full ${getBgClass(subMaterial.jenis_konten)}`}
                                    >
                                        <SubIcon
                                            size={96}
                                            class="text-white/10 transition-transform duration-500 group-hover:rotate-6"
                                        />
                                    </div>
                                    <div class="absolute top-6 left-6">
                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-slate-100 bg-white shadow-xl transition-transform"
                                        >
                                            <span
                                                class={`text-xl font-bold tracking-widest ${getTextClass(subMaterial.jenis_konten)}`}
                                            >
                                                {subMaterial.order}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="flex flex-1 flex-col justify-between p-10">
                                    <div>
                                        <div class="flex items-start justify-between gap-6">
                                            <div>
                                                <div
                                                    class={`mb-4 inline-block rounded-xl px-4 py-1.5 text-xs font-bold tracking-widest text-white uppercase shadow-sm ${getBgClass(subMaterial.jenis_konten)}`}
                                                >
                                                    {getBadgeLabel(subMaterial.jenis_konten)}
                                                </div>
                                                <h3
                                                    class="group-hover:text-primary-600 mb-4 text-3xl leading-tight font-bold tracking-widest text-slate-900 transition-colors"
                                                >
                                                    {subMaterial.title}
                                                </h3>
                                                <p
                                                    class="mb-8 line-clamp-2 text-sm leading-relaxed font-medium text-slate-500"
                                                >
                                                    {stripHtml(subMaterial.content)}
                                                </p>

                                                <div class="flex flex-wrap items-center gap-6">
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="group-hover:bg-primary-50 group-hover:text-primary-600 flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50 text-slate-400 shadow-inner transition-colors"
                                                        >
                                                            <Puzzle size={14} />
                                                        </div>
                                                        <span
                                                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                        >
                                                            {subMaterial.questions
                                                                ? subMaterial.questions.length
                                                                : 0} Soal Tersedia
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="group-hover:bg-primary-600 hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-slate-50 text-slate-900 shadow-inner transition-all duration-150 group-hover:text-white group-active:translate-y-[2px] sm:flex"
                                            >
                                                <BookOpen size={20} />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-10 md:hidden">
                                        <Button
                                            variant="primary"
                                            class="shadow-primary-900/20 w-full shadow-lg"
                                            icon={BookOpen}
                                        >
                                            Mulai Belajar
                                        </Button>
                                    </div>
                                </div>
                            {/snippet}

                            <Link
                                href={ROUTES.MAHASISWA.SUBMATERIALS.SHOW(
                                    state.material.id,
                                    subMaterial.id
                                )}
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
