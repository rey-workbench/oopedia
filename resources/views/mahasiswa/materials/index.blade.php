<x-layouts.app title="Materi Pembelajaran" theme="mahasiswa">
    @if(auth()->check() && auth()->user() === null)
    <!-- Hidden forms for guest logout and redirect -->
    <form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="redirect" value="{{ route('login') }}">
    </form>

    <form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="redirect" value="{{ route('register') }}">
    </form>
    @endif

    <div class="dashboard-header text-center">
        <h1 class="main-title">Materi Pemrograman Berorientasi Objek</h1>
        <div class="title-underline"></div>
        <p class="subtitle mt-3">Pelajari konsep dasar dan lanjutan tentang Pemrograman Berorientasi Objek</p>
    </div>

    <div class="row mt-5">
        @foreach($materials as $material)
        <div class="col-md-4 mb-4">
            <div class="material-card">
                <!-- Badge status di pojok kiri atas -->
                <div class="material-badge">
                    <span class="badge-text">Tersedia</span>
                </div>
                
                <!-- Menampilkan gambar jika ada -->
                @if($material->media && $material->media->isNotEmpty())
                    <div class="material-image">
                        <img src="{{ $material->media->first()->media_url }}" alt="{{ $material->title }}" class="img-fluid">
                    </div>
                @else
                    <div class="material-image default-image">
                        <div class="no-image-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                    </div>

                @endif
                
                <div class="material-icon">
                    <i class="fas fa-book"></i>
                </div>
                
                <div class="material-content">
                    <div class="material-title">
                        {{ $material->title }}
                    </div>
                    
                    <div class="material-meta">
                        <div class="meta-item">
                            <i class="fas fa-user"></i> {{ $material->creator ? $material->creator->name : 'Admin' }}

                        </div>
                        <div class="meta-item">
                            <i class="far fa-calendar-alt"></i> {{ $material->updated_at->format('d M Y') }}
                        </div>
                    </div>
                    
                    <div class="content-divider"></div>
                    
                    <div class="material-stats">
                        <div class="stats-pill">
                            <i class="fas fa-question-circle"></i> 
                            @php
                                $isGuest = !auth()->check();
                                
                                // Calculate configured question count
                                if ($isGuest) {
                                    // For guests, limit to 3 questions per difficulty level
                                    $beginnerCount = min(3, $material->questions->where('difficulty', 'beginner')->count());
                                    $mediumCount = min(3, $material->questions->where('difficulty', 'medium')->count());
                                    $hardCount = min(3, $material->questions->where('difficulty', 'hard')->count());
                                    $configuredTotalQuestions = $beginnerCount + $mediumCount + $hardCount;
                                } else {
                                    // For registered users, use admin configuration
                                    $config = App\Models\QuestionBankConfig::where('material_id', $material->id)
                                        ->where('is_active', true)
                                        ->first();
                                    
                                    if ($config) {
                                        $configuredTotalQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
                                    } else {
                                        $configuredTotalQuestions = $material->questions->count();
                                    }
                                }
                            @endphp
                            
                            {{ $configuredTotalQuestions }} Soal
                            @if($isGuest)
                                <span class="guest-mode-badge ms-2">
                                    <i class="fas fa-lock-open text-warning"></i>
                                    Mode Tamu
                                </span>
                            @endif
                        </div>
                        
                    </div>
                    
                    <x-ui.button href="{{ route('mahasiswa.materials.show', $material->id) }}" variant="primary" class="w-100 mt-3 d-flex justify-content-between align-items-center">
                        Baca Materi <i class="fas fa-arrow-right"></i>
                    </x-ui.button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @push('css')
        <link href="{{ asset('css/mahasiswa/materials/index.css') }}" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
        <script src="{{ asset('js/mahasiswa/materials/index.js') }}"></script>
    @endpush
</x-layouts.app>