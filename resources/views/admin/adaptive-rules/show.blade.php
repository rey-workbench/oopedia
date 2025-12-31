<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="adaptive-rules" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Detail Rule" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Detail Adaptive Rule</h6>
                                <div>
                                    <a href="{{ route('admin.adaptive-rules.edit', $adaptiveRule) }}" class="btn btn-sm btn-warning me-2">
                                        <i class="material-icons text-sm">edit</i>&nbsp;&nbsp;Edit
                                    </a>
                                    <a href="{{ route('admin.adaptive-rules.index') }}" class="btn btn-sm btn-light me-3">
                                        <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
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
                                                <span class="badge bg-info">{{ $adaptiveRule->priority }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-weight-bold">Status:</td>
                                            <td class="text-sm">
                                                <span class="badge bg-{{ $adaptiveRule->is_active ? 'success' : 'secondary' }}">
                                                    {{ $adaptiveRule->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
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
                                                            <span class="badge bg-{{ $adaptiveRule->action_value === 'hard' ? 'danger' : ($adaptiveRule->action_value === 'medium' ? 'warning' : 'success') }}">
                                                                {{ ucfirst($adaptiveRule->action_value) }}
                                                            </span>
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
                                    <div class="alert alert-info">
                                        <p class="mb-0 text-sm">
                                            <strong>IF</strong> {{ \App\Models\AdaptiveRule::CONDITION_TYPES[$adaptiveRule->condition_type] ?? $adaptiveRule->condition_type }} 
                                            {{ \App\Models\AdaptiveRule::OPERATORS[$adaptiveRule->condition_operator] ?? $adaptiveRule->condition_operator }} 
                                            {{ $adaptiveRule->condition_value }}
                                            <br>
                                            <strong>THEN</strong> {{ \App\Models\AdaptiveRule::ACTION_TYPES[$adaptiveRule->action_type] ?? $adaptiveRule->action_type }}: 
                                            {{ $adaptiveRule->action_value }}
                                        </p>
                                    </div>
                                    
                                    <div class="alert alert-secondary">
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
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <form action="{{ route('admin.adaptive-rules.destroy', $adaptiveRule) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus rule ini?')">
                                        <i class="material-icons text-sm">delete</i>&nbsp;&nbsp;Hapus Rule
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />
</x-layout>
