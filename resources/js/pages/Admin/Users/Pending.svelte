<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import { router } from "@inertiajs/svelte";

    export let pendingAdmins = [];

    function handleApprove(id) {
        router.post(`/admin/users/${id}/approve`);
    }

    function handleReject(id) {
        if (
            confirm(
                "Apakah Anda yakin ingin menolak admin ini? Akun akan diubah menjadi mahasiswa.",
            )
        ) {
            router.post(`/admin/users/${id}/reject`);
        }
    }

    function formatDate(dateString) {
        if (!dateString) return "-";
        return new Date(dateString).toLocaleDateString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }
</script>

<App title="Pending Requisitions">
    <div class="max-w-5xl mx-auto space-y-12">
        <PageHeader
            title="Permohonan Akses"
            subtitle="Otorisasi permohonan akses administratif dari entitas eksternal."
        >
            <div slot="actions">
                <Button
                    href="/admin/users"
                    variant="ghost"
                    icon="fas fa-arrow-left">KEMBALI KE REPOSITORI</Button
                >
            </div>
        </PageHeader>

        {#if pendingAdmins.length > 0}
            <Card
                padding="p-0"
                class="overflow-hidden border-slate-100 shadow-2xl"
            >
                <div
                    slot="header"
                    class="px-6 py-4 border-b border-slate-50 flex items-center gap-4"
                >
                    <div
                        class="w-1.5 h-8 bg-amber-500 rounded-full animate-pulse"
                    ></div>
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
                            {#each pendingAdmins as admin (admin.id)}
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
                                            >{formatDate(
                                                admin.created_at,
                                            )}</span
                                        >
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex justify-end gap-3">
                                            <Button
                                                on:click={() =>
                                                    handleApprove(admin.id)}
                                                variant="success"
                                                size="sm"
                                                icon="fas fa-user-check"
                                                class="shadow-lg shadow-emerald-500/20"
                                            >
                                                SETUJUI
                                            </Button>
                                            <Button
                                                on:click={() =>
                                                    handleReject(admin.id)}
                                                variant="danger"
                                                size="sm"
                                                icon="fas fa-user-xmark"
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
            <Card padding="p-20" class="text-center border-slate-100 shadow-xl">
                <div
                    class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-200"
                >
                    <i class="fas fa-inbox text-3xl"></i>
                </div>
                <h3
                    class="text-lg font-bold uppercase tracking-widest text-slate-900 mb-2"
                >
                    Antrean Kosong
                </h3>
                <p class="text-slate-400 text-xs max-w-xs mx-auto">
                    Tidak ada permohonan akses administratif yang menunggu
                    otorisasi saat ini.
                </p>
            </Card>
        {/if}
    </div>
</App>
