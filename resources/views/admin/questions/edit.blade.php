<x-layouts.app title="Rekonfigurasi Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Rekonfigurasi Soal"
            subtitle="Modifikasi parameter dan konten butir soal yang sudah ada."
        >
            <x-ui.button 
                href="{{ isset($material) ? route('admin.materials.questions.index', $material) : route('admin.questions.index') }}" 
                variant="ghost" 
                icon="fas fa-arrow-left"
            >
                KEMBALI KE DAFTAR
            </x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl">
            <x-slot:header>Registri & Logika Optimasi</x-slot:header>

            <form method="POST" 
                action="{{ isset($material) 
                    ? route('admin.materials.questions.update', ['material' => $material, 'question' => $question]) 
                    : route('admin.questions.update', $question) }}" 
                id="questionForm" 
                class="space-y-10"
            >
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    {{-- Origin & Complexity --}}
                    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-forms.form-group label="Modul Asal" name="material_id" required>
                            @if (isset($material))
                                <input type="hidden" name="material_id" id="material_id" value="{{ $material->id }}">
                                <div class="px-6 py-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl text-xs font-bold text-indigo-800 border-l-4 border-l-indigo-600 uppercase tracking-widest">
                                    {{ $material->title }}
                                </div>
                            @else
                                <select name="material_id" id="material_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold tracking-widest outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required onchange="fetchSubMaterials(this.value)">
                                    <option value="">PILIH MODUL</option>
                                    @foreach ($materials as $mat)
                                        <option value="{{ $mat->id }}" {{ $question->material_id == $mat->id ? 'selected' : '' }}>{{ $mat->title }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </x-forms.form-group>

                        <x-forms.form-group label="Sub-materi (Opsional)" name="sub_material_id">
                            <select name="sub_material_id" id="sub_material_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold tracking-widest outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase">
                                <option value="">PILIH SUB-MATERI</option>
                                @foreach ($subMaterials as $sub)
                                    <option value="{{ $sub->id }}" {{ $question->sub_material_id == $sub->id ? 'selected' : '' }}>{{ $sub->title }}</option>
                                @endforeach
                            </select>
                        </x-forms.form-group>

                        <x-forms.form-group label="Tingkat Kesulitan" name="difficulty" required>
                            <select name="difficulty" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold tracking-widest outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required>
                                <option value="beginner" {{ $question->difficulty == 'beginner' ? 'selected' : '' }}>BEGINNER</option>
                                <option value="medium" {{ $question->difficulty == 'medium' ? 'selected' : '' }}>MEDIUM</option>
                                <option value="hard" {{ $question->difficulty == 'hard' ? 'selected' : '' }}>HARD</option>
                            </select>
                        </x-forms.form-group>

                        <div class="md:col-span-2">
                            <x-forms.form-group label="Skema Evaluasi" name="question_type" required>
                                <select name="question_type" id="question_type_select" onchange="resetAnswersForNewType()" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold tracking-widest outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required>
                                    <option value="radio_button" {{ $question->question_type == 'radio_button' ? 'selected' : '' }}>TOMBOL RADIO (PILIHAN TUNGGAL)</option>
                                    <option value="drag_and_drop" {{ $question->question_type == 'drag_and_drop' ? 'selected' : '' }}>SERET DAN LEPAS (PILIHAN GANDA)</option>
                                    <option value="fill_in_the_blank" {{ $question->question_type == 'fill_in_the_blank' ? 'selected' : '' }}>ISIAN SINGKAT (COCOK KATA KUNCI)</option>
                                </select>
                            </x-forms.form-group>
                        </div>
                    </div>

                    {{-- Status Visualization --}}
                    <div class="lg:col-span-1">
                        <div class="h-full p-8 bg-indigo-950 rounded-[2rem] relative overflow-hidden flex flex-col justify-center">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-600/10 blur-3xl"></div>
                            <div class="relative z-10 flex flex-col items-center text-center">
                                <div class="w-16 h-16 rounded-3xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center mb-4">
                                    <i class="fas fa-sync-alt text-2xl animate-spin-slow"></i>
                                </div>
                                <h4 class="text-white text-[10px] font-bold uppercase tracking-widest mb-2">Rekonfigurasi Aktif</h4>
                                <p class="text-[9px] font-bold text-indigo-300 leading-relaxed uppercase tracking-wider">
                                    Status: Live Updates Enabled.<br>Semua modifikasi akan segera teraplikasi pada mesin evaluasi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question Content --}}
                <div class="space-y-4 pt-6 border-t border-slate-100">
                    <x-forms.form-group label="Pertanyaan Inti (WYSIWYG)" name="question_text">
                        <div class="quill-editor h-[300px] bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-input="question_text"></div>
                        <input type="hidden" id="question_text" name="question_text" value="{{ old('question_text', $question->question_text) }}">
                    </x-forms.form-group>
                </div>

                {{-- Answer Registry --}}
                <div class="pt-8 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-database text-blue-500 text-xs text-indigo-500"></i>
                            <h6 class="mb-0  font-bold uppercase tracking-widest text-[10px] text-slate-400">Registri Jawaban</h6>
                        </div>
                        <x-ui.button type="button" id="add-answer-btn" onclick="addAnswer()" variant="ghost" size="sm" class="text-indigo-600 font-bold uppercase tracking-widest" icon="fas fa-plus">
                            TAMBAH PILIHAN
                        </x-ui.button>
                    </div>

                    <div id="answers-container" class="space-y-4">
                        @foreach($question->answers as $index => $answer)
                            <div class="answer-entry group">
                                @if($question->question_type === 'radio_button')
                                    <div class="flex items-center gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 transition-all hover:border-blue-200">
                                        <div class="flex-1">
                                            <input type="text" name="answers[{{ $index }}][answer_text]" 
                                                class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold  focus:ring-4 focus:ring-blue-100 transition-all outline-none" 
                                                placeholder="Ketik pilihan jawaban..." required value="{{ $answer->answer_text }}">
                                        </div>
                                        <div class="flex items-center gap-6">
                                            <label class="flex items-center gap-3 cursor-pointer">
                                                <input type="radio" name="correct_answer" value="{{ $index }}" class="sr-only peer" required {{ $answer->is_correct ? 'checked' : '' }}>
                                                <div class="w-6 h-6 rounded-full border-2 border-slate-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center text-white text-[10px] transition-all">
                                                    <i class="fas fa-check scale-0 peer-checked:scale-100 transition-transform"></i>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 peer-checked:text-blue-600">Tandai Benar</span>
                                                <input type="hidden" name="answers[{{ $index }}][is_correct]" value="{{ $answer->is_correct ? '1' : '0' }}" class="is-correct-hidden">
                                            </label>
                                            <button type="button" onclick="this.closest('.answer-entry').remove()" class="text-slate-300 hover:text-red-500 transition-colors">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                @elseif($question->question_type === 'drag_and_drop')
                                    <div class="flex items-center gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 transition-all hover:border-emerald-200">
                                        <div class="flex-1">
                                            <input type="text" name="answers[{{ $index }}][answer_text]" 
                                                class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold  focus:ring-4 focus:ring-emerald-100 transition-all outline-none" 
                                                placeholder="Type answer choice..." required value="{{ $answer->answer_text }}">
                                        </div>
                                        <div class="flex items-center gap-6">
                                            <label class="flex items-center gap-3 cursor-pointer">
                                                <input type="checkbox" name="answers[{{ $index }}][is_correct]" value="1" class="sr-only peer" {{ $answer->is_correct ? 'checked' : '' }}>
                                                <div class="w-6 h-6 rounded-lg border-2 border-slate-200 peer-checked:bg-emerald-600 peer-checked:border-emerald-600 flex items-center justify-center text-white text-[10px] transition-all">
                                                    <i class="fas fa-check scale-0 peer-checked:scale-100 transition-transform"></i>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 peer-checked:text-emerald-600">Correct Item</span>
                                            </label>
                                            <button type="button" onclick="this.closest('.answer-entry').remove()" class="text-slate-300 hover:text-red-500 transition-colors">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else {{-- fill_in_the_blank --}}
                                    <div class="bg-blue-50/30 p-10 rounded-[2.5rem] border-2 border-dashed border-blue-100 text-center">
                                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-400 mb-6">Target Keyphrase Detection</p>
                                        <input type="text" name="answers[{{ $index }}][answer_text]" 
                                            class="w-full max-w-md mx-auto px-8 py-6 bg-white border-2 border-blue-100 rounded-[2rem] text-center text-xl font-bold tracking-widest text-blue-900 focus:ring-8 focus:ring-blue-50 focus:border-blue-600 transition-all outline-none uppercase" 
                                            placeholder="Masukkan kata kunci..." required value="{{ $answer->answer_text }}">
                                        <input type="hidden" name="answers[{{ $index }}][is_correct]" value="1">
                                        <input type="hidden" name="correct_answer" value="{{ $index }}">
                                        <p class="mt-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem akan melakukan pencocokan string secara fleksibel pada input mahasiswa.</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-cloud-upload-alt text-indigo-500 text-xs"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Sinkronisasi Cloud: Siap mengirimkan pembaruan</span>
                    </div>
                    <x-ui.button type="submit" variant="primary" size="lg" class="shadow-xl shadow-indigo-500/20 bg-indigo-600 hover:bg-indigo-700 font-bold tracking-widest" icon="fas fa-cloud-upload-alt">
                        SIMPAN PERUBAHAN
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    @push('scripts')
    <script>
        let answerCount = {{ count($question->answers) }};

        async function fetchSubMaterials(materialId) {
            const subMaterialSelect = document.getElementById('sub_material_id');
            subMaterialSelect.innerHTML = '<option value="">PILIH SUB-MATERI</option>';
            
            if (!materialId) return;

            try {
                const response = await fetch(`/admin/materials/${materialId}/submaterials/json`);
                const data = await response.json();
                
                data.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.title;
                    subMaterialSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching sub-materials:', error);
            }
        }

        function resetAnswersForNewType() {
            if (!confirm('Mengubah tipe soal akan menghapus semua jawaban yang ada. Lanjutkan?')) {
                // Restore previous selection if possible would be nice, but simple alert for now
                return;
            }
            
            const container = document.getElementById('answers-container');
            const typeSelect = document.getElementById('question_type_select');
            const addBtn = document.getElementById('add-answer-btn');
            
            container.innerHTML = '';
            answerCount = 0;

            if (typeSelect.value === 'fill_in_the_blank') {
                addBtn.style.display = 'none';
                addAnswer();
            } else {
                addBtn.style.display = 'inline-flex';
                for(let i = 0; i < 4; i++) {
                    addAnswer();
                }
            }
        }

        function addAnswer() {
            const container = document.getElementById('answers-container');
            const type = document.getElementById('question_type_select').value;
            const index = answerCount++;
            
            const div = document.createElement('div');
            div.className = 'answer-entry group';
            
            let html = '';
            if (type === 'radio_button') {
                html = `
                    <div class="flex items-center gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 transition-all hover:border-blue-200">
                        <div class="flex-1">
                            <input type="text" name="answers[${index}][answer_text]" 
                                class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold  focus:ring-4 focus:ring-blue-100 transition-all outline-none" 
                                placeholder="Type answer choice..." required>
                        </div>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="correct_answer" value="${index}" class="sr-only peer" required>
                                <div class="w-6 h-6 rounded-full border-2 border-slate-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center text-white text-[10px] transition-all">
                                    <i class="fas fa-check scale-0 peer-checked:scale-100 transition-transform"></i>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 peer-checked:text-blue-600">Mark Correct</span>
                                <input type="hidden" name="answers[${index}][is_correct]" value="0" class="is-correct-hidden">
                            </label>
                            <button type="button" onclick="this.closest('.answer-entry').remove()" class="text-slate-300 hover:text-red-500 transition-colors">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                `;
            } else if (type === 'drag_and_drop') {
                html = `
                    <div class="flex items-center gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 transition-all hover:border-emerald-200">
                        <div class="flex-1">
                            <input type="text" name="answers[${index}][answer_text]" 
                                class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold  focus:ring-4 focus:ring-emerald-100 transition-all outline-none" 
                                placeholder="Type answer choice..." required>
                        </div>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="answers[${index}][is_correct]" value="1" class="sr-only peer">
                                <div class="w-6 h-6 rounded-lg border-2 border-slate-200 peer-checked:bg-emerald-600 peer-checked:border-emerald-600 flex items-center justify-center text-white text-[10px] transition-all">
                                    <i class="fas fa-check scale-0 peer-checked:scale-100 transition-transform"></i>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 peer-checked:text-emerald-600">Correct Item</span>
                            </label>
                            <button type="button" onclick="this.closest('.answer-entry').remove()" class="text-slate-300 hover:text-red-500 transition-colors">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                `;
            } else {
                html = `
                    <div class="bg-blue-50/30 p-10 rounded-[2.5rem] border-2 border-dashed border-blue-100 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-400 mb-6">Target Keyphrase Detection</p>
                        <input type="text" name="answers[${index}][answer_text]" 
                            class="w-full max-w-md mx-auto px-8 py-6 bg-white border-2 border-blue-100 rounded-[2rem] text-center text-xl font-bold tracking-widest text-blue-900 focus:ring-8 focus:ring-blue-50 focus:border-blue-600 transition-all outline-none uppercase" 
                            placeholder="Enter keyword..." required>
                        <input type="hidden" name="answers[${index}][is_correct]" value="1">
                        <input type="hidden" name="correct_answer" value="${index}">
                        <p class="mt-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem akan melakukan pencocokan string secara fleksibel pada input mahasiswa.</p>
                    </div>
                `;
            }
            
            div.innerHTML = html;
            container.appendChild(div);
            initAnswerLogic();
        }

        function initAnswerLogic() {
            const radios = document.querySelectorAll('input[name="correct_answer"]');
            radios.forEach(r => {
                r.onchange = function() {
                    const parent = this.closest('.answer-entry') || this.parentElement;
                    document.querySelectorAll('.is-correct-hidden').forEach(h => h.value = '0');
                    const hidden = parent.querySelector('.is-correct-hidden');
                    if(hidden) hidden.value = '1';
                };
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initAnswerLogic();
        });
    </script>
    <style>
        .animate-spin-slow {
            animation: spin 3s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
    @endpush

    <x-admin.tutorial />
</x-layouts.app>