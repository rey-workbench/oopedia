<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="adaptive-rules" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Detail Rule" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Detail Adaptive Rule</h6>
                                <div>
                                    <x-ui.button variant="warning" size="sm" href="{{ route('admin.adaptive-rules.edit', $adaptiveRule) }}" class="me-2" icon="edit">
                                        Edit
                                    </x-ui.button>
                                    <x-ui.button variant="light" size="sm" href="{{ route('admin.adaptive-rules.index') }}" class="me-3" icon="arrow_back">
                                        Kembali
                                    </x-ui.button>
                                </div>
                            </div>
                        </x-slot:header>
                        
                        <div class="card-body pt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Informasi Umum</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%" class="text-sm font-weight-bold">Nama Rule:</td>
                                            <td class="text-sm">{{ $adaptiveRule->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-weight-bold">Deskripsi:</td>
                                            <td class="text-sm">{{ $adaptiveRule->description ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-weight-bold">Materi:</td>
                                            <td class="text-sm">{{ $adaptiveRule->material ? $adaptiveRule->material->title : 'Semua Materi' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-weight-bold">Prioritas:</td>
                                            <td class="text-sm">
                                                <x-ui.badge variant="info">{{ $adaptiveRule->priority }}</x-ui.badge>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-weight-bold">Status:</td>
                                            <td class="text-sm">
                                                <x-ui.badge :variant="$adaptiveRule->is_active ? 'success' : 'secondary'">
                                                    {{ $adaptiveRule->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </x-ui.badge>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-weight-bold">Dibuat Oleh:</td>
                                            <td class="text-sm">{{ $adaptiveRule->creator->name ?? 'Unknown' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-weight-bold">Tanggal Dibuat:</td>
                                            <td class="text-sm">{{ $adaptiveRule->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="mb-3">Rule Logic (Forward Chaining)</h6>
                                    
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-primary mb-2">
                                                <i class="material-icons text-sm">arrow_forward</i> IF (Kondisi)
                                            </h6>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td width="40%" class="text-xs font-weight-bold">Tipe:</td>
                                                    <td class="text-xs">{{ \App\Models\AdaptiveRule::CONDITION_TYPES[$adaptiveRule->condition_type] ?? $adaptiveRule->condition_type }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-xs font-weight-bold">Operator:</td>
                                                    <td class="text-xs">{{ \App\Models\AdaptiveRule::OPERATORS[$adaptiveRule->condition_operator] ?? $adaptiveRule->condition_operator }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-xs font-weight-bold">Nilai:</td>
                                                    <td class="text-xs">{{ $adaptiveRule->condition_value }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-success mb-2">
                                                <i class="material-icons text-sm">check_circle</i> THEN (Aksi)
                                            </h6>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td width="40%" class="text-xs font-weight-bold">Tipe Aksi:</td>
                                                    <td class="text-xs">{{ \App\Models\AdaptiveRule::ACTION_TYPES[$adaptiveRule->action_type] ?? $adaptiveRule->action_type }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-xs font-weight-bold">Nilai Aksi:</td>
                                                    <td class="text-xs">
                                                        @if($adaptiveRule->action_type === 'change_difficulty')
                                                            @php
                                                                $difficultyVariant = match($adaptiveRule->action_value) {
                                                                    'hard' => 'danger',
                                                                    'medium' => 'warning',
                                                                    default => 'success'
                                                                };
                                                            @endphp
                                                            <x-ui.badge :variant="$difficultyVariant">
                                                                {{ ucfirst($adaptiveRule->action_value) }}
                                                            </x-ui.badge>
                                                        @else
                                                            {{ $adaptiveRule->action_value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mb-3">Contoh Penerapan Rule</h6>
                                    <x-ui.alert type="info">
                                        <p class="mb-0 text-sm">
                                            <strong>IF</strong> {{ \App\Models\AdaptiveRule::CONDITION_TYPES[$adaptiveRule->condition_type] ?? $adaptiveRule->condition_type }} 
                                            {{ \App\Models\AdaptiveRule::OPERATORS[$adaptiveRule->condition_operator] ?? $adaptiveRule->condition_operator }} 
                                            {{ $adaptiveRule->condition_value }}
                                            <br>
                                            <strong>THEN</strong> {{ \App\Models\AdaptiveRule::ACTION_TYPES[$adaptiveRule->action_type] ?? $adaptiveRule->action_type }}: 
                                            {{ $adaptiveRule->action_value }}
                                        </p>
                                    </x-ui.alert>
                                    
                                    <x-ui.alert type="secondary" class="text-white">
                                        <p class="mb-0 text-sm">
                                            <strong>Contoh Kasus:</strong><br>
                                            @if($adaptiveRule->condition_type === 'score_range' && $adaptiveRule->action_type === 'change_difficulty')
                                                Jika mahasiswa mendapat skor {{ $adaptiveRule->condition_operator }} {{ $adaptiveRule->condition_value }}%, 
                                                maka tingkat kesulitan soal akan diubah menjadi <strong>{{ ucfirst($adaptiveRule->action_value) }}</strong>.
                                            @elseif($adaptiveRule->condition_type === 'consecutive_correct' && $adaptiveRule->action_type === 'skip_questions')
                                                Jika mahasiswa menjawab benar {{ $adaptiveRule->condition_value }} soal berturut-turut, 
                                                maka sistem akan melewati {{ $adaptiveRule->action_value }} soal berikutnya.
                                            @elseif($adaptiveRule->condition_type === 'consecutive_wrong' && $adaptiveRule->action_type === 'show_hint')
                                                Jika mahasiswa menjawab salah {{ $adaptiveRule->condition_value }} soal berturut-turut, 
                                                maka sistem akan menampilkan petunjuk.
                                            @else
                                                Rule ini akan mengevaluasi kondisi dan menjalankan aksi sesuai konfigurasi yang telah ditentukan.
                                            @endif
                                        </p>
                                    </x-ui.alert>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <form action="{{ route('admin.adaptive-rules.destroy', $adaptiveRule) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="danger" onclick="return confirm('Apakah Anda yakin ingin menghapus rule ini?')" icon="delete">
                                        Hapus Rule
                                    </x-ui.button>
                                </form>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />
</x-layouts.app>
