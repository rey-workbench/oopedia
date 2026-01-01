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
        var initialAnswerCount = {{ count($question->answers) > 0 ? count($question->answers) : 1 }};
        // We do NOT call resetAnswersForNewType here because we want to preserve existing answers.
    </script>
    <script src="{{ asset('js/admin/questions/form.js') }}"></script>
    @endpush
    <x-admin.tutorial />

</x-layouts.app>