<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="question-banks" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Kelola Soal Bank" />
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
                        
                        @if(session('error'))
                            <div class="px-4 pt-4">
                                <x-ui.alert type="danger" dismissible>
                                    {{ session('error') }}
                                </x-ui.alert>
                            </div>
                        @endif

                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Kelola Soal: {{ $questionBank->name }}</h6>
                                <x-ui.button variant="light" size="sm" href="{{ route('admin.question-banks.show', $questionBank) }}" icon="arrow_back" class="me-3">
                                    Kembali
                                </x-ui.button>
                            </div>
                        </x-slot:header>
                        
                        <div class="card-body px-4 pt-4">
                            <!-- Filter and search -->
                            <form method="GET" action="{{ route('admin.question-banks.manage-questions', $questionBank) }}">
                                <div class="row mb-4">
                                    <div class="col-md-5">
                                        <div class="input-group input-group-outline my-3">
                                            <label class="form-label">Cari soal...</label>
                                            <input type="text" name="search" class="form-control" value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group input-group-outline my-3">
                                            <select name="difficulty" class="form-control">
                                                <option value="">Semua Tingkat Kesulitan</option>
                                                <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <x-ui.button type="submit" variant="primary" class="w-100 mt-3" icon="search">
                                            Filter
                                        </x-ui.button>
                                    </div>
                                </div>
                            </form>
                            
                            <!-- Questions list -->
                            <x-ui.table>
                                <thead>
                                    <tr>
                                        <x-ui.th>Soal</x-ui.th>
                                        <x-ui.th class="ps-2">Materi</x-ui.th>
                                        <x-ui.th class="ps-2">Kesulitan</x-ui.th>
                                        <x-ui.th class="ps-2">Tipe Soal</x-ui.th>
                                        <x-ui.th class="ps-2 text-center">Aksi</x-ui.th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questions as $question)
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
                                            {{ $question->material->title ?? 'Tidak ada materi' }}
                                        </td>
                                        <td>
                                            <x-ui.badge variant="{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($question->difficulty) }}
                                            </x-ui.badge>
                                        </td>
                                        <td class="text-sm">
                                            {{ $question->formatted_type ?? ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                        </td>
                                        <td class="align-middle text-center">
                                            <form action="{{ route('admin.question-banks.add-question', ['questionBank' => $questionBank, 'question' => $question]) }}" method="POST">
                                                @csrf
                                                <x-ui.button type="submit" variant="success" size="sm" icon="add">
                                                    Tambahkan ke Bank
                                                </x-ui.button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-sm mb-0">Tidak ada soal yang tersedia untuk ditambahkan.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </x-ui.table>
                            
                            <div class="d-flex justify-content-center mt-4">
                                {{ $questions->links() }}
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layouts.app> 