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
                class="border-duo overflow-hidden rounded-[3rem] border-slate-100 p-12 text-center shadow-xl md:p-16"
            >
                <div class="mb-8 flex justify-center">
                    <div
                        class="inline-flex h-24 w-24 items-center justify-center rounded-full border-[8px] border-emerald-50 bg-emerald-100 text-emerald-500"
                    >
                        <CheckCircle2 size={40} strokeWidth={2.5} />
                    </div>
                </div>

                <h1
                    class="font-display mb-4 text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl"
                >
                    Data Berhasil <span class="text-emerald-500">Disimpan!</span>
                </h1>

                <p class="mx-auto mb-10 max-w-md text-base leading-relaxed text-slate-600">
                    Terima kasih telah meluangkan waktu untuk mengisi kuesioner MSLQ. Data Anda sangat berharga bagi platform ini.
                </p>

                <div
                    class="flex flex-col items-center justify-center gap-4 border-t border-slate-100 pt-10 sm:flex-row"
                >
                    <Link href={ROUTES.MAHASISWA.DASHBOARD} class="w-full sm:w-auto">
                        <Button
                            variant="secondary"
                            icon={LayoutDashboard}
                            class="w-full sm:w-auto"
                        >
                            Ke Dashboard
                        </Button>
                    </Link>
                    <Link href={ROUTES.MAHASISWA.MATERIALS.INDEX} class="w-full sm:w-auto">
                        <Button
                            variant="primary"
                            icon={ArrowRight}
                            class="w-full sm:w-auto shadow-lg shadow-primary-500/20"
                        >
                            Lanjut Belajar
                        </Button>
                    </Link>
                </div>
            </Card>
        </div>
    </div>
</App>
