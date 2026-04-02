<script lang="ts">
    import { Search } from 'lucide-svelte';
</script>

<section class="mx-auto mb-40 flex max-w-7xl flex-col items-center px-6">
    <h2 class="mb-10 font-serif text-4xl tracking-tighter text-black">
        Setiap pencarian membuka<br />dunia baru dalam OOP.
    </h2>

    <div
        class="group relative flex aspect-[16/9] w-full max-w-4xl items-center justify-center overflow-hidden rounded-xl bg-[#AEC0AD] shadow-lg"
    >
        <div
            class="absolute top-10 aspect-video w-48 rounded bg-black/10 opacity-70 shadow-2xl blur-[2px] transition-transform duration-1000 group-hover:-translate-y-4"
        >
            <img
                src="/images/landing/abstract2.png"
                alt=""
                class="h-full w-full object-cover opacity-80"
            />
        </div>
        <div
            class="absolute bottom-10 aspect-video w-48 rounded bg-black/20 opacity-70 shadow-2xl blur-[2px] transition-transform duration-1000 group-hover:translate-y-4"
        >
            <img
                src="/images/landing/abstract3.png"
                alt=""
                class="h-full w-full object-cover opacity-80 mix-blend-multiply"
            />
        </div>

        <div
            class="absolute z-10 flex aspect-video w-72 items-center justify-center overflow-hidden rounded-lg bg-white/50 shadow-[0_30px_60px_rgba(0,0,0,0.15)] transition-transform duration-1000 group-hover:scale-105"
        >
            <img
                src="/images/landing/abstract4.png"
                alt=""
                class="absolute inset-0 h-full w-full object-cover opacity-40 mix-blend-darken"
            />
            <div
                class="relative z-20 flex items-center gap-3 rounded-full border border-white/20 bg-black/40 px-5 py-2.5 text-[9px] font-black tracking-[0.2em] text-white shadow-2xl backdrop-blur-xl"
            >
                <Search size={12} class="opacity-70" />
                <span class="uppercase">polanya</span>
            </div>
        </div>
    </div>

    <p class="mt-8 max-w-md text-center text-[11px] font-medium tracking-[0.02em] text-black/50">
        Jelajahi berbagai paradigma dan pola desain OOP<br />
        <span class="opacity-60">dengan cara yang lebih personal.</span>
    </p>
</section>
