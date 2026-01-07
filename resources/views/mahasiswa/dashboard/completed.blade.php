<x-layouts.app theme="mahasiswa">
    <x-slot:title>Materi Selesai</x-slot:title>

    <div class="dashboard-header text-center">
        <h1 class="main-title">Materi Selesai</h1>
        <div class="title-underline"></div>
    </div>

    <div class="dashboard-content">
        @if(count($materialsWithStats) == 0)
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="empty-state-title">Belum Ada Materi yang Selesai</h3>
                <p class="empty-state-description">
                    Anda belum menyelesaikan semua soal dari materi apapun.
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
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h4 class="material-title">{{ $material->title }}</h4>
                            </div>
                            <div class="material-card-body">
                                <!-- Overall Progress -->
                                <div class="progress-section">
                                    <div class="progress-info d-flex justify-content-between">
                                        <span class="progress-text">Progress Keseluruhan</span>
                                        <span class="progress-percentage">100%</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: 100%"></div>
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
                                    <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn-view-green">
                                        <i class="fas fa-book me-2"></i>
                                        <span>Lihat Materi</span>
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
        <link href="{{ asset('css/mahasiswa/completed.css') }}" rel="stylesheet">
    </x-slot:styles>
</x-layouts.app>