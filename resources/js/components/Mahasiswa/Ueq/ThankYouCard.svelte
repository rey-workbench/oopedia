<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { Heart, Home } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
</script>

<div class="py-24 flex items-center justify-center min-h-[70vh]">
    <div class="max-w-2xl w-full px-4 text-center">
        <Card
            padding="p-0"
            class="overflow-hidden border-slate-100 shadow-2xl rounded-[3.5rem] group"
        >
            <div class="bg-primary-600 p-20 text-white relative">
                <div
                    class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-1000"
                ></div>

                <div class="relative z-10 space-y-8">
                    <div
                        class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-[2.5rem] flex items-center justify-center mx-auto shadow-2xl border border-white/30 transform rotate-12 transition-transform group-hover:rotate-0 duration-500"
                    >
                        <Heart size={48} class="text-white fill-current" />
                    </div>
                    <div class="space-y-4">
                        <h2
                            class="text-5xl font-bold tracking-[0.2em] uppercase"
                        >
                            Terima Kasih!
                        </h2>
                        <p
                            class="text-primary-100 text-lg font-bold tracking-widest uppercase"
                        >
                            Evaluasi Anda Berhasil Kami Amankan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-16 space-y-12">
                <p
                    class="text-slate-500 text-lg leading-relaxed font-bold uppercase tracking-widest"
                >
                    Setiap bit informasi yang Anda berikan adalah navigasi bagi
                    kami untuk membangun <span class="text-primary-600"
                        >Arsitektur Pembelajaran</span
                    > yang lebih sempurna.
                </p>

                <div class="pt-4">
                    <Button
                        href={ROUTES.MAHASISWA.DASHBOARD}
                        variant="primary"
                        class="px-12 py-5 shadow-xl shadow-primary-900/20"
                        icon={Home}
                    >
                        KEMBALI KE DASHBOARD
                    </Button>
                </div>
            </div>
        </Card>
    </div>
</div>
