<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { ShieldCheck, Lock } from "lucide-svelte";
</script>

<div class="space-y-8">
    <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
        Status Akun
    </h3>

    <Card padding="p-8" class="space-y-6">
        <div class="flex items-start gap-4">
            <div
                class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0"
            >
                <ShieldCheck size={20} />
            </div>
            <div>
                <h4
                    class="font-bold text-slate-900 text-sm uppercase tracking-wider"
                >
                    Identitas
                </h4>
                <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                    Informasi utama profil Anda yang terlihat oleh dosen dan
                    sistem.
                </p>
            </div>
        </div>

        <div class="flex items-start gap-4 pt-6 border-t border-slate-50">
            <div
                class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"
            >
                <Lock size={20} />
            </div>
            <div>
                <h4
                    class="font-bold text-slate-900 text-sm uppercase tracking-wider"
                >
                    Keamanan
                </h4>
                <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                    Update password secara berkala untuk menjaga keamanan akun
                    Anda.
                </p>
            </div>
        </div>
    </Card>

    <div
        class="p-8 rounded-[2rem] bg-slate-900 text-white relative overflow-hidden group"
    >
        <div
            class="absolute -top-10 -right-10 w-32 h-32 bg-primary-500/10 rounded-full blur-2xl transition-transform duration-1000"
        ></div>
        <h4 class="text-lg font-bold tracking-widest mb-4 uppercase">
            Butuh Bantuan?
        </h4>
        <p class="text-slate-400 text-xs font-medium mb-6 leading-relaxed">
            Jika Anda mengalami kendala pada akun, silakan hubungi tim
            administrator Oopedia.
        </p>
        <Button
            variant="secondary"
            size="sm"
            class="w-full font-bold uppercase tracking-widest"
            >Hubungi Admin</Button
        >
    </div>
</div>
