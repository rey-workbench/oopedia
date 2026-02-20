<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import ContentDisplay from "@/components/ui/ContentDisplay.svelte";
    import SubMaterialGrid from "@/components/Mahasiswa/Materials/Show/SubMaterialGrid.svelte";
    import { page } from "@inertiajs/svelte";
    import { ArrowLeft, BookOpen, Layers, Info } from "lucide-svelte";
    import { onMount, tick } from "svelte";
    import { enhanceCodeBlocks } from "@/utils/codeBlockEnhancer";
    import { MaterialShowState } from "@/states/Mahasiswa/MaterialState.svelte";
    import { ROUTES } from "@/utils/route";

    export let material = {};

    let contentContainer;

    onMount(async () => {
        await tick();
        if (contentContainer) enhanceCodeBlocks(contentContainer);
    });

    $: if (material && contentContainer) {
        tick().then(() => enhanceCodeBlocks(contentContainer));
    }

    // Initialize State
    const fromAdaptive = $page.props?.flash?.from_adaptive || false;
    const state = new MaterialShowState(material, fromAdaptive);
</script>

<App title={state.material?.title || "Material"}>
    <div class="space-y-12">
        <!-- Header Section -->
        <PageHeader
            title={state.material?.title || "Loading..."}
            subtitle="Kuasai konsep fondasi hingga tingkat lanjut Pemrograman Berorientasi Objek."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}
                >
                    Kembali ke Materi
                </Button>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner"
                    >
                        <BookOpen size={16} />
                    </div>
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-500"
                    >
                        {state.material?.creator?.name || "Admin System"}
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner"
                    >
                        <Layers size={16} />
                    </div>
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-500"
                    >
                        {state.subMaterials.length} Sub-Materi
                    </span>
                </div>
            </div>
        </PageHeader>

        <!-- Adaptive System Alert -->
        {#if state.fromAdaptive}
            <Card class="border-l-4 border-primary-500 bg-primary-50">
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center shrink-0"
                    >
                        <Info size={24} class="text-primary-600" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-primary-900 mb-1">
                            Rekomendasi Sistem Adaptif
                        </h3>
                        <p class="text-sm text-primary-700 leading-relaxed">
                            Sistem merekomendasikan Anda untuk mengulas kembali
                            materi ini. Pilih sub-materi yang ingin dipelajari
                            untuk memperkuat pemahaman.
                        </p>
                    </div>
                </div>
            </Card>
        {/if}

        <!-- Sub-Materials Grid -->
        <div>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2
                        class="text-3xl font-bold text-slate-900 tracking-widest mb-2"
                    >
                        Daftar Sub-Materi
                    </h2>
                    <p class="text-slate-500 font-medium">
                        Pilih sub-materi untuk memulai pembelajaran
                    </p>
                </div>
            </div>

            {#if state.subMaterials.length === 0}
                <Card class="p-20 text-center">
                    <div
                        class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6"
                    >
                        <BookOpen size={48} class="text-slate-200" />
                    </div>
                    <h3
                        class="text-xl font-bold tracking-widest text-slate-900 mb-2"
                    >
                        Belum Ada Sub-Materi
                    </h3>
                    <p class="text-slate-400 text-sm max-w-xs mx-auto mb-6">
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
                <SubMaterialGrid
                    subMaterials={state.subMaterials}
                    materialId={state.material.id}
                />
            {/if}
        </div>

        <!-- Material Content Section (Optional) -->
        {#if state.material.content}
            <Card>
                <div class="prose max-w-none">
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">
                        Tentang Materi Ini
                    </h3>
                    <div class="leading-relaxed">
                        <ContentDisplay
                            content={state.material.content || ""}
                        />
                    </div>
                </div>
            </Card>
        {/if}
    </div>
</App>
