<script lang="ts">
    import { scrollReveal } from '@/utils/animations';

    const brands = [
        { name: 'JTI', image: '/images/landing/jti.png' },
        { name: 'Polinema', image: '/images/landing/polinema.png' },
    ];
</script>

<section class="relative z-10 py-32 px-6 overflow-hidden">
    <!-- Subtle Gradient Connection -->
    <div class="absolute inset-0 bg-linear-to-b from-transparent via-white/50 to-transparent opacity-50"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="mb-16 text-center" use:scrollReveal>
            <span class="inline-block px-4 py-1 rounded-full border border-slate-900/5 bg-white/40 text-[10px] font-black tracking-[0.4em] text-slate-900/30 uppercase backdrop-blur-sm">
                Dipercaya oleh institusi terkemuka
            </span>
        </div>

        <div class="flex flex-wrap justify-center items-center gap-16 md:gap-40">
            {#each brands as brand, i}
                <div 
                    use:scrollReveal={{ delay: i * 150, y: 20 }}
                    class="group relative flex items-center justify-center transition-all duration-500 hover:scale-110"
                >
                    <!-- Background Glow on Hover -->
                    <div class="absolute inset-0 -z-10 scale-0 rounded-full bg-accent-500/10 blur-3xl transition-transform duration-500 group-hover:scale-150"></div>
                    
                    <img
                        src={brand.image}
                        alt={brand.name}
                        class="h-12 w-auto object-contain md:h-16 grayscale opacity-30 transition-all duration-500 group-hover:grayscale-0 group-hover:opacity-100"
                    />
                </div>
            {/each}
        </div>
    </div>
</section>

