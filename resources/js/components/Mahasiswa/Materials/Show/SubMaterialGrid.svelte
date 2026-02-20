<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { BookOpen, Puzzle } from "lucide-svelte";
    import {
        getBgClass,
        getTextClass,
        getIcon,
        getBadgeLabel,
        getShadowClass,
    } from "@/utils/contentTypeStyles";

    export let subMaterials = [];
    export let materialId;

    const stripHtml = (html) => {
        if (!html) return "";
        const doc = new DOMParser().parseFromString(html, "text/html");
        return doc.body.textContent || "";
    };
</script>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    {#each subMaterials as subMaterial (subMaterial.id)}
        <Card
            padding="p-0"
            class="group hover:shadow-2xl transition-all duration-300 overflow-hidden"
        >
            <!-- Header with Icon -->
            <div
                class={`relative h-48 ${getBgClass(subMaterial.jenis_konten)} flex items-center justify-center shrink-0`}
            >
                <div class="absolute inset-0 bg-black/10"></div>
                <div
                    class="absolute inset-x-0 bottom-0 h-1/2 bg-black/20"
                ></div>

                <!-- Center Icon -->
                <div class="relative z-10">
                    <svelte:component
                        this={getIcon(subMaterial.jenis_konten)}
                        size={64}
                        class="text-white/20 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500"
                    />
                </div>

                <!-- Order Badge (Top Left) -->
                <div
                    class="absolute top-4 left-4 w-10 h-10 bg-white rounded-xl shadow-lg flex items-center justify-center"
                >
                    <span
                        class={`text-lg font-bold ${getTextClass(subMaterial.jenis_konten)}`}
                        >{subMaterial.order}</span
                    >
                </div>

                <!-- Bottom Badges -->
                <div
                    class="absolute bottom-5 left-5 right-5 flex justify-between items-center"
                >
                    <div
                        class="px-3 py-1.5 bg-white/10 backdrop-blur-md rounded-xl text-white text-[9px] font-bold uppercase tracking-widest border border-white/20"
                    >
                        {getBadgeLabel(subMaterial.jenis_konten)}
                    </div>
                    <div
                        class={`flex items-center gap-2 px-3 py-1.5 ${getBgClass(subMaterial.jenis_konten)} rounded-xl text-white text-[9px] font-bold uppercase tracking-widest shadow-xl ${getShadowClass(subMaterial.jenis_konten)}`}
                    >
                        <Puzzle size={14} />
                        {subMaterial.questions
                            ? subMaterial.questions.length
                            : 0} Soal
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 flex-1 flex flex-col">
                <div class="min-h-[3.5rem] mb-3 flex items-start">
                    <h3
                        class={`text-xl font-bold text-slate-900 group-hover:${getTextClass(subMaterial.jenis_konten)} transition-colors line-clamp-2`}
                    >
                        {subMaterial.title}
                    </h3>
                </div>

                <div class="min-h-[4.5rem] mb-6">
                    <p
                        class="text-sm text-slate-600 line-clamp-3 leading-relaxed"
                    >
                        {stripHtml(subMaterial.content)}
                    </p>
                </div>

                <div class="mt-auto">
                    <Button
                        href={`/mahasiswa/materials/${materialId}/submaterials/${subMaterial.id}`}
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
