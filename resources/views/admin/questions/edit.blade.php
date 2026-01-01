<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <x-navigation.sidebar activePage="questions" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Edit Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Edit Soal</h6>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            <form method="POST" action="{{ $material 
                                ? route('admin.materials.questions.update', ['material' => $material, 'question' => $question]) 
                                : route('admin.questions.update', $question) }}" class="p-4" id="questionForm">
                                @csrf
                                @method('PUT')
                                
                                @if ($errors->any())
                                    <div class="mb-4">
                                        <x-ui.alert type="warning" dismissible>
                                            @foreach ($errors->all() as $error)
                                                {{ $error }}<br>
                                            @endforeach
                                        </x-ui.alert>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-12">
                                        <x-forms.form-group label="Material" name="material_id">
                                            @if(isset($material))
                                                <input type="hidden" name="material_id" value="{{ $material->id }}">
                                                <input type="text" class="form-control" value="{{ $material->title }}" disabled>
                                            @else
                                                <select name="material_id" id="material_id" class="form-control" required>
                                                    <option value="">Pilih Material</option>
                                                    @foreach($materials as $mat)
                                                        <option value="{{ $mat->id }}" {{ $question->material_id == $mat->id ? 'selected' : '' }}>
                                                            {{ $mat->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </x-forms.form-group>
                                    </div>
                                    <div class="col-md-12">
                                        <x-forms.form-group label="Pertanyaan" name="question_text">
                                            <div class="my-3">
                                                <textarea id="content-editor" name="question_text">{{ $question->question_text }}</textarea>
                                            </div>
                                        </x-forms.form-group>
                                    </div>
                                    <div class="col-md-12">
                                        <x-forms.form-group label="Tipe Soal" name="question_type">
                                            <div class="input-group input-group-outline">
                                                <select name="question_type" class="form-control" required>
                                                    <option value="radio_button" {{ $question->question_type == 'radio_button' ? 'selected' : '' }}>Radio Button</option>
                                                    <option value="drag_and_drop" {{ $question->question_type == 'drag_and_drop' ? 'selected' : '' }}>Drag and Drop</option>
                                                    <option value="fill_in_the_blank" {{ $question->question_type == 'fill_in_the_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                                </select>
                                            </div>
                                        </x-forms.form-group>
                                    </div>
                                    <div class="col-md-12">
                                        <x-forms.form-group label="Tingkat Kesulitan" name="difficulty">
                                            <div class="input-group input-group-outline">
                                                <select name="difficulty" class="form-control" required>
                                                    <option value="beginner" {{ $question->difficulty == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                    <option value="medium" {{ $question->difficulty == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="hard" {{ $question->difficulty == 'hard' ? 'selected' : '' }}>Hard</option>
                                                </select>
                                            </div>
                                        </x-forms.form-group>
                                    </div>
                                </div>

                                <div id="answers-container">
                                    <h6 class="mb-3">Jawaban</h6>
                                    @foreach($question->answers as $index => $answer)
                                        <div class="answer-entry mb-3">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="input-group input-group-outline">
                                                        <input type="text" name="answers[{{ $index }}][answer_text]" class="form-control" placeholder="Jawaban" required value="{{ $answer->answer_text }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        @if($question->question_type === 'radio_button')
                                                            <input class="form-check-input correct-radio" type="radio" name="correct_answer" value="{{ $index }}" {{ $answer->is_correct ? 'checked' : '' }}>
                                                            <label class="form-check-label">Jawaban Benar</label>
                                                            <input type="hidden" name="answers[{{ $index }}][is_correct]" value="{{ $answer->is_correct ? '1' : '0' }}">
                                                        @else
                                                            <input class="form-check-input" type="checkbox" name="answers[{{ $index }}][is_correct]" value="1" {{ $answer->is_correct ? 'checked' : '' }}>
                                                            <label class="form-check-label">Jawaban Benar</label>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <x-ui.button type="button" variant="outline" size="sm" class="mb-3" onclick="addAnswer()" id="add-answer-btn">
                                    Tambah Jawaban
                                </x-ui.button>

                                <div class="row">
                                    <div class="col-12 mt-3">
                                        <x-ui.button type="submit" variant="primary" id="submitBtn">Simpan Perubahan</x-ui.button>
                                        @if($material)
                                            <x-ui.button variant="outline" href="{{ route('admin.materials.questions.index', $material) }}">Batal</x-ui.button>
                                        @else
                                            <x-ui.button variant="outline" href="{{ route('admin.questions.index') }}">Batal</x-ui.button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>

    @push('js')
    <script>
        let answerCount = {{ count($question->answers) > 0 ? count($question->answers) : 1 }};

        function handleQuestionTypeChange() {
            // Kita tidak mereset jawaban saat edit kecuali tipe soal benar-benar berubah yang membuat struktur tidak valid
            // Namun untuk amannya di edit view, kita biarkan user manual mengubahnya atau hanya mereset jika user mengubah dropdown tipe soal
            // Tapi karena ini edit, logic ini mungkin perlu disesuaikan agar tidak menghapus jawaban yang sudah ada saat page load.
            // Function ini dipanggil saat change event.
            
            const questionType = document.querySelector('[name="question_type"]').value;
            const answerContainer = document.getElementById('answers-container');
            const addAnswerBtn = document.getElementById('add-answer-btn');
            
            if (questionType === 'fill_in_the_blank') {
                if (addAnswerBtn) addAnswerBtn.style.display = 'none';
                // Jika user mengubah ke fill in the blank, kita mungkin harus warn atau force reset
            } else {
                if (addAnswerBtn) addAnswerBtn.style.display = 'block';
            }
        }
        
        // Overwrite handleQuestionTypeChange listener to not reset everything implicitly on load, 
        // but we need to attach it to change event.
        // The original script had it reset everything on change. We should keep that behavior for explicit changes.

        function resetAnswersForNewType() {
            const questionType = document.querySelector('[name="question_type"]').value;
            const answerContainer = document.getElementById('answers-container');
            const addAnswerBtn = document.getElementById('add-answer-btn');
            
            // Reset container kecuali heading
            const heading = answerContainer.querySelector('h6');
            answerContainer.innerHTML = '';
            answerContainer.appendChild(heading);
            
            // Reset counter
            answerCount = 0;
            
            // Tambah jawaban berdasarkan tipe soal
            if (questionType === 'fill_in_the_blank') {
                addAnswer(); // Hanya satu jawaban untuk fill in the blank
                if (addAnswerBtn) {
                    addAnswerBtn.style.display = 'none';
                }
            } else {
                // Untuk tipe soal lain, tambahkan dua jawaban dan tampilkan tombol tambah
                addAnswer();
                addAnswer();
                if (addAnswerBtn) {
                    addAnswerBtn.style.display = 'inline-block';
                }
            }
        }

        function addAnswer() {
            const container = document.getElementById('answers-container');
            const questionType = document.querySelector('[name="question_type"]').value;
            
            // Jangan menambahkan jawaban lagi jika tipe soal adalah fill_in_the_blank dan sudah ada jawaban
            if (questionType === 'fill_in_the_blank' && container.getElementsByClassName('answer-entry').length >= 1) {
                return;
            }
            
            const newAnswer = document.createElement('div');
            newAnswer.className = 'answer-entry mb-3';
            
            if (questionType === 'radio_button') {
                newAnswer.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-outline">
                                <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input correct-radio" type="radio" name="correct_answer" value="${answerCount}">
                                <label class="form-check-label">Jawaban Benar</label>
                                <input type="hidden" name="answers[${answerCount}][is_correct]" value="0">
                            </div>
                        </div>
                    </div>
                `;
            } else if (questionType === 'drag_and_drop') {
                newAnswer.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-outline">
                                <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="answers[${answerCount}][is_correct]" value="1">
                                <label class="form-check-label">Jawaban Benar</label>
                            </div>
                        </div>
                    </div>
                `;
            } else if (questionType === 'fill_in_the_blank') {
                newAnswer.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-outline">
                                <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input correct-radio" type="radio" name="correct_answer" value="${answerCount}" checked>
                                <label class="form-check-label">Jawaban Benar</label>
                                <input type="hidden" name="answers[${answerCount}][is_correct]" value="1">
                            </div>
                        </div>
                    </div>
                `;
            }
            
            container.appendChild(newAnswer);
            answerCount++; // Increment counter setelah menambahkan elemen
            
            // Tambahkan event listener ke semua radio button
            setupRadioButtonListeners();
        }

        function setupRadioButtonListeners() {
            // Hapus event listener lama untuk menghindari duplikasi
            const correctRadios = document.querySelectorAll('.correct-radio');
            correctRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Ketika radio button dipilih, perbarui semua hidden input
                    updateAllHiddenInputs();
                });
            });
        }

        function updateAllHiddenInputs() {
            const container = document.getElementById('answers-container');
            const entries = container.getElementsByClassName('answer-entry');
            const selectedRadio = document.querySelector('input[name="correct_answer"]:checked');
            
            if (!selectedRadio) return;
            
            // Nilai terpilih
            const selectedValue = selectedRadio.value;
            
            // Update semua hidden input
            Array.from(entries).forEach((entry, index) => {
                const hiddenInput = entry.querySelector('input[type="hidden"]');
                if (hiddenInput) {
                    // Note: In edit view, index might not match value if we deleted rows, 
                    // but here we just append so it likely matches count. 
                    // To be safe in production one might use IDs, but sticking to existing logic.
                    // Actually, the original logic used index of the iteration.
                    
                    // The value of radio is the answerCount at creation time.
                    // But here we are iterating current DOM elements.
                    // Wait, the original logic: hiddenInput.value = (index.toString() === selectedValue) ? '1' : '0';
                    // The `selectedValue` comes from the radio value.
                    // The radio value in PHP loop `value="{{ $index }}"`.
                    // The radio value in JS addAnswer `value="${answerCount}"`.
                    // This mix might be dangerous if we mix PHP-rendered and JS-added rows.
                    // However, we shouldn't overcomplicate if it worked before.
                    
                    // Let's look at original logic:
                    // value="{{ $index }}" for PHP loop.
                    // value="${answerCount}" for JS. 
                    // If we have 3 existing answers (indices 0, 1, 2). answerCount starts at 3?
                    // Original script: let answerCount = 1; // Mulai dari 1 karena sudah ada 1 jawaban awal (in create).
                    // In edit: answerCount was not explicitly set to count($answers).
                    // Let's check original edit.blade.php
                    
                    // Original edit.blade.php:
                    // let answerCount = 1; // This looks like a bug in original code if there are more than 1 answers?
                    // or maybe it's fine because it just increments?
                    // Actually if we have 3 answers (0, 1, 2) and we add one, it gets value 1. Conflict!
                    // I will fix this by initializing answerCount correctly.
                    
                    // Fix logic: check the radio value of the entry to compare? 
                    // No, the radio group name is shared.
                    // We need to ensure that the value of the selected radio matches the "identifier" of the row.
                    // In PHP loop, identifier is $index.
                    // In JS, it is answerCount.
                    
                    // We need reliable matching.
                    // Let's assume the radio value is the key.
                    // We iterate all entries. inside each entry find the radio. check if it is checked.
                    // If checked, set hidden input to 1. Else 0.
                    
                    const radio = entry.querySelector('input[type="radio"]');
                    if (radio) {
                        if (radio.checked) {
                             if(hiddenInput) hiddenInput.value = '1';
                        } else {
                             if(hiddenInput) hiddenInput.value = '0';
                        }
                    } else {
                        // If no radio (e.g. checkbox type), this function shouldn't strictly run or doesn't matter for hidden input of radio logic.
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const questionTypeSelect = document.querySelector('[name="question_type"]');
            
            // Event listener untuk perubahan tipe soal - reset answers if type changes
            questionTypeSelect.addEventListener('change', resetAnswersForNewType);
            
            // Setup radio button listeners for initial elements
            setupRadioButtonListeners();
            
            // Existing answers might need listeners
            const existingRadios = document.querySelectorAll('input[name="correct_answer"]');
            existingRadios.forEach(radio => {
                radio.addEventListener('change', updateAllHiddenInputs);
            });
        });
    </script>
    @endpush
    <x-admin.tutorial />

</x-layouts.app>