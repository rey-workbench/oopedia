<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import { UserListState } from '@/states/Admin/UserState.svelte';
    import { Clock, UserPlus, ShieldCheck, Edit2, Trash2 } from '@lucide/svelte';
    import { page } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';

    let {
        users = { data: [] },
        pendingAdminsCount = 0,
    }: { users: any; pendingAdminsCount: number } = $props();

    let search: string = $state(new URLSearchParams(window.location.search).get('search') || '');

    const listState = untrack(() => new UserListState(users, search));

    const authUser = $derived((page.props as any)['auth'].user);
    const isSuperAdmin = $derived((authUser as any)?.role_id === 1);

    const columns = $derived([
        { key: 'identity', label: 'Identitas', align: 'left' },
        { key: 'email', label: 'Otorisasi Email', align: 'left' },
        { key: 'role', label: 'Peran Sistem', align: 'center' },
        { key: 'status', label: 'Status Akses', align: 'center' },
        { key: 'actions', label: 'Aksi', align: 'right' },
    ]);
</script>

<App title="Manajemen Admin">
    <div class="space-y-12">
        <PageHeader
            id="page-header"
            title="Akses Kontrol Admin"
            subtitle="Kelola akun Administrator dan Dosen pembimbing sistem."
        >
            {#if isSuperAdmin}
                <div class="flex gap-4">
                    {#if pendingAdminsCount > 0}
                        <Button
                            id="pending-requests-btn"
                            href={ROUTES.ADMIN.PENDING_ADMINS.INDEX}
                            variant="danger"
                            icon={Clock}
                        >
                            {pendingAdminsCount} Permintaan Menunggu
                        </Button>
                    {/if}
                    <Button
                        id="add-user-btn"
                        href={ROUTES.ADMIN.USERS.CREATE}
                        variant="primary"
                        icon={UserPlus}
                    >
                        Tambah User
                    </Button>
                </div>
            {/if}
        </PageHeader>

        <div id="user-directory-table">
            <DataTable
                title="Direktori Pengguna Sistem"
                items={listState.users.data}
                bind:search
                onsearch={() => {
                    listState.search = search;
                    listState.handleSearch();
                }}
                searchPlaceholder="Cari nama atau email..."
                {columns}
                links={listState.users.links}
            >
                {#snippet empty()}
                    <EmptyState
                        title="Data Pengguna Kosong"
                        description="Belum ada pengguna sistem yang ditemukan sesuai pencarian."
                        icon={ShieldCheck}
                    />
                {/snippet}

                {#snippet row(user, index)}
                    <td
                        class={`group-hover:border-l-primary-600 border-b border-l-4 border-slate-50 border-l-transparent px-6 py-6 ${user.role_id === 1 ? 'bg-slate-900/5' : ''}`}
                    >
                        <div>
                            <div class="font-bold tracking-widest text-slate-900 uppercase">
                                {user.name}
                            </div>
                            <div
                                class="mt-0.5 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                ID #{user.id}
                            </div>
                        </div>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        <span class="text-xs font-medium text-slate-500">{user.email}</span>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        <Badge
                            variant={user.role_id === 1
                                ? 'secondary'
                                : user.role_id === 2
                                  ? 'primary'
                                  : 'success'}
                            size="xs"
                        >
                            {user.role ? user.role.role_name : 'N/A'}
                        </Badge>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        {#if user.role_id === 1}
                            <span
                                class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >TANPA BATAS</span
                            >
                        {:else if user.is_approved}
                            <span
                                class="text-[10px] font-bold tracking-widest text-emerald-600 uppercase"
                                >DISETUJUI</span
                            >
                        {:else}
                            <span
                                class="text-[10px] font-bold tracking-widest text-amber-600 uppercase"
                                >MENUNGGU</span
                            >
                        {/if}
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        <div
                            id={index === 0 ? 'user-actions' : undefined}
                            class="flex justify-end gap-2"
                        >
                            <Button
                                id={index === 0 ? 'btn-edit-user' : undefined}
                                variant="ghost"
                                size="sm"
                                href={ROUTES.ADMIN.USERS.EDIT(user.id)}
                                icon={Edit2}
                            />
                            {#if isSuperAdmin && user.id !== authUser.id}
                                <Button
                                    id={index === 0 ? 'btn-delete-user' : undefined}
                                    variant="ghost"
                                    size="sm"
                                    onclick={() => listState.handleDelete(user.id)}
                                    icon={Trash2}
                                    class="text-slate-300 hover:text-rose-500"
                                />
                            {/if}
                        </div>
                    </td>
                {/snippet}
            </DataTable>
        </div>
    </div>
</App>
