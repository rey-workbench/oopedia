<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { CheckCircle2, ArrowRight, LayoutDashboard } from '@lucide/svelte';
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
</script>

<App title="Terima Kasih - MSLQ">
    <div class="flex min-h-[70vh] items-center justify-center pb-20">
        <div id="thankyou-container" class="w-full max-w-2xl px-6">
            <Card
                padding="p-0"
                class="border-duo overflow-hidden rounded-[4rem] border-slate-100 p-16 text-center shadow-2xl"
            >
                <div class="mb-10 flex justify-center">
                    <div
                        class="inline-flex h-28 w-28 animate-bounce items-center justify-center rounded-[2.5rem] border-2 border-emerald-100 bg-emerald-50 text-emerald-500 shadow-inner"
                    >
                        <CheckCircle2 size={48} strokeWidth={3} />
                    </div>
                </div>

                <h1
                    class="text-primary-500 font-display mb-6 text-4xl font-black tracking-[0.1em] uppercase"
                >
                    Data Berhasil <span class="text-accent-500">Disimpan!</span>
                </h1>

                <p
                    class="mx-auto mb-12 max-w-md text-lg leading-relaxed font-bold tracking-wide text-slate-400 uppercase"
                >
                    Terima kasih telah meluangkan waktu untuk mengisi kuesioner MSLQ. Data Anda
                    sangat berharga bagi platform ini.
                </p>

                <div
                    class="flex flex-col items-center justify-center gap-6 border-t-2 border-slate-50 pt-12 sm:flex-row"
                >
                    <Link href={ROUTES.MAHASISWA.DASHBOARD} class="w-full sm:w-auto">
                        <Button
                            variant="primary"
                            icon={LayoutDashboard}
                            size="lg"
                            class="border-duo-lg h-20 w-full px-12 active:translate-y-1 sm:w-auto"
                        >
                            KE DASHBOARD
                        </Button>
                    </Link>
                    <Link href={ROUTES.MAHASISWA.MATERIALS.INDEX} class="w-full sm:w-auto">
                        <Button
                            variant="secondary"
                            icon={ArrowRight}
                            size="lg"
                            class="border-duo-lg h-20 w-full px-12 active:translate-y-1 sm:w-auto"
                        >
                            LANJUT BELAJAR
                        </Button>
                    </Link>
                </div>
            </Card>
        </div>
    </div>
</App>
