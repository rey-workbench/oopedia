<x-layouts.app theme="mahasiswa">
    <x-slot:title>Level Soal - {{ $material->title }}</x-slot:title>
    <div class="container-fluid">
        <div class="dashboard-header text-center">
            <h1 class="main-title">Level Soal: {{ $material->title }}</h1>
            <div class="title-underline"></div>
            
        </div>

        <div class="level-container" data-is-guest="{{ !auth()->check() ? 'true' : 'false' }}">
            <!-- Tambahkan peringatan tentang sistem penilaian hanya untuk user mahasiswa (bukan tamu) -->
            @if(auth()->check() && auth()->user()->role_id === 3)
                <div class="alert alert-info mb-4" role="alert">
                    <h5><i class="fas fa-info-circle"></i> Sistem Penilaian Pada Leaderboard</h5>
                    <p>Perhatikan bahwa nilai Anda di leaderboard bergantung pada jumlah percobaan yang dibutuhkan untuk menjawab soal dengan benar:</p>
                    
                    <div class="mt-2 fw-bold text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Pastikan jawaban Anda sudah benar sebelum mengirim untuk mendapatkan nilai maksimal!
                    </div>
                </div>
            @endif

            <div class="level-legend mb-4">
                <div class="legend-title mb-3">Keterangan:</div>
                <div class="legend-items">
                    <div class="legend-item">
                        <div class="legend-icon" style="background: #2196F3;">
                            <span class="text-white"></span>
                        </div>
                        <div class="legend-text">Soal yang bisa dikerjakan</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon" style="background: #4CAF50;">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div class="legend-text">Soal yang sudah dijawab benar</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon" style="background: #e9ecef;">
                            <span style="color: #6c757d;"></span>
                        </div>
                        <div class="legend-text">Soal yang belum bisa diakses</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon trophy-circle" style="background: #e9ecef;">
                            <i class="fas fa-trophy" style="color: #adb5bd;"></i>
                        </div>
                        <div class="legend-text">Penghargaan setelah menyelesaikan semua soal</div>
                    </div>
                </div>
            </div>

            <div class="level-header text-center mb-5">
                <div class="start-text">
                    <span>START</span>
                    <div class="start-line"></div>
                </div>
            </div>
            
            <div class="level-map">
                <!-- SVG untuk jalur -->
                <svg class="level-paths" width="100%" height="100%" style="position: absolute; top: 0; left: 0; z-index: 0;">
                    <!-- Jalur akan ditambahkan secara dinamis dengan JavaScript -->
                </svg>
                
                @foreach($levels as $index => $level)
                    <div class="level-row {{ $index % 3 == 0 ? 'center' : ($index % 3 == 1 ? 'left' : 'right') }}">
                        <div class="level-item {{ $level['status'] }}" data-level="{{ $level['level'] }}" data-question-id="{{ $level['question_id'] }}" {{ $level['status'] === 'unlocked' ? 'id=unlockedLevel' : '' }}>
                            @if($level['status'] === 'locked')
                                <div class="level-circle">
                                    <span class="level-number">{{ $level['level'] }}</span>
                                </div>
                            @elseif($level['status'] === 'completed')
                                <div class="level-circle completed">
                                    <span class="level-number">{{ $level['level'] }}</span>
                                    <i class="fas fa-check-circle completed-icon"></i>
                                </div>
                            @else
                                <a href="{{ route('mahasiswa.materials.questions.show', [
                                    'material' => $material->id,
                                    'question' => $level['question_id']
                                ]) }}" class="level-link">
                                    <div class="level-circle unlocked">
                                        <span class="level-number">{{ $level['level'] }}</span>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
                
                <div class="level-row center">
                    <div class="level-item trophy {{ count(array_filter($levels, function($level) { return $level['status'] !== 'completed'; })) === 0 ? 'completed' : 'locked' }}">
                        <div class="level-circle trophy-circle">
                            <i class="fas fa-trophy trophy-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="level-actions mt-4">
                <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Materi
                </a>
            </div>
        </div>
    </div>

    <x-slot:styles>
        <link href="{{ asset('css/mahasiswa/levels.css') }}" rel="stylesheet">
    </x-slot:styles>

    <x-slot:scripts>
        <script src="{{ asset('js/mahasiswa/levels.js') }}"></script>
    </x-slot:scripts>
</x-layouts.app>