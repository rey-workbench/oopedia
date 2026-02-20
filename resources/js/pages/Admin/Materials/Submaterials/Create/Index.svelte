<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import SubmaterialForm from "@/components/Admin/Materials/Submaterials/SubmaterialForm.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    export let material;
</script>

<App title={`Tambah Sub-Materi: ${material.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Inisialisasi Unit"
            subtitle={`Menambahkan sub-unit pembelajaran baru ke dalam materi ${material.title}`}
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(
                        material.id,
                    )}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <SubmaterialForm {material} />
    </div>
</App>
