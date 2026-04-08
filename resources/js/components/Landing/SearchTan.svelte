<script lang="ts">
    import { onMount } from 'svelte';
    import { tweened } from 'svelte/motion';
    import { cubicOut, elasticOut } from 'svelte/easing';

    let element: HTMLElement;

    const containerOpacity = tweened(0, { duration: 600, easing: cubicOut });
    const containerScale = tweened(0.8, { duration: 1000, easing: elasticOut });

    onMount(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        containerOpacity.set(1);
                        containerScale.set(1);
                        animateItems();
                    }
                });
            },
            { threshold: 0.2 }
        );
        observer.observe(element);
        return () => observer.disconnect();
    });

    const animateItems = () => {
        const gridItems = element.querySelectorAll('[data-animate]');
        gridItems.forEach((item, i) => {
            const el = item as HTMLElement;
            el.style.opacity = '0';
            el.style.transform = 'translateY(40px) rotate(-5deg)';
            setTimeout(() => {
                el.style.transition = `all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) ${i * 100}ms`;
                el.style.opacity = '1';
                el.style.transform = 'translateY(0) rotate(0deg)';
            }, 200);
        });
    };
</script>

<section bind:this={element} class="mx-auto mb-40 flex max-w-7xl flex-col items-center px-6">
    <div
        class="relative flex aspect-video w-full max-w-4xl justify-center overflow-hidden rounded-2xl border-2 border-black/10 border-b-6 bg-[#EECA9D] py-16 transition-all duration-1000"
        style="opacity: {$containerOpacity}; transform: scale({$containerScale});"
    >
        <div class="grid w-[80%] grid-cols-2 gap-8 md:grid-cols-4">
            <div
                data-animate
                class="aspect-4/5 w-full overflow-hidden rounded-2xl border-2 border-black/5 border-b-4 bg-stone-800/20 transition-all hover:scale-105"
            >
                <img
                    src="/images/landing/abstract3.png"
                    alt=""
                    aria-hidden="true"
                    role="presentation"
                    class="h-full w-full object-cover opacity-60 mix-blend-multiply"
                />
            </div>
            <div
                data-animate
                class="mt-8 aspect-square w-full overflow-hidden rounded-2xl border-2 border-black/5 border-b-4 bg-stone-700/30 transition-all hover:scale-105"
            >
                <img
                    src="/images/landing/abstract4.png"
                    alt=""
                    aria-hidden="true"
                    role="presentation"
                    class="h-full w-full object-cover opacity-70 mix-blend-multiply"
                />
            </div>
            <div
                data-animate
                class="aspect-4/5 w-full overflow-hidden rounded-2xl border-2 border-black/5 border-b-4 bg-stone-900/10 transition-all hover:scale-105"
            >
                <img
                    src="/images/landing/abstract1.png"
                    alt=""
                    aria-hidden="true"
                    role="presentation"
                    class="h-full w-full object-cover opacity-50 mix-blend-multiply"
                />
            </div>
            <div
                data-animate
                class="mt-12 aspect-square w-full overflow-hidden rounded-2xl border-2 border-black/5 border-b-4 bg-stone-800/40 transition-all hover:scale-105"
            >
                <img
                    src="/images/landing/abstract2.png"
                    alt=""
                    aria-hidden="true"
                    role="presentation"
                    class="h-full w-full object-cover opacity-80 mix-blend-multiply"
                />
            </div>
        </div>
    </div>

    <p class="mt-8 text-[10px] font-black tracking-[0.2em] text-slate-900 uppercase">
        berdasarkan kesamaan visual
    </p>
</section>
