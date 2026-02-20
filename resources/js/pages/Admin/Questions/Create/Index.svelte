<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import QuestionForm from "@/components/Admin/Questions/QuestionForm.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    export let materials = [];
    export let material = null;
    export let subMaterials = [];
</script>

<App title="Buat Instrumen Baru">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Engineering Evaluasi"
            subtitle="Membangun instrumen penilaian baru dengan parameter algoritma yang presisi."
        >
            <div slot="actions">
                <Button
                    href={material
                        ? ROUTES.ADMIN.MATERIALS.SHOW_QUESTIONS(material.id)
                        : ROUTES.ADMIN.QUESTIONS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>BATALKAN</Button
                >
            </div>
        </PageHeader>

        <QuestionForm {materials} {material} {subMaterials} />
    </div>
</App>
