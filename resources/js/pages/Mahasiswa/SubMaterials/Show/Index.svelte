<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import ContentDisplay from '@/components/ui/ContentDisplay.svelte';
    import { Link } from '@inertiajs/svelte';
    import {
        Home,
        ChevronRight,
        Puzzle,
        Clock,
        BookOpen,
        CheckCheck,
        Play,
        ArrowRight,
    } from 'lucide-svelte';
    import {
        getBgClass,
        getShadowClass,
        getSubMaterialBg,
        getSubMaterialText,
        getHoverBorderClass,
    } from '@/utils/contentTypeStyles';
    import { SubMaterialState } from '@/states/Mahasiswa/MaterialState.svelte';
    import PageHeader from '@/components/shared/PageHeader.svelte';
    import { ROUTES } from '@/utils/route';
    import { onMount, tick, untrack } from 'svelte';
    import { enhanceCodeBlocks } from '@/utils/codeBlockEnhancer';
    import type { Material, SubMaterial } from '@/types';

    const { material, subMaterial }: { material: Material; subMaterial: SubMaterial } = $props();

    const state = untrack(() => new SubMaterialState(material, subMaterial));

    let contentContainer: HTMLElement | undefined;

    onMount(async () => {
        await tick();
        if (contentContainer) enhanceCodeBlocks(contentContainer);
    });

    $effect(() => {
        if (state.subMaterial && contentContainer) {
            tick().then(() => enhanceCodeBlocks(contentContainer!));
        }
    });
</script>

<App title={state.subMaterial.title} fullWidth={true}>
    <div class="mx-auto max-w-[1600px] space-y-12 px-4 py-8 sm:px-6 lg:px-12">
        <PageHeader title={state.subMaterial.title} />
        <!-- Breadcrumb Navigation -->
        <div class="flex items-center gap-3 text-sm">
            <Link
                href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                class="hover:text-primary-600 font-bold text-slate-400 transition-colors"
            >
                <Home size={14} class="mr-1" /> Materi
            </Link>
            <ChevronRight size={12} class="text-slate-300" />
            <Link
                href={ROUTES.MAHASISWA.MATERIALS.SHOW(state.material.id)}
                class="hover:text-primary-600 font-bold text-slate-400 transition-colors"
            >
                {state.material.title}
            </Link>
            <ChevronRight size={12} class="text-slate-300" />
            <span class="font-bold text-slate-900">{state.subMaterial.title}</span>
        </div>

        <!-- Header -->

        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                {state.subMaterial.title}
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                {`Bagian ${state.subMaterial.order} dari modul ${state.material.title}.`}
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-4">
                        <div
                            class={`px-4 py-2 ${getBgClass(state.subMaterial.jenis_konten)} flex items-center justify-center rounded-2xl`}
                        >
                            <span
                                class="text-[10px] font-bold tracking-widest text-white uppercase"
                            >
                                {state.subMaterial.jenis_konten === 'sintaks' ? 'Sintaks' : 'Teori'}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 shadow-inner"
                        >
                            <Puzzle size={16} />
                        </div>
                        <span
                            class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                        >
                            {state.subMaterial.questions ? state.subMaterial.questions.length : 0} Soal
                            Latihan
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 shadow-inner"
                        >
                            <Clock size={16} />
                        </div>
                        <span
                            class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                        >
                            ~{(state.subMaterial.questions
                                ? state.subMaterial.questions.length
                                : 0) * 2} Menit
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-4">
            <div bind:this={contentContainer} class="lg:col-span-3">
                <Card class="p-10 md:p-16">
                    {#snippet header()}
                        <div class="mb-10">
                            <h2
                                class="font-display text-3xl font-extrabold tracking-tight text-slate-900"
                            >
                                Materi Pembelajaran
                            </h2>
                            <div class="mt-3 flex items-center gap-2" role="presentation">
                                <div
                                    class={`h-1.5 w-12 ${getBgClass(state.subMaterial.jenis_konten)} rounded-full shadow-sm`}
                                ></div>
                                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
                            </div>
                        </div>
                    {/snippet}

                    {#if state.subMaterial.content && state.subMaterial.content.trim()}
                        <ContentDisplay content={state.subMaterial.content} />
                    {:else}
                        <div class="py-12 text-center">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50"
                            >
                                <BookOpen size={32} class="text-slate-300" />
                            </div>
                            <p class="font-medium text-slate-400">
                                Konten materi sedang dalam pengembangan.
                            </p>
                            <p class="mt-2 text-sm text-slate-300">
                                Silakan lanjutkan ke latihan soal atau sub-materi lainnya.
                            </p>
                        </div>
                    {/if}

                    <!-- Action Footer -->
                    <div
                        class="mt-16 flex flex-col items-center justify-between gap-8 border-t border-slate-100 pt-10 md:flex-row"
                    >
                        <div class="flex items-center gap-4 text-slate-400">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50"
                            >
                                <CheckCheck size={24} />
                            </div>
                            <div>
                                <p
                                    class="text-xs font-bold tracking-widest text-slate-400 uppercase"
                                >
                                    Siap untuk latihan?
                                </p>
                                <p class="text-sm font-bold text-slate-600">
                                    {state.subMaterial.questions
                                        ? state.subMaterial.questions.length
                                        : 0} soal menanti Anda
                                </p>
                            </div>
                        </div>

                        <Button
                            href={`/mahasiswa/materials/${state.material.id}/questions?sub_material=${state.subMaterial.id}`}
                            variant="primary"
                            size="xl"
                            icon={Play}
                            class={`w-full shadow-2xl md:w-auto ${getShadowClass(state.subMaterial.jenis_konten)}`}
                        >
                            Mulai Latihan Soal
                        </Button>
                    </div>
                </Card>
            </div>

            <div>
                {#if state.otherSubMaterials.length > 0}
                    <Card>
                        <h3 class="mb-6 text-2xl font-bold text-slate-900">Sub-Materi Lainnya</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-1">
                            {#each state.otherSubMaterials as otherSub (otherSub.id)}
                                <Link
                                    href={ROUTES.MAHASISWA.SUBMATERIALS.SHOW(
                                        state.material.id,
                                        otherSub.id
                                    )}
                                    class={`group rounded-2xl border-2 border-slate-100 p-6 ${getHoverBorderClass(otherSub.jenis_konten)} transition-all hover:shadow-lg`}
                                >
                                    <div class="flex items-start gap-4">
                                        <div
                                            class={`h-12 w-12 rounded-xl ${getSubMaterialBg(otherSub.jenis_konten)} flex shrink-0 items-center justify-center transition-transform group-hover:scale-110`}
                                        >
                                            <span
                                                class={`text-lg font-bold ${getSubMaterialText(otherSub.jenis_konten)}`}
                                                >{otherSub.order}</span
                                            >
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="mb-1 font-bold text-slate-900">
                                                {otherSub.title}
                                            </h4>
                                            <p
                                                class="text-xs font-bold tracking-wider text-slate-500 uppercase"
                                            >
                                                {otherSub.jenis_konten === 'sintaks'
                                                    ? 'Sintaks'
                                                    : 'Teori'} • {otherSub.questions
                                                    ? otherSub.questions.length
                                                    : 0} Soal
                                            </p>
                                        </div>
                                        <ArrowRight
                                            size={16}
                                            class="text-slate-300 transition-all group-hover:translate-x-1"
                                        />
                                    </div>
                                </Link>
                            {/each}
                        </div>
                    </Card>
                {/if}
            </div>
        </div>
    </div>
</App>
