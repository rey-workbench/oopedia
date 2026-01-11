<x-layouts.app title="Pusat Pembuatan Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Pusat Pembuatan Soal"
            subtitle="Ciptakan butir soal evaluasi baru dengan parameter kustom."
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
            <x-slot:header>Spesifikasi & Registri Evaluasi</x-slot:header>

            <form method="POST" 
                action="{{ isset($material) ? route('admin.materials.questions.store', $material) : route('admin.questions.store') }}" 
                id="questionForm" 
                class="space-y-10"
            >
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    {{-- Origin & Complexity --}}
                    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-forms.form-group label="Modul Asal" name="material_id" required>
                            @if (isset($material))
                                <input type="hidden" name="material_id" id="material_id" value="{{ $material->id }}">
                                <div class="px-6 py-4 bg-blue-50/50 border border-blue-100 rounded-2xl text-sm font-black italic text-blue-900 border-l-4 border-l-blue-600 uppercase">
                                    {{ $material->title }}
                                </div>
                            @else
                                <select name="material_id" id="material_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required onchange="fetchSubMaterials(this.value)">
                                    <option value="">PILIH MODUL</option>
                                    @foreach ($materials as $mat)
                                        <option value="{{ $mat->id }}">{{ $mat->title }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </x-forms.form-group>

                        <x-forms.form-group label="Sub-materi (Opsional)" name="sub_material_id">
                            <select name="sub_material_id" id="sub_material_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase">
                                <option value="">PILIH SUB-MATERI</option>
                                @foreach ($subMaterials as $sub)
                                    <option value="{{ $sub->id }}" {{ old('sub_material_id') == $sub->id ? 'selected' : '' }}>{{ $sub->title }}</option>
                                @endforeach
                            </select>
                        </x-forms.form-group>

                        <x-forms.form-group label="Tingkat Kesulitan" name="difficulty" required>
                            <select name="difficulty" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required>
                                <option value="beginner">BEGINNER</option>
                                <option value="medium">MEDIUM</option>
                                <option value="hard">HARD</option>
                            </select>
                        </x-forms.form-group>

                        <div class="md:col-span-2">
                            <x-forms.form-group label="Skema Evaluasi" name="question_type" required>
                                <select name="question_type" id="question_type_select" onchange="resetAnswersForNewType()" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required>
                                    <option value="radio_button">TOMBOL RADIO (PILIHAN TUNGGAL)</option>
                                    <option value="drag_and_drop">SERET DAN LEPAS (PILIHAN GANDA)</option>
                                    <option value="fill_in_the_blank">ISIAN SINGKAT (COCOK KATA KUNCI)</option>
                                </select>
                            </x-forms.form-group>
                        </div>
                    </div>

                    {{-- Summary Visualization (Optional visual spacer or stats) --}}
                    <div class="lg:col-span-1">
                        <div class="h-full p-8 bg-slate-900 rounded-[2rem] relative overflow-hidden flex flex-col justify-center">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/10 blur-3xl"></div>
                            <div class="relative z-10 flex flex-col items-center text-center">
                                <div class="w-16 h-16 rounded-3xl bg-blue-600/20 text-blue-500 flex items-center justify-center mb-4">
                                    <i class="fas fa-microchip text-2xl"></i>
                                </div>
                                <h4 class="text-white text-xs font-black uppercase tracking-widest mb-2 italic">Neural Logic Engine</h4>
                                <p class="text-[9px] font-bold text-slate-400 leading-relaxed uppercase tracking-wider">
                                    Setiap soal dipetakan ke dalam graf pengetahuan sistem untuk optimasi pembelajaran adaptif.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question Content --}}
                <div class="space-y-4 pt-6 border-t border-slate-100">
                    <x-forms.form-group label="Pertanyaan Soal (WYSIWYG)" name="question_text">
                        <div class="quill-editor h-[300px] bg-white border border-slate-200 rounded-2xl overflow-hidden" data-input="question_text"></div>
                        <input type="hidden" id="question_text" name="question_text" value="{{ old('question_text') }}">
                    </x-forms.form-group>
                </div>

                {{-- Answer Registry --}}
                <div class="pt-8 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-tasks text-blue-500 text-xs"></i>
                            <h6 class="mb-0 italic font-black uppercase tracking-widest text-[10px] text-slate-400">Registri Jawaban</h6>
                        </div>
                        <x-ui.button type="button" id="add-answer-btn" onclick="addAnswer()" variant="ghost" size="sm" class="text-blue-600 font-black italic uppercase tracking-widest" icon="fas fa-plus">
                            TAMBAH PILIHAN
                        </x-ui.button>
                    </div>

                    <div id="answers-container" class="space-y-4">
                        {{-- Populated by JS --}}
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shield-check text-emerald-500 text-xs"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status Validasi: Semua parameter dioptimalkan</span>
                    </div>
                    <x-ui.button type="submit" variant="primary" size="lg" class="shadow-xl shadow-blue-500/20" icon="fas fa-save">
                        BUAT SOAL SEKARANG
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    @push('scripts')
    <script>
        let answerCount = 0;

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
            const container = document.getElementById('answers-container');
            const typeSelect = document.getElementById('question_type_select');
            const addBtn = document.getElementById('add-answer-btn');
            
            container.innerHTML = '';
            answerCount = 0;

            if (typeSelect.value === 'fill_in_the_blank') {
                addBtn.style.display = 'none';
                addAnswer(); // Only one for fill in the blank
            } else {
                addBtn.style.display = 'inline-flex';
                // Add initial 4 choices for multiple choice types
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
                                class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold italic focus:ring-4 focus:ring-blue-100 transition-all outline-none" 
                                placeholder="Ketik pilihan jawaban..." required>
                        </div>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="correct_answer" value="${index}" class="sr-only peer" required>
                                <div class="w-6 h-6 rounded-full border-2 border-slate-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center text-white text-[10px] transition-all">
                                    <i class="fas fa-check scale-0 peer-checked:scale-100 transition-transform"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:text-blue-600">Tandai Benar</span>
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
                                class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold italic focus:ring-4 focus:ring-emerald-100 transition-all outline-none" 
                                placeholder="Ketik pilihan jawaban..." required>
                        </div>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="answers[${index}][is_correct]" value="1" class="sr-only peer">
                                <div class="w-6 h-6 rounded-lg border-2 border-slate-200 peer-checked:bg-emerald-600 peer-checked:border-emerald-600 flex items-center justify-center text-white text-[10px] transition-all">
                                    <i class="fas fa-check scale-0 peer-checked:scale-100 transition-transform"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:text-emerald-600">Item Benar</span>
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
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 mb-6">Deteksi Frasa Kunci Target</p>
                        <input type="text" name="answers[${index}][answer_text]" 
                            class="w-full max-w-md mx-auto px-8 py-6 bg-white border-2 border-blue-100 rounded-[2rem] text-center text-xl font-black italic tracking-tighter text-blue-900 focus:ring-8 focus:ring-blue-50 focus:border-blue-600 transition-all outline-none uppercase" 
                            placeholder="Masukkan kata kunci..." required>
                        <input type="hidden" name="answers[${index}][is_correct]" value="1">
                        <input type="hidden" name="correct_answer" value="${index}">
                        <p class="mt-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem akan melakukan pencocokan string secara fleksibel pada input mahasiswa.</p>
                    </div>
                `;
            }
            
            div.innerHTML = html;
            container.appendChild(div);

            // Special logic for radio buttons to sync the hidden is_correct field
            if (type === 'radio_button') {
                const radios = document.querySelectorAll('input[name="correct_answer"]');
                radios.forEach(r => {
                    r.onchange = function() {
                        document.querySelectorAll('.is-correct-hidden').forEach(h => h.value = '0');
                        this.parentElement.querySelector('.is-correct-hidden').value = '1';
                    };
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            resetAnswersForNewType();
        });
    </script>
    @endpush

    <x-admin.tutorial />
</x-layouts.app>