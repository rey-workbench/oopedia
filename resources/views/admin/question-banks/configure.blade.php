<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="question-banks" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Konfigurasi Bank Soal" />
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
                                <h6 class="text-white text-capitalize ps-3 mb-0">Konfigurasi Bank Soal: {{ $questionBank->name }}</h6>
                                <x-ui.button variant="light" size="sm" href="{{ route('admin.question-banks.show', $questionBank) }}" icon="arrow_back" class="me-3">
                                    Kembali
                                </x-ui.button>
                            </div>
                        </x-slot:header>
                        
                        <div class="card-body px-4 pt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Form untuk menambah atau mengedit konfigurasi -->
                                    <x-ui.card class="bg-white border shadow-none h-100">
                                        <div class="card-header bg-light border-bottom p-3">
                                            <h6 class="mb-0">{{ isset($editConfig) ? 'Edit Konfigurasi' : 'Tambah Konfigurasi Baru' }}</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <form action="{{ route('admin.question-banks.store-config', $questionBank) }}" method="POST">
                                                @csrf
                                                
                                                @if(isset($editConfig))
                                                    <input type="hidden" name="config_id" value="{{ $editConfig->id }}">
                                                @endif
                                                
                                                @php
                                                    $materialOptions = $materials->pluck('title', 'id')->toArray();
                                                @endphp
                                                
                                                <div class="mb-3">
                                                    <x-forms.form-group label="Materi" name="material_id">
                                                        @if(isset($editConfig))
                                                            <input type="text" class="form-control" value="{{ $editConfig->material->title }}" disabled>
                                                            <input type="hidden" name="material_id" value="{{ $editConfig->material_id }}">
                                                        @else
                                                            <x-forms.select name="material_id" :options="$materialOptions" selected="{{ old('material_id') }}" required placeholder="-- Pilih Materi --" />
                                                        @endif
                                                    </x-forms.form-group>
                                                </div>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <x-forms.form-group label="Soal Beginner" name="beginner_count" required>
                                                            <x-ui.input type="number" name="beginner_count" min="0" value="{{ old('beginner_count', isset($editConfig) ? $editConfig->beginner_count : 0) }}" required />
                                                        </x-forms.form-group>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-forms.form-group label="Soal Medium" name="medium_count" required>
                                                            <x-ui.input type="number" name="medium_count" min="0" value="{{ old('medium_count', isset($editConfig) ? $editConfig->medium_count : 0) }}" required />
                                                        </x-forms.form-group>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-forms.form-group label="Soal Hard" name="hard_count" required>
                                                            <x-ui.input type="number" name="hard_count" min="0" value="{{ old('hard_count', isset($editConfig) ? $editConfig->hard_count : 0) }}" required />
                                                        </x-forms.form-group>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <x-forms.checkbox name="is_active" label="Aktifkan Konfigurasi" checked="{{ (isset($editConfig) && $editConfig->is_active) || old('is_active') }}" />
                                                </div>
                                                
                                                <x-ui.alert type="info" id="totalQuestions">
                                                    Total: <span id="totalCount">{{ old('beginner_count', isset($editConfig) ? $editConfig->beginner_count : 0) + old('medium_count', isset($editConfig) ? $editConfig->medium_count : 0) + old('hard_count', isset($editConfig) ? $editConfig->hard_count : 0) }}</span> soal
                                                </x-ui.alert>
                                                
                                                <div class="mt-4">
                                                    <x-ui.button type="submit" variant="primary">
                                                        {{ isset($editConfig) ? 'Perbarui Konfigurasi' : 'Tambah Konfigurasi' }}
                                                    </x-ui.button>
                                                    <x-ui.button variant="outline" href="{{ route('admin.question-banks.configure', $questionBank) }}">Batal</x-ui.button>
                                                </div>
                                            </form>
                                        </div>
                                    </x-ui.card>
                                </div>
                                
                                <div class="col-md-6 mt-4 mt-md-0">
                                    <!-- Daftar konfigurasi yang sudah ada -->
                                    <x-ui.card class="bg-white border shadow-none h-100">
                                        <div class="card-header bg-light border-bottom p-3">
                                            <h6 class="mb-0">Konfigurasi yang Ada</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            @if($configs->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th class="ps-3 text-secondary text-xs font-weight-bolder opacity-7">Materi</th>
                                                                <th class="text-secondary text-xs font-weight-bolder opacity-7">B</th>
                                                                <th class="text-secondary text-xs font-weight-bolder opacity-7">M</th>
                                                                <th class="text-secondary text-xs font-weight-bolder opacity-7">H</th>
                                                                <th class="text-secondary text-xs font-weight-bolder opacity-7">Status</th>
                                                                <th class="text-secondary text-xs font-weight-bolder opacity-7 ps-2">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($configs as $config)
                                                                <tr>
                                                                    <td class="ps-3 text-sm">{{ $config->material ? $config->material->title : 'Tidak ada' }}</td>
                                                                    <td class="text-sm">{{ $config->beginner_count }}</td>
                                                                    <td class="text-sm">{{ $config->medium_count }}</td>
                                                                    <td class="text-sm">{{ $config->hard_count }}</td>
                                                                    <td>
                                                                        <x-ui.badge variant="{{ $config->is_active ? 'success' : 'danger' }}">
                                                                            {{ $config->is_active ? 'Aktif' : 'Nonaktif' }}
                                                                        </x-ui.badge>
                                                                    </td>
                                                                    <td>
                                                                        <x-ui.button variant="info" size="sm" href="{{ route('admin.question-banks.configure', ['questionBank' => $questionBank, 'edit' => $config->id]) }}" icon="edit" />
                                                                        <form action="{{ route('admin.question-bank-configs.delete', $config) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <x-ui.button type="submit" variant="danger" size="sm" icon="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus konfigurasi ini?')" />
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="p-3">
                                                    <x-ui.alert type="info" class="mb-0">
                                                        Belum ada konfigurasi untuk bank soal ini.
                                                    </x-ui.alert>
                                                </div>
                                            @endif
                                        </div>
                                    </x-ui.card>
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

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to calculate total questions
        function calculateTotal() {
            const beginnerCount = parseInt(document.getElementsByName('beginner_count')[0].value) || 0;
            const mediumCount = parseInt(document.getElementsByName('medium_count')[0].value) || 0;
            const hardCount = parseInt(document.getElementsByName('hard_count')[0].value) || 0;
            
            const total = beginnerCount + mediumCount + hardCount;
            document.getElementById('totalCount').textContent = total;
            
            // Visual feedback
            const totalEl = document.getElementById('totalQuestions');
            if (total <= 0) {
                totalEl.classList.remove('alert-info');
                totalEl.classList.add('alert-danger');
            } else {
                totalEl.classList.remove('alert-danger');
                totalEl.classList.add('alert-info');
            }
        }
        
        // Add event listeners to all count inputs
        const beginnerInput = document.getElementsByName('beginner_count')[0];
        const mediumInput = document.getElementsByName('medium_count')[0];
        const hardInput = document.getElementsByName('hard_count')[0];
        
        if (beginnerInput) beginnerInput.addEventListener('input', calculateTotal);
        if (mediumInput) mediumInput.addEventListener('input', calculateTotal);
        if (hardInput) hardInput.addEventListener('input', calculateTotal);
        
        // Calculate on page load
        calculateTotal();
    });
</script>
@endpush