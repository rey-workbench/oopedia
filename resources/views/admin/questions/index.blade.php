<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="questions" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="{{ $material ? 'Soal untuk '.$material->title : 'Semua Soal' }}" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ $material ? route('admin.materials.questions.index', $material) : route('admin.questions.index') }}" class="mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <x-forms.input-group>
                            <x-ui.input name="search" placeholder="Cari berdasarkan soal, tipe soal, atau pembuat..." value="{{ request('search') }}" />
                        </x-forms.input-group>
                    </div>
                    <div class="col-md-3">
                        <x-forms.input-group>
                            <select name="difficulty" class="form-control">
                                <option value="">Semua Tingkat Kesulitan</option>
                                <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </x-forms.input-group>
                    </div>
                    <div class="col-md-3">
                        <x-ui.button type="submit" variant="primary" class="w-100 my-2" icon="search">
                            Cari
                        </x-ui.button>
                    </div>
                </div>
            </form>

            <!-- Questions Table -->    
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
                                <h6 class="text-white text-capitalize ps-3 mb-0">
                                    {{ $material ? 'Soal untuk Materi: ' . $material->title : 'Daftar Soal' }}
                                </h6>
                                @if($material)
                                    <x-ui.button variant="light" size="sm" href="{{ route('admin.materials.questions.create', $material) }}" class="me-3">Tambah Soal</x-ui.button>
                                @else
                                    <x-ui.button variant="light" size="sm" href="{{ route('admin.questions.create') }}" class="me-3">Tambah Soal</x-ui.button>
                                @endif
                            </div>
                        </x-slot:header>
                        
                        <div class="card-body px-0 pb-2">
                            <x-ui.table>
                                <thead>
                                    <tr>
                                        <x-ui.th>Materi</x-ui.th>
                                        <x-ui.th>Pertanyaan</x-ui.th>
                                        <x-ui.th class="ps-2">Tipe Soal</x-ui.th>
                                        <x-ui.th class="ps-2">Kesulitan</x-ui.th>
                                        <x-ui.th class="ps-2">Pembuat</x-ui.th>
                                        <x-ui.th class="text-center">Aksi</x-ui.th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questions as $question)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $question->material->title }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <p class="text-sm mb-0">
                                                {!! Str::limit(strip_tags($question->question_text), 100) !!}
                                                @if(strlen(strip_tags($question->question_text)) > 100)
                                                    <a href="#" onclick="viewFullQuestion({{ $question->id }})" class="text-info">Lihat selengkapnya</a>
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $question->formatted_type }}</p>
                                        </td>
                                        <td>
                                            <x-ui.badge variant="{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($question->difficulty) }}
                                            </x-ui.badge>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $question->createdBy->name }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($material)
                                                <x-ui.button variant="info" size="sm" href="{{ route('admin.materials.questions.edit', ['material' => $material, 'question' => $question]) }}">Edit</x-ui.button>
                                                <form action="{{ route('admin.materials.questions.destroy', ['material' => $material, 'question' => $question]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button type="submit" variant="danger" size="sm" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">Hapus</x-ui.button>
                                                </form>
                                            @else
                                                <x-ui.button variant="info" size="sm" href="{{ route('admin.questions.edit', $question) }}">Edit</x-ui.button>
                                                <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button type="submit" variant="danger" size="sm" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">Hapus</x-ui.button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    <!-- Answers Section -->
                                    <tr>
                                        <td colspan="6" class="border-bottom">
                                            <div class="ms-4 mb-3">
                                                <strong class="text-xs">Jawaban:</strong>
                                                <ul class="list-unstyled ms-3 mb-0">
                                                    @foreach($question->answers as $answer)
                                                    <li class="text-xs {{ $answer->is_correct ? 'text-success' : '' }}">
                                                        <strong>{{ $answer->answer_text }}</strong>
                                                        @if($answer->is_correct)
                                                            <x-ui.badge variant="success" size="sm">Benar</x-ui.badge>
                                                        @endif
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </x-ui.table>
                        </div>
                        <div class="px-3 pb-3">
                            {{ $questions->links() }}
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>

        <!-- Modal for displaying full question -->
        <div class="modal fade" id="fullQuestionModal" tabindex="-1" aria-labelledby="fullQuestionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fullQuestionModalLabel">Detail Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="fullQuestionContent">
                        <!-- Question content will be loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('js')
    <script>
        // Store questions data for use in JavaScript
        const questionsData = [
            @foreach($questions as $q)
                {
                    id: {{ $q->id }},
                    text: {!! json_encode($q->question_text) !!}
                },
            @endforeach
        ];
    </script>
    <script src="{{ asset('js/admin/questions/index.js') }}"></script>
    @endpush
    <x-admin.tutorial />

</x-layouts.app>
