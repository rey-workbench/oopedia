<div class="review-container">
    <div class="review-header d-flex justify-content-between align-items-center">
        <h3 class="review-title">
            <i class="fas fa-clipboard-check me-2"></i>Review Semua Soal
            @if(!auth()->check())
                <small class="text-muted">(Tamu)</small>
            @endif
        </h3>
        @if(!auth()->check())
            <div class="action-buttons">
                <form action="{{ route('mahasiswa.materials.reset', $material->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-redo me-2"></i>Kerjakan Ulang
                    </button>
                </form>
            </div>
        @endif
    </div>

    @foreach($material->questions as $index => $question)
        <x-ui.card class="question-review mb-4" :hover="true">
            <x-slot:header>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="question-number font-weight-bold">
                        <i class="fas fa-question-circle me-1"></i>
                        Soal {{ $index + 1 }} dari {{ $material->questions->count() }}
                    </span>
                    <x-ui.badge variant="{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                        {{ ucfirst($question->difficulty) }}
                    </x-ui.badge>
                </div>
            </x-slot:header>
            
            <div class="question-content">
                <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                <div class="question-text">
                    {!! $question->question_text !!}
                </div>

                <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Pilihan Jawaban</h5>
                <div class="answers-container">
                    @foreach($question->answers as $answer)
                        <div class="answer-option {{ $answer->is_correct ? 'correct-answer' : '' }}">
                            <div class="answer-text">
                                @if($answer->is_correct)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                @endif
                                {!! $answer->answer_text !!}
                            </div>
                            @if($answer->explanation)
                                <div class="answer-explanation">
                                    <i class="fas fa-info-circle"></i>
                                    {!! $answer->explanation !!}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </x-ui.card>
    @endforeach

    <div class="navigation-buttons">
        <x-ui.button href="{{ route('mahasiswa.materials.questions.index') }}" variant="primary" icon="list" class="me-2">
            Kembali ke Daftar Soal
        </x-ui.button>
        @if(!auth()->check())
            <x-ui.button href="{{ route('mahasiswa.dashboard') }}" variant="info" icon="home">
                Dashboard
            </x-ui.button>
        @endif
    </div>
</div>

<div class="level-item trophy {{ count(array_filter($levels, function($level) { return $level['status'] !== 'completed'; })) === 0 ? 'completed' : '' }}">
    <div class="level-circle trophy-circle">
        <i class="fas fa-trophy trophy-icon"></i>
    </div>
</div>

{{-- Note: CSS for trophy animation should be included in the parent view --}}
{{-- <link href="{{ asset('css/mahasiswa/partials/question-review.css') }}" rel="stylesheet"> --}}