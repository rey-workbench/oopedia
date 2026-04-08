<script lang="ts">
    import { onMount } from 'svelte';
    import { tweened } from 'svelte/motion';
    import { cubicOut, backOut } from 'svelte/easing';

    let element: HTMLElement;
    const opacity = tweened(0, { duration: 800, easing: cubicOut });
    const translateX = tweened(-60, { duration: 1000, easing: backOut });
    const rotate = tweened(-5, { duration: 1000, easing: cubicOut });

    onMount(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        opacity.set(1);
                        translateX.set(0);
                        rotate.set(0);
                    }
                });
            },
            { threshold: 0.2 }
        );
        observer.observe(element);
        return () => observer.disconnect();
    });
</script>

<section
    bind:this={element}
    class="mx-auto mb-40 flex max-w-7xl flex-col items-center px-6"
    style="opacity: {$opacity}; transform: translateX({$translateX}px) rotate({$rotate}deg);"
>
    <h2
        class="mb-10 max-w-sm text-center font-serif text-[2.5rem] leading-[0.95] tracking-tighter text-slate-900"
    >
        Cari sesuai cara<br />berpikir Anda.
    </h2>

    <div
        class="group relative flex aspect-2/1 w-full max-w-4xl items-center justify-center gap-[10%] overflow-hidden rounded-2xl border-2 border-black/10 border-b-6 bg-[#C1583D]"
    >
        <div
            class="relative z-10 aspect-3/4 w-[30%] overflow-hidden rounded-2xl border-2 border-orange-900/30 border-b-4 bg-orange-950/60 transition-transform duration-700 hover:scale-110 hover:rotate-3"
        >
            <img
                src="/images/landing/abstract1.png"
                alt=""
                aria-hidden="true"
                role="presentation"
                class="h-full w-full object-cover opacity-70 mix-blend-multiply"
            />
        </div>
        <div
            class="relative z-10 mb-10 aspect-2/3 w-[20%] overflow-hidden rounded-2xl border-2 border-red-900/30 border-b-4 bg-red-950/80 transition-transform delay-100 duration-700 hover:scale-110 hover:-rotate-3"
        >
            <img
                src="/images/landing/abstract2.png"
                alt=""
                aria-hidden="true"
                role="presentation"
                class="h-full w-full object-cover opacity-90 mix-blend-multiply"
            />
        </div>

        <div class="absolute top-1/2 left-1/2 z-20 -translate-x-[60%] -translate-y-1/2">
            <div
                class="flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-white/20 border-b-4 bg-[#A43B25] px-8 py-4 text-[11px] font-black tracking-[0.2em] text-white uppercase transition-all duration-300 hover:scale-110 active:translate-y-[2px] active:border-b-2"
            >
                <div
                    class="h-3 w-3 animate-pulse rounded-full border-2 border-[#EB8E78] bg-[#EB8E78]"
                ></div>
                ARSip
            </div>
        </div>
    </div>

    <p class="mt-8 text-[10px] font-black tracking-[0.2em] text-slate-900 uppercase">
        berdasarkan paradigma
    </p>
</section>
