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
    const parallax = spring({ x: 0, y: 0 }, {
        stiffness: 0.05,
        damping: 0.5
    });

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
        { image: images[1] as string, top: '2%',   left: '2%',  width: '120px', height: '140px', rotation: 25,  opacity: 0.9, blur: 'blur-[2px]', depth: -20, zIndex: 0 },
        { image: images[0] as string, top: '15%',  left: '18%', width: '130px', height: '130px', rotation: 40,  opacity: 1,   blur: '', depth: 35, zIndex: 10 },
        { image: images[2] as string, top: '35%',  left: '6%',  width: '140px', height: '140px', rotation: -15, opacity: 1,   blur: '', depth: 45, zIndex: 20 },
        
        // Bottom Left Cluster
        { image: images[3] as string, top: '55%',  left: '4%',  width: '90px',  height: '130px', rotation: -10, opacity: 1,   blur: '', depth: 50, zIndex: 30 },
        { image: images[1] as string, top: '78%',  left: '10%', width: '160px', height: '160px', rotation: 30,  opacity: 0.9, blur: 'blur-sm', depth: 25, zIndex: 0 },
        { image: images[2] as string, top: '70%',  left: '32%', width: '120px', height: '120px', rotation: 45,  opacity: 0.95,blur: '', depth: 15, zIndex: 10 },

        // Top Center 
        { image: images[0] as string, top: '-5%',  left: '52%', width: '180px', height: '130px', rotation: -5,  opacity: 1,   blur: '', depth: -10, zIndex: 0 },
        { image: images[1] as string, top: '22%',  left: '42%', width: '90px',  height: '90px',  rotation: -8,  opacity: 0.3, blur: 'blur-md', depth: -30, zIndex: 0 },
        
        // Bottom Center
        { image: images[2] as string, top: '85%',  left: '45%', width: '100px', height: '140px', rotation: -15, opacity: 0.2, blur: 'blur-[6px]', depth: -40, zIndex: 0 },
        { image: images[0] as string, top: '82%',  left: '65%', width: '90px',  height: '120px', rotation: 20,  opacity: 0.15,blur: 'blur-sm', depth: -25, zIndex: 0 },
        
        // Top Right Cluster
        { image: images[3] as string, top: '4%',   right: '25%',width: '120px', height: '180px', rotation: -35, opacity: 0.9, blur: 'blur-[1px]', depth: 10, zIndex: 0 },
        { image: images[0] as string, top: '25%',  right: '18%',width: '120px', height: '90px',  rotation: -25, opacity: 1,   blur: '', depth: 40, zIndex: 20 },
        { image: images[1] as string, top: '18%',  right: '-2%',width: '150px', height: '180px', rotation: 15,  opacity: 1,   blur: '', depth: 50, zIndex: 10 },
        
        // Center Right Cluster
        { image: images[2] as string, top: '48%',  right: '15%',width: '140px', height: '90px',  rotation: -8,  opacity: 0.9, blur: 'blur-[2px]', depth: 20, zIndex: 0 },
        { image: images[3] as string, top: '40%',  right: '-3%',width: '40px',  height: '160px', rotation: 0,   opacity: 1,   blur: '', depth: 60, zIndex: 30 },
        
        // Bottom Right Cluster
        { image: images[0] as string, top: '65%',  right: '20%',width: '130px', height: '130px', rotation: -30, opacity: 1,   blur: '', depth: 45, zIndex: 20 },
        { image: images[1] as string, top: '62%',  right: '2%', width: '120px', height: '160px', rotation: 15,  opacity: 0.8, blur: 'blur-[1px]', depth: 30, zIndex: 0 },
        { image: images[2] as string, top: '85%',  right: '6%', width: '130px', height: '110px', rotation: 40,  opacity: 0.3, blur: 'blur-sm', depth: -15, zIndex: 0 },
    ];
</script>

<svelte:window 
    bind:scrollY 
    bind:innerWidth={windowWidth} 
    bind:innerHeight={windowHeight} 
    onmousemove={handleMousemove} 
/>

