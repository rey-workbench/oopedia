<script lang="ts">
    import { onMount } from 'svelte';
    import { tweened } from 'svelte/motion';
    import { cubicOut, backOut } from 'svelte/easing';

    const brands = [
        { name: 'JTI', image: '/images/landing/jti.png' },
        { name: 'Polinema', image: '/images/landing/polinema.png' },
    ];

    let element: HTMLElement;
    const opacity = tweened(0, { duration: 800, easing: cubicOut });
    const translateY = tweened(50, { duration: 1000, easing: backOut });

    onMount(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        opacity.set(1);
                        translateY.set(0);
                        animateLogos();
                    }
                });
            },
            { threshold: 0.2 }
        );
        observer.observe(element);
        return () => observer.disconnect();
    });

    const animateLogos = () => {
        const logos = element.querySelectorAll('[data-logo]');
        logos.forEach((logo, i) => {
            const el = logo as HTMLElement;
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px) scale(0.8)';
            setTimeout(() => {
                el.style.transition = `all 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) ${i * 150}ms`;
                el.style.opacity = '1';
                el.style.transform = 'translateY(0) scale(1)';
            }, 300);
        });
    };
</script>

<section
    bind:this={element}
    class="bg-white px-6 py-24"
    style="opacity: {$opacity}; transform: translateY({$translateY}px);"
>
    <div class="mx-auto max-w-7xl">
        <div class="mb-16 text-center">
            <p class="mb-4 text-[9px] font-black tracking-[0.3em] text-slate-900/40 uppercase">
                Dipercaya oleh
            </p>
            <h2 class="font-serif text-4xl leading-tight tracking-tight text-slate-900 md:text-5xl">
                Institusi terkemuka.
            </h2>
        </div>

        <div class="flex flex-wrap justify-center gap-12 md:gap-20">
            {#each brands as brand}
                <div
                    data-logo
                    class="flex items-center justify-center p-6"
                >
                    <img
                        src={brand.image}
                        alt={brand.name}
                        class="h-16 w-auto object-contain opacity-40 grayscale transition-all duration-500 hover:opacity-60 md:h-24"
                    />
                </div>
            {/each}
        </div>
    </div>
</section>
