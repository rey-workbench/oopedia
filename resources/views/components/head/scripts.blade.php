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
<script src="{{ asset('js/utils/http.js') }}"></script>
<script src="{{ asset('js/utils/ui.js') }}"></script>

{{-- Component JS --}}
{{-- Component JS --}}
{{-- Fix: navigation.js and ui.js do not exist. Using navbar.js and sidebar.js --}}
<script src="{{ asset('js/components/navbar.js') }}"></script>
<script src="{{ asset('js/components/sidebar.js') }}"></script>
{{-- <script src="{{ asset('js/components/ui.js') }}"></script> --}}

{{-- TinyMCE Library (Required before init) --}}
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script src="{{ asset('js/components/tinymce-init.js') }}"></script>
<script src="{{ asset('js/utils/scrollbar.js') }}"></script>

{{-- Theme-specific scripts --}}
@if($theme === 'admin')
    {{-- Admin-specific scripts --}}
@elseif($theme === 'mahasiswa')
    {{-- Mahasiswa-specific scripts --}}
@endif

{{-- Additional scripts slot --}}
{{ $slot }}
