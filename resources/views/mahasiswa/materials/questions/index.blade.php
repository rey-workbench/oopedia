<x-layouts.app title="Latihan Soal PBO" theme="mahasiswa">
    @if(!auth()->check())
    <form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="redirect" value="{{ route('login') }}">
    </form>

    <form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="redirect" value="{{ route('register') }}">
    </form>
    @endif

    <div class="space-y-12">
        <x-ui.page-header
            title="Latihan Soal PBO"
            subtitle="Uji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi"
        />

        @if(!auth()->check())
        <x-ui.alert variant="warning" :dismissible="false">
            <div class="flex flex-col gap-2">
                <span class="font-bold text-lg tracking-tight">Mode Tamu Aktif!</span>
                <p class="text-sm">Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap tingkat kesulitan. Untuk akses penuh, silakan login atau daftar.</p>
                <div class="flex gap-4 mt-2">
                    <x-ui.button href="{{ route('login') }}" variant="primary" size="sm">Login Sekarang</x-ui.button>
                    <x-ui.button href="{{ route('register') }}" variant="ghost" size="sm">Daftar Akun</x-ui.button>
                </div>
            </div>
        </x-ui.alert>
        @endif

        <div class="grid grid-cols-1 gap-10">
            @foreach($materials as $material)
            <x-ui.card padding="p-0" :hover="true" class="overflow-hidden">
                <a href="{{ route('mahasiswa.materials.questions.levels', $material->id) }}" class="flex flex-col md:flex-row h-full">
                    {{-- Graphic Section --}}
                    <div class="md:w-72 lg:w-96 relative shrink-0">
                        @if($material->media && $material->media->isNotEmpty())
                            <div class="h-60 md:h-full">
                                <img src="{{ $material->media->first()->media_url }}" alt="{{ $material->title }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors"></div>
                            </div>
                        @else
                            <div class="h-60 md:h-full bg-gradient-to-br from-indigo-600 to-blue-700 flex items-center justify-center">
                                <i class="fas fa-shapes text-8xl text-white/10 group-hover:rotate-6 transition-transform"></i>
                            </div>
                        @endif
                        <div class="absolute top-6 left-6">
                            <x-ui.badge variant="primary" size="sm" class="shadow-xl">MODUL AKTIF</x-ui.badge>
                        </div>
                    </div>

                    {{-- Content Section --}}
                    <div class="flex-1 p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start gap-6">
                                <div>
                                    <h2 class="text-3xl font-bold text-slate-900 leading-tight mb-3 group-hover:text-blue-600 transition-colors tracking-tight">
                                        {{ $material->title }}
                                    </h2>
                                    <div class="flex flex-wrap items-center gap-6">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xs shadow-inner">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $material->student_count }} Mahasiswa</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xs shadow-inner">
                                                <i class="fas fa-puzzle-piece"></i>
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $material->total_questions }} Soal Latihan</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="hidden sm:flex w-14 h-14 rounded-2xl bg-slate-50 text-slate-900 items-center justify-center shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <i class="fas fa-play ml-1"></i>
                                </div>
                            </div>

                            @if(!auth()->check())
                                <div class="mt-8 p-6 bg-amber-50 rounded-[2rem] border border-amber-100 flex items-center gap-6 ring-8 ring-amber-50/50">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-amber-200">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-black text-amber-800 uppercase tracking-widest block mb-1">Akses Terbatas</span>
                                        <p class="text-xs text-amber-700 font-medium">Selesaikan pendaftaran untuk membuka semua level soal.</p>
                                    </div>
                                </div>
                            @else
                                <div class="mt-8 space-y-4">
                                    <div class="flex justify-between items-center px-1">
                                        <div class="flex items-center gap-3">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Progress Mastery</span>
                                            <x-ui.badge variant="success" size="xs">{{ $material->progress_percentage }}%</x-ui.badge>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-300 uppercase italic">{{ $material->completed_questions }}/{{ $material->total_questions }} SOAL</span>
                                    </div>
                                    <x-ui.progress-bar :value="$material->progress_percentage" size="sm" :showPercentage="false" variant="success" />
                                </div>
                            @endif
                        </div>

                        <div class="mt-10 md:hidden">
                            <x-ui.button variant="primary" class="w-full" icon="fas fa-play">Mulai Latihan</x-ui.button>
                        </div>
                    </div>
                </a>
            </x-ui.card>
            @endforeach
        </div>
    </div>
</x-layouts.app>
