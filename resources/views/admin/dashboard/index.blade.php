<x-layouts.app title="OOPEDIA" bodyClass="dashboard-layout g-sidenav-show">
    <x-navigation.sidebar activePage="dashboard" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navigation.navbar titlePage="Dashboard Admin" />
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <!-- Statistics Cards Row -->
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl">
                                <i class="material-icons opacity-10">group</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Mahasiswa</p>
                                <h4 class="mb-0">{{ $totalStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl">
                                <i class="material-icons opacity-10">person_outline</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Mahasiswa Aktif</p>
                                <h4 class="mb-0">{{ $activeStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl">
                                <i class="material-icons opacity-10">library_books</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Materi</p>
                                <h4 class="mb-0">{{ $totalMaterials }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl">
                                <i class="material-icons opacity-10">quiz</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Soal</p>
                                <h4 class="mb-0">{{ $totalQuestions }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Students Section -->
            <div class="row mt-4">
                <div class="col-lg-12 mb-4">
                    <x-ui.card>
                        <x-slot:header>
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Mahasiswa dengan Performa Terbaik</h6>
                                <x-ui.button variant="info" size="sm" href="{{ route('admin.students.index') }}">
                                    <i class="material-icons text-sm">visibility</i>
                                    Lihat Semua
                                </x-ui.button>
                            </div>
                        </x-slot:header>
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mahasiswa</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Soal Diselesaikan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progress Materi</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Terakhir Aktif</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentProgress as $student)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="avatar avatar-sm me-3 bg-gradient-primary rounded-circle">
                                                    <span class="text-white text-xs">{{ substr($student->name, 0, 1) }}</span>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $student->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $student->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $student->completed_questions }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2 text-xs font-weight-bold">{{ $student->materials_progress }}%</span>
                                                <div class="progress" style="width: 100px; height: 5px;">
                                                    <div class="progress-bar bg-gradient-info" role="progressbar" 
                                                         aria-valuenow="{{ $student->materials_progress }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100" 
                                                         style="width: {{ $student->materials_progress }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $student->last_active ? $student->last_active->diffForHumans() : 'Belum pernah' }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <x-ui.button variant="info" size="sm" href="{{ route('admin.students.progress', $student->id) }}" icon="assessment" />
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-ui.card>
                </div>
            </div>

            <!-- Material Completion Stats -->
            <div class="row mt-4">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <x-ui.card>
                        <x-slot:header>
                            <h6 class="mb-0">Statistik Penyelesaian Materi</h6>
                        </x-slot:header>
                        <div class="p-3">
                            <div class="chart">
                                <canvas id="material-completion-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
                <div class="col-lg-6">
                    <x-ui.card>
                        <x-slot:header>
                            <h6 class="mb-0">Materi Paling Populer</h6>
                        </x-slot:header>
                        <div class="p-3">
                            <ul class="list-group">
                                @foreach($popularMaterials as $material)
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                            <i class="material-icons opacity-10 text-white">book</i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">{{ $material->title }}</h6>
                                            <span class="text-xs">{{ $material->students_count }} mahasiswa aktif</span>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <span class="text-success text-sm font-weight-bolder">{{ $material->completion_rate }}% selesai</span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </x-ui.card>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row mt-4">
                <div class="col-12">
                    <x-ui.card>
                        <x-slot:header>
                            <h6 class="mb-0">Aktivitas Penyelesaian Terbaru</h6>
                        </x-slot:header>
                        
                        <div class="timeline timeline-one-side p-3">
                            @foreach($recentProgress as $progress)
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    @if($progress->is_correct)
                                        <i class="material-icons text-success">check_circle</i>
                                    @else
                                        <i class="material-icons text-warning">error_outline</i>
                                    @endif
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">
                                        {{ optional($progress->user)->name ?? 'unknown' }} 
                                        {{ $progress->is_correct ? 'berhasil menyelesaikan' : 'mencoba' }} soal
                                    </h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        {{ optional($progress->material)->title ?? '-' }} - 
                                        <x-ui.badge variant="{{ $progress->is_correct ? 'success' : 'warning' }}" class="bg-gradient-{{ $progress->is_correct ? 'success' : 'warning' }}">
                                            {{ ucfirst($progress->question->difficulty ?? 'unknown') }}
                                        </x-ui.badge>
                                    </p>
                                    <p class="text-sm mt-3 mb-0">
                                        {{ $progress->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </x-ui.card>
                </div>
            </div>

            <x-navigation.footer />
        </div>
    </main>
    
    <x-admin.tutorial />
    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Pass PHP data to JS
            var materialStats = {
                labels: {!! json_encode($materialStats->pluck('title')) !!},
                data: {!! json_encode($materialStats->pluck('completion_rate')) !!}
            };
        </script>
        <script src="{{ asset('js/admin/dashboard/index.js') }}"></script>
    </x-slot:scripts>
</x-layouts.app>
