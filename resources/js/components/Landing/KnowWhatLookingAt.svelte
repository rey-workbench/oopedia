<script lang="ts">
    import ScrollReveal from './ScrollReveal.svelte';
</script>

<ScrollReveal>
    <section class="mx-auto mb-[15vh] flex max-w-7xl flex-col items-center px-6 text-center">
        <h2 class="mb-16 font-serif text-[2.5rem] tracking-tighter text-black">
            Ketahui apa yang<br />Anda pelajari.
        </h2>

        <div class="group relative mt-4 inline-block">
            <div
                class="relative z-0 aspect-4/5 w-[300px] overflow-hidden rounded-sm bg-[#E3E1DE] shadow-[0_20px_50px_rgba(0,0,0,0.1)] transition-transform duration-1000 group-hover:scale-105"
            >
                <img
                    src="/images/landing/abstract4.png"
                    alt=""
                    aria-hidden="true"
                    role="presentation"
                    class="h-full w-full object-cover opacity-[0.85] mix-blend-multiply contrast-125 filter"
                />
            </div>

            <div
                class="absolute top-[50%] left-[70%] z-10 max-w-[200px] translate-y-4 transform rounded-[12px] border border-white/5 bg-[#1A1A1A]/90 p-4 text-left text-[10px] leading-relaxed font-medium text-white/90 opacity-0 shadow-2xl backdrop-blur-xl transition-all delay-100 duration-700 group-hover:translate-y-0 group-hover:opacity-100"
            >
                OOPedia dapat mengidentifikasi paradigma atau pola desain yang sedang Anda pelajari.
            </div>
        </div>
    </section>
</ScrollReveal>
