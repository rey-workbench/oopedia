<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { fade } from 'svelte/transition';
    import { spring } from 'svelte/motion';
    import FloatingItem from '@/components/ui/FloatingItem.svelte';
    import { Search, Plus, Play, Disc2, Navigation } from 'lucide-svelte';

    let scrollY = $state(0);

    // Mouse Parallax System
    let windowWidth = $state(1920);
    let windowHeight = $state(1080);

    // Spring physics configuration
    const parallax = spring(
        { x: 0, y: 0 },
        {
            stiffness: 0.05,
            damping: 0.5,
        }
    );

    const handleMousemove = (e: MouseEvent) => {
        // Normalize mouse position between -1 and 1
        const x = (e.clientX / windowWidth) * 2 - 1;
        const y = (e.clientY / windowHeight) * 2 - 1;
        parallax.set({ x, y });
    };

    const images = [
        '/images/landing/abstract1.png',
        '/images/landing/abstract2.png',
        '/images/landing/abstract3.png',
        '/images/landing/abstract4.png',
    ];

    // Mapped precisely to the cosmos.so screenshot scattered design
    const floatingDecor = [
        // Top Left Cluster
        {
            image: images[1] as string,
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
            image: images[0] as string,
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
            image: images[2] as string,
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

        // Bottom Left Cluster
        {
            image: images[3] as string,
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
            image: images[1] as string,
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
            image: images[2] as string,
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

        // Top Center
        {
            image: images[0] as string,
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
            image: images[1] as string,
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

        // Bottom Center
        {
            image: images[2] as string,
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
            image: images[0] as string,
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

        // Top Right Cluster
        {
            image: images[3] as string,
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
            image: images[0] as string,
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
            image: images[1] as string,
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

        // Center Right Cluster
        {
            image: images[2] as string,
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
            image: images[3] as string,
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

        // Bottom Right Cluster
        {
            image: images[0] as string,
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
            image: images[1] as string,
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
            image: images[2] as string,
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

<style>
    /* Spin animation for the dotted play button hexagon */
    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    .animate-spin-slow {
        animation: spin-slow 15s linear infinite;
    }
</style>

<svelte:window
    bind:scrollY
    bind:innerWidth={windowWidth}
    bind:innerHeight={windowHeight}
    onmousemove={handleMousemove}
/>

<svelte:head>
    <title>OOPedia — Platform Pembelajaran Interaktif</title>
</svelte:head>

<div class="min-h-screen bg-[#FDFDFB] font-sans text-black antialiased">
    <!-- Navbar (Cosmos style) -->
    <header
        class="pointer-events-none fixed top-0 right-0 left-0 z-50 flex items-center justify-between p-6"
    >
        <!-- Left: Menu Pill -->
        <div class="pointer-events-auto">
            <button
                type="button"
                class="flex cursor-pointer items-center gap-2 rounded-full border border-black/5 bg-white/80 px-5 py-3 text-[10px] font-black tracking-[0.2em] uppercase shadow-xl backdrop-blur-3xl transition-all hover:bg-white"
            >
                Menu
                <Plus size={12} class="opacity-40" />
            </button>
        </div>

        <!-- Center: Search Bar -->
        <div
            class="pointer-events-auto absolute left-1/2 hidden w-full max-w-md -translate-x-1/2 md:block"
        >
            <div
                class="flex cursor-text items-center gap-4 rounded-full border border-black/5 bg-white/80 px-5 py-3.5 shadow-xl backdrop-blur-3xl transition-all hover:bg-white"
            >
                <Search size={16} class="text-black/30" />
                <input
                    type="text"
                    placeholder="Search OOPedia..."
                    class="w-full border-none bg-transparent text-xs font-medium outline-none placeholder:text-black/30"
                />
            </div>
        </div>

        <!-- Right: Auth -->
        <div class="pointer-events-auto flex items-center gap-5">
            <Link
                href={ROUTES.AUTH.LOGIN}
                class="text-[10px] font-black tracking-[0.2em] text-black/60 uppercase transition-colors hover:text-black"
            >
                Login
            </Link>
            <Link
                href={ROUTES.AUTH.REGISTER}
                class="rounded-full bg-black px-6 py-3.5 text-[9px] font-black tracking-[0.2em] text-white uppercase shadow-2xl transition-transform hover:scale-105 active:scale-95"
            >
                Sign up
            </Link>
        </div>
    </header>

    <main class="relative w-full">
        <!-- SEC: Hero -->
        <section
            class="relative -mt-16 flex h-screen flex-col items-center justify-center overflow-hidden bg-gradient-to-b from-[#F9F9F9] to-transparent px-6 text-center"
        >
            {#each floatingDecor as item}
                <FloatingItem {...item} parallaxX={$parallax.x} parallaxY={$parallax.y} />
            {/each}

            <div
                class="relative z-10 mt-12 flex flex-col items-center"
                in:fade={{ duration: 1500 }}
            >
                <!-- Mimicking exactly the top small bold text "COSMOS" -->
                <span class="mb-3 text-[17px] font-bold tracking-tight text-black">OOPEDIA</span>

                <!-- Match precise Cosmos.so font scaling, tight tracking, and sans-serif spacing -->
                <h1
                    class="mb-10 px-4 font-sans text-[5rem] leading-[0.95] font-medium tracking-[-0.05em] text-black drop-shadow-sm md:text-[7.5rem]"
                >
                    Your space<br />for inspiration
                </h1>

                <!-- Black Pill Button -->
                <Link
                    href={ROUTES.AUTH.REGISTER}
                    class="rounded-full bg-[#111] px-8 py-3.5 text-[15px] font-medium tracking-tight text-white shadow-lg transition-colors hover:bg-black active:scale-95"
                >
                    Get the app
                </Link>
            </div>

            <!-- "Watch the new film" completely at the bottom -->
            <div
                class="group absolute bottom-8 left-1/2 z-10 flex w-full -translate-x-1/2 cursor-pointer items-center justify-center gap-3 text-[14px] font-semibold text-black/50 transition-colors hover:text-black"
            >
                <Play size={12} class="fill-current" />
                <span>Lihat Demo</span>
            </div>
        </section>

        <!-- SEC: Cinematic Film Bleed -->
        <section class="mb-40 h-auto w-full px-4 sm:px-8">
            <div
                class="group relative flex aspect-[21/9] w-full cursor-pointer items-center justify-center overflow-hidden rounded-xl bg-[#E1E6DC] shadow-2xl"
            >
                <!-- Blurred placeholder image -->
                <img
                    src="/images/landing/abstract1.png"
                    alt="film blur"
                    class="absolute inset-0 h-full w-full scale-125 object-cover opacity-40 blur-[40px] transition-all duration-[2s] group-hover:scale-[1.3] group-hover:blur-[50px]"
                />

                <!-- Dotted Play Button Hexagon -->
                <div
                    class="relative z-10 flex h-24 w-24 items-center justify-center opacity-90 drop-shadow-2xl transition-transform duration-700 group-hover:scale-110"
                >
                    <div
                        class="animate-spin-slow absolute inset-0 rounded-full border-[4px] border-dotted border-white/90"
                    ></div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-white pl-1"
                    >
                        <Play size={16} class="fill-black text-black" />
                    </div>
                </div>
            </div>
        </section>

        <!-- SEC: Every Search (Olive) -->
        <section class="mx-auto mb-40 flex max-w-7xl flex-col items-center px-6">
            <h2 class="mb-10 font-serif text-4xl tracking-tighter text-black">
                Every search opens a new world.
            </h2>

            <div
                class="group relative flex aspect-[16/9] w-full max-w-4xl items-center justify-center overflow-hidden rounded-xl bg-[#AEC0AD] shadow-lg"
            >
                <!-- Stacked images inside Olive -->
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
                        <span class="uppercase">patterns</span>
                    </div>
                </div>
            </div>

            <p
                class="mt-8 max-w-md text-center text-[11px] font-medium tracking-[0.02em] text-black/50"
            >
                Visual reflections, just a little sharper, just a little...<br />
                <span class="opacity-60">more like you.</span>
            </p>
        </section>

        <!-- SEC: Search the way you think (Rust Red) -->
        <section class="mx-auto mb-40 flex max-w-7xl flex-col items-center px-6">
            <h2
                class="mb-10 max-w-sm text-center font-serif text-[2.5rem] leading-[0.95] tracking-tighter text-black"
            >
                Search the way<br />you think.
            </h2>

            <div
                class="relative flex aspect-[18/9] w-full max-w-4xl items-center justify-center gap-[10%] overflow-hidden rounded-xl bg-[#C1583D] shadow-lg"
            >
                <!-- Dual Images inside Rust -->
                <div
                    class="relative z-10 aspect-[3/4] w-[30%] overflow-hidden rounded-sm bg-orange-950/60 mix-blend-multiply shadow-[0_20px_50px_rgba(0,0,0,0.3)] contrast-125 filter transition-transform duration-700 hover:scale-105"
                >
                    <img
                        src="/images/landing/abstract1.png"
                        alt=""
                        class="h-full w-full object-cover opacity-70"
                    />
                </div>
                <div
                    class="relative z-10 mb-10 aspect-[2/3] w-[20%] overflow-hidden rounded-sm bg-red-950/80 mix-blend-multiply shadow-[0_20px_50px_rgba(0,0,0,0.3)] contrast-150 filter transition-transform delay-100 duration-700 hover:scale-105"
                >
                    <img
                        src="/images/landing/abstract2.png"
                        alt=""
                        class="h-full w-full object-cover opacity-90"
                    />
                </div>

                <!-- Central ARCHIVE Pill -->
                <div class="absolute top-1/2 left-1/2 z-20 -translate-x-[60%] -translate-y-1/2">
                    <div
                        class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-[#A43B25]/90 px-6 py-2 text-[9px] font-black tracking-[0.2em] text-white uppercase shadow-[0_10px_40px_rgba(0,0,0,0.2)] backdrop-blur-3xl transition-transform hover:scale-110"
                    >
                        <div class="h-3 w-3 rounded-full border-[2px] border-[#EB8E78]"></div>
                        ARCHIVE
                    </div>
                </div>
            </div>

            <p class="mt-8 text-[9px] font-black tracking-[0.2em] text-black uppercase">
                by paradigm
            </p>
        </section>

        <!-- SEC: By Visual Similarity (Tan) -->
        <section class="mx-auto mb-40 flex max-w-7xl flex-col items-center px-6">
            <div
                class="relative flex aspect-video w-full max-w-4xl justify-center overflow-hidden rounded-xl bg-[#EECA9D] py-16 shadow-lg"
            >
                <!-- Abstract floating grid inside Tan -->
                <div class="grid w-[80%] grid-cols-2 gap-6 opacity-90 md:grid-cols-4">
                    <div
                        class="aspect-[4/5] w-full rounded bg-stone-800/20 mix-blend-multiply shadow-xl transition-transform hover:-translate-y-2"
                    >
                        <img
                            src="/images/landing/abstract3.png"
                            alt=""
                            class="h-full w-full object-cover opacity-60"
                        />
                    </div>
                    <div
                        class="mt-8 aspect-square w-full rounded bg-stone-700/30 mix-blend-multiply shadow-xl transition-transform delay-75 hover:-translate-y-2"
                    >
                        <img
                            src="/images/landing/abstract4.png"
                            alt=""
                            class="h-full w-full object-cover opacity-70"
                        />
                    </div>
                    <div
                        class="aspect-[4/5] w-full rounded bg-stone-900/10 mix-blend-multiply shadow-xl transition-transform delay-100 hover:-translate-y-2"
                    >
                        <img
                            src="/images/landing/abstract1.png"
                            alt=""
                            class="h-full w-full object-cover opacity-50"
                        />
                    </div>
                    <div
                        class="mt-12 aspect-square w-full rounded bg-stone-800/40 mix-blend-multiply shadow-xl transition-transform delay-150 hover:-translate-y-2"
                    >
                        <img
                            src="/images/landing/abstract2.png"
                            alt=""
                            class="h-full w-full object-cover opacity-80"
                        />
                    </div>
                </div>
            </div>

            <p class="mt-8 text-[9px] font-black tracking-[0.2em] text-black uppercase">
                by visual similarity
            </p>
        </section>

        <!-- SEC: Artificial AI (Macro dark) -->
        <section class="mb-40 w-full">
            <div
                class="relative flex h-[80vh] w-full flex-col items-center justify-center overflow-hidden bg-[#0A0A0A]"
            >
                <!-- Extremely macro, dark moody background -->
                <img
                    src="/images/landing/abstract3.png"
                    alt=""
                    class="absolute inset-0 h-full w-full scale-150 object-cover opacity-40 mix-blend-luminosity blur-md"
                />

                <!-- Floating Prompt Modal -->
                <div
                    class="relative z-10 w-[420px] transform rounded-[20px] bg-[#FDFDFB] p-6 shadow-[0_30px_60px_rgba(0,0,0,0.6)] transition-transform duration-500 hover:scale-[1.02]"
                >
                    <div
                        class="mb-4 flex items-center gap-2 text-[9px] font-black tracking-[0.2em] text-black"
                    >
                        <Disc2 size={12} /> OOPEDIA
                    </div>
                    <p class="mb-8 font-serif text-lg leading-snug text-black/70">
                        Find architectural patterns<br />based on story board for a...
                    </p>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            class="rounded-full bg-[#111] px-5 py-2.5 text-[9px] font-black tracking-widest text-white uppercase shadow-md transition-colors hover:bg-black"
                            >Clear</button
                        >
                        <button
                            type="button"
                            class="rounded-full border-2 border-dashed border-black/10 bg-white px-5 py-2.5 text-[9px] font-black tracking-widest uppercase transition-colors hover:border-black/30"
                            >Go</button
                        >
                    </div>
                </div>
            </div>

            <div class="flex w-full justify-center">
                <p class="mt-8 text-[9px] font-black tracking-[0.2em] text-[#D26D3F] uppercase">
                    with Artificial AI
                </p>
            </div>
        </section>

        <!-- SEC: Know what you're looking at -->
        <section class="mx-auto mb-[15vh] flex max-w-7xl flex-col items-center px-6 text-center">
            <h2 class="mb-16 font-serif text-[2.5rem] tracking-tighter text-black">
                Know what you're looking at.
            </h2>

            <div class="group relative mt-4 inline-block">
                <div
                    class="relative z-0 aspect-[4/5] w-[300px] overflow-hidden rounded-sm bg-[#E3E1DE] shadow-[0_20px_50px_rgba(0,0,0,0.1)] transition-transform duration-1000 group-hover:scale-105"
                >
                    <img
                        src="/images/landing/abstract4.png"
                        alt=""
                        class="h-full w-full object-cover opacity-[0.85] mix-blend-multiply contrast-125 filter"
                    />
                </div>

                <!-- Floating Tooltip Pointer -->
                <div
                    class="absolute top-[50%] left-[70%] z-10 max-w-[200px] translate-y-4 transform rounded-[12px] border border-white/5 bg-[#1A1A1A]/90 p-4 text-left text-[10px] leading-relaxed font-medium text-white/90 opacity-0 shadow-2xl backdrop-blur-xl transition-all delay-100 duration-700 group-hover:translate-y-0 group-hover:opacity-100"
                >
                    Oopedia can identify the specific paradigm or logic structure you are viewing.
                </div>
            </div>
        </section>

        <!-- SEC: Inspiration Space -->
        <section class="flex h-[40vh] w-full flex-col items-center justify-center px-6 text-center">
            <h2
                class="max-w-2xl font-serif text-3xl leading-[1.05] tracking-tighter text-black md:text-5xl"
            >
                Inspiration for the world's<br />top creative engineers.
            </h2>
        </section>

        <!-- Pre-Footer -->
        <section class="flex flex-col items-center justify-center pt-10 pb-[15vh] text-center">
            <p class="mb-6 text-[9px] font-black tracking-[0.3em] text-black/30 uppercase">
                Ready to get it all?
            </p>
            <Link
                href={ROUTES.AUTH.REGISTER}
                class="rounded-full bg-black px-10 py-4 text-[9px] font-black tracking-[0.3em] text-white uppercase shadow-[0_10px_30px_rgba(0,0,0,0.2)] transition-transform hover:scale-105 active:scale-95"
            >
                Get the App
            </Link>
        </section>

        <!-- SEC: Massive Footer -->
        <footer class="relative flex w-full flex-col overflow-hidden pt-12">
            <!-- Footer Links -->
            <div
                class="flex w-full flex-col items-center justify-between gap-8 px-6 pb-[2vh] text-[9px] font-black tracking-[0.2em] text-black/40 uppercase md:flex-row md:gap-0"
            >
                <div class="flex gap-6">
                    <button
                        type="button"
                        class="transition-colors hover:text-black focus:outline-none"
                        >Instagram</button
                    >
                    <button
                        type="button"
                        class="transition-colors hover:text-black focus:outline-none"
                        >Twitter</button
                    >
                    <button
                        type="button"
                        class="transition-colors hover:text-black focus:outline-none"
                        >YouTube</button
                    >
                </div>

                <div
                    class="group cursor-pointer text-black transition-transform duration-700 hover:rotate-90"
                >
                    <Navigation
                        size={20}
                        class="fill-current transition-transform group-hover:scale-110"
                    />
                </div>

                <div class="flex gap-6">
                    <button
                        type="button"
                        class="transition-colors hover:text-black focus:outline-none"
                        >Contact</button
                    >
                    <button
                        type="button"
                        class="transition-colors hover:text-black focus:outline-none">Terms</button
                    >
                    <button
                        type="button"
                        class="transition-colors hover:text-black focus:outline-none"
                        >Privacy</button
                    >
                </div>
            </div>

            <!-- Massive Logotype -->
            <div
                class="pointer-events-none flex w-full translate-y-[15%] justify-center overflow-hidden"
            >
                <span
                    class="text-[26vw] leading-[0.7] font-black tracking-tighter text-black select-none"
                >
                    OOPEDIA
                </span>
            </div>
        </footer>
    </main>
</div>
