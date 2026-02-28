<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import DataTable from "@/components/ui/DataTable.svelte";
    import Card from "@/components/ui/Card.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import { ArrowLeft, Inbox, UserCheck, UserX } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { formatDate } from "@/utils/formatters";
    import { PendingAdminState } from "@/states/Admin/UserState.svelte";

    export let pendingAdmins = [];

    const state = new PendingAdminState(pendingAdmins);

    const columns = [
        { key: "identity", label: "Identitas", align: "left" },
        { key: "email", label: "Sumber Email", align: "left" },
        { key: "date", label: "Tanggal Pengajuan", align: "center" },
        { key: "actions", label: "Aksi Otorisasi", align: "right" },
    ];
</script>

<App title="Pending Requisitions">
    <div class="max-w-5xl mx-auto space-y-12">
        <PageHeader
            title="Permohonan Akses"
            subtitle="Otorisasi permohonan akses administratif dari entitas eksternal."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.USERS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE REPOSITORI</Button
                >
            </div>
        </PageHeader>

        {#if state.pendingAdmins.length > 0}
            <DataTable
                title="Antrean Otorisasi Tertunda"
                items={state.pendingAdmins}
                {columns}
                hideSearch={true}
            >
                <svelte:fragment slot="row" let:item={admin}>
                    <td class="px-6 py-6 border-b border-slate-50">
                        <div class="flex items-center gap-4">
                            <UserAvatar name={admin.name} />
                            <div
                                class="font-bold text-slate-900 uppercase tracking-widest"
                            >
                                {admin.name}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-6 border-b border-slate-50">
                        <span class="text-xs font-medium text-slate-500"
                            >{admin.email}</span
                        >
                    </td>
                    <td class="px-6 py-6 border-b border-slate-50">
                        <span class="text-xs font-medium text-slate-400">
                            {admin.created_at
                                ? formatDate(admin.created_at)
                                : "-"}
                        </span>
                    </td>
                    <td class="px-6 py-6 border-b border-slate-50">
                        <div class="flex justify-end gap-2">
                            <button
                                on:click={() => state.handleApprove(admin.id)}
                                class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all"
                            >
                                <UserCheck size={14} /> Setujui
                            </button>
                            <button
                                on:click={() => state.handleReject(admin.id)}
                                class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all"
                            >
                                <UserX size={14} /> Tolak
                            </button>
                        </div>
                    </td>
                </svelte:fragment>
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
