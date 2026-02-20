<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import UserImportForm from "@/components/Admin/Users/UserImportForm.svelte";
    import { ArrowLeft } from "lucide-svelte";
</script>

<App title="Impor Data Admin">
    <div class="space-y-12">
        <PageHeader
            title="Protokol Impor Admin"
            subtitle="Unggah dataset admin melalui berkas Excel untuk otorisasi massal."
        >
            <div slot="actions">
                <Button href="/admin/users" variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <UserImportForm />
    </div>
</App>
