{{-- Scripts Component - Centralized JavaScript loading --}}
@props([
    'theme' => null,
])

{{-- jQuery (optional - only if needed) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Main Application JS --}}
<script src="{{ asset('js/app.js') }}"></script>

{{-- Component JS --}}
<script src="{{ asset('js/components/navigation.js') }}"></script>
<script src="{{ asset('js/components/ui.js') }}"></script>

{{-- Theme-specific scripts --}}
@if($theme === 'admin')
    {{-- Admin-specific scripts --}}
@elseif($theme === 'mahasiswa')
    {{-- Mahasiswa-specific scripts --}}
@endif

{{-- Additional scripts slot --}}
{{ $slot }}
