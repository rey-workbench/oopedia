<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import SubmaterialList from "@/components/Admin/Materials/Submaterials/SubmaterialList.svelte";
    import { Plus, ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    export let material;
    export let subMaterials = [];
</script>

<App title={`Kelola Sub-Materi: ${material.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Organisasi Sub-Materi"
            subtitle={`Daftar unit pembelajaran untuk modul utama: ${material.title}`}
        >
            <div slot="actions" class="flex flex-wrap items-center gap-4">
                <Button
                    href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.CREATE(
                        material.id,
                    )}
                    variant="primary"
                    icon={Plus}>Tambah Sub-Materi</Button
                >
                <Button
                    href={ROUTES.ADMIN.MATERIALS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE MATERI</Button
                >
            </div>
        </PageHeader>

        <SubmaterialList {material} {subMaterials} />
    </div>
</App>
