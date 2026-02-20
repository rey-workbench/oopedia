<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import MaterialForm from "@/components/Admin/Materials/MaterialForm.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
</script>

<App title="Tambah Materi">
    <div class="space-y-12">
        <PageHeader
            title="Arsitek Konten Kurikulum"
            subtitle="Publikasikan modul pembelajaran baru dengan visualisasi premium."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.MATERIALS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>BATALKAN PUBLIKASI</Button
                >
            </div>
        </PageHeader>

        <MaterialForm />
    </div>
</App>
