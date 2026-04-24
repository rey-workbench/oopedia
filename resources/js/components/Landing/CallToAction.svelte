<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { ArrowRight, Sparkles } from 'lucide-svelte';
    import { scrollReveal } from '@/utils/animations';
    import { Motion } from 'svelte-motion';
</script>

<section class="relative overflow-hidden py-32 sm:py-48">
    <div class="relative z-10 mx-auto max-w-6xl px-6">
        <div 
            use:scrollReveal={{ y: 80, scale: 0.95 }}
            class="relative flex flex-col items-center overflow-hidden rounded-[4rem] border-2 border-slate-950/5 bg-white/40 p-12 text-center backdrop-blur-3xl shadow-2xl md:p-24"
        >
            <!-- Decorative Accents inside card -->
            <div class="pointer-events-none absolute inset-0 -z-10 opacity-30">
                <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-[#D26D3F]/20 blur-[100px]"></div>
                <div class="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-[#AEC0AD]/20 blur-[100px]"></div>
            </div>

            <div class="mb-10 inline-flex items-center gap-3 rounded-full border border-slate-950/5 bg-slate-950/5 px-6 py-2.5 text-[10px] font-black tracking-[0.3em] text-slate-900/40 uppercase">
                <Sparkles size={14} class="text-[#D26D3F]" />
                Mulai Masa Depan Belajar Anda
            </div>

            <h2 class="mb-10 font-display text-[2.5rem] leading-[1.1] font-black tracking-tight text-slate-900 sm:text-[4rem] md:text-[5rem]">
                Siap untuk menguasai<br />
                <span class="text-[#D26D3F]">Paradigma Modern?</span>
            </h2>

            <p class="mb-16 max-w-2xl text-lg font-medium text-slate-900/40 md:text-xl">
                Bergabunglah dengan ribuan pembelajar lainnya dan mulai petualangan 
                interaktif Anda dalam dunia Object-Oriented Programming hari ini.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-8">
                <Motion whileHover={{ scale: 1.05 }} whileTap={{ scale: 0.95 }} let:motion>
                    <div use:motion>
                        <Link
                            href={ROUTES.AUTH.REGISTER}
                            class="group flex items-center gap-4 rounded-full border-2 border-b-8 border-slate-950 bg-slate-900 px-12 py-6 text-sm font-black tracking-widest text-white uppercase transition-all hover:bg-slate-800 active:translate-y-[4px] active:border-b-4 shadow-xl"
                        >
                            <span>Daftar Gratis</span>
                            <ArrowRight size={20} class="transition-transform group-hover:translate-x-2" />
                        </Link>
                    </div>
                </Motion>

                <Link
                    href={ROUTES.AUTH.LOGIN}
                    class="rounded-full px-8 py-4 text-xs font-black tracking-widest text-slate-900/40 uppercase transition-colors hover:text-slate-900"
                >
                    Masuk ke Akun
                </Link>
            </div>
        </div>
    </div>
</section>


