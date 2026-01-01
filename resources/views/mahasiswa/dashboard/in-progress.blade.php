<x-layouts.app title="Materi Sedang Dipelajari">
    <div class="dashboard-header text-center">
        <h1 class="main-title">Materi Sedang Dipelajari</h1>
        <div class="title-underline"></div>
    </div>

    <div class="dashboard-content">
        @if(count($materialsWithStats) == 0)
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3 class="empty-state-title">Belum Ada Materi yang Sedang Dipelajari</h3>
                <p class="empty-state-description">
                    Anda belum memulai belajar materi apapun atau semua materi sudah selesai.
                </p>
                <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-primary">
                    <i class="fas fa-book me-2"></i>Lihat Daftar Materi
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach($materialsWithStats as $materialData)
                    @php
                        $material = $materialData['material'];
                        $stats = $materialData['stats'];
                    @endphp
                    <div class="col-md-12 col-lg-6">
                        <div class="material-card">
                            <div class="material-card-header">
                                <div class="material-icon">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <h4 class="material-title">{{ $material->title }}</h4>
                            </div>
                            <div class="material-card-body">
                                <!-- Overall Progress -->
                                <div class="progress-section">
                                    <div class="progress-info d-flex justify-content-between">
                                        <span class="progress-text">Progress Keseluruhan</span>
                                        <span class="progress-percentage">{{ $stats['overall']['percentage'] }}%</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: {{ $stats['overall']['percentage'] }}%"></div>
                                    </div>
                                    <div class="progress-details">
                                        <small>{{ $stats['overall']['correct'] }} dari {{ $stats['overall']['total'] }} soal selesai</small>
                                    </div>
                                </div>
                                
                                <div class="difficulty-progress-container">
                                    <!-- Beginner Progress -->
                                    <div class="difficulty-progress beginner">
                                        <div class="difficulty-label">
                                            <i class="fas fa-battery-quarter"></i>
                                            <span>Beginner</span>
                                        </div>
                                        <div class="difficulty-bar-container">
                                            <div class="difficulty-bar" style="width: {{ $stats['beginner']['percentage'] }}%"></div>
                                        </div>
                                        <div class="difficulty-percentage">{{ $stats['beginner']['percentage'] }}%</div>
                                        <div class="difficulty-details">
                                            <small>{{ $stats['beginner']['correct'] }}/{{ $stats['beginner']['configured_total'] }} soal</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Medium Progress -->
                                    <div class="difficulty-progress medium">
                                        <div class="difficulty-label">
                                            <i class="fas fa-battery-half"></i>
                                            <span>Medium</span>
                                        </div>
                                        <div class="difficulty-bar-container">
                                            <div class="difficulty-bar" style="width: {{ $stats['medium']['percentage'] }}%"></div>
                                        </div>
                                        <div class="difficulty-percentage">{{ $stats['medium']['percentage'] }}%</div>
                                        <div class="difficulty-details">
                                            <small>{{ $stats['medium']['correct'] }}/{{ $stats['medium']['configured_total'] }} soal</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Hard Progress -->
                                    <div class="difficulty-progress hard">
                                        <div class="difficulty-label">
                                            <i class="fas fa-battery-full"></i>
                                            <span>Hard</span>
                                        </div>
                                        <div class="difficulty-bar-container">
                                            <div class="difficulty-bar" style="width: {{ $stats['hard']['percentage'] }}%"></div>
                                        </div>
                                        <div class="difficulty-percentage">{{ $stats['hard']['percentage'] }}%</div>
                                        <div class="difficulty-details">
                                            <small>{{ $stats['hard']['correct'] }}/{{ $stats['hard']['configured_total'] }} soal</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="material-actions">
                                    <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn-view-material">
                                        <i class="fas fa-book me-2"></i>
                                        <span>Lihat Materi</span>
                                    </a>
                                    <a href="{{ route('mahasiswa.materials.questions.show', $material->id) }}" class="btn-read-material">
                                        <i class="fas fa-question-circle me-2"></i>
                                        <span>Lanjut Latihan</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-slot:styles>
        <link href="{{ asset('css/mahasiswa/dashboard/in-progress.css') }}" rel="stylesheet">
    </x-slot:styles>
</x-layouts.app>