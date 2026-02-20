<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import UserList from "@/components/Admin/Users/UserList.svelte";
    import { Clock, UserPlus } from "lucide-svelte";
    import { page } from "@inertiajs/svelte";

    export let users = { data: [] }; // Paginator object
    export let pendingAdminsCount = 0;

    let search =
        new URLSearchParams(window.location.search).get("search") || "";

    $: authUser = $page.props.auth.user;
    $: isSuperAdmin = authUser.role_id === 1;
</script>

<App title="Manajemen Admin">
    <div class="space-y-12">
        <PageHeader
            title="Akses Kontrol Admin"
            subtitle="Kelola akun Administrator dan Dosen pembimbing sistem."
        >
            <div slot="actions">
                {#if isSuperAdmin}
                    <div class="flex flex-wrap items-center gap-4">
                        {#if pendingAdminsCount > 0}
                            <Button
                                href="/admin/pending-admins"
                                variant="danger"
                                icon={Clock}
                            >
                                {pendingAdminsCount} Permintaan Menunggu
                            </Button>
                        {/if}
                        <Button
                            href="/admin/users/create"
                            variant="primary"
                            icon={UserPlus}>Tambah User</Button
                        >
                    </div>
                {/if}
            </div>
        </PageHeader>

        <UserList {users} {search} />
    </div>
</App>
