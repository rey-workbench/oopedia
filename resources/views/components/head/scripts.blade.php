@props([
    'theme' => null,
])

{{-- jQuery (Minimized usage) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- IntroJS --}}
@if($theme === 'mahasiswa' || $theme === 'admin' || $theme === 'guest')
<script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/intro.min.js"></script>
@endif

{{-- Confetti --}}
@if($theme === 'mahasiswa')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
@endif

{{-- Quill JS is now bundled via Vite in admin.js --}}
@if($theme === 'admin')
    {{-- Admin specific scripts if any --}}
@endif

{{-- Navbar/Sidebar Logic --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('aside');
        const overlay = document.getElementById('sidebar-overlay');
        const toggles = document.querySelectorAll('[data-sidebar-toggle]');
        
        const toggleSidebar = () => {
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
                if (overlay) overlay.classList.toggle('hidden');
            }
        };

        toggles.forEach(btn => btn.addEventListener('click', toggleSidebar));
        if (overlay) overlay.addEventListener('click', toggleSidebar);
    });
</script>

{{ $slot }}
