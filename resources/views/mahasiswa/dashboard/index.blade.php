<x-layouts.app title="Dashboard" theme="mahasiswa">


    <div id="dashboard-container">
        <div class="dashboard-header text-center compact-header">
            <h1 class="main-title">Dashboard</h1>
            <div class="title-underline"></div>
        </div>
    
        <div class="welcome-banner">
            <div class="welcome-content">
                <div class="welcome-icon">
                    <i class="fas fa-hand-sparkles"></i>
                </div>
                <div class="welcome-text">
                    <h2 class="welcome-title">Selamat Datang Kembali,</h2>
                    <h3 class="welcome-name">{{ auth()->user()->name }}</h3>
                    <p class="welcome-message">Lanjutkan perjalanan belajar Anda hari ini!</p>
                </div>
            </div>
        </div>
    
        <div class="container-fluid">
            <div class="row g-4">
                <!-- Material Overview -->
                <div class="col-md-6">
                    <x-ui.card class="h-100">
                        <h3 class="materi-title">Materi Pembelajaran</h3>
                        <div class="materi-overview">
                            <div class="materi-count text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ asset('images/book-icon.png') }}" alt="Materi" class="dashboard-icon-large mb-2">
                                    <div class="count-number-large">{{ $totalMaterials }}</div>
                                </div>
                            </div>
                            <p class="materi-description">Total materi tersedia untuk dipelajari</p>
                            <div class="button-container">
                                <x-ui.button 
                                    href="{{ route('mahasiswa.materials.index') }}" 
                                    variant="primary" 
                                    class="w-100"
                                    icon="fas fa-book"
                                >
                                    Lihat Semua Materi
                                </x-ui.button>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
    
                <!-- Question Progress Overview -->
                <div class="col-md-6">
                    <x-ui.card class="h-100">
                        <h3 class="materi-title">Latihan Soal</h3>
                        <div class="materi-overview">
                            <div class="materi-count text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ asset('images/question-icon.png') }}" alt="Soal" class="dashboard-icon-large mb-2">
                                    <div class="count-number-large">{{ $totalQuestions }}</div>
                                </div>
                            </div>
                            <p class="materi-description">Total soal tersedia untuk latihan</p>
                            <div class="difficulty-breakdown">
                                <div class="difficulty-item">
                                    <x-ui.badge variant="success">Beginner</x-ui.badge>
                                    <span class="difficulty-count">{{ $easyQuestions }}</span>
                                </div>
                                <div class="difficulty-item">
                                    <x-ui.badge variant="warning">Medium</x-ui.badge>
                                    <span class="difficulty-count">{{ $mediumQuestions }}</span>
                                </div>
                                <div class="difficulty-item">
                                    <x-ui.badge variant="danger">Hard</x-ui.badge>
                                    <span class="difficulty-count">{{ $hardQuestions }}</span>
                                </div>
                            </div>
                            <div class="button-container">
                                <x-ui.button 
                                    href="{{ route('mahasiswa.materials.questions.index') }}" 
                                    variant="primary" 
                                    class="w-100"
                                    icon="fas fa-question-circle"
                                >
                                    Latihan Soal
                                </x-ui.button>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
    
                <!-- Recent Activities -->
                <div class="col-12">
                    <x-ui.card>
                        <h3 class="materi-title">Aktivitas Terbaru</h3>
                        <div class="activity-timeline">
                            @forelse($recentActivities as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon 
                                        @if($activity->type === 'achievement') bg-success
                                        @elseif($activity->type === 'milestone') bg-warning
                                        @else bg-info @endif">
                                        @if($activity->type === 'achievement')
                                            <i class="fas fa-trophy" style="color: white;"></i>
                                        @elseif($activity->type === 'milestone')
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="fas fa-tasks"></i>
                                        @endif
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">
                                            @if($activity->type === 'achievement')
                                                Pencapaian Baru!
                                            @elseif($activity->type === 'milestone')
                                                Milestone Tercapai!
                                            @else
                                                Progress Pembelajaran
                                            @endif
                                        </div>
                                        <div class="activity-details">
                                            @if($activity->type === 'achievement')
                                            Menyelesaikan {{ $activity->total_correct }} soal di materi 
                                            <span class="fw-bold">{{ $activity->material_title }}</span>
                                            @elseif($activity->type === 'milestone')
                                            Berhasil menyelesaikan soal level hard di materi 
                                            <span class="fw-bold">{{ $activity->material_title }}</span>
                                            @else
                                            Mengerjakan soal {{ $activity->difficulty }} di materi 
                                            <span class="fw-bold">{{ $activity->material_title }}</span>
                                            @endif
                                        </div>
                                        <div class="activity-time">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    Belum ada aktivitas
                                </div>
                            @endforelse
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
        <script src="{{ asset('js/mahasiswa/dashboard/index.js') }}"></script>
    </x-slot:scripts>
</x-layouts.app>