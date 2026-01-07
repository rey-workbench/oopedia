@if($isAdminRole())
    {{-- Admin/Dosen Navbar --}}
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
        navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $titlePage }}</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">{{ $titlePage }}</h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item d-flex align-items-center">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-ui.button type="submit" variant="link" class="nav-link px-3" icon="logout">
                                <span class="nav-link-text ms-1">Logout</span>
                            </x-ui.button>
                        </form>
                    </li>
                    <li class="nav-item px-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" onclick="resetAllTutorials()">
                            <i class="fa fa-redo me-sm-1"></i>
                            <span class="d-sm-inline d-none">Reset Tutorial</span>
                        </a>
                    </li>
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="{{ asset('js/components/navbar.js') }}"></script>

@elseif($isStudentRole())
    {{-- Mahasiswa/Guest Navbar --}}
    @push('css')
    <link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">
    @endpush

    <nav class="navbar">
        <div class="container-fluid">
            <!-- Left side group -->
            <div class="d-flex align-items-center h-100">
                <!-- Sidebar Toggle Button - hanya muncul di mobile -->
                <x-ui.button id="sidebarToggleBtn" class="btn-icon d-lg-none me-2">
                    <i class="fas fa-bars"></i>
                </x-ui.button>
                
                <!-- Navigation links -->
                <div class="nav-links">
                    <ul class="nav-menu">
                        @foreach($getMahasiswaNavItems() as $item)
                            @php
                                $isActive = $isRouteActive($item['pattern']);
                                if (isset($item['excludePattern']) && $isRouteActive($item['excludePattern'])) {
                                    $isActive = false;
                                }
                            @endphp
                            <li>
                                <a href="{{ route($item['route']) }}" 
                                   class="nav-link {{ $isActive ? 'active' : '' }}"
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="bottom" 
                                   title="{{ $item['tooltip'] ?? '' }}">
                                    <i class="fas {{ $item['icon'] }} me-2"></i>
                                    <span>{{ $item['label'] }}</span>
                                    @if(!empty($item['badge']))
                                        <small class="badge {{ $item['badgeClass'] ?? 'bg-warning text-dark' }} ms-1">{{ $item['badge'] }}</small>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Right side - Profile/Logout/Login/Register -->
            <div class="d-flex align-items-center">
                @guest
                    <div class="auth-buttons me-3 d-none d-md-flex">
                        <x-ui.button href="{{ route('login') }}" variant="primary" size="sm" class="me-2" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="Login untuk akses semua soal latihan tanpa batasan"
                           icon="sign-in-alt">
                           Login
                        </x-ui.button>
                        <x-ui.button href="{{ route('register') }}" variant="primary" size="sm"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="Buat akun baru untuk akses semua soal latihan tanpa batasan"
                           icon="user-plus">
                           Register
                        </x-ui.button>
                    </div>
                    <!-- Tampilkan tombol kecil untuk login di mobile -->
                    <div class="d-md-none">
                        <x-ui.button href="{{ route('login') }}" size="sm" class="btn-icon" icon="sign-in-alt" />
                    </div>
                @endguest
                
                @auth
                <div class="dropdown profile-dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('images/profile.gif') }}" alt="Profile" class="profile-image me-1" width="30" height="30">
                        <span class="profile-name d-none d-sm-inline">
                            {{ $userName }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-ui.button type="submit" class="dropdown-item" variant="ghost" icon="logout">
                                    Logout
                                </x-ui.button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    @if($isRouteActive('mahasiswa.dashboard*'))
    <div class="container-fluid px-4 pt-3">
        @guest
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Silakan login untuk mengakses semua fitur pembelajaran
            </div>
        @endguest
    </div>
    @endif

    @push('scripts')
    <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
    <script>
        // Simpan URL route
        const routeLogin = "{{ route('login') }}";
        const routeRegister = "{{ route('register') }}";
        const routeDashboard = "{{ route('mahasiswa.dashboard') }}";
        const routeMateri = "{{ route('mahasiswa.materials.index') }}";
        const routeSoal = "{{ route('mahasiswa.materials.questions.index') }}";
        const routeLeaderboard = "{{ route('mahasiswa.leaderboard') }}";
        const isLoggedIn = {{ $isAuthenticated() ? 'true' : 'false' }};

        // Variabel untuk menandai klik sidebar
        let sidebarClicked = false;

        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi tooltip
            var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(function(el) {
                return new bootstrap.Tooltip(el);
            });

            // Only show tutorial for authenticated users on dashboard page and only once
            const isMainTutorialCompleted = sessionStorage.getItem('main_tutorial_complete');
            const isDashboardPage = {{ $isRouteActive('mahasiswa.dashboard*') ? 'true' : 'false' }};
            const isQuestionsPage = {{ $isRouteActive('mahasiswa.materials.questions*') ? 'true' : 'false' }};
            
            // Skip tutorial for guests and on question pages
            if (isLoggedIn && !isMainTutorialCompleted && isDashboardPage && !sidebarClicked && !sessionStorage.getItem('skip_tour')) {
                startTutorial();
            }
            
            // SOLUSI BARU: Pendekatan langsung untuk toggle sidebar
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            const sidebar = document.querySelector('.sidebar');
            let sidebarBackdrop = document.querySelector('.sidebar-backdrop');
            
            if (!sidebarBackdrop) {
                sidebarBackdrop = document.createElement('div');
                sidebarBackdrop.className = 'sidebar-backdrop';
                document.body.appendChild(sidebarBackdrop);
            }
            
            function toggleSidebar() {
                if (sidebar) {
                    sidebar.classList.toggle('show');
                    sidebarBackdrop.classList.toggle('show');
                }
            }
            
            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleSidebar();
                });
            }
            
            sidebarBackdrop.addEventListener('click', function() {
                if (sidebar && sidebar.classList.contains('show')) {
                    toggleSidebar();
                }
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
                    toggleSidebar();
                }
            });
            
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 991.98 && sidebar && sidebar.classList.contains('show')) {
                        toggleSidebar();
                    }
                });
            });
            
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            if (sidebarCloseBtn) {
                sidebarCloseBtn.addEventListener('click', function() {
                    if (sidebar && sidebar.classList.contains('show')) {
                        toggleSidebar();
                    }
                });
            }
        });

        // Fungsi untuk memulai tutorial
        function startTutorial() {
            let steps = [
                {
                    intro: "Halo! Mari kita mulai dengan mengenal tampilan website ini."
                }
            ];

            if (!isLoggedIn) {
                steps.push(
                    {
                        element: document.querySelector('a.btn[href="' + routeLogin + '"]'),
                        intro: "Klik tombol Login ini untuk masuk ke akun Anda"
                    },
                    {
                        element: document.querySelector('a.btn[href="' + routeRegister + '"]'),
                        intro: "Atau klik tombol Register untuk membuat akun baru"
                    }
                );
            }

            if (isLoggedIn) {
                steps.push({
                    element: document.querySelector('.nav-link[href="' + routeDashboard + '"]'),
                    intro: "Ini adalah dashboard. Kamu bisa melihat ringkasan aktivitas di sini."
                });
            }
            
            steps.push(
                {
                    element: document.querySelector('.nav-link[href="' + routeMateri + '"]'),
                    intro: "Di sini kamu bisa belajar berbagai materi pembelajaran."
                },
                {
                    element: document.querySelector('.nav-link[href="' + routeSoal + '"]'),
                    intro: "Cek pemahamanmu di bagian latihan soal ini!"
                },
                {
                    element: document.querySelector('.nav-link[href="' + routeLeaderboard + '"]'),
                    intro: "Periksa peringkat dan capaian pengguna di leaderboard!"
                },
                {
                    intro: "Siap menjelajah? Klik di mana saja untuk menyelesaikan tutorial ini!"
                }
            );

            introJs().setOptions({
                steps: steps,
                showProgress: true,
                exitOnOverlayClick: true,
                showBullets: false,
                scrollToElement: true,
                nextLabel: 'Berikutnya',
                prevLabel: 'Sebelumnya',
                doneLabel: 'Selesai'
            }).oncomplete(function() {
                sessionStorage.setItem('main_tutorial_complete', 'true');
            }).start();
        }

        // Tambahkan event listener untuk semua link di sidebar
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function(event) {
                sidebarClicked = true;
                sessionStorage.setItem('skip_tour', 'true');
                
                if (window.innerWidth <= 991.98) {
                    const sidebar = document.querySelector('.sidebar');
                    const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
                    
                    if (sidebar && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        if (sidebarBackdrop) {
                            sidebarBackdrop.classList.remove('show');
                        }
                    }
                }
            });
        });
    </script>
    @endpush

@else
    {{-- Guest Navbar (for login/register pages) --}}
    <nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent">
        <div class="container">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 text-white" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="OOPedia" height="50" class="me-2 navbar-logo">
                <span class="logo-fallback">OOPedia</span>
            </a>
            <x-ui.button type="button" class="navbar-toggler shadow-none ms-2" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation" variant="ghost">
                <span class="navbar-toggler-icon mt-2">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </span>
            </x-ui.button>
            <div class="collapse navbar-collapse" id="navigation">
                <ul class="navbar-nav ms-auto">
                    @guest
                        {{-- Login/Register buttons can be added here if needed --}}
                    @endguest

                    @auth
                        @if(Auth::check())
                            <li class="nav-item">
                                <a class="nav-link text-warning fw-bold" href="#">
                                    Mode Tamu Aktif
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
@endif