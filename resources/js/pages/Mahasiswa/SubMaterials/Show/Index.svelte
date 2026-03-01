<script lang="ts">
    import App from "@/layouts/App.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import ContentDisplay from "@/components/ui/ContentDisplay.svelte";
    import { Link } from "@inertiajs/svelte";
    import {
        Home,
        ChevronRight,
        Puzzle,
        Clock,
        BookOpen,
        CheckCheck,
        Play,
        ArrowRight,
    } from "lucide-svelte";
    import {
        getBgClass,
        getShadowClass,
        getSubMaterialBg,
        getSubMaterialText,
        getHoverBorderClass,
    } from "@/utils/contentTypeStyles";
    import { SubMaterialState } from "@/states/Mahasiswa/MaterialState.svelte";
    import { ROUTES } from "@/utils/route";
    import { onMount, tick } from "svelte";
    import { enhanceCodeBlocks } from "@/utils/codeBlockEnhancer";
    import type { Material } from "@/types";

    const { material, subMaterial }: { material: Material; subMaterial: any } = $props();

    const state = new SubMaterialState(material, subMaterial);

    let contentContainer: HTMLElement | undefined = $state();

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
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12 py-8 space-y-12">
        <!-- Breadcrumb Navigation -->
        <div class="flex items-center gap-3 text-sm">
            <Link
                href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                class="text-slate-400 hover:text-primary-600 font-bold transition-colors"
            >
                <Home size={14} class="mr-1" /> Materi
            </Link>
            <ChevronRight size={12} class="text-slate-300" />
            <Link
                href={ROUTES.MAHASISWA.MATERIALS.SHOW(state.material.id)}
                class="text-slate-400 hover:text-primary-600 font-bold transition-colors"
            >
                {state.material.title}
            </Link>
            <ChevronRight size={12} class="text-slate-300" />
            <span class="text-slate-900 font-bold"
                >{state.subMaterial.title}</span
            >
        </div>

        <!-- Header -->
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        {state.subMaterial.title}
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        {`Bagian ${state.subMaterial.order} dari modul ${state.material.title}.`}
    </p>
    <div class="mt-6 flex flex-wrap gap-4">
        <div>
                <div class="flex items-center gap-4">
                    <div
                        class={`px-4 py-2 ${getBgClass(state.subMaterial.jenis_konten)} rounded-2xl flex items-center justify-center`}
                    >
                        <span
                            class="text-[10px] font-bold text-white uppercase tracking-widest"
                        >
                            {state.subMaterial.jenis_konten === "sintaks"
                                ? "Sintaks"
                                : "Teori"}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner"
                    >
                        <Puzzle size={16} />
                    </div>
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-500"
                    >
                        {state.subMaterial.questions
                            ? state.subMaterial.questions.length
                            : 0} Soal Latihan
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner"
                    >
                        <Clock size={16} />
                    </div>
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-500"
                    >
                        ~{(state.subMaterial.questions
                            ? state.subMaterial.questions.length
                            : 0) * 2} Menit
                    </span>
                </div>
            </div>
    </div>
</div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <div bind:this={contentContainer} class="lg:col-span-3">
                <Card class="p-10 md:p-16">
                    {#snippet header()}
                        <div class="mb-10">
                            <h2
                                class="text-3xl font-extrabold tracking-tight text-slate-900 font-display"
                            >
                                Materi Pembelajaran
                            </h2>
                            <div
                                class="flex items-center gap-2 mt-3"
                                role="presentation"
                            >
                                <div
                                    class={`h-1.5 w-12 ${getBgClass(state.subMaterial.jenis_konten)} rounded-full shadow-sm`}
                                ></div>
                                <div
                                    class="h-1.5 w-4 bg-slate-200 rounded-full"
                                ></div>
                                <div
                                    class="h-1.5 w-2 bg-slate-100 rounded-full"
                                ></div>
                            </div>
                        </div>
                    {/snippet}

                    {#if state.subMaterial.content && state.subMaterial.content.trim()}
                        <ContentDisplay content={state.subMaterial.content} />
                    {:else}
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4"
                            >
                                <BookOpen size={32} class="text-slate-300" />
                            </div>
                            <p class="text-slate-400 font-medium">
                                Konten materi sedang dalam pengembangan.
                            </p>
                            <p class="text-sm text-slate-300 mt-2">
                                Silakan lanjutkan ke latihan soal atau
                                sub-materi lainnya.
                            </p>
                        </div>
                    {/if}

                    <!-- Action Footer -->
                    <div
                        class="mt-16 pt-10 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-8"
                    >
                        <div class="flex items-center gap-4 text-slate-400">
                            <div
                                class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center"
                            >
                                <CheckCheck size={24} />
                            </div>
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-widest text-slate-400"
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
                            class={`w-full md:w-auto shadow-2xl ${getShadowClass(state.subMaterial.jenis_konten)}`}
                        >
                            Mulai Latihan Soal
                        </Button>
                    </div>
                </Card>
            </div>

            <div>
                {#if state.otherSubMaterials.length > 0}
                    <Card>
                        <h3 class="text-2xl font-bold text-slate-900 mb-6">
                            Sub-Materi Lainnya
                        </h3>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4"
                        >
                            {#each state.otherSubMaterials as otherSub (otherSub.id)}
                                <Link
                                    href={ROUTES.MAHASISWA.SUBMATERIALS.SHOW(
                                        state.material.id,
                                        otherSub.id,
                                    )}
                                    class={`group p-6 rounded-2xl border-2 border-slate-100 ${getHoverBorderClass(otherSub.jenis_konten)} hover:shadow-lg transition-all`}
                                >
                                    <div class="flex items-start gap-4">
                                        <div
                                            class={`w-12 h-12 rounded-xl ${getSubMaterialBg(otherSub.jenis_konten)} flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform`}
                                        >
                                            <span
                                                class={`text-lg font-bold ${getSubMaterialText(otherSub.jenis_konten)}`}
                                                >{otherSub.order}</span
                                            >
                                        </div>
                                        <div class="flex-1">
                                            <h4
                                                class="font-bold text-slate-900 mb-1"
                                            >
                                                {otherSub.title}
                                            </h4>
                                            <p
                                                class="text-xs text-slate-500 uppercase tracking-wider font-bold"
                                            >
                                                {otherSub.jenis_konten ===
                                                "sintaks"
                                                    ? "Sintaks"
                                                    : "Teori"} • {otherSub.questions
                                                    ? otherSub.questions.length
                                                    : 0} Soal
                                            </p>
                                        </div>
                                        <ArrowRight
                                            size={16}
                                            class="text-slate-300 group-hover:translate-x-1 transition-all"
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
