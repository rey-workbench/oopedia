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

@push('styles')
<style>
    /* Existing styles... */
    
    /* Trophy Animation Styles */
    .trophy-circle {
        background: #444;
        transition: all 0.5s ease;
    }
    
    .trophy-icon {
        font-size: 24px;
        color: #777;
        transition: all 0.5s ease;
    }
    
    .trophy.completed .trophy-circle {
        background: linear-gradient(145deg, #FFD700, #FFA500);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
        animation: pulseGold 2s infinite;
    }
    
    .trophy.completed .trophy-icon {
        color: #fff;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        animation: rotateIcon 20s linear infinite;
    }
    
    @keyframes pulseGold {
        0% {
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
            transform: scale(1);
        }
        50% {
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.8);
            transform: scale(1.05);
        }
        100% {
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
            transform: scale(1);
        }
    }
    
    @keyframes rotateIcon {
        0% {
            transform: rotateY(0deg);
        }
        100% {
            transform: rotateY(360deg);
        }
    }
    
    /* Connector to Trophy */
    .level-item:last-child + .trophy {
        margin-left: 20px;
    }
    
    .level-connector:last-of-type {
        background: linear-gradient(90deg, 
            var(--connector-color) 0%,
            #FFD700 100%
        );
    }
</style>
@endpush 