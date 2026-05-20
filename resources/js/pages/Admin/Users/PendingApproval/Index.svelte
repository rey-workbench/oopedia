<script>
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Clock, LogOut } from '@lucide/svelte';
    import { PendingUsersState } from '@/states/Admin/UserState.svelte';

    const state = new PendingUsersState();
</script>

<App title="OOPedia - Menunggu Persetujuan" showSidebar={false}>
    <div class="mx-auto max-w-4xl py-12">
        <Card padding="p-0" class="group overflow-hidden border-slate-100 shadow-2xl">
            <div class="relative bg-amber-400 p-20 text-white">
                <div
                    class="absolute -top-12 -right-12 h-48 w-48 rounded-full bg-white/20 blur-3xl transition-all duration-1000 group-hover:bg-white/30"
                ></div>

                <div class="relative z-10 space-y-8">
                    <div
                        class="mx-auto flex h-24 w-24 rotate-12 transform items-center justify-center rounded-3xl border border-white/30 bg-white/20 shadow-2xl backdrop-blur-md transition-transform duration-500 group-hover:rotate-0"
                    >
                        <Clock size={48} class="text-white" />
                    </div>
                    <div class="space-y-4 text-center">
                        <h2 class="text-5xl font-bold tracking-[0.2em] uppercase">
                            Menunggu Persetujuan
                        </h2>
                        <p class="text-lg font-bold tracking-widest text-amber-100 uppercase">
                            Akun Anda Sedang Ditinjau Oleh Administrator
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-12 p-16 text-center">
                <p
                    class="mx-auto max-w-2xl text-lg leading-relaxed font-bold tracking-widest text-slate-500 uppercase"
                >
                    Terima kasih telah mendaftar sebagai Dosen. Kami sedang melakukan verifikasi
                    data Anda. Notifikasi akan dikirimkan ke email Anda setelah proses selesai.
                </p>

                <div class="pt-4">
                    <Button
                        id="logout-btn"
                        onclick={() => state.logout()}
                        variant="secondary"
                        class="px-12 py-5 shadow-xl shadow-slate-200"
                        icon={LogOut}
                    >
                        KELUAR DARI AKUN
                    </Button>
                </div>
            </div>
        </Card>
    </div>
</App>
