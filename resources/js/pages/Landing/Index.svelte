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
    <title>OOPedia — Platform Pembelajaran OOP Interaktif</title>
    <meta
        name="description"
        content="OOPedia adalah platform pembelajaran Object-Oriented Programming interaktif dengan AI. Belajar paradigma, pola desain, dan arsitektur dengan cara yang personal."
    />
    <meta property="og:title" content="OOPedia — Platform Pembelajaran OOP Interaktif" />
    <meta
        property="og:description"
        content="Platform pembelajaran OOP dengan AI yang membantu Anda memahami paradigma, pola desain, dan arsitektur perangkat lunak."
    />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
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
