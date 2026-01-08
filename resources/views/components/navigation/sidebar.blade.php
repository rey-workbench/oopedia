@php
    $isAuthenticated = auth()->check();
    $userRole = $isAuthenticated ? auth()->user()->role_id : null;
    $isAdminRole = $isAuthenticated && in_array($userRole, [1, 2]);
    $isStudentRole = $isAuthenticated && in_array($userRole, [3, 4]);
@endphp

{{-- Sidebar Container --}}
<aside id="sidebar" class="fixed left-0 top-0 z-50 h-screen w-72 transition-transform duration-500 -translate-x-full lg:translate-x-0 overflow-y-auto 
    {{ $isAdminRole ? 'bg-[#0f172a] border-r border-slate-800' : 'bg-white border-r border-slate-50' }}">
    
    {{-- Header --}}
    <div class="px-8 py-10 flex items-center justify-between" data-intro="Ini adalah Logo OOPEDIA. Kamu bisa kembali ke dashboard dengan mengklik logo ini." data-step="1">
        <a href="{{ $isAdminRole ? route('admin.dashboard') : route('mahasiswa.dashboard') }}" class="flex items-center gap-4 group">
            <div class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg p-2 group-hover:rotate-12 transition-transform">
                <img src="{{ asset('images/logo.png') }}" alt="OOPedia" class="w-full h-auto">
            </div>
            <span class="text-2xl font-bold tracking-tight {{ $isAdminRole ? 'text-white' : 'text-slate-900' }}">OOPEDIA</span>
        </a>
        <button id="sidebar-close" class="lg:hidden p-2 rounded-xl {{ $isAdminRole ? 'text-slate-400 hover:text-white bg-slate-800' : 'text-slate-400 hover:text-slate-900 bg-slate-100' }}">
            <i class="fas fa-xmark"></i>
        </button>
    </div>

    {{-- Nav Links --}}
    <nav class="px-5 space-y-10 pb-10" data-intro="Gunakan menu navigasi ini untuk menjelajahi fitur-fitur yang tesedia di OOPEDIA." data-step="2">
        @if($isAdminRole)
            {{-- Admin Menu --}}
            <div class="space-y-6">
                <div class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 flex items-center gap-3">
                    <span class="w-2 h-0.5 bg-indigo-500/50"></span>
                    Utama
                </div>
                <div class="space-y-2">
                    <x-navigation.sidebar-link href="{{ route('admin.dashboard') }}" icon="fas fa-chart-line" :active="request()->routeIs('admin.dashboard')">Dashboard</x-navigation.sidebar-link>
                </div>
            </div>

            <div class="space-y-6">
                <div class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 flex items-center gap-3">
                    <span class="w-2 h-0.5 bg-indigo-500/50"></span>
                    Kurikulum
                </div>
                <div class="space-y-2">
                    <x-navigation.sidebar-link href="{{ route('admin.materials.index') }}" icon="fas fa-book" :active="request()->routeIs('admin.materials.*')">Kelola Materi</x-navigation.sidebar-link>
                    <x-navigation.sidebar-link href="{{ route('admin.questions.index') }}" icon="fas fa-vial" :active="request()->routeIs('admin.questions.*')">Kelola Soal</x-navigation.sidebar-link>
                    <x-navigation.sidebar-link href="{{ route('admin.question-banks.index') }}" icon="fas fa-database" :active="request()->routeIs('admin.question-banks.*')">Bank Soal</x-navigation.sidebar-link>
                </div>
            </div>

            <div class="space-y-6">
                <div class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 flex items-center gap-3">
                    <span class="w-2 h-0.5 bg-indigo-500/50"></span>
                    Manajemen
                </div>
                <div class="space-y-2">
                    <x-navigation.sidebar-link href="{{ route('admin.students.index') }}" icon="fas fa-user-graduate" :active="request()->routeIs('admin.students.*')">Data Mahasiswa</x-navigation.sidebar-link>
                    @if(auth()->user()->role_id == 1)
                        <x-navigation.sidebar-link href="{{ route('admin.users.index') }}" icon="fas fa-users-gear" :active="request()->routeIs('admin.users.*')">Daftar Admin</x-navigation.sidebar-link>
                    @endif
                <x-navigation.sidebar-link href="{{ route('admin.ueq.index') }}" icon="fas fa-poll-h" :active="request()->routeIs('admin.ueq.*')">Survey UEQ</x-navigation.sidebar-link>
                </div>
            </div>

            <div class="space-y-6 pt-10 border-t border-slate-800/50">
                <div class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 flex items-center gap-3">
                    <span class="w-2 h-0.5 bg-rose-500/50"></span>
                    Sesi
                </div>
                <div class="space-y-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-6 py-4 text-sm font-bold text-slate-400 hover:text-rose-500 hover:bg-slate-800/50 rounded-2xl transition-all group">
                            <i class="fas fa-power-off w-5 group-hover:scale-110 transition-transform"></i>
                            Keluar Sistem
                        </button>
                    </form>
                </div>
            </div>

        @elseif($isStudentRole)
            {{-- Student Menu --}}
            <div class="space-y-6">
                <div class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 flex items-center gap-3">
                    <span class="w-2 h-0.5 bg-blue-500/50"></span>
                    Belajar
                </div>
                <div class="space-y-2">
                    <x-navigation.sidebar-link href="{{ route('mahasiswa.dashboard') }}" icon="fas fa-shapes" :active="request()->routeIs('mahasiswa.dashboard*')">Dashboard</x-navigation.sidebar-link>
                    <x-navigation.sidebar-link href="{{ route('mahasiswa.materials.index') }}" icon="fas fa-book-open-reader" :active="request()->routeIs('mahasiswa.materials.*')">Materi PBO</x-navigation.sidebar-link>
                    <x-navigation.sidebar-link href="{{ route('mahasiswa.materials.questions.index') }}" icon="fas fa-vial-circle-check" :active="request()->routeIs('mahasiswa.materials.questions.*')">Latihan Soal</x-navigation.sidebar-link>
                </div>
            </div>

            <div class="space-y-6">
                <div class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 flex items-center gap-3">
                    <span class="w-2 h-0.5 bg-blue-500/50"></span>
                    Pencapaian
                </div>
                <div class="space-y-2">
                    <x-navigation.sidebar-link href="{{ route('mahasiswa.leaderboard') }}" icon="fas fa-trophy" :active="request()->routeIs('mahasiswa.leaderboard')">Leaderboard</x-navigation.sidebar-link>
                </div>
            </div>

            <div class="space-y-6 pb-10">
                <div class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 flex items-center gap-3">
                    <span class="w-2 h-0.5 bg-blue-500/50"></span>
                    Akun
                </div>
                <div class="space-y-2">
                    <x-navigation.sidebar-link href="{{ route('mahasiswa.profile') }}" icon="fas fa-user-astronaut" :active="request()->routeIs('mahasiswa.profile')">Profil Saya</x-navigation.sidebar-link>
                </div>
            </div>

            <div class="space-y-6 pt-10 border-t border-slate-100">
                <div class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 flex items-center gap-3">
                    <span class="w-2 h-0.5 bg-rose-500/50"></span>
                    Sesi
                </div>
                <div class="space-y-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-6 py-4 text-sm font-bold text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-2xl transition-all group">
                            <i class="fas fa-power-off w-5 group-hover:scale-110 transition-transform"></i>
                            Keluar Sistem
                        </button>
                    </form>
                </div>
            </div>
@endif
    </nav>
</aside>

{{-- Sidebar Overlay --}}
<div id="sidebar-overlay" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden hidden transition-all duration-300"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const closeBtn = document.getElementById('sidebar-close');
        
        // Toggle function will be called from navbar
        window.toggleSidebar = function() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        };

        if (closeBtn) {
            closeBtn.addEventListener('click', toggleSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', toggleSidebar);
        }
    });
</script>
