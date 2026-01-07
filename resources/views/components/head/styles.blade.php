@props([
    'theme' => null,  // 'admin', 'mahasiswa', or null for default
])
<!-- Current Theme: {{ $theme ?? 'null' }} -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

{{-- Bootstrap CSS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

{{-- Material Icons (for admin) --}}
@if($theme === 'admin')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endif

{{-- Main Application CSS --}}
<link href="{{ asset('css/app.css') }}" rel="stylesheet">

{{-- Base CSS Components --}}
<link href="{{ asset('css/components/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/utils.css') }}" rel="stylesheet">

{{-- Theme-specific CSS --}}
@if($theme === 'admin')
    {{-- Modular Admin Theme --}}
    <link href="{{ asset('css/themes/admin/colors.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/admin/layout.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/admin/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/admin/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/admin/introjs.css') }}" rel="stylesheet">
@elseif($theme === 'mahasiswa')
    {{-- Modular Student Theme --}}
    <link href="{{ asset('css/themes/student/layout.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/student/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/student/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/student/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/student/materials.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/student/profile.css') }}" rel="stylesheet">
@endif

{{-- Component CSS --}}
<link href="{{ asset('css/components/buttons.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/forms.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/cards.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/alerts.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/badges.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/images.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/loading.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/base-layout.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/navbar.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/sidebar.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/footer.css') }}" rel="stylesheet">



{{-- Additional styles slot --}}
{{ $slot }}
