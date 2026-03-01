<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Heart, Home } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
</script>

<App title="Terima Kasih">
    <div class="flex min-h-[70vh] items-center justify-center py-24">
        <div class="w-full max-w-2xl px-4 text-center">
            <Card
                padding="p-0"
                class="group overflow-hidden rounded-[3.5rem] border-slate-100 shadow-2xl"
            >
                <div class="bg-primary-600 relative p-20 text-white">
                    <div
                        class="absolute -top-12 -right-12 h-48 w-48 rounded-full bg-white/10 blur-3xl transition-all duration-1000 group-hover:bg-white/20"
                    ></div>

                    <div class="relative z-10 space-y-8">
                        <div
                            class="mx-auto flex h-24 w-24 rotate-12 transform items-center justify-center rounded-[2.5rem] border border-white/30 bg-white/20 shadow-2xl backdrop-blur-md transition-transform duration-500 group-hover:rotate-0"
                        >
                            <Heart size={48} class="fill-current text-white" />
                        </div>
                        <div class="space-y-4">
                            <h2 class="text-5xl font-bold tracking-[0.2em] uppercase">
                                Terima Kasih!
                            </h2>
                            <p class="text-primary-100 text-lg font-bold tracking-widest uppercase">
                                Evaluasi Anda Berhasil Kami Amankan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-12 p-16">
                    <p
                        class="text-lg leading-relaxed font-bold tracking-widest text-slate-500 uppercase"
                    >
                        Setiap bit informasi yang Anda berikan adalah navigasi bagi kami untuk
                        membangun
                        <span class="text-primary-600"> Arsitektur Pembelajaran</span> yang lebih sempurna.
                    </p>

                    <div class="pt-4">
                        <Button
                            href={ROUTES.MAHASISWA.DASHBOARD}
                            variant="primary"
                            class="shadow-primary-900/20 px-12 py-5 shadow-xl"
                            icon={Home}
                        >
                            KEMBALI KE DASHBOARD
                        </Button>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</App>
