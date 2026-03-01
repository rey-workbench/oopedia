<script>
    import App from "@/layouts/App.svelte";
        import Button from "@/components/ui/Button.svelte";
    import DataTable from "@/components/shared/DataTable.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import Pagination from "@/components/ui/Pagination.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import { UserListState } from "@/states/Admin/UserState.svelte";
    import { Clock, UserPlus, ShieldCheck, Edit2, Trash2 } from "lucide-svelte";
    import { page } from "@inertiajs/svelte";
    import { ROUTES } from "@/utils/route";

    export let users = { data: [] }; // Paginator object
    export let pendingAdminsCount = 0;

    let search =
        new URLSearchParams(window.location.search).get("search") || "";

    const state = new UserListState(users, search);

    $: authUser = $page.props.auth.user;
    $: isSuperAdmin = authUser.role_id === 1;

    $: columns = [
        { key: "identity", label: "Identitas", align: "left" },
        { key: "email", label: "Otorisasi Email", align: "left" },
        { key: "role", label: "Peran Sistem", align: "center" },
        { key: "status", label: "Status Akses", align: "center" },
        { key: "actions", label: "Aksi", align: "right" },
    ];
</script>

<App title="Manajemen Admin">
    <div class="space-y-12">
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Akses Kontrol Admin
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        Kelola akun Administrator dan Dosen pembimbing sistem.
    </p>
    <div class="mt-6 flex flex-wrap gap-4">
        <div>
                {#if isSuperAdmin}
                    <div class="flex flex-wrap items-center gap-4">
                        {#if pendingAdminsCount > 0}
                            <Button
                                href={ROUTES.ADMIN.PENDING_ADMINS.INDEX}
                                variant="danger"
                                icon={Clock}
                            >
                                {pendingAdminsCount} Permintaan Menunggu
                            </Button>
                        {/if}
                        <Button
                            href={ROUTES.ADMIN.USERS.CREATE}
                            variant="primary"
                            icon={UserPlus}>Tambah User</Button
                        >
                    </div>
                {/if}
            </div>
    </div>
</div>

        <DataTable
            title="Direktori Pengguna Sistem"
            items={state.users.data}
            bind:search
            onSearch={() => {
                state.search = search;
                state.handleSearch();
            }}
            searchPlaceholder="Cari nama atau email..."
            {columns}
        >
            <svelte:fragment slot="empty">
                <EmptyState
                    title="Data Pengguna Kosong"
                    description="Belum ada pengguna sistem yang ditemukan sesuai pencarian."
                    icon={ShieldCheck}
                />
            </svelte:fragment>

            <svelte:fragment slot="row" let:item={user}>
                <td
                    class={`px-6 py-6 border-b border-slate-50 border-l-4 border-l-transparent group-hover:border-l-primary-600 ${user.role_id === 1 ? "bg-slate-900/5" : ""}`}
                >
                    <div>
                        <div
                            class="font-bold text-slate-900 uppercase tracking-widest"
                        >
                            {user.name}
                        </div>
                        <div
                            class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"
                        >
                            ID #{user.id}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-6 border-b border-slate-50">
                    <span class="text-xs font-medium text-slate-500"
                        >{user.email}</span
                    >
                </td>
                <td class="px-6 py-6 border-b border-slate-50">
                    <Badge
                        variant={user.role_id === 1
                            ? "secondary"
                            : user.role_id === 2
                              ? "primary"
                              : "success"}
                        size="xs"
                    >
                        {user.role ? user.role.name : "N/A"}
                    </Badge>
                </td>
                <td class="px-6 py-6 border-b border-slate-50">
                    {#if user.role_id === 1}
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                            >TANPA BATAS</span
                        >
                    {:else if user.approved_at}
                        <span
                            class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest"
                            >DISETUJUI</span
                        >
                    {:else}
                        <span
                            class="text-[10px] font-bold text-amber-600 uppercase tracking-widest"
                            >MENUNGGU</span
                        >
                    {/if}
                </td>
                <td class="px-6 py-6 border-b border-slate-50">
                    <div class="flex justify-end gap-2">
                        <Button
                            variant="ghost"
                            size="sm"
                            href={ROUTES.ADMIN.USERS.EDIT(user.id)}
                            icon={Edit2}
                        />
                        {#if isSuperAdmin && user.id !== authUser.id}
                            <Button
                                variant="ghost"
                                size="sm"
                                on:click={() => state.handleDelete(user.id)}
                                icon={Trash2}
                                class="text-slate-300 hover:text-rose-500"
                            />
                        {/if}
                    </div>
                </td>
            </svelte:fragment>
        </DataTable>

        {#if state.users.data && state.users.data.length > 0}
            <div class="mt-6">
                <Pagination links={state.users.links || []} />
            </div>
        {/if}
    </div>
</App>
