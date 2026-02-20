<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import ContentDisplay from "@/components/ui/ContentDisplay.svelte";
    import { BookOpen, CheckCheck, Play } from "lucide-svelte";
    import { getBgClass, getShadowClass } from "@/utils/contentTypeStyles";

    export let subMaterial = {};
    export let materialId;
</script>

<Card class="p-10 md:p-16">
    <div slot="header" class="mb-10">
        <h2
            class="text-3xl font-extrabold tracking-tight text-slate-900 font-display"
        >
            Materi Pembelajaran
        </h2>
        <div class="flex items-center gap-2 mt-3" role="presentation">
            <div
                class={`h-1.5 w-12 ${getBgClass(subMaterial.jenis_konten)} rounded-full shadow-sm`}
            ></div>
            <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
            <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
        </div>
    </div>

    {#if subMaterial.content && subMaterial.content.trim()}
        <ContentDisplay content={subMaterial.content} />
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
                Silakan lanjutkan ke latihan soal atau sub-materi lainnya.
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
                    {subMaterial.questions ? subMaterial.questions.length : 0} soal
                    menanti Anda
                </p>
            </div>
        </div>

        <Button
            href={`/mahasiswa/materials/${materialId}/questions?sub_material=${subMaterial.id}`}
            variant="primary"
            size="xl"
            icon={Play}
            class={`w-full md:w-auto shadow-2xl ${getShadowClass(subMaterial.jenis_konten)}`}
        >
            Mulai Latihan Soal
        </Button>
    </div>
</Card>
