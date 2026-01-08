<x-layouts.app :title="'Tantangan - ' . $material->title" theme="mahasiswa">
    <div class="space-y-12">
        <x-ui.page-header
            title="Peta Tantangan"
            subtitle="Modul: {{ $material->title }}"
        >
             <x-ui.button href="{{ route('mahasiswa.materials.index') }}" variant="ghost" icon="fas fa-chevron-left">Log Materi</x-ui.button>
        </x-ui.page-header>

        <div class="max-w-6xl mx-auto space-y-12">
            @if(auth()->check() && auth()->user()->role_id === 3)
                <x-ui.alert variant="primary" :dismissible="false">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-600/10 rounded-2xl flex items-center justify-center shrink-0">
                            <i class="fas fa-microchip text-blue-600"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-bold tracking-tight text-blue-900 mb-1">Mekanisme Poin Berbasis Akurasi</h5>
                            <p class="text-sm font-medium text-blue-800 italic leading-relaxed">
                                Sistem menghitung skor leaderboard berdasarkan efisiensi jawaban. Setiap percobaan yang salah akan mengurangi potensi poin maksimal. Fokuslah pada akurasi untuk mendaki peringkat teratas.
                            </p>
                        </div>
                    </div>
                </x-ui.alert>
            @endif

            {{-- Navigation Legend --}}
            <x-ui.card padding="p-8" class="bg-slate-900 border-slate-800 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-10 -top-10 opacity-10">
                    <i class="fas fa-map-marked-alt text-[150px] text-white"></i>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Tersedia</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <i class="fas fa-check-double text-white"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tuntas</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center">
                            <i class="fas fa-lock text-slate-600"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Terkunci</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <i class="fas fa-crown text-white"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Mastery</span>
                    </div>
                </div>
            </x-ui.card>

            {{-- The Path --}}
            <div class="relative py-20 overflow-hidden bg-slate-50/50 rounded-[3rem] border border-dashed border-slate-200">
                <div class="absolute inset-0 opacity-[0.03] pattern-grid-lg"></div>
                
                <div class="max-w-xl mx-auto relative px-10">
                    {{-- The Wire --}}
                    <div class="absolute left-1/2 top-0 bottom-0 w-1 bg-slate-200 -translate-x-1/2 rounded-full hidden md:block"></div>

                    <div class="flex justify-center mb-16 relative">
                        <div class="px-6 py-2 bg-slate-900 text-white rounded-full text-[10px] font-bold uppercase tracking-widest shadow-xl z-20">Episode Start</div>
                    </div>

                    @foreach($levels as $index => $level)
                        <div class="flex {{ $index % 2 == 0 ? 'justify-start' : 'justify-end' }} mb-20 relative">
                            {{-- Step connector --}}
                            <div class="absolute top-1/2 {{ $index % 2 == 0 ? 'left-1/2' : 'right-1/2' }} h-0.5 bg-slate-200 w-16 -translate-y-1/2 hidden md:block -z-0"></div>
                            
                            <div class="relative z-10 group">
                                @if($level['status'] === 'locked')
                                    <div class="w-20 h-20 rounded-[2rem] bg-slate-100 border-4 border-white flex items-center justify-center text-slate-300 font-bold text-2xl shadow-inner">
                                        {{ $level['level'] }}
                                    </div>
                                @elseif($level['status'] === 'completed')
                                    <div class="relative">
                                        <div class="w-20 h-20 rounded-[2rem] bg-emerald-500 border-4 border-white flex items-center justify-center text-white font-bold text-2xl shadow-xl shadow-emerald-200 transition-transform group-hover:scale-110">
                                            {{ $level['level'] }}
                                        </div>
                                        <div class="absolute -top-3 -right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg border-2 border-emerald-50 text-emerald-500">
                                            <i class="fas fa-check-double text-xs"></i>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ route('mahasiswa.materials.questions.show', ['material' => $material->id, 'question' => $level['question_id']]) }}" class="relative block transform hover:scale-110 transition-all duration-500 group">
                                        <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-blue-600 to-indigo-700 border-4 border-white flex items-center justify-center text-white font-bold text-2xl shadow-2xl shadow-blue-500/40">
                                            {{ $level['level'] }}
                                        </div>
                                        <div class="absolute inset-0 rounded-[2rem] bg-blue-500 animate-ping opacity-20 -z-10"></div>
                                        <div class="absolute -top-3 -right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg border-2 border-blue-50 text-blue-600">
                                            <i class="fas fa-play text-[10px] ml-0.5"></i>
                                        </div>
                                    </a>
                                @endif
                                
                                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 whitespace-nowrap text-[9px] font-black text-slate-400 uppercase tracking-widest italic opacity-0 group-hover:opacity-100 transition-all">
                                    TANTANGAN {{ $level['level'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-center mt-24">
                        @php $allCompleted = count(array_filter($levels, function($level) { return $level['status'] !== 'completed'; })) === 0; @endphp
                        <div class="relative">
                            <div class="w-32 h-32 rounded-[3rem] {{ $allCompleted ? 'bg-gradient-to-br from-amber-400 to-orange-500 shadow-2xl shadow-amber-200' : 'bg-slate-100 border-4 border-white' }} flex items-center justify-center transition-all duration-1000 group">
                                <i class="fas fa-trophy text-5xl {{ $allCompleted ? 'text-white animate-bounce' : 'text-slate-200' }}"></i>
                                @if($allCompleted)
                                    <div class="absolute inset-0 rounded-[3rem] bg-amber-400 animate-pulse opacity-20"></div>
                                @endif
                            </div>
                            <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 whitespace-nowrap text-[10px] font-black text-amber-600 uppercase tracking-[0.4em] italic {{ $allCompleted ? 'opacity-100 animate-pulse' : 'opacity-20' }}">MASTERY ZONE</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                <x-ui.button
                    href="{{ route('mahasiswa.materials.index') }}"
                    variant="ghost"
                    size="lg"
                    icon="fas fa-arrow-left"
                    class="text-slate-400"
                >
                    Kembali ke Katalog
                </x-ui.button>
            </div>
        </div>
    </div>
</x-layouts.app>
