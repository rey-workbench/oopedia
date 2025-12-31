<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="adaptive-rules" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Edit Rule" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Edit Adaptive Rule</h6>
                                <a href="{{ route('admin.adaptive-rules.index') }}" class="btn btn-sm btn-light me-3">
                                    <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Terjadi kesalahan:</strong>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <form action="{{ route('admin.adaptive-rules.update', $adaptiveRule) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama Rule <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-outline focused is-focused">
                                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $adaptiveRule->name) }}" required>
                                            </div>
                                            @error('name')
                                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="material_id" class="form-label">Materi (Opsional)</label>
                                            <div class="input-group input-group-outline focused is-focused">
                                                <select name="material_id" id="material_id" class="form-control">
                                                    <option value="">Semua Materi</option>
                                                    @foreach($materials as $material)
                                                        <option value="{{ $material->id }}" {{ old('material_id', $adaptiveRule->material_id) == $material->id ? 'selected' : '' }}>
                                                            {{ $material->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('material_id')
                                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <div class="input-group input-group-outline focused is-focused">
                                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $adaptiveRule->description) }}</textarea>
                                    </div>
                                    @error('description')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">
                                <h6 class="mb-3">Kondisi (IF)</h6>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="condition_type" class="form-label">Tipe Kondisi <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-outline focused is-focused">
                                                <select name="condition_type" id="condition_type" class="form-control" required>
                                                    <option value="">Pilih Tipe</option>
                                                    @foreach(\App\Models\AdaptiveRule::CONDITION_TYPES as $key => $label)
                                                        <option value="{{ $key }}" {{ old('condition_type', $adaptiveRule->condition_type) == $key ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('condition_type')
                                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="condition_operator" class="form-label">Operator <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-outline focused is-focused">
                                                <select name="condition_operator" id="condition_operator" class="form-control" required>
                                                    <option value="">Pilih Operator</option>
                                                    @foreach(\App\Models\AdaptiveRule::OPERATORS as $key => $label)
                                                        <option value="{{ $key }}" {{ old('condition_operator', $adaptiveRule->condition_operator) == $key ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('condition_operator')
                                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="condition_value" class="form-label">Nilai <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-outline focused is-focused">
                                                <input type="text" name="condition_value" id="condition_value" class="form-control" 
                                                    value="{{ old('condition_value', $adaptiveRule->condition_value) }}" placeholder="Contoh: 70 atau 60-80" required>
                                            </div>
                                            <small class="text-muted">Untuk operator "Antara", gunakan format: min-max (contoh: 60-80)</small>
                                            @error('condition_value')
                                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h6 class="mb-3">Aksi (THEN)</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="action_type" class="form-label">Tipe Aksi <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-outline focused is-focused">
                                                <select name="action_type" id="action_type" class="form-control" required>
                                                    <option value="">Pilih Tipe Aksi</option>
                                                    @foreach(\App\Models\AdaptiveRule::ACTION_TYPES as $key => $label)
                                                        <option value="{{ $key }}" {{ old('action_type', $adaptiveRule->action_type) == $key ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('action_type')
                                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="action_value" class="form-label">Nilai Aksi <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-outline focused is-focused" id="action_value_container">
                                                @if(old('action_type', $adaptiveRule->action_type) === 'change_difficulty')
                                                    <select name="action_value" id="action_value" class="form-control" required>
                                                        <option value="">Pilih Tingkat Kesulitan</option>
                                                        <option value="beginner" {{ old('action_value', $adaptiveRule->action_value) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                        <option value="medium" {{ old('action_value', $adaptiveRule->action_value) === 'medium' ? 'selected' : '' }}>Medium</option>
                                                        <option value="hard" {{ old('action_value', $adaptiveRule->action_value) === 'hard' ? 'selected' : '' }}>Hard</option>
                                                    </select>
                                                @else
                                                    <input type="text" name="action_value" id="action_value" class="form-control" 
                                                        value="{{ old('action_value', $adaptiveRule->action_value) }}" placeholder="Masukkan nilai aksi" required>
                                                @endif
                                            </div>
                                            <small class="text-muted" id="action_value_hint">Nilai tergantung tipe aksi yang dipilih</small>
                                            @error('action_value')
                                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h6 class="mb-3">Pengaturan Lainnya</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="priority" class="form-label">Prioritas <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-outline focused is-focused">
                                                <input type="number" name="priority" id="priority" class="form-control" 
                                                    value="{{ old('priority', $adaptiveRule->priority) }}" min="0" required>
                                            </div>
                                            <small class="text-muted">Semakin tinggi angka, semakin tinggi prioritas</small>
                                            @error('priority')
                                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                                    {{ old('is_active', $adaptiveRule->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Aktifkan Rule
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('admin.adaptive-rules.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                                    <button type="submit" class="btn btn-primary">Perbarui Rule</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />
</x-layout>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionTypeSelect = document.getElementById('action_type');
    const actionValueContainer = document.getElementById('action_value_container');
    const actionValueHint = document.getElementById('action_value_hint');
    const currentActionValue = '{{ old('action_value', $adaptiveRule->action_value) }}';
    
    actionTypeSelect.addEventListener('change', function() {
        const actionType = this.value;
        let html = '';
        let hint = '';
        
        if (actionType === 'change_difficulty') {
            html = `<select name="action_value" id="action_value" class="form-control" required>
                        <option value="">Pilih Tingkat Kesulitan</option>
                        <option value="beginner" ${currentActionValue === 'beginner' ? 'selected' : ''}>Beginner</option>
                        <option value="medium" ${currentActionValue === 'medium' ? 'selected' : ''}>Medium</option>
                        <option value="hard" ${currentActionValue === 'hard' ? 'selected' : ''}>Hard</option>
                    </select>`;
            hint = 'Pilih tingkat kesulitan yang akan diterapkan';
        } else if (actionType === 'skip_questions') {
            html = `<input type="number" name="action_value" id="action_value" class="form-control" 
                        value="${currentActionValue}" placeholder="Jumlah soal" min="1" required>`;
            hint = 'Masukkan jumlah soal yang akan dilewati';
        } else if (actionType === 'recommend_material') {
            html = `<input type="text" name="action_value" id="action_value" class="form-control" 
                        value="${currentActionValue}" placeholder="ID atau nama materi" required>`;
            hint = 'Masukkan ID atau nama materi yang akan direkomendasikan';
        } else {
            html = `<input type="text" name="action_value" id="action_value" class="form-control" 
                        value="${currentActionValue}" placeholder="Masukkan nilai aksi" required>`;
            hint = 'Nilai tergantung tipe aksi yang dipilih';
        }
        
        actionValueContainer.innerHTML = html;
        actionValueHint.textContent = hint;
    });
});
</script>
@endpush
