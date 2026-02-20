<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import PendingAdminList from "@/components/Admin/Users/PendingAdminList.svelte";
    import { ArrowLeft } from "lucide-svelte";

    export let pendingAdmins = [];
</script>

<App title="Pending Requisitions">
    <div class="max-w-5xl mx-auto space-y-12">
        <PageHeader
            title="Permohonan Akses"
            subtitle="Otorisasi permohonan akses administratif dari entitas eksternal."
        >
            <div slot="actions">
                <Button href="/admin/users" variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE REPOSITORI</Button
                >
            </div>
        </PageHeader>

        <PendingAdminList {pendingAdmins} />
    </div>
</App>
