<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <x-navigation.sidebar activePage="questions" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Tambah Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Tambah Soal Baru</h6>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            @if (isset($material))
                                <form method="POST" action="{{ route('admin.materials.questions.store', $material) }}"
                                    class="p-4" id="questionForm">
                            @else
                                <form method="POST" action="{{ route('admin.questions.store') }}" class="p-4"
                                    id="questionForm">
                            @endif
                            @csrf

                            @if ($errors->any())
                                <div class="mb-4">
                                    <x-ui.alert type="warning" dismissible>
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </x-ui.alert>
                                </div>
                            @endif

                            @if (session('warning'))
                                <div class="mb-4">
                                    <x-ui.alert type="warning" dismissible>
                                        {{ session('warning') }}
                                    </x-ui.alert>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12">
                                    <x-forms.form-group label="Material" name="material_id">
                                        @if (isset($material))
                                            <input type="hidden" name="material_id" value="{{ $material->id }}">
                                            <input type="text" class="form-control" value="{{ $material->title }}" disabled>
                                        @else
                                            <select name="material_id" id="material_id" class="form-control" required>
                                                <option value="">Pilih Material</option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">{{ $material->title }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </x-forms.form-group>
                                </div>
                                <div class="col-md-12">
                                    <x-forms.form-group label="Pertanyaan" name="question_text">
                                        <div class="my-3">
                                            <textarea id="content-editor" name="question_text">{{ old('question_text') }}</textarea>
                                        </div>
                                    </x-forms.form-group>
                                </div>
                                <div class="col-md-12">
                                    <x-forms.form-group label="Tipe Soal" name="question_type">
                                        <div class="input-group input-group-outline">
                                            <select name="question_type" class="form-control" required>
                                                <option value="fill_in_the_blank">Fill in the Blank</option>
                                                <option value="radio_button">Radio Button</option>
                                                <option value="drag_and_drop">Drag and Drop</option>
                                            </select>
                                        </div>
                                    </x-forms.form-group>
                                </div>
                                <div class="col-md-12">
                                    <x-forms.form-group label="Tingkat Kesulitan" name="difficulty">
                                        <div class="input-group input-group-outline">
                                            <select name="difficulty" class="form-control" required>
                                                <option value="beginner">Beginner</option>
                                                <option value="medium">Medium</option>
                                                <option value="hard">Hard</option>
                                            </select>
                                        </div>
                                    </x-forms.form-group>
                                </div>
                            </div>

                            <div id="answers-container">
                                <h6 class="mb-3">Jawaban</h6>
                                <div class="answer-entry mb-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="input-group input-group-outline">
                                                <input type="text" name="answers[0][answer_text]" class="form-control" placeholder="Jawaban" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="correct_answer" value="0">
                                                <label class="form-check-label">Jawaban Benar</label>
                                                <input type="hidden" name="answers[0][is_correct]" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <x-ui.button type="button" variant="outline" size="sm" class="mb-3" onclick="addAnswer()" id="add-answer-btn">
                                Tambah Jawaban
                            </x-ui.button>

                            <div class="row">
                                <div class="col-12 mt-3">
                                    <x-ui.button type="submit" variant="primary" id="submitBtn">Simpan Soal</x-ui.button>
                                    @if (isset($material))
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
            // Initializing variables for form.js
            var initialAnswerCount = 1;

            // Pre-populate logic if needed, but for create we rely on resetAnswersForNewType() usually, 
            // but we need to trigger it once on load.
            document.addEventListener('DOMContentLoaded', function() {
                 // Trigger reset to setup initial 2 answers for non-blank types
                 // Note: we can't call resetAnswersForNewType directly here as it's in the external file 
                 // which loads after. But form.js triggers handleQuestionTypeChange/reset on load/change.
                 
                 // In form.js we attached change listener. 
                 // We need to trigger it.
                 resetAnswersForNewType(); 
            });
        </script>
        <script src="{{ asset('js/admin/questions/form.js') }}"></script>
    @endpush
    <x-admin.tutorial />
</x-layouts.app>