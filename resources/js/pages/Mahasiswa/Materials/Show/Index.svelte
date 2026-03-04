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
    import {
        getBgClass,
        getTextClass,
        getIcon,
        getBadgeLabel,
        getShadowClass,
    } from '@/utils/contentTypeStyles';
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
    const fromAdaptive = ($page.props as any)?.flash?.from_adaptive || false;
    const state = untrack(() => new MaterialShowState(material, fromAdaptive));
</script>

<App title={state.material?.title || 'Material'}>
    <div class="space-y-12">
        <PageHeader title={state.material?.title || material.title} />

        <!-- Adaptive System Alert -->
        {#if state.fromAdaptive}
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
        {/if}

        <!-- Sub-Materials Grid -->
        <div>
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
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    {#each state.subMaterials as subMaterial (subMaterial.id)}
                        {@const SubIcon = getIcon(subMaterial.jenis_konten)}
                        <Card
                            padding="p-0"
                            class="group overflow-hidden transition-all duration-300 hover:shadow-2xl"
                        >
                            <!-- Header with Icon -->
                            <div
                                class={`relative h-48 ${getBgClass(subMaterial.jenis_konten)} flex shrink-0 items-center justify-center`}
                            >
                                <div class="absolute inset-0 bg-black/10"></div>
                                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-black/20"></div>
                                <div class="relative z-10">
                                    <SubIcon
                                        size={64}
                                        class="text-white/20 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6"
                                    />
                                </div>
                                <div
                                    class="absolute top-4 left-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-lg"
                                >
                                    <span
                                        class={`text-lg font-bold ${getTextClass(subMaterial.jenis_konten)}`}
                                        >{subMaterial.order}</span
                                    >
                                </div>
                                <div
                                    class="absolute right-5 bottom-5 left-5 flex items-center justify-between"
                                >
                                    <div
                                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-1.5 text-[9px] font-bold tracking-widest text-white uppercase backdrop-blur-md"
                                    >
                                        {getBadgeLabel(subMaterial.jenis_konten)}
                                    </div>
                                    <div
                                        class={`flex items-center gap-2 px-3 py-1.5 ${getBgClass(subMaterial.jenis_konten)} rounded-xl text-[9px] font-bold tracking-widest text-white uppercase shadow-xl ${getShadowClass(subMaterial.jenis_konten)}`}
                                    >
                                        <Puzzle size={14} />
                                        {subMaterial.questions ? subMaterial.questions.length : 0} Soal
                                    </div>
                                </div>
                            </div>
                            <!-- Content -->
                            <div class="flex flex-1 flex-col p-6">
                                <div class="mb-3 flex min-h-[3.5rem] items-start">
                                    <h3
                                        class={`text-xl font-bold text-slate-900 group-hover:${getTextClass(subMaterial.jenis_konten)} line-clamp-2 transition-colors`}
                                    >
                                        {subMaterial.title}
                                    </h3>
                                </div>
                                <div class="mb-6 min-h-[4.5rem]">
                                    <p class="line-clamp-3 text-sm leading-relaxed text-slate-600">
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
                                        class="w-full"
                                        icon={BookOpen}
                                    >
                                        Lihat Materi
                                    </Button>
                                </div>
                            </div>
                        </Card>
                    {/each}
                </div>
            {/if}
        </div>

        <!-- Material Content Section (Optional) -->
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
</App>
