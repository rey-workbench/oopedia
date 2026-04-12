<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { CheckCircle2, ArrowRight, LayoutDashboard } from 'lucide-svelte';
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
</script>

<App title="Terima Kasih - MSLQ">
    <div class="flex min-h-[70vh] items-center justify-center pb-20">
        <div id="thankyou-container" class="w-full max-w-2xl px-6">
            <Card class="overflow-hidden rounded-[3rem] border-slate-100 p-12 text-center shadow-2xl">
                <div class="mb-8 flex justify-center">
                    <div class="bg-emerald-50 text-emerald-500 inline-flex h-24 w-24 items-center justify-center rounded-3xl shadow-inner shadow-emerald-100">
                        <CheckCircle2 size={48} />
                    </div>
                </div>

                <h1 class="mb-4 text-3xl font-black tracking-tight text-slate-900 uppercase">
                    Data Berhasil <span class="text-emerald-500">Disimpan!</span>
                </h1>
                
                <p class="mx-auto mb-12 max-w-md text-slate-500 font-medium leading-relaxed">
                    Terima kasih telah meluangkan waktu untuk mengisi kuesioner MSLQ. Data Anda sangat berharga bagi penelitian dan pengembangan platform ini.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 border-t border-slate-50 pt-10">
                    <Link href={ROUTES.MAHASISWA.DASHBOARD} class="w-full sm:w-auto">
                        <Button variant="primary" icon={LayoutDashboard} class="w-full sm:w-auto h-14 px-8 rounded-2xl shadow-lg shadow-indigo-900/20">
                            KE DASHBOARD
                        </Button>
                    </Link>
                    <Link href={ROUTES.MAHASISWA.MATERIALS.INDEX} class="w-full sm:w-auto">
                        <Button variant="outline" icon={ArrowRight} class="w-full sm:w-auto h-14 px-8 rounded-2xl">
                            LANJUT BELAJAR
                        </Button>
                    </Link>
                </div>
            </Card>
        </div>
    </div>
</App>
