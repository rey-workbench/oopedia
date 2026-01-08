<x-layouts.app :title="'Latihan Soal - ' . $material->title" theme="mahasiswa">
    <x-slot:styles>

    </x-slot:styles>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h1 class="text-4xl font-black text-gray-900 italic tracking-tighter uppercase flex items-center justify-center gap-4">
                    <i class="fas fa-terminal text-blue-600"></i>
                    Evaluasi: {{ $material->title }}
                </h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.3em] mt-3 italic">Mode Ujian Terkendali & Aman</p>
                <div class="h-1 w-32 bg-gray-200 mx-auto mt-6 rounded-full overflow-hidden">
                    @php
                        $progressWidth = ($material->questions_count > 0) 
                            ? ($currentQuestionNumber / $material->questions_count) * 100 
                            : 0;
                    @endphp
                    <div class="h-full bg-blue-600 transition-all duration-1000" style="width: {{ $progressWidth }}%"></div>
                </div>
            </div>

            @if(session('success'))
                <x-ui.alert variant="success" class="mb-6 shadow-sm border-l-4">
                    {{ session('success') }}
                </x-ui.alert>
            @endif

            @if(session('error'))
                <x-ui.alert variant="danger" class="mb-6 shadow-sm border-l-4">
                    {{ session('error') }}
                </x-ui.alert>
            @endif

            @if(!auth()->check())
                <div class="mb-8 p-5 bg-amber-50 border border-amber-100 rounded-2xl shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-user-shield text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <strong class="text-amber-900 text-lg block mb-1">Mode Tamu Aktif!</strong>
                        <p class="text-amber-800">
                            Anda hanya dapat melihat sebagian dari soal latihan ini. Untuk akses penuh, silakan
                            <a href="{{ route('login') }}" class="font-bold underline hover:text-amber-950 transition-colors" onclick="event.preventDefault(); document.getElementById('guest-logout-login-form').submit();">login</a>
                            atau
                            <a href="{{ route('register') }}" class="font-bold underline hover:text-amber-950 transition-colors" onclick="event.preventDefault(); document.getElementById('guest-logout-register-form').submit();">daftar</a>
                            sebagai mahasiswa.
                        </p>
                    </div>
                </div>

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
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-12 text-center text-white relative">
                        <div class="absolute top-0 right-0 p-8 opacity-10">
                            <i class="fas fa-trophy text-9xl"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl border-4 border-white/30">
                                <i class="fas fa-check text-4xl"></i>
                            </div>
                            <h2 class="text-4xl font-black mb-3 italic tracking-tight">LUAR BIASA!</h2>
                            <p class="text-emerald-50 text-xl font-medium">Anda telah menyelesaikan semua soal pada materi ini.</p>
                        </div>
                    </div>
                    
                    <div class="p-10 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <a href="{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id, 'difficulty' => $difficulty]) }}" 
                               class="group p-6 rounded-2xl bg-gray-50 border-2 border-transparent hover:border-emerald-200 hover:bg-emerald-50 transition-all text-center">
                                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-list-ol"></i>
                                </div>
                                <span class="font-bold text-gray-700 block">Pilih Level</span>
                            </a>

                            <a href="{{ route('mahasiswa.materials.show', $material->id) }}" 
                               class="group p-6 rounded-2xl bg-gray-50 border-2 border-transparent hover:border-blue-200 hover:bg-blue-50 transition-all text-center">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-book"></i>
                                </div>
                                <span class="font-bold text-gray-700 block">Baca Materi</span>
                            </a>

                            <a href="{{ route('mahasiswa.dashboard') }}" 
                               class="group p-6 rounded-2xl bg-gray-50 border-2 border-transparent hover:border-indigo-200 hover:bg-indigo-50 transition-all text-center">
                                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-home"></i>
                                </div>
                                <span class="font-bold text-gray-700 block">Dashboard</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        </div>
    </div>

    <x-slot:scripts>
        <script src="{{ asset('js/mahasiswa/partials/question.js') }}"></script>
        <script src="{{ asset('js/mahasiswa/materials/questions/show.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Config variable for use in scripts
                const config = {
                    materialId: '{{ $material->id }}',
                    isGuest: {{ auth()->check() ? 'false' : 'true' }},
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