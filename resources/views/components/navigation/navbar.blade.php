@php
    $isAuthenticated = auth()->check();
    $userRole = $isAuthenticated ? auth()->user()->role_id : null;
    $isAdminRole = $isAuthenticated && in_array($userRole, [1, 2]);
    $isStudentRole = $isAuthenticated && in_array($userRole, [3, 4]);
    $userName = $isAuthenticated ? auth()->user()->name : 'Tamu';
@endphp

<nav class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-gray-100 px-4 sm:px-6 lg:px-8">
    <div class="flex h-20 items-center justify-between">
        {{-- Left: Breadcrumbs or Title --}}
        <div class="flex items-center gap-4">
            <button data-sidebar-toggle class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-50 transition-colors">
                <i class="fas fa-bars-staggered text-xl"></i>
            </button>
            
            <div class="hidden sm:block" data-intro="Breadcrumbs menunjukkan di mana posisi kamu saat ini." data-step="3">
                <nav class="flex text-sm" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <span class="text-slate-400 font-medium">Halaman</span>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-[10px] text-slate-300 mx-2"></i>
                                <span class="text-slate-900 font-bold italic tracking-tight uppercase">{{ $titlePage }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Right: User Actions --}}
        <div class="flex items-center gap-2 sm:gap-4">
            @auth
                <div class="hidden md:flex flex-col items-end mr-2">
                    <span class="text-sm font-black text-slate-900 italic leading-none">{{ $userName }}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $isAdminRole ? 'Administrator' : 'Mahasiswa' }}</span>
                </div>

                {{-- Tutorial Button --}}
                <button id="start-page-tour" class="p-2 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-blue-600 transition-all group relative" title="Mulai Tutorial" data-intro="Klik tombol ini kapan saja jika kamu butuh bantuan atau ingin mengulang tutorial di halaman ini." data-step="4">
                    <i class="fas fa-question-circle text-xl"></i>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                    </span>
                </button>

                <div class="relative group" data-intro="Kelola profil kamu atau keluar dari akun melalui menu ini." data-step="5">
                    <button class="flex items-center gap-2 p-1 rounded-2xl border-2 border-transparent hover:border-blue-100 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl overflow-hidden shadow-inner ring-2 ring-white group-hover:ring-blue-50 transition-all">
                            <img src="{{ asset('images/profile.gif') }}" alt="Profile" class="w-full h-full object-cover">
                        </div>
                    </button>
                    
                    {{-- Dropdown --}}
                    <div class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl bg-white p-2 shadow-2xl ring-1 ring-black/5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100">
                        <div class="px-4 py-3 border-b border-slate-50 mb-1">
                            <p class="text-sm font-black text-slate-900 italic">{{ $userName }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        
                        <a href="{{ route('mahasiswa.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                            <i class="fas fa-user-circle w-5"></i>
                            Profil Saya
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="px-6 py-2.5 text-sm font-black italic uppercase tracking-widest text-slate-600 hover:text-blue-600 transition-all">Masuk</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-slate-900 text-white text-sm font-black italic uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-slate-200">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
