<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import { Link } from "@inertiajs/svelte";
    import {
        Home,
        ChevronRight,
        Puzzle,
        Clock,
        CheckCheck,
        Play,
        ArrowRight,
    } from "lucide-svelte";

    export let material = {};
    export let subMaterial = {};

    // Format content for display (replace newlines with <br> inside p tag context or similar)
    // Blade used {!! nl2br(e($subMaterial->content)) !!}
    // We'll split by newline and render paragraphs or breaks
    $: contentLines = subMaterial.content
        ? subMaterial.content.split("\n")
        : [];

    $: otherSubMaterials = material.sub_materials
        ? material.sub_materials.filter((sm) => sm.id !== subMaterial.id)
        : [];

    function getGradientClass(type) {
        return type === "sintaks"
            ? "from-emerald-600 to-teal-600"
            : "from-blue-600 to-indigo-600";
    }

    function getBorderClass(type) {
        return type === "sintaks" ? "border-emerald-100" : "border-blue-100";
    }

    function getBgClass(type) {
        return type === "sintaks" ? "bg-emerald-50" : "bg-blue-50";
    }

    function getSubMaterialBg(type) {
        return type === "sintaks" ? "bg-emerald-50" : "bg-blue-50";
    }

    function getSubMaterialText(type) {
        return type === "sintaks" ? "text-emerald-600" : "text-blue-600";
    }

    function getHoverBorderClass(type) {
        return type === "sintaks"
            ? "hover:border-emerald-500"
            : "hover:border-blue-500";
    }

    function getShadowClass(type) {
        return type === "sintaks"
            ? "shadow-emerald-500/20"
            : "shadow-blue-500/20";
    }
</script>

<App title={subMaterial.title}>
    <div class="space-y-12">
        <!-- Breadcrumb Navigation -->
        <div class="flex items-center gap-3 text-sm">
            <Link
                href="/mahasiswa/materials"
                class="text-slate-400 hover:text-blue-600 font-bold transition-colors"
            >
                <Home size={14} class="mr-1" /> Materi
            </Link>
            <ChevronRight size={12} class="text-slate-300" />
            <Link
                href={`/mahasiswa/materials/${material.id}`}
                class="text-slate-400 hover:text-blue-600 font-bold transition-colors"
            >
                {material.title}
            </Link>
            <ChevronRight size={12} class="text-slate-300" />
            <span class="text-slate-900 font-bold">{subMaterial.title}</span>
        </div>

        <!-- Header -->
        <PageHeader
            title={subMaterial.title}
            subtitle={`Bagian ${subMaterial.order} dari modul ${material.title}.`}
        >
            <div slot="actions">
                <div class="flex items-center gap-4">
                    <div
                        class={`px-4 py-2 ${getBgClass(subMaterial.jenis_konten)} border ${getBorderClass(subMaterial.jenis_konten)} rounded-2xl`}
                    >
                        <span
                            class={`text-[10px] font-bold ${getSubMaterialText(subMaterial.jenis_konten)} uppercase tracking-widest`}
                        >
                            {subMaterial.jenis_konten === "sintaks"
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
                        {subMaterial.questions
                            ? subMaterial.questions.length
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
                        ~{(subMaterial.questions
                            ? subMaterial.questions.length
                            : 0) * 2} Menit
                    </span>
                </div>
            </div>
        </PageHeader>

        <!-- Content Section -->
        <Card class="p-10 md:p-16">
            <article class="prose prose-lg prose-slate max-w-none">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">
                        Materi Pembelajaran
                    </h2>
                    <div
                        class={`h-1 w-20 bg-gradient-to-r ${getGradientClass(subMaterial.jenis_konten)} rounded-full`}
                    ></div>
                </div>

                <div class="text-slate-700 leading-relaxed whitespace-pre-line">
                    {subMaterial.content || ""}
                </div>
            </article>

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
                            {subMaterial.questions
                                ? subMaterial.questions.length
                                : 0} soal menanti Anda
                        </p>
                    </div>
                </div>

                <Button
                    href={`/mahasiswa/materials/${material.id}/questions?sub_material=${subMaterial.id}`}
                    variant="primary"
                    size="xl"
                    icon={Play}
                    class={`w-full md:w-auto shadow-2xl ${getShadowClass(subMaterial.jenis_konten)}`}
                >
                    Mulai Latihan Soal
                </Button>
            </div>
        </Card>

        <!-- Navigation to Other Sub-Materials -->
        {#if otherSubMaterials.length > 0}
            <Card>
                <h3 class="text-2xl font-bold text-slate-900 mb-6">
                    Sub-Materi Lainnya
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {#each otherSubMaterials as otherSub (otherSub.id)}
                        <Link
                            href={`/mahasiswa/materials/${material.id}/submaterials/${otherSub.id}`}
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
                                        class={`font-bold text-slate-900 mb-1 group-hover:${getSubMaterialText(otherSub.jenis_konten).replace("text-", "")} transition-colors`}
                                    >
                                        {otherSub.title}
                                    </h4>
                                    <p
                                        class="text-xs text-slate-500 uppercase tracking-wider font-bold"
                                    >
                                        {otherSub.jenis_konten === "sintaks"
                                            ? "Sintaks"
                                            : "Teori"} • {otherSub.questions
                                            ? otherSub.questions.length
                                            : 0} Soal
                                    </p>
                                </div>
                                <ArrowRight
                                    size={16}
                                    class={`text-slate-300 group-hover:${getSubMaterialText(otherSub.jenis_konten).replace("text-", "")} group-hover:translate-x-1 transition-all`}
                                />
                            </div>
                        </Link>
                    {/each}
                </div>
            </Card>
        {/if}
    </div>
</App>
