<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { Disc2, ArrowRight } from 'lucide-svelte';
</script>

<section class="relative min-h-screen w-full overflow-hidden bg-[#0A0A0A]">
    <div
        class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(210,109,63,0.24),transparent_45%),radial-gradient(circle_at_80%_80%,rgba(174,192,173,0.16),transparent_50%)]"
    ></div>

    <img
        src="/images/landing/abstract3.png"
        alt=""
        aria-hidden="true"
        role="presentation"
        class="absolute inset-0 h-full w-full scale-150 object-cover opacity-25 mix-blend-luminosity blur-md"
    />

    <div
        class="relative z-10 flex min-h-screen flex-col items-center justify-center gap-16 px-6 py-32"
    >
        <div class="flex flex-col items-center text-center">
            <p class="mb-4 text-[9px] font-black tracking-[0.3em] text-[#E2885F] uppercase">
                dengan Kecerdasan Buatan
            </p>
            <h2
                class="max-w-3xl font-serif text-4xl leading-[1.02] tracking-tight text-white md:text-6xl"
            >
                Temukan pola arsitektur<br />berdasarkan konsep untuk...
            </h2>
        </div>

        <div class="w-full max-w-md">
            <div
                class="rounded-3xl border-2 border-slate-200 border-b-8 bg-white p-8"
            >
                <div class="mb-6 flex items-center gap-3">
                    <Disc2 size={14} class="text-slate-900" />
                    <span class="text-[10px] font-black tracking-[0.2em] text-slate-900 uppercase"
                        >OOPEDIA AI</span
                    >
                </div>

                <div
                    class="mb-6 flex min-h-[80px] items-center justify-center rounded-xl border-2 border-slate-100 bg-slate-50 p-4"
                >
                    <p class="font-serif text-sm text-slate-400">
                        Ketik pertanyaanmu di sini...
                    </p>
                </div>

                <div class="flex gap-4">
                    <button
                        type="button"
                        class="flex-1 rounded-2xl border-2 border-black border-b-4 bg-slate-900 py-3.5 text-[10px] font-black tracking-[0.15em] text-white uppercase transition-all hover:bg-slate-800 active:translate-y-[2px] active:border-b-2"
                    >
                        Cari
                    </button>
                    <button
                        type="button"
                        class="flex-1 rounded-2xl border-2 border-slate-200 border-b-4 bg-white py-3.5 text-[10px] font-black tracking-[0.15em] uppercase transition-all hover:bg-slate-50 active:translate-y-[2px] active:border-b-2"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center text-center">
            <p class="mb-6 text-[11px] font-medium tracking-wide text-white/40">
                Atau mulai belajar sekarang
            </p>
            <Link
                href={ROUTES.AUTH.REGISTER}
                class="group inline-flex items-center gap-3 rounded-2xl border-2 border-white border-b-6 bg-white px-10 py-4 text-[11px] font-black tracking-[0.2em] text-slate-900 uppercase transition-all hover:bg-slate-50 active:translate-y-[2px] active:border-b-4"
            >
                <span>Daftar Sekarang</span>
                <ArrowRight size={16} class="transition-transform group-hover:translate-x-1" />
            </Link>
        </div>
    </div>
</section>
