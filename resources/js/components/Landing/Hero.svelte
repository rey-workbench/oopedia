<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { fade } from 'svelte/transition';
    import { spring } from 'svelte/motion';
    import FloatingItem from '@/components/ui/FloatingItem.svelte';
    import { Play } from 'lucide-svelte';

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
            image: '/images/landing/abstract2.png',
            top: '2%',
            left: '2%',
            width: '120px',
            height: '140px',
            mobileWidth: '50px',
            mobileHeight: '60px',
            mobileZIndex: -1,
            rotation: 25,
            opacity: 0.9,
            blur: 'blur-[2px]',
            depth: -20,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract1.png',
            top: '15%',
            left: '18%',
            width: '130px',
            height: '130px',
            mobileWidth: '45px',
            mobileHeight: '45px',
            mobileZIndex: -1,
            rotation: 40,
            opacity: 1,
            blur: '',
            depth: 35,
            zIndex: 10,
        },
        {
            image: '/images/landing/abstract3.png',
            top: '35%',
            left: '6%',
            width: '140px',
            height: '140px',
            mobileWidth: '55px',
            mobileHeight: '55px',
            mobileZIndex: -1,
            rotation: -15,
            opacity: 1,
            blur: '',
            depth: 45,
            zIndex: 20,
        },
        {
            image: '/images/landing/abstract4.png',
            top: '55%',
            left: '4%',
            width: '90px',
            height: '130px',
            mobileWidth: '40px',
            mobileHeight: '55px',
            mobileZIndex: -1,
            rotation: -10,
            opacity: 1,
            blur: '',
            depth: 50,
            zIndex: 30,
        },
        {
            image: '/images/landing/abstract2.png',
            top: '78%',
            left: '10%',
            width: '160px',
            height: '160px',
            mobileWidth: '60px',
            mobileHeight: '60px',
            mobileZIndex: -1,
            rotation: 30,
            opacity: 0.9,
            blur: 'blur-sm',
            depth: 25,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract3.png',
            top: '70%',
            left: '32%',
            width: '120px',
            height: '120px',
            mobileWidth: '45px',
            mobileHeight: '45px',
            mobileZIndex: -1,
            rotation: 45,
            opacity: 0.95,
            blur: '',
            depth: 15,
            zIndex: 10,
        },
        {
            image: '/images/landing/abstract1.png',
            top: '-5%',
            left: '52%',
            width: '180px',
            height: '130px',
            mobileWidth: '70px',
            mobileHeight: '50px',
            mobileZIndex: -1,
            rotation: -5,
            opacity: 1,
            blur: '',
            depth: -10,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract2.png',
            top: '22%',
            left: '42%',
            width: '90px',
            height: '90px',
            mobileWidth: '35px',
            mobileHeight: '35px',
            mobileZIndex: -1,
            rotation: -8,
            opacity: 0.3,
            blur: 'blur-md',
            depth: -30,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract3.png',
            top: '85%',
            left: '45%',
            width: '100px',
            height: '140px',
            mobileWidth: '40px',
            mobileHeight: '55px',
            mobileZIndex: -1,
            rotation: -15,
            opacity: 0.2,
            blur: 'blur-[6px]',
            depth: -40,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract1.png',
            top: '82%',
            left: '65%',
            width: '90px',
            height: '120px',
            mobileWidth: '35px',
            mobileHeight: '50px',
            mobileZIndex: -1,
            rotation: 20,
            opacity: 0.15,
            blur: 'blur-sm',
            depth: -25,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract4.png',
            top: '4%',
            right: '25%',
            width: '120px',
            height: '180px',
            mobileWidth: '50px',
            mobileHeight: '70px',
            mobileZIndex: -1,
            rotation: -35,
            opacity: 0.9,
            blur: 'blur-[1px]',
            depth: 10,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract1.png',
            top: '25%',
            right: '18%',
            width: '120px',
            height: '90px',
            mobileWidth: '45px',
            mobileHeight: '35px',
            mobileZIndex: -1,
            rotation: -25,
            opacity: 1,
            blur: '',
            depth: 40,
            zIndex: 20,
        },
        {
            image: '/images/landing/abstract2.png',
            top: '18%',
            right: '-2%',
            width: '150px',
            height: '180px',
            mobileWidth: '60px',
            mobileHeight: '70px',
            mobileZIndex: -1,
            rotation: 15,
            opacity: 1,
            blur: '',
            depth: 50,
            zIndex: 10,
        },
        {
            image: '/images/landing/abstract3.png',
            top: '48%',
            right: '15%',
            width: '140px',
            height: '90px',
            mobileWidth: '55px',
            mobileHeight: '35px',
            mobileZIndex: -1,
            rotation: -8,
            opacity: 0.9,
            blur: 'blur-[2px]',
            depth: 20,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract4.png',
            top: '40%',
            right: '-3%',
            width: '40px',
            height: '160px',
            mobileWidth: '20px',
            mobileHeight: '65px',
            mobileZIndex: -1,
            rotation: 0,
            opacity: 1,
            blur: '',
            depth: 60,
            zIndex: 30,
        },
        {
            image: '/images/landing/abstract1.png',
            top: '65%',
            right: '20%',
            width: '130px',
            height: '130px',
            mobileWidth: '50px',
            mobileHeight: '50px',
            mobileZIndex: -1,
            rotation: -30,
            opacity: 1,
            blur: '',
            depth: 45,
            zIndex: 20,
        },
        {
            image: '/images/landing/abstract2.png',
            top: '62%',
            right: '2%',
            width: '120px',
            height: '160px',
            mobileWidth: '50px',
            mobileHeight: '65px',
            mobileZIndex: -1,
            rotation: 15,
            opacity: 0.8,
            blur: 'blur-[1px]',
            depth: 30,
            zIndex: 0,
        },
        {
            image: '/images/landing/abstract3.png',
            top: '85%',
            right: '6%',
            width: '130px',
            height: '110px',
            mobileWidth: '50px',
            mobileHeight: '45px',
            mobileZIndex: -1,
            rotation: 40,
            opacity: 0.3,
            blur: 'blur-sm',
            depth: -15,
            zIndex: 0,
        },
    ];
