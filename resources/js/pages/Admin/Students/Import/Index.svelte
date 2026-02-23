<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import DataForm from "@/components/ui/DataForm.svelte";
    import ImportInstructions from "@/components/ui/ImportInstructions.svelte";
    import FileUploadZone from "@/components/ui/FileUploadZone.svelte";
    import { ArrowLeft, Upload } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { StudentImportState } from "@/states/Admin/StudentState.svelte";

    const state = new StudentImportState();
    const form = state.form;

    const items = [
        "File harus dalam format <strong>.xlsx</strong> atau <strong>.xls</strong>",
        "Mahasiswa yang sudah terdaftar akan dilewati secara otomatis",
        "Role otomatis ditentukan sebagai Mahasiswa (Level 3)",
        "Gunakan template resmi yang telah disediakan",
    ];
</script>

<App title="Impor Data Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Protokol Impor Mahasiswa"
            subtitle="Integrasi massal bios data mahasiswa ke dalam registry OOPedia."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.STUDENTS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <div class="max-w-2xl mx-auto">
            <DataForm
                title="Impor Dataset Mahasiswa"
                onSubmit={() => state.submit()}
                isEdit={false}
                processing={$form.processing}
                submitLabel="EKSEKUSI IMPOR DATASET"
                submitIcon={Upload}
                cancelHref={null}
            >
                <ImportInstructions {items} />
                <FileUploadZone
                    {form}
                    onFileChange={(e) => state.handleFileChange(e)}
                    label="Berkas Dataset (Excel)"
                    downloadHref="/admin/students/download-template"
                    downloadLabel="TEMPLATE FORMAL"
                />
            </DataForm>
        </div>
    </div>
</App>
