<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import UserForm from "@/components/Admin/Users/UserForm.svelte";
    import { ArrowLeft } from "lucide-svelte";

    export let user;
</script>

<App title="Edit Administrator">
    <div class="space-y-12">
        <PageHeader
            title="Pembaruan Kredensial"
            subtitle="Modifikasi data identitas dan kunci keamanan entitas."
        >
            <div slot="actions">
                <Button href="/admin/users" variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <UserForm {user} />
    </div>
</App>
