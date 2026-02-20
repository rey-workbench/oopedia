<script>
    import Card from "@/components/ui/Card.svelte";
    import { Link } from "@inertiajs/svelte";
    import { ArrowRight } from "lucide-svelte";
    import {
        getSubMaterialBg,
        getSubMaterialText,
        getHoverBorderClass,
    } from "@/utils/contentTypeStyles";

    export let otherSubMaterials = [];
    export let materialId;
</script>

{#if otherSubMaterials.length > 0}
    <Card>
        <h3 class="text-2xl font-bold text-slate-900 mb-6">
            Sub-Materi Lainnya
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {#each otherSubMaterials as otherSub (otherSub.id)}
                <Link
                    href={`/mahasiswa/materials/${materialId}/submaterials/${otherSub.id}`}
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
