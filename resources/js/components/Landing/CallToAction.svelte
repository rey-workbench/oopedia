<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { Disc2, ArrowRight } from 'lucide-svelte';
</script>

<section class="relative min-h-screen w-full overflow-hidden bg-[#0A0A0A]">
    <img
        src="/images/landing/abstract3.png"
        alt=""
        aria-hidden="true"
        role="presentation"
        class="absolute inset-0 h-full w-full scale-150 object-cover opacity-30 mix-blend-luminosity blur-md"
    />

    <div
        class="relative z-10 flex min-h-screen flex-col items-center justify-center gap-16 px-6 py-32"
    >
        <div class="flex flex-col items-center text-center">
            <p class="mb-4 text-[9px] font-black tracking-[0.3em] text-[#D26D3F] uppercase">
                dengan Kecerdasan Buatan
            </p>
            <h2
                class="max-w-2xl font-serif text-4xl leading-tight tracking-tight text-white md:text-6xl"
            >
                Temukan pola arsitektur<br />berdasarkan konsep untuk...
            </h2>
        </div>

        <div class="w-full max-w-md">
            <div class="rounded-2xl bg-[#FDFDFB] p-8 shadow-[0_40px_80px_rgba(0,0,0,0.5)]">
                <div class="mb-6 flex items-center gap-3">
                    <Disc2 size={14} class="text-black" />
                    <span class="text-[10px] font-black tracking-[0.2em] text-black uppercase"
                        >OOPEDIA AI</span
                    >
                </div>

                <div
                    class="mb-6 flex min-h-[80px] items-center justify-center rounded-xl bg-[#F5F5F3] p-4"
                >
                    <p class="font-serif text-sm text-black/50">Ketik pertanyaanmu di sini...</p>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        class="flex-1 rounded-full bg-[#111] py-3.5 text-[10px] font-black tracking-[0.15em] text-white uppercase transition-colors hover:bg-black"
                    >
                        Cari
                    </button>
                    <button
                        type="button"
                        class="flex-1 rounded-full border border-black/10 bg-white py-3.5 text-[10px] font-black tracking-[0.15em] uppercase transition-colors hover:border-black/30"
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
                class="group inline-flex items-center gap-3 rounded-full bg-white px-10 py-4 text-[11px] font-black tracking-[0.2em] text-black uppercase shadow-[0_20px_50px_rgba(0,0,0,0.4)] transition-all hover:scale-105 active:scale-95"
            >
                <span>Daftar Sekarang</span>
                <ArrowRight size={16} class="transition-transform group-hover:translate-x-1" />
            </Link>
        </div>
    </div>
</section>
