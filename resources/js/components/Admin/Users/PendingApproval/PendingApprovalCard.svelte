<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { Clock, LogOut } from "lucide-svelte";
    import { PendingUsersState } from "@/states/Admin/UserState.svelte";

    const state = new PendingUsersState();
</script>

<Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl group">
    <div class="bg-amber-400 p-20 text-white relative">
        <div
            class="absolute -top-12 -right-12 w-48 h-48 bg-white/20 rounded-full blur-3xl group-hover:bg-white/30 transition-all duration-1000"
        ></div>

        <div class="relative z-10 space-y-8">
            <div
                class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-[2.5rem] flex items-center justify-center mx-auto shadow-2xl border border-white/30 transform rotate-12 transition-transform group-hover:rotate-0 duration-500"
            >
                <Clock size={48} class="text-white" />
            </div>
            <div class="space-y-4 text-center">
                <h2 class="text-5xl font-bold tracking-[0.2em] uppercase">
                    Menunggu Persetujuan
                </h2>
                <p
                    class="text-amber-100 text-lg font-bold tracking-widest uppercase"
                >
                    Akun Anda Sedang Ditinjau Oleh Administrator
                </p>
            </div>
        </div>
    </div>

    <div class="p-16 space-y-12 text-center">
        <p
            class="text-slate-500 text-lg leading-relaxed font-bold uppercase tracking-widest max-w-2xl mx-auto"
        >
            Terima kasih telah mendaftar sebagai Dosen. Kami sedang melakukan
            verifikasi data Anda. Notifikasi akan dikirimkan ke email Anda
            setelah proses selesai.
        </p>

        <div class="pt-4">
            <Button
                on:click={() => state.logout()}
                variant="secondary"
                class="px-12 py-5 shadow-xl shadow-slate-200"
                icon={LogOut}
            >
                KELUAR DARI AKUN
            </Button>
        </div>
    </div>
</Card>
