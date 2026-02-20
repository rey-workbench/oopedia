<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import MaterialForm from "@/components/Admin/Materials/MaterialForm.svelte";
    import { ArrowLeft } from "lucide-svelte";

    export let material;
</script>

<App title="Edit Materi">
    <div class="space-y-12">
        <PageHeader
            title="Pembaruan Kurikulum"
            subtitle="Modifikasi konten instruksional dan optimasi media visual."
        >
            <div slot="actions">
                <Button href="/admin/materials" variant="ghost" icon={ArrowLeft}
                    >BATALKAN MODIFIKASI</Button
                >
            </div>
        </PageHeader>

        <MaterialForm {material} />
    </div>
</App>
