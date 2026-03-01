<script lang="ts">
    import App from "@/layouts/App.svelte";
        import Button from "@/components/ui/Button.svelte";
    import DataTable from "@/components/shared/DataTable.svelte";
    import Card from "@/components/ui/Card.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import { ArrowLeft, Inbox, UserCheck, UserX } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { formatDate } from "@/utils/formatters";
    import { untrack } from 'svelte';
    import { PendingAdminState } from "@/states/Admin/UserState.svelte";

    let { pendingAdmins = [] }: { pendingAdmins: any[] } = $props();

    const state = untrack(() => new PendingAdminState(pendingAdmins));

    const columns = [
        { key: "identity", label: "Identitas", align: "left" },
        { key: "email", label: "Sumber Email", align: "left" },
        { key: "date", label: "Tanggal Pengajuan", align: "center" },
        { key: "actions", label: "Aksi Otorisasi", align: "right" },
    ];
</script>

<App title="Pending Requisitions">
    <div class="max-w-5xl mx-auto space-y-12">
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Permohonan Akses
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        Otorisasi permohonan akses administratif dari entitas eksternal.
    </p>
    <div class="mt-6 flex flex-wrap gap-4">
        <div>
                <Button
                    href={ROUTES.ADMIN.USERS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE REPOSITORI</Button
                >
            </div>
    </div>
</div>

        {#if state.pendingAdmins.length > 0}
            <DataTable
                title="Antrean Otorisasi Tertunda"
                items={state.pendingAdmins}
                {columns}
                hideSearch={true}
            >
                {#snippet row(admin)}
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
                            onclick={() => state.handleApprove(admin.id)}
                                class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all"
                            >
                                <UserCheck size={14} /> Setujui
                            </button>
                            <button
                            onclick={() => state.handleReject(admin.id)}
                                class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all"
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
