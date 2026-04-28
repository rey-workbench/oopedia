<script lang="ts">
    import {
        Navbar,
        Hero,
        BrandLogos,
        BentoGrid,
        CinematicFilm,
        CallToAction,
        MassiveFooter,
    } from '@/components/Landing';
    import BackgroundMesh from '@/components/ui/BackgroundMesh.svelte';
</script>

<svelte:head>
    <title>OOPedia — Platform Pembelajaran OOP Interaktif</title>
</svelte:head>

<div class="relative min-h-screen font-sans text-slate-900 antialiased">
    <!-- Global Atmosphere -->
    <BackgroundMesh />

    <Navbar />

    <main class="relative w-full">
        <Hero />

        <!-- Core Feature Showcase -->
        <BentoGrid />

        <!-- Cinematic Experience -->
        <CinematicFilm />

        <BrandLogos />

        <!-- Final Call -->
        <CallToAction />

        <MassiveFooter />
    </main>
</div>
