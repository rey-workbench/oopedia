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

    const SITE_URL = 'https://oopedia.example.com';
</script>

<svelte:head>
    <title>OOPedia — Platform Pembelajaran OOP Interaktif</title>
    <meta
        name="description"
        content="OOPedia adalah platform pembelajaran Object-Oriented Programming interaktif dengan AI. Belajar paradigma, pola desain, dan arsitektur dengan cara yang personal."
    />
    <meta
        name="keywords"
        content="OOP, Object-Oriented Programming, pembelajaran, kursus, paradigma, desain pola, arsitektur perangkat lunak"
    />
    <meta name="author" content="OOPedia Team" />
    <meta name="robots" content="index, follow" />

    <!-- Open Graph -->
    <meta property="og:title" content="OOPedia — Platform Pembelajaran OOP Interaktif" />
    <meta
        property="og:description"
        content="Platform pembelajaran OOP dengan AI yang membantu Anda memahami paradigma, pola desain, dan arsitektur perangkat lunak."
    />
    <meta property="og:type" content="website" />
    <meta property="og:url" content={SITE_URL} />
    <meta property="og:image" content="{SITE_URL}/images/og-image.png" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:site_name" content="OOPedia" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="OOPedia — Platform Pembelajaran OOP Interaktif" />
    <meta
        name="twitter:description"
        content="Platform pembelajaran OOP dengan AI yang membantu Anda memahami paradigma, pola desain, dan arsitektur perangkat lunak."
    />
    <meta name="twitter:image" content="{SITE_URL}/images/og-image.png" />

    <!-- Canonical URL -->
    <link rel="canonical" href={SITE_URL} />

    <!-- Structured Data - WebSite -->
    {@html `<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "OOPedia",
        "url": "${SITE_URL}",
        "description": "Platform pembelajaran Object-Oriented Programming interaktif dengan AI",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "${SITE_URL}/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    <\/script>`}

    <!-- Structured Data - Organization -->
    {@html `<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "OOPedia",
        "url": "${SITE_URL}",
        "logo": "${SITE_URL}/images/logo.png",
        "description": "Platform pembelajaran Object-Oriented Programming interaktif dengan AI",
        "sameAs": []
    }
    <\/script>`}

    <!-- Structured Data - SoftwareApplication -->
    {@html `<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "OOPedia",
        "applicationCategory": "EducationalApplication",
        "operatingSystem": "Web",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "IDR"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.8",
            "ratingCount": "1200"
        }
    }
    <\/script>`}
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
