<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import SubmaterialForm from "@/components/Admin/Materials/Submaterials/SubmaterialForm.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    export let material;
    export let submaterial;
</script>

<App title={`Edit Sub-Materi: ${submaterial.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Modifikasi Unit"
            subtitle={`Memperbarui konten pembelajaran untuk unit sub-materi: ${submaterial.title}`}
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

        <SubmaterialForm {material} {submaterial} />
    </div>
</App>
