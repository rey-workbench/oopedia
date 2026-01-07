@if($isAdminRole())
    {{-- Admin/Dosen Sidebar --}}
    <aside
        class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark"
        id="sidenav-main">
        <br>
        <div class="sidenav-header d-flex flex-column align-items-center justify-content-center py-3">
            <a class="navbar-brand w-100 text-center" href="{{ route($getDashboardRoute()) }}">
                <img src="{{ asset('images/logo.png') }}" alt="OOPEDIA" class="img-fluid logo-component">
            </a>
        </div>
        <br>
        <hr class="horizontal light mt-0 mb-2">
        <div class="d-flex align-items-center mx-3">
            <i class="material-icons opacity-10 me-2">person</i>
            <div class="flex-grow-1 text-center">
                <span class="font-weight-bold text-white">{{ $userName }}</span>
            </div>
            <span class="text-white ms-2">{{ $userRole }}</span>
        </div>
        <hr class="horizontal light mt-2 mb-2">
        <div class="w-auto max-height-vh-100" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                {{-- Menu Dashboard untuk Semua Role --}}
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'dashboard' ? 'active bg-gradient-primary' : '' }}"
                        href="{{ route($getDashboardRoute()) }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">dashboard</i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>

                {{-- Menu Pembelajaran --}}
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Kelola Pembelajaran</h6>
                </li>
                
                {{-- Menu Materi --}}
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'materials' ? 'active bg-gradient-primary' : '' }}"
                        href="{{ route('admin.materials.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">library_books</i>
                        </div>
                        <span class="nav-link-text ms-1">Kelola Materi</span>
                    </a>
                </li>

                {{-- Menu Soal dengan Dropdown --}}
                <li class="nav-item">
                    <a class="nav-link text-white" 
                       data-bs-toggle="collapse" 
                       href="#questionsMenu" 
                       role="button" 
                       aria-expanded="{{ str_contains($activePage, 'questions') ? 'true' : 'false' }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">quiz</i>
                        </div>
                        <span class="nav-link-text ms-1">Kelola Soal</span>
                        <i class="material-icons ms-auto">keyboard_arrow_down</i>
                    </a>
                    <div class="collapse {{ str_contains($activePage, 'questions') ? 'show' : '' }}" id="questionsMenu">
                        <ul class="nav">
                            @forelse($materials ?? [] as $material)
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ $activePage == 'questions-'.$material->id ? 'active bg-gradient-primary' : '' }}"
                                        href="{{ route('admin.materials.questions.index', $material->id) }}">
                                        <span class="sidenav-mini-icon">
                                            <i class="material-icons opacity-10">article</i>
                                        </span>
                                        <span class="sidenav-normal ms-2">{{ $material->title }}</span>
                                    </a>
                                </li>
                            @empty
                                <li class="nav-item">
                                    <span class="nav-link text-white-50">
                                        <i class="material-icons opacity-10">info</i>
                                        <span class="ms-2">Belum ada materi</span>
                                    </span>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </li>

                {{-- Menu Bank Soal hanya untuk Admin dan Superadmin --}}
                @if($isAuthenticated() && auth()->user()->role_id <= 2)
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'question-banks' ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.question-banks.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">quiz</i>
                        </div>
                        <span class="nav-link-text ms-1">Bank Soal</span>
                    </a>
                </li>
                @endif


                {{-- Menu Adaptive System dengan Dropdown hanya untuk Admin dan Superadmin --}}
                @if($isAuthenticated() && auth()->user()->role_id <= 2)
                <li class="nav-item">
                    <a class="nav-link text-white" 
                       data-bs-toggle="collapse" 
                       href="#adaptiveMenu" 
                       role="button" 
                       aria-expanded="{{ str_contains($activePage, 'adaptive') || str_contains($activePage, 'formulas') ? 'true' : 'false' }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">psychology</i>
                        </div>
                        <span class="nav-link-text ms-1">Adaptive System</span>
                        <i class="material-icons ms-auto">keyboard_arrow_down</i>
                    </a>
                    <div class="collapse {{ str_contains($activePage, 'adaptive') || str_contains($activePage, 'formulas') ? 'show' : '' }}" id="adaptiveMenu">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-white {{ $activePage == 'adaptive-rules' ? 'active bg-gradient-primary' : '' }}" 
                                   href="{{ route('admin.adaptive-rules.index') }}">
                                    <span class="sidenav-mini-icon">
                                        <i class="material-icons opacity-10">rule</i>
                                    </span>
                                    <span class="sidenav-normal ms-2">Adaptive Rules</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ $activePage == 'formulas' ? 'active bg-gradient-primary' : '' }}" 
                                   href="{{ route('admin.formulas.index') }}">
                                    <span class="sidenav-mini-icon">
                                        <i class="material-icons opacity-10">functions</i>
                                    </span>
                                    <span class="sidenav-normal ms-2">Formulas</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ $activePage == 'attributes' ? 'active bg-gradient-primary' : '' }}" 
                                   href="{{ route('admin.attribute-definitions.index') }}">
                                    <span class="sidenav-mini-icon">
                                        <i class="material-icons opacity-10">settings</i>
                                    </span>
                                    <span class="sidenav-normal ms-2">Attributes (View Only)</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                {{-- Menu Progress Mahasiswa --}}
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Data Mahasiswa</h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'students' ? 'active bg-gradient-primary' : '' }}"
                        href="{{ route('admin.students.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">school</i>
                        </div>
                        <span class="nav-link-text ms-1">Data Mahasiswa</span>
                    </a>
                </li>

                {{-- Menu Admin hanya untuk Superadmin --}}
                @if($isAuthenticated() && auth()->user()->role_id == 1)
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Data Dosen</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'users' ? 'active bg-gradient-primary' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">person</i>
                        </div>
                        <span class="nav-link-text ms-1">Data Dosen</span>
                    </a>
                </li>
                
                {{-- Menu Admin Pending hanya untuk Superadmin --}}
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'pending-users' ? 'active bg-gradient-primary' : '' }}" 
                       href="{{ route('admin.pending-admins') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">person_add</i>
                        </div>
                        <span class="nav-link-text ms-1">Dosen Pending</span>
                        @php
                            $pendingCount = $getPendingAdminsCount();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge bg-danger ms-auto">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
                @endif

                {{-- Menu UEQ Survey Results hanya untuk Admin dan Superadmin --}}
                @if($isAuthenticated() && auth()->user()->role_id <= 2)
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Feedback</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'ueq' ? 'active bg-gradient-primary' : '' }}"
                        href="{{ route('admin.ueq.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">poll</i>
                        </div>
                        <span class="nav-link-text ms-1">UEQ Survey Results</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </aside>

    @push('css')
    <link rel="stylesheet" href="{{ asset('css/components/sidebar.css') }}">
    @endpush

    @push('scripts')
    <script src="{{ asset('js/components/sidebar.js') }}"></script>
    @endpush

