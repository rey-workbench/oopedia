<x-layouts.app title="Latihan Soal PBO" theme="mahasiswa">
    <x-slot:styles>
        <link href="{{ asset('css/mahasiswa/materials/questions/index.css') }}" rel="stylesheet">
        <!-- Intro.js CSS -->
        <link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">
    </x-slot:styles>

    @if(auth()->check() && auth()->user()->role_id === 4)
    <form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="redirect" value="{{ route('login') }}">
    </form>

    <form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="redirect" value="{{ route('register') }}">
    </form>
    @endif

    @if(!auth()->check() || (auth()->check() && auth()->user()->role_id === 4))
    <x-ui.alert variant="warning" class="mb-4" :dismissible="false">
        <strong>Mode Tamu Aktif!</strong> 
        Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap tingkat kesulitan yang ditampilkan. 
        Untuk akses penuh, silakan 
        <a href="{{ route('login') }}" class="alert-link">login</a> 
        atau 
        <a href="{{ route('register') }}" class="alert-link">daftar</a> 
        sebagai mahasiswa.
    </x-ui.alert>
    @endif

    <div class="dashboard-header text-center">
        <h1 class="main-title">Latihan Soal PBO</h1>
        <div class="title-underline"></div>
        <p class="subtitle mt-3">Uji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi</p>
    </div>

    <div class="materials-container">
        <div class="row">
            @foreach($materials as $material)
            <div class="col-md-12 mb-4">
                <a href="{{ route('mahasiswa.materials.questions.levels', $material->id) }}" class="card-link">
                    <div class="material-question-card horizontal">
                        <!-- Bagian Gambar Material (Kiri) -->
                        <div class="material-left-section">
                            @if($material->media && $material->media->isNotEmpty())
                                <div class="material-question-image">
                                    <img src="{{ $material->media->first()->media_url }}" alt="{{ $material->title }}">
                                </div>
                            @else
                                <div class="material-question-image default-image">
                                    <div class="no-image-icon">
                                        <i class="fas fa-code"></i>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Bagian Konten (Kanan) -->
                        <div class="material-right-section">
                            <div class="material-top-section">
                                <div class="material-info">
                                    <div class="material-badges">
                                        <div class="material-badge">
                                            <span class="badge-text">Tersedia</span>
                                        </div>
                                    </div>
                                    <h2 class="material-question-title">{{ $material->title }}</h2>
                                    <!-- Material Meta Info dengan jumlah mahasiswa sebenarnya -->
                                    <div class="material-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-users"></i>
                                            <span>{{ $material->student_count }} Mahasiswa</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Navigation Icon -->
                                <div class="nav-icon">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                            
                            <div class="material-bottom-section">
                                <!-- Progress Section -->
                                @if(!auth()->check() || (auth()->check() && auth()->user()->role_id === 4))
                                    <!-- Guest Mode Display -->
                                    <div class="guest-limit-section">
                                        <div class="guest-info-icon">
                                            <i class="fas fa-lock text-warning"></i>
                                        </div>
                                        <div class="guest-limit-text">
                                            <span>Mode Tamu: Akses Terbatas</span>
                                            <small>Hanya 3 soal per tingkat kesulitan. Login untuk akses penuh</small>
                                        </div>
                                    </div>
                                @else
                                    <!-- Regular Progress Section for Registered Users -->
                                    <div class="progress-section">
                                        <div class="progress-header">
                                            <span class="progress-label">Progress</span>
                                            <span class="progress-percentage">{{ $material->progress_percentage }}%</span>
                                        </div>
                                        <div class="progress-bar-wrapper">
                                            <div class="progress-bar-bg">
                                                <div class="progress-bar-fill" style="width: {{ $material->progress_percentage }}%"></div>
                                            </div>
                                        </div>
                                        <div class="progress-detail">
                                            {{ $material->completed_questions }} dari {{ $material->total_questions }} soal selesai
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Action Button -->
                                <div class="btn-start-exercise">
                                    <span>Detail</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <x-slot:scripts>
        <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
        <script src="{{ asset('js/mahasiswa/materials/questions/index.js') }}"></script>
    </x-slot:scripts>
</x-layouts.app>