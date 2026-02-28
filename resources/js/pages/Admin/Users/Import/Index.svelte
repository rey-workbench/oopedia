<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import DataForm from "@/components/ui/DataForm.svelte";
    import { ArrowLeft, Upload } from "lucide-svelte";
    import ImportInstructions from "@/components/ui/ImportInstructions.svelte";
    import FileUploadZone from "@/components/ui/FileUploadZone.svelte";
    import { ROUTES } from "@/utils/route";
    import { UserImportState } from "@/states/Admin/UserState.svelte";

    const state = new UserImportState();
    const form = state.form;

    const items = [
        "File harus dalam format <strong>.xlsx</strong> atau <strong>.xls</strong>",
        "Kolom nama, email, dan password wajib diisi",
        "Email harus unik dan belum terdaftar di sistem",
        "Gunakan template resmi untuk memastikan format yang benar",
    ];
</script>

<App title="Impor Data Admin">
    <div class="space-y-12">
        <PageHeader
            title="Protokol Impor Admin"
            subtitle="Unggah dataset admin melalui berkas Excel untuk otorisasi massal."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.USERS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <div class="max-w-2xl mx-auto">
            <DataForm
                title="Impor Dataset Admin"
                onSubmit={() => state.submit()}
                isEdit={false}
                processing={$form.processing}
                submitLabel="MULAI IMPOR DATASET"
                submitIcon={Upload}
                cancelHref={null}
            >
                <ImportInstructions {items} />
                <FileUploadZone
                    {form}
                    onFileChange={(e) => state.handleFileChange(e)}
                    label="Berkas Dataset (Excel)"
                    downloadHref="/admin/users/download-template"
                    downloadLabel="UNDUH TEMPLATE"
                />
            </DataForm>
        </div>
    </div>
</App>
