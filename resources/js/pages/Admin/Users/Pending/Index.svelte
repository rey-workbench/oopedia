<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import Card from '@/components/ui/Card.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { ChevronLeft, Inbox, UserCheck, UserX } from '@lucide/svelte';
    import { ROUTES } from '@/utils/route';
    import { formatDate } from '@/utils/formatters';
    import { untrack } from 'svelte';
    import { PendingAdminState } from '@/states/Admin/UserState.svelte';

    let { pendingAdmins = [] }: { pendingAdmins: any[] } = $props();

    const state = untrack(() => new PendingAdminState(pendingAdmins));

    const columns = [
        { key: 'identity', label: 'Identitas', align: 'left' },
        { key: 'email', label: 'Sumber Email', align: 'left' },
        { key: 'date', label: 'Tanggal Pengajuan', align: 'center' },
        { key: 'actions', label: 'Aksi Otorisasi', align: 'right' },
    ];
</script>

<App title="Pending Requisitions">
    <div class="mx-auto max-w-5xl space-y-12">
        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                Permohonan Akses
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                Otorisasi permohonan akses administratif dari entitas eksternal.
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                <Button
                    href={ROUTES.ADMIN.USERS.INDEX}
                    variant="ghost"
                    size="sm"
                    icon={ChevronLeft}
                    class="text-slate-400 transition-colors hover:text-slate-900"
                    >KEMBALI KE REPOSITORI</Button
                >
            </div>
        </div>

        {#if state.pendingAdmins.length > 0}
            <DataTable
                title="Antrean Otorisasi Tertunda"
                items={state.pendingAdmins}
                {columns}
                hideSearch={true}
                itemsPerPage={10}
            >
                {#snippet row(admin: any, index)}
                    <td class="border-b border-slate-50 px-6 py-6">
                        <div class="flex items-center gap-4">
                            <UserAvatar name={admin.name} />
                            <div
                                id={index === 0 ? 'pending-admin-identity' : undefined}
                                class="font-bold tracking-widest text-slate-900 uppercase"
                            >
                                {admin.name}
                            </div>
                        </div>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        <span class="text-xs font-medium text-slate-500">{admin.email}</span>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        <span class="text-xs font-medium text-slate-400">
                            {admin.created_at ? formatDate(admin.created_at) : '-'}
                        </span>
                    </td>
                    <td class="border-b border-slate-50 px-6 py-6">
                        <div
                            id={index === 0 ? 'pending-admin-actions' : undefined}
                            class="flex justify-end gap-2"
                        >
                            <button
                                onclick={() => state.handleApprove(admin.id)}
                                class="flex items-center gap-2 rounded-xl border-b-2 border-emerald-100 bg-emerald-50 px-4 py-2 text-[10px] font-bold tracking-widest text-emerald-600 uppercase transition-all hover:bg-emerald-100 active:translate-y-px active:border-b-0"
                            >
                                <UserCheck size={14} /> Setujui
                            </button>
                            <button
                                onclick={() => state.handleReject(admin.id)}
                                class="flex items-center gap-2 rounded-xl border-b-2 border-rose-100 bg-rose-50 px-4 py-2 text-[10px] font-bold tracking-widest text-rose-600 uppercase transition-all hover:bg-rose-100 active:translate-y-px active:border-b-0"
                            >
                                <UserX size={14} /> Tolak
                            </button>
                        </div>
                    </td>
                {/snippet}
            </DataTable>
        {:else}
            <Card class="border-slate-100 shadow-xl">
                <EmptyState
                    title="Antrean Kosong"
                    description="Tidak ada permohonan akses administratif yang menunggu otorisasi saat ini."
                    icon={Inbox}
                />
            </Card>
        {/if}
    </div>
</App>
