@php
    use App\Models\Progress;
@endphp

<div class="materi-card shadow-sm rounded">
    <div class="materi-card-body p-4">
        
        <!-- Adaptive Stats Bar - Only for Logged In Users -->
        @if(!isset($isGuest) || !$isGuest)
            @php $isGuest = !auth()->check(); @endphp
        @endif

        @if(!$isGuest)
        <div class="difficulty-indicator mb-4 p-3 rounded bg-light d-flex justify-content-between align-items-center">
            <div class="level-indicator">
                <span class="text-muted small text-uppercase">Difficulty</span>
                <h5 class="mb-0 fw-bold text-{{ $difficulty == 'hard' ? 'danger' : ($difficulty == 'medium' ? 'warning' : 'success') }}">
                    {{ ucfirst($difficulty) }}
                </h5>
            </div>
        </div>
        @else
        <div class="guest-stats-bar mb-4 p-3 rounded bg-light border-start border-4 border-warning">
             <div class="d-flex align-items-center">
                 <i class="fas fa-user-clock fa-2x text-warning me-3"></i>
                 <div>
                     <h5 class="mb-0 fw-bold">Mode Tamu / Preview</h5>
                     <small class="text-muted">Login untuk fitur lengkap.</small>
                 </div>
             </div>
        </div>
        @endif
        
        <!-- Hidden Inputs for JS Context -->
        <input type="hidden" id="currentLevel" value="{{ $difficulty }}">

        <div id="questionContainer">
            @if($currentQuestion)
            <form id="questionForm"
                action="{{ route('mahasiswa.materials.questions.check', [
                    'material' => $material->id,
                    'question' => $currentQuestion->id,
                ]) }}"
                method="POST">
                @csrf
                <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                <input type="hidden" name="material_id" value="{{ $material->id }}">

                <div class="question-header mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                         <span class="badge bg-gradient-primary p-2 px-3">
                            <i class="fas fa-question-circle me-2"></i> Soal
                        </span>
                        <span class="badge bg-secondary p-2 px-3">
                             {{ ucfirst($currentQuestion->difficulty) }} Question
                        </span>
                    </div>
                </div>

                <div class="answers-container">
                    <!-- Tampilkan input teks jika tipe soal adalah fill_in_the_blank -->
                    @if ($currentQuestion->question_type === 'fill_in_the_blank')
                        <div class="question-content mb-4">
                            <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                            <div class="question-text whitespace-pre-wrap">
                                {!! $currentQuestion->question_text !!}
                            </div>
                        </div>
                        <div class="fill-in-blank-container p-3 mb-3 rounded">
                            <label for="fill_in_the_blank_answer" class="form-label">Jawaban Anda:</label>
                            <input type="text" name="fill_in_the_blank_answer" id="fill_in_the_blank_answer"
                                class="form-control" placeholder="Ketik jawaban Anda di sini..." required>
                        </div>
                    @else
                        @if ($currentQuestion->question_type === 'drag_and_drop')
                            <div class="question-content">
                                <h5 class=""><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                                <div class="question-html"
                                    style="font-family: monospace; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                                    @php
                                        // Hilangkan tag <p> dan </p> terlebih dahulu
                                        $rawText = str_replace(['<p>', '</p>'], ["", "\n"], $currentQuestion->question_text);

                                        // Hilangkan tag <br>
                                        $finalText = str_replace('<br>', "\n", $rawText);

                                        // Escape karakter < dan >
                                        $escapedText = str_replace(['<', '>'], ['&lt;', '&gt;'], $finalText);

                                        // Ganti [zone] dengan drop-zone span
                                        $zoneCount = substr_count($escapedText, '[zone]');
                                        for ($i = 1; $i <= $zoneCount; $i++) {
                                            $escapedText = preg_replace(
                                                '/\[zone\]/',
                                                '<span class="drop-zone" id="dropZone' .
                                                    $i .
                                                    '" data-zone="' .
                                                    $i .
                                                    '" data-user-answer=""></span>',
                                                $escapedText,
                                                1,
                                            );
                                        }
                                    @endphp
                                    <pre>{!! $escapedText !!}</pre>
                                </div>
                                {{-- Drag and Drop --}}

                                <input type="hidden" name="drag_and_drop_answers" id="dragAndDropAnswers">

                                <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Pilihan Jawaban</h5>
                                <div class="drag-items d-flex flex-wrap gap-2 mt-2">
                                    @foreach ($currentQuestion->answers as $answer)
                                        <div class="draggable btn btn-outline-primary" draggable="true"
                                            data-value="{{ $answer->answer_text }}">
                                            {{ $answer->answer_text }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="question-content mb-4">
                                <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                                <div class="question-text whitespace-pre-wrap">
                                    {!! $currentQuestion->question_text !!}
                                </div>
                            </div>
                            <!-- Tampilkan radio button untuk tipe soal lainnya -->
                            @foreach ($currentQuestion->answers as $answer)
                                <div class="answer-option p-3 mb-3 rounded d-flex align-items-center">
                                    <input type="radio" name="answer" id="answer{{ $answer->id }}"
                                        value="{{ $answer->id }}" class="me-3" required>
                                    <label for="answer{{ $answer->id }}"
                                        class="mb-0 w-100">{{ $answer->answer_text }}</label>
                                </div>
                            @endforeach
                        @endif
                    @endif
                </div>

                <div class="d-grid">
                    <x-ui.button type="submit" id="checkAnswerBtn" variant="primary" class="py-2" icon="check-circle">
                        Periksa Jawaban
                    </x-ui.button>
                </div>
            </form>
            @else
                <div class="text-center p-5">
                    <h4>Level Completed!</h4>
                    <p>You have answered all questions in the {{ ucfirst($difficulty) }} level.</p>
                    <p>Continue practicing to maintain your streak or wait for new content.</p>
                </div>
            @endif
        </div>

        <!-- Feedback container (initially hidden) -->
        <div class="exercise-feedback" style="display: none;">
            <div class="feedback-container">
                <div id="feedbackIcon" class="feedback-icon">
                    <!-- Icon will be inserted here by JS -->
                </div>
                <div id="feedbackStatus">
                    <!-- Status will be inserted here by JS -->
                </div>
                
                <!-- Adaptive Feedback Area -->
                <div id="adaptiveFeedback" class="alert mt-3" style="display: none;">
                    <!-- Adaptive messages (Level up, XP gain) go here -->
                </div>

                <div id="explanationBox" style="display: none;" class="explanation-box mt-4 p-3 bg-light rounded">
                    <h5><i class="fas fa-info-circle me-2"></i>Penjelasan</h5>
                    <p id="explanationText" class="mb-0"></p>
                </div>
                <div class="feedback-actions mt-4">
                    <x-ui.button id="tryAgainBtn" variant="outline-light" class="px-4 py-2" icon="redo">
                        Coba Lagi
                    </x-ui.button>
                    <x-ui.button id="nextQuestionBtn" variant="success" class="px-4 py-2" style="display: none;">
                        Lanjut ke Soal Berikutnya <i class="fas fa-arrow-right ms-2"></i>
                    </x-ui.button>
                     <x-ui.button href="{{ route('mahasiswa.dashboard') }}" id="dashboardBtn" variant="secondary" class="px-4 py-2" style="display: none;" icon="home">
                        Dashboard
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>