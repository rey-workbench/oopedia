<div class="space-y-12">
  {{-- Header Section --}}
  <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 flex flex-col md:flex-row justify-between items-center gap-6">
    <div class="flex items-center gap-5">
      <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner">
        <i class="fas fa-filter text-2xl"></i>
      </div>
      <div>
        <h3 class="text-2xl font-bold text-gray-900 tracking-widest uppercase">
          Review Soal: <span class="text-blue-600">{{ $difficulty !== 'all' ? ucfirst($difficulty) : 'Semua Tingkat' }}</span>
        </h3>
        <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest">Tinjau kembali progres belajar Anda</p>
      </div>
    </div>
  </div>
  
  <div class="space-y-8">
    @if(count($questions) > 0)
      @foreach($questions as $index => $question)
        <div class="group">
          <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-500">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
              <div class="flex items-center gap-4">
                <span class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center font-bold shadow-lg">
                  {{ $index + 1 }}
                </span>
                <div class="flex items-center gap-3">
                  @php
                    $diffColor = $question->difficulty == 'hard' ? 'rose' : ($question->difficulty == 'medium' ? 'amber' : 'emerald');
                  @endphp
                  <div class="px-4 py-1.5 rounded-full bg-{{ $diffColor }}-50 text-{{ $diffColor }}-600 text-[10px] font-bold uppercase tracking-widest border border-{{ $diffColor }}-100 shadow-sm">
                    {{ $question->difficulty }}
                  </div>
                  <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                  </span>
                </div>
              </div>
            </div>

            <div class="p-8 md:p-10">
              <div class="mb-10">
                <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                  <i class="fas fa-question text-blue-500 text-xs"></i>
                  Pertanyaan
                </h5>
                <div class="text-xl font-bold text-gray-800 leading-relaxed bg-gray-50/50 p-6 rounded-3xl border border-gray-50">
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
                    <div class="p-6 rounded-[2rem] border-2 transition-all {{ $answer->is_correct ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-50 bg-white' }}">
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
                          @if($answer->is_correct && $answer->explanation)
                            <div class="mt-4 p-4 rounded-2xl bg-white/60 border border-emerald-100/50 text-sm font-medium text-emerald-800 flex gap-3">
                              <i class="fas fa-lightbulb shrink-0 mt-1 opacity-50"></i>
                              <div>
                                <strong class="uppercase text-[10px] tracking-widest not- block mb-1 opacity-60">Penjelasan:</strong>
                                {!! $answer->explanation !!}
                              </div>
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
    @else
      <div class="text-center py-20 bg-white rounded-[2.5rem] shadow-sm border border-gray-100">
        <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner">
          <i class="fas fa-info-circle text-3xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2 uppercase tracking-widest">Tidak Ada Data</h3>
        <p class="text-gray-500 font-medium">Tidak ada soal yang tersedia untuk ditampilkan.</p>
      </div>
    @endif
  </div>
</div>

{{-- Note: CSS should be included in the parent view --}}
