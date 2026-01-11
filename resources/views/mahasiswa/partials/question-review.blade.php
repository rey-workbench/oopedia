<div class="space-y-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner">
                <i class="fas fa-clipboard-check text-2xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900  tracking-widest uppercase">
                    Review Semua Soal
                    @if(!auth()->check())
                        <span class="text-blue-600 ml-2">(MODE TAMU)</span>
                    @endif
                </h3>
                <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest">Tinjau kembali pemahaman Anda</p>
            </div>
        </div>
        
        @if(!auth()->check())
            <form action="{{ route('mahasiswa.materials.reset', $material->id) }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-8 py-4 bg-amber-500 text-white rounded-2xl font-bold  uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-amber-100 active:scale-95">
                    <i class="fas fa-redo"></i>
                    Kerjakan Ulang
                </button>
            </form>
        @endif
    </div>

    {{-- Questions List --}}
    <div class="space-y-8">
        @foreach($material->questions as $index => $question)
            <div class="group">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-500">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <span class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center font-bold  shadow-lg">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-widest ">
                                Soal {{ $index + 1 }} / {{ $material->questions->count() }}
                            </span>
                        </div>
                        <div class="flex gap-1">
                            @php
                                $diffColor = $question->difficulty == 'hard' ? 'rose' : ($question->difficulty == 'medium' ? 'amber' : 'emerald');
                            @endphp
                            <div class="px-4 py-1.5 rounded-full bg-{{ $diffColor }}-50 text-{{ $diffColor }}-600 text-[10px] font-bold uppercase tracking-[0.2em] border border-{{ $diffColor }}-100 shadow-sm">
                                {{ $question->difficulty }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8 md:p-10">
                        <div class="mb-10">
                            <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-question text-blue-500 text-xs"></i>
                                Pertanyaan
                            </h5>
                            <div class="text-xl font-bold text-gray-800 leading-relaxed ">
                                {!! $question->question_text !!}
                            </div>
                        </div>

                        <div>
                            <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <i class="fas fa-list-ul text-blue-500 text-xs"></i>
                                Pilihan Jawaban
                            </h5>
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($question->answers as $answer)
                                    <div class="p-6 rounded-[2rem] border-2 transition-all {{ $answer->is_correct ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-50 bg-white group-hover:bg-gray-50/30' }}">
                                        <div class="flex items-start gap-4">
                                            @if($answer->is_correct)
                                                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-100">
                                                    <i class="fas fa-check text-[10px]"></i>
                                                </div>
                                            @else
                                                <div class="w-6 h-6 rounded-full border-2 border-gray-100 shrink-0"></div>
                                            @endif
                                            
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-700 {!! $answer->is_correct ? 'text-emerald-900' : '' !!}">
                                                    {!! $answer->answer_text !!}
                                                </div>
                                                @if($answer->explanation)
                                                    <div class="mt-4 p-4 rounded-2xl bg-white/60 border border-emerald-100/50 text-sm font-medium text-emerald-800  flex gap-3">
                                                        <i class="fas fa-lightbulb shrink-0 mt-1 opacity-50"></i>
                                                        <div>{!! $answer->explanation !!}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Footer Actions --}}
    <div class="flex flex-col sm:flex-row justify-center gap-4 mt-12 pb-12">
        <a href="{{ route('mahasiswa.materials.questions.index') }}" 
           class="flex items-center justify-center gap-3 px-10 py-5 bg-gray-900 text-white rounded-2xl font-bold  uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-gray-200">
            <i class="fas fa-list"></i>
            Kembali ke Daftar Soal
        </a>
        @if(!auth()->check())
            <a href="{{ route('mahasiswa.dashboard') }}" 
               class="flex items-center justify-center gap-3 px-10 py-5 bg-white text-gray-900 border-2 border-gray-100 rounded-2xl font-bold  uppercase tracking-widest hover:bg-gray-50 transition-all">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        @endif
    </div>
</div>

{{-- Trophy Section --}}
@php
    $allCompleted = count(array_filter($levels, function($level) { return $level['status'] !== 'completed'; })) === 0;
@endphp

@if($allCompleted)
    <div class="fixed bottom-12 right-12 z-50 animate-bounce">
        <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-amber-600 rounded-3xl shadow-2xl flex items-center justify-center border-4 border-white">
            <i class="fas fa-trophy text-3xl text-white"></i>
        </div>
    </div>
@endif

{{-- Note: CSS for trophy animation should be included in the parent view --}}
