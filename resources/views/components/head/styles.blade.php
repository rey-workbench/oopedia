@props([
  'theme' => null,
])

@php
  $jsBundle = match($theme) {
    'admin' => 'resources/js/bundles/admin.js',
    'mahasiswa' => 'resources/js/bundles/mahasiswa.js',
    default => 'resources/js/app.js',
  };
@endphp

{{-- 1. Typography & Icons --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@if($theme === 'admin')
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endif

{{-- 2. Vendor CSS --}}
@if(in_array($theme, ['mahasiswa', 'admin', 'guest']))
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css">
@endif

{{-- 3. Core Framework & App Assets --}}
@vite(['resources/css/app.css', $jsBundle])

{{-- 4. Custom Design tokens --}}
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
