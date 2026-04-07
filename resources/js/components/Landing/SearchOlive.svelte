<script lang="ts">
    import { onMount } from 'svelte';
    import { tweened } from 'svelte/motion';
    import { cubicOut } from 'svelte/easing';
    import { Search } from 'lucide-svelte';

    let element: HTMLElement;

    const opacity = tweened(0, { duration: 800, easing: cubicOut });
    const translateY = tweened(80, { duration: 1000, easing: cubicOut });
    const scale = tweened(0.9, { duration: 1000, easing: cubicOut });

    onMount(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        opacity.set(1);
                        translateY.set(0);
                        scale.set(1);
                    }
                });
            },
            { threshold: 0.15 }
        );

        observer.observe(element);
        return () => observer.disconnect();
    });
</script>

<section
    bind:this={element}
    class="mx-auto mb-40 flex max-w-7xl flex-col items-center px-6"
    style="opacity: {$opacity}; transform: translateY({$translateY}px) scale({$scale});"
>
    <h2 class="mb-10 font-serif text-4xl tracking-tighter text-black md:text-5xl">
        Setiap pencarian membuka<br />dunia baru dalam OOP.
    </h2>

    <div
        class="group relative flex aspect-video w-full max-w-4xl items-center justify-center overflow-hidden rounded-2xl bg-[#AEC0AD] shadow-2xl transition-transform duration-700 hover:scale-[1.02]"
    >
        <div
            class="absolute top-8 aspect-video w-48 rounded-xl bg-black/10 shadow-2xl blur-[2px] transition-transform duration-1000 group-hover:translate-x-4 group-hover:-translate-y-6"
        >
            <img
                src="/images/landing/abstract2.png"
                alt=""
                class="h-full w-full object-cover opacity-80"
            />
        </div>
        <div
            class="absolute right-12 bottom-8 aspect-video w-48 rounded-xl bg-black/20 shadow-2xl blur-[2px] transition-transform duration-1000 group-hover:-translate-x-6 group-hover:-translate-y-4"
        >
            <img
                src="/images/landing/abstract3.png"
                alt=""
                class="h-full w-full object-cover opacity-80 mix-blend-multiply"
            />
        </div>

        <div
            class="absolute z-10 flex aspect-video w-72 items-center justify-center overflow-hidden rounded-2xl bg-white/60 shadow-[0_30px_60px_rgba(0,0,0,0.2)] backdrop-blur-sm transition-transform duration-700 group-hover:scale-110"
        >
            <img
                src="/images/landing/abstract4.png"
                alt=""
                class="absolute inset-0 h-full w-full object-cover opacity-40 mix-blend-darken"
            />
            <div
                class="relative z-20 flex items-center gap-3 rounded-full border border-white/30 bg-black/50 px-6 py-3 text-[10px] font-black tracking-[0.2em] text-white shadow-xl backdrop-blur-xl"
            >
                <Search size={14} class="opacity-70" />
                <span class="uppercase">polanya</span>
            </div>
        </div>
    </div>

    <p class="mt-12 max-w-md text-center text-[12px] font-medium tracking-wide text-black/50">
        Jelajahi berbagai paradigma dan pola desain OOP<br />
        <span class="opacity-60">dengan cara yang lebih personal.</span>
    </p>
</section>
