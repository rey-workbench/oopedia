<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import { UserCheck, UserX, Inbox } from "lucide-svelte";
    import { formatDate } from "@/utils/formatters";
    import { PendingAdminState } from "@/states/Admin/UserState.svelte";

    export let pendingAdmins = [];

    const state = new PendingAdminState(pendingAdmins);
</script>

{#if state.pendingAdmins.length > 0}
    <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
        <div slot="header" class="flex items-center gap-4">
            <p
                class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
            >
                Antrean Otorisasi Tertunda
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th
                            class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Identitas</th
                        >
                        <th
                            class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Sumber Email</th
                        >
                        <th
                            class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Tanggal Pengajuan</th
                        >
                        <th
                            class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Aksi Otorisasi</th
                        >
                    </tr>
                </thead>
                <tbody>
                    {#each state.pendingAdmins as admin (admin.id)}
                        <tr
                            class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                        >
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-bold shadow-sm uppercase text-xs"
                                    >
                                        {admin.name.charAt(0)}
                                    </div>
                                    <div
                                        class="font-bold text-slate-900 uppercase tracking-widest"
                                    >
                                        {admin.name}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <span
                                    class="text-xs font-bold text-slate-400 underline decoration-slate-200 underline-offset-4"
                                    >{admin.email}</span
                                >
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                    >{formatDate(admin.created_at, {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    }) || "-"}</span
                                >
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex justify-end gap-3">
                                    <Button
                                        on:click={() =>
                                            state.handleApprove(admin.id)}
                                        variant="success"
                                        size="sm"
                                        icon={UserCheck}
                                        class="shadow-lg shadow-emerald-500/20"
                                    >
                                        SETUJUI
                                    </Button>
                                    <Button
                                        on:click={() =>
                                            state.handleReject(admin.id)}
                                        variant="danger"
                                        size="sm"
                                        icon={UserX}
                                        class="shadow-lg shadow-rose-500/20"
                                    >
                                        TOLAK
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </Card>
{:else}
    <Card class="border-slate-100 shadow-xl">
        <EmptyState
            title="Antrean Kosong"
            description="Tidak ada permohonan akses administratif yang menunggu otorisasi saat ini."
            icon={Inbox}
        />
    </Card>
{/if}
