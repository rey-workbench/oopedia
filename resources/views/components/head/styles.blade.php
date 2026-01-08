@props([
    'theme' => null,
])

{{-- Google Fonts - Poppins (User Request) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

{{-- Material Icons (for admin) --}}
@if($theme === 'admin')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endif

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{-- IntroJS CSS --}}
@if($theme === 'mahasiswa' || $theme === 'admin' || $theme === 'guest')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css">
@endif

{{-- Tailwind CSS via Vite --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Custom Style Tokens --}}
<style>
    :root {
        --font-poppins: 'Poppins', sans-serif;
    }
    
    body {
        font-family: var(--font-poppins);
    }
    
    h1, h2, h3, h4, h5, h6, .font-heading, .font-poppins {
        font-family: var(--font-poppins);
    }
</style>

{{ $slot }}
