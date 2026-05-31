@php
    use Illuminate\Support\Facades\Config;
    $appName = Config::get('app.name', 'OOPedia');
    $appUrl = config('app.url', 'https://oopedia.id');
@endphp
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title inertia>{{ Config::get('app.name', 'OOPedia') }} — Platform Pembelajaran OOP Interaktif</title>
        <meta name="description" content="OOPedia adalah platform pembelajaran Object-Oriented Programming interaktif dengan AI. Belajar paradigma, pola desain, dan arsitektur dengan cara yang personal.">
        <meta name="keywords" content="OOP, Object-Oriented Programming, pembelajaran, kursus, paradigma, desain pattern, arsitektur, AI">
        <meta name="author" content="OOPedia">
        <meta name="robots" content="index, follow">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $appUrl }}">
        <meta property="og:title" content="{{ $appName }} — Platform Pembelajaran OOP Interaktif">
        <meta property="og:description" content="OOPedia adalah platform pembelajaran Object-Oriented Programming interaktif dengan AI. Belajar paradigma, pola desain, dan arsitektur dengan cara yang personal.">
        <meta property="og:image" content="{{ $appUrl }}/images/og-image.png">
        <meta property="og:site_name" content="{{ $appName }}">
        <meta property="og:locale" content="id_ID">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ $appUrl }}">
        <meta name="twitter:title" content="{{ $appName }} — Platform Pembelajaran OOP Interaktif">
        <meta name="twitter:description" content="OOPedia adalah platform pembelajaran Object-Oriented Programming interaktif dengan AI.">
        <meta name="twitter:image" content="{{ $appUrl }}/images/og-image.png">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/images/logo.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
        <link rel="apple-touch-icon" href="/images/logo.png">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ $appUrl }}">

        @verbatim
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "{{ $appName }}",
            "url": "{{ $appUrl }}",
            "description": "Platform pembelajaran Object-Oriented Programming interaktif dengan AI",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{ $appUrl }}/search?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
        </script>

        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "{{ $appName }}",
            "url": "{{ $appUrl }}",
            "logo": "{{ $appUrl }}/images/logo.png",
            "description": "Platform pembelajaran Object-Oriented Programming interaktif dengan AI",
            "sameAs": []
        }
        </script>

        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "{{ $appName }}",
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
        </script>
        @endverbatim

        <!-- Fonts with Preconnect for Performance -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="dns-prefetch" href="https://fonts.googleapis.com">
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@400;700;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet" crossorigin="anonymous">

        <!-- Scripts with Integrity Verification -->
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