<svelte:head>
    <title>OOPedia — Your space for mastery</title>
</svelte:head>

<div class="min-h-screen bg-[#FDFDFB] text-black antialiased font-sans">
    
    <!-- Navbar (Cosmos style) -->
    <header class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between p-6 pointer-events-none">
        
        <!-- Left: Menu Pill -->
        <div class="pointer-events-auto">
            <button type="button" class="bg-white/80 backdrop-blur-3xl border border-black/5 rounded-full px-5 py-3 flex items-center gap-2 text-[10px] font-black tracking-[0.2em] uppercase shadow-xl hover:bg-white transition-all cursor-pointer">
                Menu
                <Plus size={12} class="opacity-40" />
            </button>
        </div>

        <!-- Center: Search Bar -->
        <div class="absolute left-1/2 -translate-x-1/2 w-full max-w-md hidden md:block pointer-events-auto">
            <div class="bg-white/80 backdrop-blur-3xl border border-black/5 rounded-full px-5 py-3.5 flex items-center gap-4 shadow-xl hover:bg-white transition-all cursor-text">
                <Search size={16} class="text-black/30" />
                <input 
                    type="text" 
                    placeholder="Search OOPedia..."
                    class="bg-transparent border-none outline-none text-xs w-full placeholder:text-black/30 font-medium"
                />
            </div>
        </div>

        <!-- Right: Auth -->
        <div class="flex items-center gap-5 pointer-events-auto">
            <Link href={ROUTES.AUTH.LOGIN} class="text-[10px] font-black tracking-[0.2em] uppercase text-black/60 hover:text-black transition-colors">
                Login
            </Link>
            <Link href={ROUTES.AUTH.REGISTER} class="bg-black text-white px-6 py-3.5 text-[9px] font-black tracking-[0.2em] uppercase rounded-full shadow-2xl hover:scale-105 active:scale-95 transition-transform">
                Sign up
            </Link>
        </div>
    </header>

    <main class="relative w-full">
        
        <!-- SEC: Hero -->
        <section class="relative h-screen flex flex-col items-center justify-center -mt-16 px-6 text-center overflow-hidden bg-gradient-to-b from-[#F9F9F9] to-transparent">
            
            {#each floatingDecor as item}
                <FloatingItem {...item} parallaxX={$parallax.x} parallaxY={$parallax.y} />
            {/each}

            <div class="z-10 relative flex flex-col items-center mt-12" in:fade={{ duration: 1500 }}>
                <!-- Mimicking exactly the top small bold text "COSMOS" -->
                <span class="text-[17px] font-bold tracking-tight text-black mb-3">OOPEDIA</span>
                
                <!-- Match precise Cosmos.so font scaling, tight tracking, and sans-serif spacing -->
                <h1 class="font-sans font-medium text-[5rem] md:text-[7.5rem] leading-[0.95] tracking-[-0.05em] mb-10 text-black drop-shadow-sm px-4">
                    Your space<br />for inspiration
                </h1>
                
                <!-- Black Pill Button -->
                <Link href={ROUTES.AUTH.REGISTER} class="bg-[#111] text-white px-8 py-3.5 text-[15px] font-medium tracking-tight rounded-full shadow-lg hover:bg-black transition-colors active:scale-95">
                    Get the app
                </Link>
            </div>

            <!-- "Watch the new film" completely at the bottom -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-3 text-[14px] font-semibold text-black/50 hover:text-black transition-colors cursor-pointer group z-10 w-full justify-center">
                <Play size={12} class="fill-current" />
                <span>Watch the new film</span>
            </div>
        </section>

        <!-- SEC: Cinematic Film Bleed -->
        <section class="w-full h-auto px-4 sm:px-8 mb-40">
            <div class="relative w-full aspect-[21/9] bg-[#E1E6DC] rounded-xl overflow-hidden shadow-2xl flex items-center justify-center cursor-pointer group">
                <!-- Blurred placeholder image -->
                <img src="/images/landing/abstract1.png" alt="film blur" class="absolute inset-0 w-full h-full object-cover blur-[40px] scale-125 opacity-40 group-hover:scale-[1.3] group-hover:blur-[50px] transition-all duration-[2s]" />
                
                <!-- Dotted Play Button Hexagon -->
                <div class="relative z-10 w-24 h-24 flex items-center justify-center drop-shadow-2xl opacity-90 group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 border-[4px] border-dotted border-white/90 rounded-full animate-spin-slow"></div>
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center pl-1">
                        <Play size={16} class="text-black fill-black" />
                    </div>
                </div>
            </div>
        </section>

        <!-- SEC: Every Search (Olive) -->
        <section class="max-w-7xl mx-auto px-6 mb-40 flex flex-col items-center">
            <h2 class="font-serif text-4xl tracking-tighter mb-10 text-black">Every search opens a new world.</h2>
            
            <div class="relative w-full max-w-4xl aspect-[16/9] bg-[#AEC0AD] rounded-xl overflow-hidden flex items-center justify-center shadow-lg group">
                <!-- Stacked images inside Olive -->
                <div class="absolute top-10 w-48 aspect-video bg-black/10 rounded shadow-2xl blur-[2px] opacity-70 transition-transform duration-1000 group-hover:-translate-y-4">
                    <img src="/images/landing/abstract2.png" alt="" class="w-full h-full object-cover opacity-80" />
                </div>
                <div class="absolute bottom-10 w-48 aspect-video bg-black/20 rounded shadow-2xl blur-[2px] opacity-70 transition-transform duration-1000 group-hover:translate-y-4">
                    <img src="/images/landing/abstract3.png" alt="" class="w-full h-full object-cover opacity-80 mix-blend-multiply" />
                </div>
                
                <div class="absolute w-72 aspect-video bg-white/50 rounded-lg shadow-[0_30px_60px_rgba(0,0,0,0.15)] z-10 flex items-center justify-center overflow-hidden transition-transform duration-1000 group-hover:scale-105">
                    <img src="/images/landing/abstract4.png" alt="" class="absolute inset-0 w-full h-full object-cover mix-blend-darken opacity-40" />
                    <div class="bg-black/40 backdrop-blur-xl border border-white/20 text-white rounded-full px-5 py-2.5 flex items-center gap-3 shadow-2xl text-[9px] font-black tracking-[0.2em] relative z-20">
                        <Search size={12} class="opacity-70" /> 
                        <span class="uppercase">patterns</span>
                    </div>
                </div>
            </div>
            
            <p class="mt-8 text-[11px] font-medium tracking-[0.02em] text-black/50 max-w-md text-center">
                Visual reflections, just a little sharper, just a little...<br/>
                <span class="opacity-60">more like you.</span>
            </p>
        </section>

        <!-- SEC: Search the way you think (Rust Red) -->
        <section class="max-w-7xl mx-auto px-6 mb-40 flex flex-col items-center">
            <h2 class="font-serif text-[2.5rem] tracking-tighter mb-10 max-w-sm text-center leading-[0.95] text-black">
                Search the way<br/>you think.
            </h2>
            
            <div class="relative w-full max-w-4xl aspect-[18/9] bg-[#C1583D] rounded-xl overflow-hidden shadow-lg flex items-center justify-center gap-[10%]">
                <!-- Dual Images inside Rust -->
                <div class="w-[30%] aspect-[3/4] bg-orange-950/60 shadow-[0_20px_50px_rgba(0,0,0,0.3)] relative z-10 mix-blend-multiply rounded-sm overflow-hidden filter contrast-125 hover:scale-105 transition-transform duration-700">
                    <img src="/images/landing/abstract1.png" alt="" class="w-full h-full object-cover opacity-70" />
                </div>
                <div class="w-[20%] aspect-[2/3] bg-red-950/80 shadow-[0_20px_50px_rgba(0,0,0,0.3)] relative z-10 mb-10 mix-blend-multiply rounded-sm overflow-hidden filter contrast-150 hover:scale-105 transition-transform duration-700 delay-100">
                    <img src="/images/landing/abstract2.png" alt="" class="w-full h-full object-cover opacity-90" />
                </div>
                
                <!-- Central ARCHIVE Pill -->
                <div class="absolute left-1/2 top-1/2 -translate-x-[60%] -translate-y-1/2 z-20">
                    <div class="bg-[#A43B25]/90 backdrop-blur-3xl border border-white/10 text-white rounded-full px-6 py-2 shadow-[0_10px_40px_rgba(0,0,0,0.2)] flex items-center gap-3 text-[9px] font-black tracking-[0.2em] uppercase hover:scale-110 transition-transform cursor-pointer">
                        <div class="w-3 h-3 rounded-full border-[2px] border-[#EB8E78]"></div> 
                        ARCHIVE
                    </div>
                </div>
            </div>
            
            <p class="mt-8 text-[9px] font-black tracking-[0.2em] uppercase text-black">
                by paradigm
            </p>
        </section>

        <!-- SEC: By Visual Similarity (Tan) -->
        <section class="max-w-7xl mx-auto px-6 mb-40 flex flex-col items-center">
            <div class="relative w-full max-w-4xl aspect-video bg-[#EECA9D] rounded-xl overflow-hidden shadow-lg flex justify-center py-16">
                <!-- Abstract floating grid inside Tan -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 w-[80%] opacity-90">
                    <div class="w-full aspect-[4/5] bg-stone-800/20 mix-blend-multiply shadow-xl rounded hover:-translate-y-2 transition-transform">
                        <img src="/images/landing/abstract3.png" alt="" class="w-full h-full object-cover opacity-60" />
                    </div>
                    <div class="w-full aspect-square bg-stone-700/30 mix-blend-multiply shadow-xl rounded hover:-translate-y-2 transition-transform delay-75 mt-8">
                        <img src="/images/landing/abstract4.png" alt="" class="w-full h-full object-cover opacity-70" />
                    </div>
                    <div class="w-full aspect-[4/5] bg-stone-900/10 mix-blend-multiply shadow-xl rounded hover:-translate-y-2 transition-transform delay-100">
                        <img src="/images/landing/abstract1.png" alt="" class="w-full h-full object-cover opacity-50" />
                    </div>
                    <div class="w-full aspect-square bg-stone-800/40 mix-blend-multiply shadow-xl rounded hover:-translate-y-2 transition-transform delay-150 mt-12">
                        <img src="/images/landing/abstract2.png" alt="" class="w-full h-full object-cover opacity-80" />
                    </div>
                </div>
            </div>
            
            <p class="mt-8 text-[9px] font-black tracking-[0.2em] uppercase text-black">
                by visual similarity
            </p>
        </section>

        <!-- SEC: Artificial AI (Macro dark) -->
        <section class="w-full mb-40">
            <div class="relative w-full h-[80vh] bg-[#0A0A0A] overflow-hidden flex flex-col items-center justify-center">
                <!-- Extremely macro, dark moody background -->
                <img src="/images/landing/abstract3.png" alt="" class="absolute inset-0 w-full h-full object-cover opacity-40 scale-150 blur-md mix-blend-luminosity" />
                
                <!-- Floating Prompt Modal -->
                <div class="relative z-10 w-[420px] bg-[#FDFDFB] rounded-[20px] shadow-[0_30px_60px_rgba(0,0,0,0.6)] p-6 transform hover:scale-[1.02] transition-transform duration-500">
                    <div class="flex items-center gap-2 mb-4 text-[9px] font-black tracking-[0.2em] text-black">
                        <Disc2 size={12}/> OOPEDIA
                    </div>
                    <p class="text-lg font-serif mb-8 text-black/70 leading-snug">
                        Find architectural patterns<br/>based on story board for a...
                    </p>
                    <div class="flex gap-2">
                        <button type="button" class="bg-[#111] text-white px-5 py-2.5 rounded-full text-[9px] font-black tracking-widest uppercase shadow-md hover:bg-black transition-colors">Clear</button>
                        <button type="button" class="bg-white border-2 border-dashed border-black/10 px-5 py-2.5 rounded-full text-[9px] font-black tracking-widest uppercase hover:border-black/30 transition-colors">Go</button>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center w-full">
                <p class="mt-8 text-[9px] font-black tracking-[0.2em] uppercase text-[#D26D3F]">
                    with Artificial AI
                </p>
            </div>
        </section>

        <!-- SEC: Know what you're looking at -->
        <section class="max-w-7xl mx-auto px-6 mb-[15vh] text-center flex flex-col items-center">
            <h2 class="font-serif text-[2.5rem] tracking-tighter mb-16 text-black">Know what you're looking at.</h2>
            
            <div class="relative inline-block mt-4 group">
                <div class="w-[300px] aspect-[4/5] bg-[#E3E1DE] shadow-[0_20px_50px_rgba(0,0,0,0.1)] relative z-0 overflow-hidden rounded-sm transition-transform duration-1000 group-hover:scale-105">
                    <img src="/images/landing/abstract4.png" alt="" class="w-full h-full object-cover opacity-[0.85] mix-blend-multiply filter contrast-125" />
                </div>
                
                <!-- Floating Tooltip Pointer -->
                <div class="absolute top-[50%] left-[70%] z-10 bg-[#1A1A1A]/90 backdrop-blur-xl border border-white/5 text-white/90 text-[10px] p-4 rounded-[12px] shadow-2xl max-w-[200px] text-left transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-700 delay-100 leading-relaxed font-medium">
                    Oopedia can identify the specific paradigm or logic structure you are viewing.
                </div>
            </div>
        </section>

        <!-- SEC: Inspiration Space -->
        <section class="w-full h-[40vh] flex flex-col items-center justify-center text-center px-6">
            <h2 class="font-serif text-3xl md:text-5xl tracking-tighter text-black max-w-2xl leading-[1.05]">
                Inspiration for the world's<br/>top creative engineers.
            </h2>
        </section>

        <!-- Pre-Footer -->
        <section class="flex flex-col items-center justify-center text-center pt-10 pb-[15vh]">
            <p class="text-[9px] font-black tracking-[0.3em] uppercase text-black/30 mb-6">Ready to get it all?</p>
            <Link href={ROUTES.AUTH.REGISTER} class="bg-black text-white px-10 py-4 text-[9px] font-black tracking-[0.3em] uppercase rounded-full shadow-[0_10px_30px_rgba(0,0,0,0.2)] hover:scale-105 active:scale-95 transition-transform">
                Get the App
            </Link>
        </section>
        
        <!-- SEC: Massive Footer -->
        <footer class="w-full relative overflow-hidden flex flex-col pt-12">
            <!-- Footer Links -->
            <div class="w-full px-6 flex flex-col md:flex-row justify-between items-center text-[9px] font-black tracking-[0.2em] uppercase text-black/40 gap-8 md:gap-0 pb-[2vh]">
                
                <div class="flex gap-6">
                    <button type="button" class="hover:text-black transition-colors focus:outline-none">Instagram</button>
                    <button type="button" class="hover:text-black transition-colors focus:outline-none">Twitter</button>
                    <button type="button" class="hover:text-black transition-colors focus:outline-none">YouTube</button>
                </div>
                
                <div class="text-black group cursor-pointer hover:rotate-90 transition-transform duration-700">
                    <Navigation size={20} class="fill-current group-hover:scale-110 transition-transform"/>
                </div>
                
                <div class="flex gap-6">
                    <button type="button" class="hover:text-black transition-colors focus:outline-none">Contact</button>
                    <button type="button" class="hover:text-black transition-colors focus:outline-none">Terms</button>
                    <button type="button" class="hover:text-black transition-colors focus:outline-none">Privacy</button>
                </div>

            </div>

            <!-- Massive Logotype -->
            <div class="w-full flex justify-center overflow-hidden translate-y-[15%] pointer-events-none">
                <span class="text-[26vw] leading-[0.7] font-black tracking-tighter text-black select-none">
                    OOPEDIA
                </span>
            </div>
        </footer>

    </main>
</div>

<style>
    /* Spin animation for the dotted play button hexagon */
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 15s linear infinite;
    }
</style>