@elseif($isStudentRole())
    {{-- Mahasiswa/Guest Sidebar --}}
    <div class="sidebar">
        <!-- Close button for mobile -->
        <button class="sidebar-close d-block d-lg-none" id="sidebarCloseBtn">
            <i class="fas fa-times"></i>
        </button>

        <!-- Logo Section -->
        <div class="text-center py-3">
            <a href="{{ route('mahasiswa.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="OOPEDIA" class="img-fluid logo-component--small">
            </a>
        </div>

        <div class="sidebar-header">
            <h5 class="sidebar-title">{{ $getSidebarTitle() }}</h5>
        </div>

        @if($isRouteActive('mahasiswa.profile') && $isAuthenticated())
            {{-- Profile page menu --}}
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('mahasiswa.dashboard') }}"
                       class="menu-item {{ $isRouteActive('mahasiswa.dashboard') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat statistik dan progres pembelajaran Anda">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.profile') }}" 
                       class="menu-item {{ $isRouteActive('mahasiswa.profile') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat dan ubah profil Anda">
                        <i class="fas fa-user"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
            </ul>
        @elseif($isRouteActive('mahasiswa.dashboard*') && $isAuthenticated())
            {{-- Dashboard menu --}}
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('mahasiswa.dashboard') }}" 
                       class="menu-item {{ $isRouteActive('mahasiswa.dashboard') && !$isRouteActive('mahasiswa.dashboard.*') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat ringkasan progres pembelajaran Anda">
                        <i class="fas fa-home"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.dashboard.in-progress') }}" 
                       class="menu-item {{ $isRouteActive('mahasiswa.dashboard.in-progress') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat materi yang sedang Anda pelajari">
                        <i class="fas fa-spinner"></i>
                        <span>Sedang Dipelajari</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.dashboard.completed') }}" 
                       class="menu-item {{ $isRouteActive('mahasiswa.dashboard.completed') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat materi yang telah Anda selesaikan">
                        <i class="fas fa-check-circle"></i>
                        <span>Selesai</span>
                    </a>
                </li>
            </ul>
        @elseif($isRouteActive('mahasiswa.ueq.create') || $isRouteActive('mahasiswa.ueq.thankyou'))
            {{-- UEQ Survey menu --}}
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('mahasiswa.dashboard') }}" 
                       class="menu-item"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Kembali ke dashboard">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.ueq.create') }}" 
                       class="menu-item active"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Isi survei pengalaman pengguna">
                        <i class="fas fa-poll"></i>
                        <span>UEQ Survey</span>
                    </a>
                </li>
            </ul>
        @elseif($isRouteActive('mahasiswa.materials*') && !$isRouteActive('mahasiswa.materials.questions*'))
            {{-- Materials page menu --}}
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('mahasiswa.materials.index') }}" 
                       class="menu-item {{ request()->is('mahasiswa/materials') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat semua materi pembelajaran">
                        <i class="fas fa-list"></i>
                        <span>Semua Materi</span>
                    </a>
                </li>
            </ul>
            
            {{-- Materi PBO Section --}}
            <div class="sidebar-header mt-3">
                <h5 class="sidebar-title">Materi PBO</h5>
            </div>
            
            <ul class="nav-menu">
                @foreach($getSidebarMaterials() as $m)
                    <li class="materi-item {{ request()->segment(3) == $m->id ? 'active' : '' }}">
                        <a href="{{ route('mahasiswa.materials.show', $m->id) }}"
                           class="menu-item {{ request()->segment(3) == $m->id ? 'active' : '' }}"
                           data-bs-toggle="tooltip"
                           data-bs-placement="right"
                           title="Pelajari materi {{ $m->title }}">
                            <i class="fas fa-book"></i>
                            <span>{{ $m->title }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @elseif($isRouteActive('mahasiswa.materials.questions*'))
            {{-- Questions page menu --}}
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                       class="menu-item {{ request()->is('mahasiswa/materials/questions') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat daftar latihan soal per materi">
                        <i class="fas fa-list"></i>
                        <span>Daftar Latihan Soal</span>
                    </a>
                </li>
            </ul>

            {{-- Daftar Materi --}}
            <div class="sidebar-header mt-3">
                <h5 class="sidebar-title">MATERI</h5>
            </div>

            <ul class="nav-menu">
                @foreach($getSidebarMaterials() as $materialItem)
                    <li>
                        <a href="{{ route('mahasiswa.materials.questions.show', $materialItem->id) }}"
                           class="menu-item {{ request()->segment(3) == $materialItem->id ? 'active' : '' }}"
                           data-bs-toggle="tooltip"
                           data-bs-placement="right"
                           title="Latihan soal untuk materi {{ $materialItem->title }}">
                            <i class="fas fa-folder-open"></i>
                            <span>{{ $materialItem->title }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            {{-- Generic navigation --}}
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('mahasiswa.dashboard') }}" 
                       class="menu-item {{ $isRouteActive('mahasiswa.dashboard') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat statistik dan progres pembelajaran Anda">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.materials.index') }}" 
                       class="menu-item {{ $isRouteActive('mahasiswa.materials.index') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Akses materi pembelajaran PBO">
                        <i class="fas fa-book"></i>
                        <span>Materi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                       class="menu-item {{ $isRouteActive('mahasiswa.materials.questions.index') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Uji pemahaman Anda dengan latihan soal">
                        <i class="fas fa-question-circle"></i>
                        <span>Latihan Soal</span>
                    </a>
                </li>
                @if($isAuthenticated())
                    <li>
                        <a href="{{ route('mahasiswa.profile') }}" 
                           class="menu-item {{ $isRouteActive('mahasiswa.profile') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="right" 
                           title="Lihat dan ubah profil Anda">
                            <i class="fas fa-user"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                @endif
            </ul>
        @endif

        {{-- Additional sections (not on UEQ pages) --}}
        @if(!$isRouteActive('mahasiswa.ueq.create') && !$isRouteActive('mahasiswa.ueq.thankyou'))
            {{-- Leaderboard Section --}}
            <div class="sidebar-header mt-4">
                <h5 class="sidebar-title">Leaderboard</h5>
            </div>
            
            <ul class="nav-menu">
                <li>
                    @if($isAuthenticated())
                        <a href="{{ route('mahasiswa.leaderboard') }}" 
                           class="menu-item {{ $isRouteActive('mahasiswa.leaderboard') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="right" 
                           title="Lihat peringkat pengguna">
                            <i class="fas fa-trophy"></i>
                            <span>Peringkat</span>
                        </a>
                    @else
                        <a href="#" 
                           class="menu-item"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="right" 
                           title="Silakan login untuk melihat peringkat">
                            <i class="fas fa-trophy"></i>
                            <span>Peringkat</span>
                            <span class="badge bg-danger text-white ms-1">Perlu Login</span>
                        </a>
                    @endif
                </li>
            </ul>

            {{-- UEQ Survey Section (only for logged-in students) --}}
            @if($isAuthenticated() && auth()->user()->role_id == 3)
            <div class="sidebar-header mt-4">
                <h5 class="sidebar-title">Feedback</h5>
            </div>
            
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('mahasiswa.ueq.create') }}" 
                       class="menu-item {{ $isRouteActive('mahasiswa.ueq.create') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Berikan feedback tentang sistem">
                        <i class="fas fa-poll"></i>
                        <span>UEQ Survey</span>
                    </a>
                </li>
            </ul>
            @endif
        @endif
    </div>
    @push('scripts')
    <script src="{{ asset('js/components/sidebar.js') }}"></script>
    @endpush
@endif
