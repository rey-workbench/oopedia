<x-layouts.app title="Tambah Soal" theme="admin">
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <div class="max-w-4xl mx-auto space-y-12">
        <x-ui.page-header
            title="Question Factory"
            subtitle="Ciptakan butir soal evaluasi baru dengan parameter kustom."
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
                action="{{ isset($material) ? route('admin.materials.questions.store', $material) : route('admin.questions.store') }}" 
                id="questionForm" 
                class="space-y-10"
            >
                @csrf

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
                                    <option value="{{ $mat->id }}">{{ $mat->title }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Difficulty Selection --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Complexity Grade</label>
                        <select name="difficulty" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-3xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase font-poppins" required>
                            <option value="beginner">BEGINNER</option>
                            <option value="medium">MEDIUM</option>
                            <option value="hard">HARD</option>
                        </select>
                    </div>

                    {{-- Type Selection --}}
                    <div class="md:col-span-2 space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Evaluation Schema</label>
                        <select name="question_type" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-3xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase font-poppins" required>
                            <option value="radio_button">RADIO BUTTON (SINGLE CHOICE)</option>
                            <option value="drag_and_drop">DRAG AND DROP (MULTI CHOICE)</option>
                            <option value="fill_in_the_blank">FILL IN THE BLANK (KEYWORD MATCH)</option>
                        </select>
                    </div>
                </div>

                {{-- Question Content --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Question Content</label>
                    <div class="rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
                        <textarea id="content-editor" name="question_text">{{ old('question_text') }}</textarea>
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
                        {{-- To be populated by form.js --}}
                    </div>
                </div>

                <div class="pt-10 flex gap-4">
                    <x-ui.button type="submit" variant="primary" id="submitBtn" class="flex-1 h-[60px] shadow-2xl shadow-blue-500/30" icon="fas fa-save">
                        INITIALIZE QUESTION
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    @push('js')
        <script src="{{ asset('js/admin/questions/form.js') }}"></script>
        <script>
            var initialAnswerCount = 0;
            
            document.addEventListener('DOMContentLoaded', function() {
                // Initial trigger to setup answers based on default type
                resetAnswersForNewType();
            });
        </script>
    @endpush
    <x-admin.tutorial />
</x-layouts.app>