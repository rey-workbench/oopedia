<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="question-banks" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Detail Bank Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        @if(session('success'))
                            <div class="px-4 pt-4">
                                <x-ui.alert type="success" dismissible>
                                    {{ session('success') }}
                                </x-ui.alert>
                            </div>
                        @endif

                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Detail Bank Soal</h6>
                                <div>
                                    <x-ui.button variant="success" size="sm" href="{{ route('admin.question-banks.manage-questions', $questionBank) }}" icon="question_answer" class="me-2">
                                        Kelola Soal
                                    </x-ui.button>
                                    <x-ui.button variant="warning" size="sm" href="{{ route('admin.question-banks.configure', $questionBank) }}" icon="settings" class="me-2">
                                        Konfigurasi
                                    </x-ui.button>
                                    <x-ui.button variant="light" size="sm" href="{{ route('admin.question-banks.index') }}" icon="arrow_back" class="me-3">
                                        Kembali
                                    </x-ui.button>
                                </div>
                            </div>
                        </x-slot:header>
                        
                        <div class="card-body px-4 py-3">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h4>{{ $questionBank->name }}</h4>
                                    <p class="text-muted">{{ $questionBank->description }}</p>
                                    <div class="d-flex gap-4">
                                        <p class="mb-0"><strong>Dibuat oleh:</strong> {{ $questionBank->creator->name ?? 'Unknown' }}</p>
                                        <p class="mb-0"><strong>Tanggal dibuat:</strong> {{ $questionBank->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Statistik Soal -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3">Statistik Soal</h5>
                                </div>
                                <div class="col-md-4">
                                    <x-ui.card class="bg-light shadow-none border">
                                        <div class="card-body text-center p-3">
                                            <h6 class="card-title text-success">Beginner</h6>
                                            <h3 class="mb-0">{{ $questionCounts['beginner'] }}</h3>
                                            <p class="text-muted mb-0">soal</p>
                                        </div>
                                    </x-ui.card>
                                </div>
                                <div class="col-md-4">
                                    <x-ui.card class="bg-light shadow-none border">
                                        <div class="card-body text-center p-3">
                                            <h6 class="card-title text-warning">Medium</h6>
                                            <h3 class="mb-0">{{ $questionCounts['medium'] }}</h3>
                                            <p class="text-muted mb-0">soal</p>
                                        </div>
                                    </x-ui.card>
                                </div>
                                <div class="col-md-4">
                                    <x-ui.card class="bg-light shadow-none border">
                                        <div class="card-body text-center p-3">
                                            <h6 class="card-title text-danger">Hard</h6>
                                            <h3 class="mb-0">{{ $questionCounts['hard'] }}</h3>
                                            <p class="text-muted mb-0">soal</p>
                                        </div>
                                    </x-ui.card>
                                </div>
                            </div>
                            
                            <!-- Daftar Soal -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-3">Daftar Soal ({{ $questionBank->questions->count() }} soal)</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Soal</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kesulitan</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipe Soal</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($questionBank->questions as $question)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <div class="mb-0 text-sm">
                                                                    {!! Str::limit(strip_tags($question->question_text), 100) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <x-ui.badge variant="{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($question->difficulty) }}
                                                        </x-ui.badge>
                                                    </td>
                                                    <td class="text-sm">
                                                        {{ $question->formatted_type ?? ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('admin.question-banks.remove-question', ['questionBank' => $questionBank, 'question' => $question]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <x-ui.button type="submit" variant="danger" size="sm" icon="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini dari bank soal?')">
                                                                Hapus
                                                            </x-ui.button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center p-4">
                                                        <p class="mb-3">Belum ada soal dalam bank soal ini.</p>
                                                        <x-ui.button variant="primary" size="sm" href="{{ route('admin.question-banks.manage-questions', $questionBank) }}" icon="add">
                                                            Tambahkan Soal
                                                        </x-ui.button>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layouts.app> 