<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import SubMaterialContent from "@/components/Mahasiswa/SubMaterials/SubMaterialContent.svelte";
    import RelatedSubMaterials from "@/components/Mahasiswa/SubMaterials/RelatedSubMaterials.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Home, ChevronRight, Puzzle, Clock } from "lucide-svelte";
    import { getBgClass } from "@/utils/contentTypeStyles";
    import { onMount, tick } from "svelte";
    import { enhanceCodeBlocks } from "@/utils/codeBlockEnhancer";
    import { SubMaterialState } from "@/states/Mahasiswa/SubMaterialState.svelte";

    export let material = {};
    export let subMaterial = {};

    const state = new SubMaterialState(material, subMaterial);

    let contentContainer;

    onMount(async () => {
        await tick();
        if (contentContainer) enhanceCodeBlocks(contentContainer);
    });

    $: if (state.subMaterial && contentContainer) {
        tick().then(() => enhanceCodeBlocks(contentContainer));
    }
</script>

<App title={state.subMaterial.title}>
    <div class="space-y-12">
        <!-- Breadcrumb Navigation -->
        <div class="flex items-center gap-3 text-sm">
            <Link
                href="/mahasiswa/materials"
                class="text-slate-400 hover:text-primary-600 font-bold transition-colors"
            >
                <Home size={14} class="mr-1" /> Materi
            </Link>
            <ChevronRight size={12} class="text-slate-300" />
            <Link
                href={`/mahasiswa/materials/${state.material.id}`}
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
        <PageHeader
            title={state.subMaterial.title}
            subtitle={`Bagian ${state.subMaterial.order} dari modul ${state.material.title}.`}
        >
            <div slot="actions">
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
        </PageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div bind:this={contentContainer} class="lg:col-span-2 space-y-8">
                <SubMaterialContent
                    subMaterial={state.subMaterial}
                    materialId={state.material.id}
                />
            </div>

            <!-- Sidebar / Additional Info -->
            <div class="space-y-8">
                <RelatedSubMaterials
                    otherSubMaterials={state.otherSubMaterials}
                    materialId={state.material.id}
                />
            </div>
        </div>
    </div>
</App>
