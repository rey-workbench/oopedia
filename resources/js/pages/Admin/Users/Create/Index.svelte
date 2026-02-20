<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import UserForm from "@/components/Admin/Users/UserForm.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    export let roles = [];
</script>

<App title="Pembuatan Administrator">
    <div class="space-y-12">
        <PageHeader
            title="Pembuatan Administrator"
            subtitle="Otorisasi entitas baru ke dalam pusat kendali sistem."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.USERS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <UserForm {roles} />
    </div>
</App>