</script>

<svelte:window
    bind:innerWidth={windowWidth}
    bind:innerHeight={windowHeight}
    onmousemove={handleMousemove}
/>

<section
    class="relative -mt-16 flex min-h-screen flex-col items-center justify-center overflow-hidden bg-linear-to-b from-[#FAFAF8] via-[#FDFDFB] to-[#F3F1EC] px-6 text-center"
>
    <div
        class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_10%,rgba(210,109,63,0.1),transparent_45%),radial-gradient(circle_at_80%_20%,rgba(174,192,173,0.14),transparent_45%),radial-gradient(circle_at_50%_80%,rgba(238,202,157,0.2),transparent_50%)]"
    ></div>

    {#each floatingDecor as item (`${item.image}-${item.top}-${item.left ?? ''}-${item.right ?? ''}`)}
        <FloatingItem {...item} parallaxX={$parallax.x} parallaxY={$parallax.y} />
    {/each}

    <div
        class="relative z-10 mt-12 flex max-w-5xl flex-col items-center"
        in:fade={{ duration: 1500 }}
    >
        <span
            class="mb-4 rounded-xl border-2 border-slate-900/10 bg-white/70 px-4 py-2 text-[11px] font-black tracking-[0.2em] text-slate-900/80 uppercase backdrop-blur"
            >OOPEDIA · PLATFORM PEMBELAJARAN MODERN</span
        >

        <h1
            class="mb-6 px-4 font-sans text-[2.8rem] leading-[0.95] font-black tracking-[-0.05em] text-slate-900 drop-shadow-sm sm:text-[3.8rem] md:text-[5.5rem] lg:text-[7.5rem]"
        >
            Ruang belajar Anda<br />menguasai OOP
        </h1>

        <p
            class="mb-10 max-w-2xl px-6 text-sm leading-relaxed font-medium text-slate-900/60 sm:text-base"
        >
            Belajar paradigma, pola desain, dan arsitektur perangkat lunak dengan pengalaman
            interaktif yang terasa personal sejak sesi pertama.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-6">
            <Link
                href={ROUTES.AUTH.REGISTER}
                class="rounded-3xl border-2 border-slate-950 border-b-8 bg-slate-900 px-10 py-5 text-[14px] font-black tracking-[0.14em] text-white uppercase shadow-xl transition-all hover:bg-slate-800 active:translate-y-[4px] active:border-b-4 sm:px-12"
            >
                Mulai Belajar
            </Link>

            <a
                href="#fitur"
                class="rounded-3xl border-2 border-slate-200 border-b-8 bg-white px-10 py-5 text-[14px] font-black tracking-[0.14em] text-slate-900 shadow-lg uppercase backdrop-blur transition-all hover:bg-slate-50 active:translate-y-[4px] active:border-b-4 sm:px-12"
            >
                Eksplor Fitur
            </a>
        </div>
    </div>

    <div
        class="group absolute bottom-8 left-1/2 z-10 flex w-full -translate-x-1/2 cursor-pointer items-center justify-center gap-3 text-[14px] font-semibold text-slate-900/50 transition-colors hover:text-slate-900"
    >
        <Play size={12} class="fill-current" />
        <span>Lihat Demo</span>
    </div>
</section>
