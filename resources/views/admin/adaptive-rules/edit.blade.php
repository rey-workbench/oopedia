<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="adaptive-rules" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Edit Rule" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Edit Adaptive Rule</h6>
                                <x-ui.button variant="light" size="sm" href="{{ route('admin.adaptive-rules.index') }}" icon="arrow_back" class="me-3">
                                    Kembali
                                </x-ui.button>
                            </div>
                        </x-slot:header>
                        
                        <x-ui.alert type="danger" :message="session('error')" />
                        
                        @if($errors->any())
                            <x-ui.alert type="danger">
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </x-ui.alert>
                        @endif
                            
                        <form action="{{ route('admin.adaptive-rules.update', $adaptiveRule) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <x-forms.form-group label="Nama Rule" name="name" required>
                                        <x-ui.input name="name" :value="$adaptiveRule->name" required />
                                    </x-forms.form-group>
                                </div>
                                
                                <div class="col-md-6">
                                    <x-forms.form-group label="Materi (Opsional)" name="material_id">
                                        <x-forms.select 
                                            name="material_id" 
                                            :options="$materials->pluck('title', 'id')" 
                                            :selected="$adaptiveRule->material_id"
                                            placeholder="Semua Materi"
                                        />
                                    </x-forms.form-group>
                                </div>
                            </div>

                            <x-forms.form-group label="Deskripsi" name="description" class="mb-3">
                                <x-ui.input type="textarea" name="description" :value="$adaptiveRule->description" rows="3" />
                            </x-forms.form-group>

                            <hr class="my-4">
                            <h6 class="mb-3">Kondisi (IF)</h6>

                            <div class="row">
                                <div class="col-md-4">
                                    <x-forms.form-group label="Tipe Kondisi" name="condition_type" required>
                                        <x-forms.select 
                                            name="condition_type" 
                                            :options="\App\Models\AdaptiveRule::CONDITION_TYPES" 
                                            :selected="$adaptiveRule->condition_type"
                                            placeholder="Pilih Tipe"
                                            required
                                        />
                                    </x-forms.form-group>
                                </div>

                                <div class="col-md-4">
                                    <x-forms.form-group label="Operator" name="condition_operator" required>
                                        <x-forms.select 
                                            name="condition_operator" 
                                            :options="\App\Models\AdaptiveRule::OPERATORS" 
                                            :selected="$adaptiveRule->condition_operator"
                                            placeholder="Pilih Operator"
                                            required
                                        />
                                    </x-forms.form-group>
                                </div>

                                <div class="col-md-4">
                                    <x-forms.form-group label="Nilai" name="condition_value" required helpText="Untuk operator 'Antara', gunakan format: min-max (contoh: 60-80)">
                                        <x-ui.input name="condition_value" :value="$adaptiveRule->condition_value" placeholder="Contoh: 70 atau 60-80" required />
                                    </x-forms.form-group>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3">Aksi (THEN)</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <x-forms.form-group label="Tipe Aksi" name="action_type" required>
                                        <x-forms.select 
                                            name="action_type" 
                                            :options="\App\Models\AdaptiveRule::ACTION_TYPES" 
                                            :selected="$adaptiveRule->action_type"
                                            placeholder="Pilih Tipe Aksi"
                                            required
                                        />
                                    </x-forms.form-group>
                                </div>

                                <div class="col-md-6">
                                    <x-forms.form-group label="Nilai Aksi" name="action_value" required>
                                        <div id="action_value_container">
                                            @if(old('action_type', $adaptiveRule->action_type) === 'change_difficulty')
                                                <select name="action_value" id="action_value" class="form-control" required>
                                                    <option value="">Pilih Tingkat Kesulitan</option>
                                                    <option value="beginner" {{ old('action_value', $adaptiveRule->action_value) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                    <option value="medium" {{ old('action_value', $adaptiveRule->action_value) === 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="hard" {{ old('action_value', $adaptiveRule->action_value) === 'hard' ? 'selected' : '' }}>Hard</option>
                                                </select>
                                            @else
                                                <x-ui.input name="action_value" :value="$adaptiveRule->action_value" placeholder="Masukkan nilai aksi" required />
                                            @endif
                                        </div>
                                        <small class="text-muted" id="action_value_hint">Nilai tergantung tipe aksi yang dipilih</small>
                                    </x-forms.form-group>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3">Pengaturan Lainnya</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <x-forms.form-group label="Prioritas" name="priority" required helpText="Semakin tinggi angka, semakin tinggi prioritas">
                                        <x-ui.input type="number" name="priority" :value="$adaptiveRule->priority" min="0" required />
                                    </x-forms.form-group>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="mt-4">
                                            <x-forms.checkbox 
                                                name="is_active" 
                                                label="Aktifkan Rule" 
                                                :checked="$adaptiveRule->is_active" 
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <x-ui.button variant="outline" class="me-2" href="{{ route('admin.adaptive-rules.index') }}">Batal</x-ui.button>
                                <x-ui.button type="submit" variant="primary">Perbarui Rule</x-ui.button>
                            </div>
                        </form>
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
