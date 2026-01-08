<x-layouts.app title="Edit Soal" theme="admin">
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <div class="max-w-4xl mx-auto space-y-12">
        <x-ui.page-header
            title="Question Reconfiguration"
            subtitle="Modifikasi parameter dan konten butir soal yang sudah ada."
        >
            <x-ui.button 
                href="{{ isset($material) ? route('admin.materials.questions.index', $material) : route('admin.questions.index') }}" 
                variant="ghost" 
                icon="fas fa-arrow-left"
            >
                BACK TO LIST
            </x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl overflow-visible">
            <x-slot:header>
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                    <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400">Specifications Segment</h6>
                </div>
            </x-slot:header>

            <form method="POST" 
                action="{{ isset($material) 
                    ? route('admin.materials.questions.update', ['material' => $material, 'question' => $question]) 
                    : route('admin.questions.update', $question) }}" 
                id="questionForm" 
                class="space-y-10"
            >
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Material Selection --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Origin Module</label>
                        @if (isset($material))
                            <input type="hidden" name="material_id" value="{{ $material->id }}">
                            <div class="px-6 py-4 bg-slate-50 border border-slate-200 rounded-3xl text-sm font-black italic text-slate-900 border-l-4 border-l-blue-600">
                                {{ $material->title }}
                            </div>
                        @else
                            <select name="material_id" id="material_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-3xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase font-poppins" required>
                                <option value="">SELECT MODULE</option>
                                @foreach ($materials as $mat)
                                    <option value="{{ $mat->id }}" {{ $question->material_id == $mat->id ? 'selected' : '' }}>{{ $mat->title }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Difficulty Selection --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Complexity Grade</label>
                        <select name="difficulty" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-3xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase font-poppins" required>
                            <option value="beginner" {{ $question->difficulty == 'beginner' ? 'selected' : '' }}>BEGINNER</option>
                            <option value="medium" {{ $question->difficulty == 'medium' ? 'selected' : '' }}>MEDIUM</option>
                            <option value="hard" {{ $question->difficulty == 'hard' ? 'selected' : '' }}>HARD</option>
                        </select>
                    </div>

                    {{-- Type Selection --}}
                    <div class="md:col-span-2 space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Evaluation Schema</label>
                        <select name="question_type" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-3xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase font-poppins" required>
                            <option value="radio_button" {{ $question->question_type == 'radio_button' ? 'selected' : '' }}>RADIO BUTTON (SINGLE CHOICE)</option>
                            <option value="drag_and_drop" {{ $question->question_type == 'drag_and_drop' ? 'selected' : '' }}>DRAG AND DROP (MULTI CHOICE)</option>
                            <option value="fill_in_the_blank" {{ $question->question_type == 'fill_in_the_blank' ? 'selected' : '' }}>FILL IN THE BLANK (KEYWORD MATCH)</option>
                        </select>
                    </div>
                </div>

                {{-- Question Content --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Question Content</label>
                    <div class="rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
                        <textarea id="content-editor" name="question_text">{{ $question->question_text }}</textarea>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-8 bg-emerald-500 rounded-full"></div>
                            <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400">Answer Registry</h6>
                        </div>
                        <x-ui.button type="button" variant="ghost" size="sm" onclick="addAnswer()" id="add-answer-btn" icon="fas fa-plus">
                            APPEND CHOICE
                        </x-ui.button>
                    </div>

                    <div id="answers-container" class="space-y-4">
                        @foreach($question->answers as $index => $answer)
                            <div class="answer-entry mb-3">
                                @if($question->question_type === 'radio_button')
                                    <div class="row items-center gap-4">
                                        <div class="col-md-7">
                                            <input type="text" name="answers[{{ $index }}][answer_text]" 
                                                class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold italic focus:ring-4 focus:ring-blue-100 transition-all outline-none" 
                                                placeholder="Type answer choice..." required value="{{ $answer->answer_text }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="flex items-center gap-3 cursor-pointer group">
                                                <div class="relative">
                                                    <input type="radio" name="correct_answer" value="{{ $index }}" class="correct-radio peer sr-only" {{ $answer->is_correct ? 'checked' : '' }}>
                                                    <div class="w-6 h-6 rounded-full border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-600 transition-all flex items-center justify-center text-white text-[10px]">
                                                        <i class="fas fa-check scale-0 peer-checked:scale-100 transition-transform"></i>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-slate-900 transition-colors">Mark Correct</span>
                                                <input type="hidden" name="answers[{{ $index }}][is_correct]" value="{{ $answer->is_correct ? '1' : '0' }}">
                                            </label>
                                        </div>
                                    </div>
                                @elseif($question->question_type === 'drag_and_drop')
                                    <div class="row items-center gap-4">
                                        <div class="col-md-7">
                                            <input type="text" name="answers[{{ $index }}][answer_text]" 
                                                class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold italic focus:ring-4 focus:ring-blue-100 transition-all outline-none" 
                                                placeholder="Type answer choice..." required value="{{ $answer->answer_text }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="flex items-center gap-3 cursor-pointer group">
                                                <div class="relative">
                                                    <input type="checkbox" name="answers[{{ $index }}][is_correct]" value="1" class="peer sr-only" {{ $answer->is_correct ? 'checked' : '' }}>
                                                    <div class="w-6 h-6 rounded-lg border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-600 transition-all flex items-center justify-center text-white text-[10px]">
                                                        <i class="fas fa-check scale-0 peer-checked:scale-100 transition-transform"></i>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-slate-900 transition-colors">Correct Item</span>
                                            </label>
                                        </div>
                                    </div>
                                @else {{-- fill_in_the_blank --}}
                                    <div class="row items-center gap-4">
                                        <div class="col-md-7">
                                            <input type="text" name="answers[{{ $index }}][answer_text]" 
                                                class="w-full px-5 py-8 bg-blue-50/50 border-2 border-dashed border-blue-200 rounded-[2rem] text-center text-sm font-black italic tracking-tighter text-blue-900 focus:ring-0 focus:border-blue-600 transition-all outline-none uppercase" 
                                                placeholder="Type the expected keyword..." required value="{{ $answer->answer_text }}">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="flex items-center gap-3 opacity-50">
                                                <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white text-[10px]">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">Primary Key</span>
                                                <input type="hidden" name="answers[{{ $index }}][is_correct]" value="1">
                                                <input type="radio" name="correct_answer" value="{{ $index }}" checked class="sr-only">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-10 flex gap-4">
                    <x-ui.button type="submit" variant="primary" id="submitBtn" class="flex-1 h-[60px] shadow-2xl shadow-blue-500/30" icon="fas fa-save">
                        UPDATE REPOSITORY
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    @push('js')
        <script src="{{ asset('js/admin/questions/form.js') }}"></script>
        <script>
            var initialAnswerCount = {{ count($question->answers) }};
        </script>
    @endpush
    <x-admin.tutorial />
</x-layouts.app>