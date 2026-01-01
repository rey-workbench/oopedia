<x-layouts.app :title="'Review Soal - ' . $material->title">
    <x-slot:styles>
        <link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/material-show.css') }}">
        <link rel="stylesheet" href="{{ asset('css/question-review.css') }}">
        <link rel="stylesheet" href="{{ asset('css/mahasiswa/materials/questions/review.css') }}">
    </x-slot:styles>

    <div class="row">
        <!-- Sidebar for materials navigation -->
        <div class="col-lg-3 mb-4">
            <x-ui.card class="materi-sidebar h-100">
                <div class="sidebar-header mb-3">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Daftar Materi</h5>
                </div>
                <div class="sidebar-body">
                    <ul class="materi-list list-unstyled">
                        @foreach($materials as $m)
                            <li class="mb-2 {{ $m->id == $material->id ? 'active' : '' }}">
                                <a href="{{ route('mahasiswa.materials.show', $m->id) }}" class="text-decoration-none d-block p-2 rounded {{ $m->id == $material->id ? 'bg-primary text-white' : 'text-dark hover:bg-light' }}">
                                    <i class="fas fa-file-alt me-2"></i>{{ $m->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </x-ui.card>
        </div>
        
        <!-- Main content area -->
        <div class="col-lg-9">
            <x-ui.card shadow="sm">
                <div class="review-content">
                    <div class="question-review-container">
                        <div class="review-header mb-4">
                            <h3>Review Soal {{ $difficulty !== 'all' ? ucfirst($difficulty) : 'Semua Tingkat' }}</h3>
                            <p class="text-muted">Berikut adalah review dari soal-soal yang telah Anda kerjakan.</p>
                            
                            <!-- Difficulty filter buttons -->
                            <div class="difficulty-filter mt-3 mb-4">
                                <x-ui.button 
                                    href="{{ route('mahasiswa.materials.questions.review', $material->id) }}?difficulty=all" 
                                    variant="{{ $difficulty == 'all' ? 'primary' : 'outline-primary' }}" 
                                    class="me-2"
                                >
                                    Semua
                                </x-ui.button>

                                <x-ui.button 
                                    href="{{ route('mahasiswa.materials.questions.review', $material->id) }}?difficulty=beginner" 
                                    variant="{{ $difficulty == 'beginner' ? 'success' : 'outline-success' }}" 
                                    class="me-2"
                                >
                                    Beginner
                                </x-ui.button>

                                <x-ui.button 
                                    href="{{ route('mahasiswa.materials.questions.review', $material->id) }}?difficulty=medium" 
                                    variant="{{ $difficulty == 'medium' ? 'warning' : 'outline-warning' }}" 
                                    class="me-2"
                                >
                                    Medium
                                </x-ui.button>

                                <x-ui.button 
                                    href="{{ route('mahasiswa.materials.questions.review', $material->id) }}?difficulty=advanced" 
                                    variant="{{ $difficulty == 'advanced' ? 'danger' : 'outline-danger' }}"
                                >
                                    Advanced
                                </x-ui.button>
                            </div>
                        </div>
                        
                        @if($questions->count() > 0)
                            @foreach($questions as $index => $question)
                                <div class="question-review mb-4 p-4 border rounded">
                                    <div class="question-header d-flex justify-content-between align-items-center mb-3">
                                        <span class="question-number fw-bold">
                                            <i class="fas fa-question-circle me-2"></i>
                                            Soal {{ $index + 1 }} dari {{ $questions->count() }}
                                        </span>
                                        <x-ui.badge variant="{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}" class="p-2">
                                            {{ ucfirst($question->difficulty) }}
                                        </x-ui.badge>
                                    </div>
                                    
                                    <div class="question-content">
                                        <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                                        <div class="question-text p-3 bg-light rounded shadow-sm">
                                            {!! $question->question_text !!}
                                        </div>
                                    
                                        <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Pilihan Jawaban</h5>
                                        <div class="answers-container">
                                            @foreach($question->answers as $answer)
                                                <div class="answer-option p-3 mb-2 rounded d-flex align-items-center {{ $answer->is_correct ? 'border-success bg-success bg-opacity-10' : 'bg-white border' }}">
                                                    <div class="answer-text">
                                                        @if($answer->is_correct)
                                                            <i class="fas fa-check-circle text-success me-2"></i>
                                                        @endif
                                                        {!! $answer->answer_text !!}
                                                    </div>
                                                </div>
                                                @if($answer->is_correct && $answer->explanation)
                                                    <div class="answer-explanation p-3 mb-3 bg-light rounded border-start border-4 border-info">
                                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                                        <strong>Penjelasan:</strong> {!! $answer->explanation !!}
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <x-ui.alert variant="info" icon="info-circle">
                                Tidak ada soal yang tersedia untuk ditampilkan.
                            </x-ui.alert>
                        @endif
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <x-ui.button 
                        href="{{ route('mahasiswa.materials.questions.show', $material->id) }}?difficulty={{ $difficulty }}" 
                        variant="primary" 
                        class="me-2"
                        icon="arrow-left"
                    >
                        Kembali ke Soal
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('mahasiswa.materials.show', $material->id) }}" 
                        variant="secondary"
                        icon="book"
                    >
                        Kembali ke Materi
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>