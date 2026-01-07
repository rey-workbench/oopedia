<x-layouts.app :title="$material->title" theme="mahasiswa">
    <x-slot:styles>
        <link rel="stylesheet" href="{{ asset('css/mahasiswa/materials/show.css') }}">
        <link rel="stylesheet" href="{{ asset('css/mahasiswa/materials/questionsindex.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    </x-slot:styles>

    <div class="container-fluid px-4">
        <!-- Judul Materi -->
        <h1 class="materi-heading">{{ $material->title }}</h1>
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
    
        @guest
            <x-ui.alert variant="warning" class="mb-4">
                <strong>Mode Tamu Aktif!</strong> 
                Anda hanya dapat melihat sebagian dari konten materi ini. Untuk akses penuh, silakan 
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
        @endguest
    
        <!-- Content Section -->
        <x-ui.card class="mb-4">
            <div class="content-text">
                {!! $material->content !!}
            </div>
        </x-ui.card>
    
        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-4 mb-5">
            <x-ui.button 
                href="{{ route('mahasiswa.materials.index') }}" 
                variant="secondary"
                icon="fas fa-arrow-left"
            >
                Kembali ke Daftar Materi
            </x-ui.button>
            
                href="{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id]) }}" 
                variant="primary"
            >
                Latihan Soal <i class="fas fa-arrow-right ms-2"></i>
            </x-ui.button>
        </div>
    </div>

    <x-slot:scripts>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
        <script src="{{ asset('js/mahasiswa/materials/show.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize form logic if applicable
                const config = {
                    isGuest: {{ auth()->check() ? (auth()->user()->role_id === 4 ? 'true' : 'false') : 'true' }},
                    maxQuestionsForGuest: 3, // Default fallback
                    routes: {
                        levelsUrl: "{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id, 'difficulty' => request()->query('difficulty', 'all')]) }}"
                    }
                };
                initializeQuestionForm(config);
            });
        </script>
    </x-slot:scripts>
</x-layouts.app>
