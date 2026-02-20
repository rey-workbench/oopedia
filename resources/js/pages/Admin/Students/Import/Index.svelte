<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import StudentImportForm from "@/components/Admin/Students/StudentImportForm.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
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

        <StudentImportForm />
    </div>
</App>
