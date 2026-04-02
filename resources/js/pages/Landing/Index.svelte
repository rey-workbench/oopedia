<script lang="ts">
    import {
        Navbar,
        Hero,
        CinematicFilm,
        SearchOlive,
        SearchRust,
        SearchTan,
        BrandLogos,
        KnowWhatLookingAt,
        CallToAction,
        MassiveFooter,
    } from '@/components/Landing';
</script>

<svelte:head>
    <title>OOPedia — Platform Pembelajaran Interaktif</title>
</svelte:head>

<div class="min-h-screen bg-[#FDFDFB] font-sans text-black antialiased">
    <Navbar />

    <main class="relative w-full">
        <Hero />
        <CinematicFilm />

        <SearchOlive />
        <SearchRust />
        <SearchTan />
        <KnowWhatLookingAt />
        <BrandLogos />

        <CallToAction />
        <MassiveFooter />
    </main>
</div>
