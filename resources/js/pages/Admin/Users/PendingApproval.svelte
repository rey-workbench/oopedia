<script>
    import App from "../../../layouts/App.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import { router } from "@inertiajs/svelte";

    function handleLogout() {
        router.post("/admin/logout");
    }
</script>

<App title="OOPedia - Menunggu Persetujuan" showSidebar={false}>
    <div class="max-w-4xl mx-auto py-12">
        <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <div slot="header" class="bg-blue-600 px-8 py-6 text-white">
                <h6 class="text-lg font-bold tracking-widest uppercase mb-0">
                    Dosen Menunggu Persetujuan
                </h6>
            </div>

            <div class="p-8">
                <div class="flex flex-col items-center text-center space-y-6">
                    <div
                        class="w-24 h-24 bg-amber-50 rounded-[2rem] flex items-center justify-center shadow-lg shadow-amber-100"
                    >
                        <i class="fas fa-clock text-4xl text-amber-500"></i>
                    </div>

                    <div class="space-y-2">
                        <h3
                            class="text-xl font-bold text-slate-900 uppercase tracking-widest"
                        >
                            Akun Menunggu Validasi
                        </h3>
                        <p
                            class="text-slate-400 font-bold text-xs uppercase tracking-widest max-w-md"
                        >
                            Kredensial dosen Anda sedang dalam tahap peninjauan
                            oleh sistem superadmin.
                        </p>
                    </div>

                    <div
                        class="bg-slate-50 p-6 rounded-3xl border border-slate-100 w-full max-w-md"
                    >
                        <p
                            class="text-xs font-bold text-slate-600 leading-relaxed"
                        >
                            Otorisasi akses akan diberikan setelah proses audit
                            identitas selesai. Silakan hubungi koordinator
                            sistem jika memerlukan eskalasi darurat.
                        </p>
                    </div>

                    <div class="pt-4 flex gap-4 w-full justify-center">
                        <Button
                            on:click={handleLogout}
                            variant="danger"
                            icon="fas fa-sign-out-alt"
                            class="shadow-xl shadow-rose-500/10"
                        >
                            LOGOUT DARI SISTEM
                        </Button>
                    </div>
                </div>
            </div>
        </Card>
    </div>
</App>
