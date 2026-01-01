@props([
    'theme' => null,  // 'admin', 'mahasiswa', or null for default
])
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

{{-- Theme-specific CSS --}}
@if($theme === 'admin')
    <link href="{{ asset('css/themes/admin.css') }}" rel="stylesheet">
@elseif($theme === 'mahasiswa')
    <link href="{{ asset('css/themes/mahasiswa.css') }}" rel="stylesheet">
@endif

{{-- Component CSS --}}
<link href="{{ asset('css/components/navigation.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/forms.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/ui.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/loading-overlay.css') }}" rel="stylesheet">
<link href="{{ asset('css/components/base-layout.css') }}" rel="stylesheet">

{{-- Theme Layout Overrides --}}
@if($theme === 'admin')
    <link href="{{ asset('css/themes/admin-layout-overrides.css') }}" rel="stylesheet">
@endif

{{-- Additional styles slot --}}
{{ $slot }}
