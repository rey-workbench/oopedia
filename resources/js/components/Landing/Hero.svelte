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
    class="relative -mt-16 flex h-screen flex-col items-center justify-center overflow-hidden bg-gradient-to-b from-[#F9F9F9] to-transparent px-6 text-center"
>
    {#each floatingDecor as item}
        <FloatingItem {...item} parallaxX={$parallax.x} parallaxY={$parallax.y} />
    {/each}

    <div class="relative z-10 mt-12 flex flex-col items-center" in:fade={{ duration: 1500 }}>
        <span class="mb-3 text-[17px] font-bold tracking-tight text-black">OOPEDIA</span>

        <h1
            class="mb-10 px-4 font-sans text-[5rem] leading-[0.95] font-medium tracking-[-0.05em] text-black drop-shadow-sm md:text-[7.5rem]"
        >
            Ruang belajar Anda<br />untuk menguasai OOP
        </h1>

        <Link
            href={ROUTES.AUTH.REGISTER}
            class="rounded-full bg-[#111] px-8 py-3.5 text-[15px] font-medium tracking-tight text-white shadow-lg transition-colors hover:bg-black active:scale-95"
        >
            Mulai Belajar
        </Link>
    </div>

    <div
        class="group absolute bottom-8 left-1/2 z-10 flex w-full -translate-x-1/2 cursor-pointer items-center justify-center gap-3 text-[14px] font-semibold text-black/50 transition-colors hover:text-black"
    >
        <Play size={12} class="fill-current" />
        <span>Lihat Demo</span>
    </div>
</section>
