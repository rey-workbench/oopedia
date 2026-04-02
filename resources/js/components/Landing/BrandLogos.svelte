<script lang="ts">
    const brands = [
        { name: 'JTI', image: '/assets/landing/brands/jti.png' },
        { name: 'Polinema', image: '/assets/landing/brands/polinema.png' },
    ];

</script>

<section class="bg-white px-6 py-24">
    <div class="mx-auto max-w-7xl">
        <div class="mb-16 text-center">
            <p class="mb-4 text-[9px] font-black tracking-[0.3em] text-black/40 uppercase">
                Dipercaya oleh
            </p>
            <h2 class="font-serif text-4xl leading-tight tracking-tight text-black md:text-5xl">
                Institusi terkemuka.
            </h2>
        </div>

        <div class="flex flex-wrap justify-center gap-8 md:gap-16">
            {#each brands as brand}
                <div class="flex items-center justify-center p-4">
                    <img
                        src={brand.image}
                        alt={brand.name}
                        class="h-16 w-auto opacity-50 grayscale transition-opacity hover:opacity-80 md:h-20"
                    />
                </div>
            {/each}
        </div>
    </div>
</section>
