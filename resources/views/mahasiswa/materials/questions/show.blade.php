<x-layouts.app :title="'Latihan Soal - ' . $material->title" theme="mahasiswa">
    <x-slot:styles>

        <link rel="stylesheet" href="{{ asset('css/material-show.css') }}">
        <link rel="stylesheet" href="{{ asset('css/question-review.css') }}">
        <link rel="stylesheet" href="{{ asset('css/mahasiswa/materials/questions/show.css') }}">
        <link rel="stylesheet" href="{{ asset('css/mahasiswa/partials/question.css') }}">
        <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
    </x-slot:styles>

    <div class="container-fluid py-4">
        <h1 class="materi-heading">Latihan Soal: {{ $material->title }}</h1>
        <div class="heading-underline mb-4"></div>

        @if(session('success'))
            <x-ui.alert variant="success" class="mb-4">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        @if(session('error'))
            <x-ui.alert variant="danger" class="mb-4">
                {{ session('error') }}
            </x-ui.alert>
        @endif

        @if(auth()->check() && auth()->user()->role_id === 4)
            <x-ui.alert variant="warning" class="mb-4">
                <strong>Mode Tamu Aktif!</strong> 
                Anda hanya dapat melihat sebagian dari soal latihan ini. Untuk akses penuh, silakan 
                <a href="{{ route('login') }}" class="alert-link" onclick="event.preventDefault(); document.getElementById('guest-logout-login-form').submit();">login</a> 
                atau 
                <a href="{{ route('register') }}" class="alert-link" onclick="event.preventDefault(); document.getElementById('guest-logout-register-form').submit();">daftar</a> 
                sebagai mahasiswa.
            </x-ui.alert>

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

        @if($currentQuestion)
            @include('mahasiswa.partials.question')
        @else
            <x-ui.card class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h3 class="mb-3">Selamat! Semua Soal Telah Terjawab</h3>
                <p class="text-muted mb-4">
                    Anda telah menyelesaikan semua soal pada materi ini.
                </p>
                <div class="mt-4">
                    <x-ui.button 
                        href="{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id, 'difficulty' => $difficulty]) }}" 
                        variant="success" 
                        class="me-2"
                        icon="list-ol"
                    >
                        Kembali ke Level
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('mahasiswa.materials.show', $material->id) }}" 
                        variant="primary" 
                        class="me-2"
                        icon="book"
                    >
                        Kembali ke Materi
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('mahasiswa.dashboard') }}" 
                        variant="secondary"
                        icon="home"
                    >
                        Dashboard
                    </x-ui.button>
                </div>
            </x-ui.card>
        @endif
    </div>

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
        <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
        <script src="{{ asset('js/mahasiswa/materials/questions/show.js') }}"></script>
        <script src="{{ asset('js/mahasiswa/partials/question.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Config variable for use in scripts
                const config = {
                    materialId: '{{ $material->id }}',
                    isGuest: {{ auth()->check() ? (auth()->user()->role_id === 4 ? 'true' : 'false') : 'true' }},
                    currentQuestionNumber: {{ $currentQuestionNumber ?? 1 }},
                    routes: {
                        materialShow: "{{ route('mahasiswa.materials.show', $material->id) }}",
                        dashboard: "{{ route('mahasiswa.dashboard') }}",
                        levels: "{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id, 'difficulty' => request()->query('difficulty')]) }}"
                    }
                };

                // Initialize logic from show.js
                initializeQuestionForm(config);
                initializeTutorial();
                
                // localStorage logic for levels
                 @if($currentQuestion)
                const currentLevel = '{{ $currentQuestion->id }}';
                localStorage.setItem('currentQuestionLevel', currentLevel);
                @endif
            });
            
            // Global redirect variable used by show.js in some contexts
            const redirectUrl = "{{ route('mahasiswa.materials.show', $material->id) }}";
        </script>
    </x-slot:scripts>
</x-layouts.app>