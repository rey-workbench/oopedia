<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { spring } from 'svelte/motion';
    import { Motion } from 'svelte-motion';
    import FloatingItem from '@/components/ui/FloatingItem.svelte';
    import { ArrowRight, Sparkles, MousePointer2 } from 'lucide-svelte';

    let windowWidth = $state(1920);
    let windowHeight = $state(1080);

    const parallax = spring({ x: 0, y: 0 }, { stiffness: 0.05, damping: 0.5 });

    const handleMousemove = (e: MouseEvent) => {
        const x = (e.clientX / windowWidth) * 2 - 1;
        const y = (e.clientY / windowHeight) * 2 - 1;
        parallax.set({ x, y });
    };

    const floatingDecor = [
        {
            image: '/images/landing/abstract1.png',
            top: '8%',
            left: '4%',
            width: '150px',
            depth: 40,
            rotation: 12,
            blur: 'blur-[2px]',
        },
        {
            image: '/images/landing/abstract2.png',
            top: '12%',
            right: '6%',
            width: '130px',
            depth: -35,
            rotation: -18,
            blur: 'blur-[1px]',
        },
        {
            image: '/images/landing/abstract3.png',
            bottom: '15%',
            left: '8%',
            width: '180px',
            depth: 25,
            rotation: 35,
            blur: 'blur-[3px]',
        },
        {
            image: '/images/landing/abstract4.png',
            bottom: '10%',
            right: '10%',
            width: '140px',
            depth: 55,
            rotation: -12,
        },
        {
            image: '/images/landing/abstract2.png',
            top: '45%',
            right: '4%',
            width: '60px',
            depth: 70,
            rotation: 25,
            opacity: 0.6,
        },
        {
            image: '/images/landing/abstract1.png',
            top: '55%',
            left: '2%',
            width: '80px',
            depth: -50,
            rotation: -10,
            opacity: 0.4,
            blur: 'blur-md',
        },
    ];

    const title = 'Ruang belajar Anda menguasai OOP';
    const words = title.split(' ');
</script>

<svelte:window
    bind:innerWidth={windowWidth}
    bind:innerHeight={windowHeight}
    onmousemove={handleMousemove}
/>

<section
    class="relative -mt-16 flex min-h-screen flex-col items-center justify-center overflow-hidden bg-transparent px-6 text-center"
>
    <!-- Floating Decorative Items -->
    {#each floatingDecor as item, i (i)}
        <FloatingItem
            {...item}
            parallaxX={$parallax.x}
            parallaxY={$parallax.y}
            delay={300 + i * 150}
        />
    {/each}

    <div class="relative z-10 flex max-w-7xl flex-col items-center pt-24">
        <!-- Floating Badge -->
        <Motion
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 1, delay: 0.2, ease: 'easeOut' }}
            let:motion
        >
            <div
                use:motion
                class="mb-10 flex items-center gap-3 rounded-full border border-slate-900/5 bg-white/40 px-5 py-2 text-[10px] font-black tracking-[0.25em] text-slate-900/50 uppercase shadow-sm backdrop-blur-md"
            >
                <div class="flex -space-x-2">
                    <div class="bg-accent-500 h-4 w-4 rounded-full border-2 border-white"></div>
                    <div class="bg-primary-500 h-4 w-4 rounded-full border-2 border-white"></div>
                </div>
                Platform Pembelajaran Modern
                <Sparkles size={12} class="text-accent-500 animate-pulse" />
            </div>
        </Motion>

        <!-- Staggered Character Headline Reveal -->
        <h1
            class="font-display mb-8 flex flex-wrap justify-center gap-x-[0.25em] px-4 text-center text-[3rem] leading-[0.9] font-black tracking-[-0.05em] text-slate-900 sm:text-[4.5rem] md:text-[6rem] lg:text-[7rem] xl:text-[8rem]"
        >
            {#each words as word, wi}
                <Motion
                    initial={{ opacity: 0, y: 60, filter: 'blur(12px)' }}
                    animate={{ opacity: 1, y: 0, filter: 'blur(0px)' }}
                    transition={{
                        duration: 0.9,
                        delay: 0.3 + wi * 0.12,
                        ease: [0.21, 0.47, 0.32, 0.98],
                    }}
                    let:motion
                >
                    <span use:motion class="inline-block">{word}</span>
                </Motion>
            {/each}
        </h1>

        <!-- Subtext Reveal with Highlight -->
        <Motion
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 1.2, delay: 1.4 }}
            let:motion
        >
            <p
                use:motion
                class="mb-14 max-w-3xl px-6 text-lg leading-relaxed font-medium text-slate-900/40 sm:text-xl"
            >
                Belajar paradigma, pola desain, dan arsitektur perangkat lunak dengan pengalaman
                interaktif yang terasa <span
                    class="relative inline-block font-bold text-slate-900 italic"
                >
                    personal
                    <span
                        class="bg-accent-500/20 absolute bottom-1 left-0 -z-10 h-2 w-full -rotate-1 rounded-sm"
                    ></span>
                </span> sejak sesi pertama.
            </p>
        </Motion>

        <!-- CTA Section with Premium Hover Effects -->
        <div class="flex flex-wrap items-center justify-center gap-8">
            <Motion
                whileHover={{ scale: 1.05, y: -4 }}
                whileTap={{ scale: 0.95 }}
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.5, delay: 1.6 }}
                let:motion
            >
                <div use:motion>
                    <Link
                        href={ROUTES.AUTH.REGISTER}
                        class="group relative flex items-center gap-4 rounded-full border-2 border-b-8 border-slate-950 bg-slate-900 px-12 py-6 text-[15px] font-black tracking-[0.15em] text-white uppercase shadow-2xl transition-colors hover:bg-slate-800 active:translate-y-[4px] active:border-b-4"
                    >
                        <span>Mulai Belajar</span>
                        <ArrowRight
                            size={20}
                            class="transition-transform group-hover:translate-x-2"
                        />
                    </Link>
                </div>
            </Motion>

            <Motion
                whileHover={{ scale: 1.05, y: -4 }}
                whileTap={{ scale: 0.95 }}
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.5, delay: 1.7 }}
                let:motion
            >
                <div use:motion>
                    <a
                        href="#fitur"
                        class="rounded-full border-2 border-b-8 border-slate-200 bg-white px-12 py-6 text-[15px] font-black tracking-[0.15em] text-slate-900 uppercase shadow-lg transition-colors hover:bg-slate-50 active:translate-y-[4px] active:border-b-4"
                    >
                        Eksplor Fitur
                    </a>
                </div>
            </Motion>
        </div>
    </div>
    <!-- Interactive Mouse Follower Hint -->
    <div
        class="pointer-events-none absolute right-12 bottom-12 z-10 hidden flex-col items-end gap-2 text-[10px] font-bold text-slate-900/10 lg:flex"
    >
        <MousePointer2 size={16} />
        <span class="tracking-widest">INTERAKTIF</span>
    </div>
</section>
