<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200" theme="admin">
    <x-navigation.sidebar activePage="adaptive-rules" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Buat Rule Baru" />
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <x-ui.card class="shadow-lg">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="d-flex justify-content-between align-items-center px-3">
                                    <h5 class="text-white mb-0">🎯 Buat Rule Baru</h5>
                                    <x-ui.button variant="light" size="sm" href="{{ route('admin.adaptive-rules.index') }}" icon="arrow_back">
                                        Kembali
                                    </x-ui.button>
                                </div>
                            </div>
                        </x-slot:header>
                        
                        <x-ui.alert type="danger" :message="session('error')" />
                            
                        <form action="{{ route('admin.adaptive-rules.store') }}" method="POST" id="ruleForm">
                            @csrf
                            <!-- Info Box -->
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading">
                                    <i class="material-icons text-sm">info</i> Cara Kerja Adaptive Rules
                                </h6>
                                <p class="mb-2"><strong>Format:</strong> <code>IF [KONDISI] THEN [AKSI]</code></p>
                                <hr>
                                <p class="mb-1"><strong>Contoh 1:</strong> Naikkan Level</p>
                                <small class="text-muted">
                                    IF <code>accuracy >= 80</code> THEN <code>SET current_level = "medium"</code>
                                </small>
                                <p class="mb-1 mt-2"><strong>Contoh 2:</strong> Berikan Hint</p>
                                <small class="text-muted">
                                    IF <code>wrong_streak >= 3</code> THEN <code>INCREMENT hints_available BY 1</code>
                                </small>
                                <x-ui.button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" />
                            </div>

                            <x-forms.form-group label="📝 Nama Rule" name="name" required class="mb-4">
                                <x-ui.input name="name" class="form-control-lg" placeholder="Contoh: Naikkan Kesulitan untuk Mahasiswa Pintar" required />
                            </x-forms.form-group>

                            <x-forms.form-group label="Deskripsi" name="description" class="mb-3">
                                <x-ui.input type="textarea" name="description" rows="2" placeholder="Jelaskan tujuan rule ini..." />
                            </x-forms.form-group>

                            <x-forms.form-group label="Materi (Opsional)" name="material_id" class="mb-4">
                                <select name="material_id" class="form-control">
                                    <option value="">Semua Materi</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}">{{ $material->title }}</option>
                                    @endforeach
                                </select>
                            </x-forms.form-group>

                            <!-- Visual Query Builder UI -->
                            <link rel="stylesheet" href="{{ asset('css/components/query-builder.css') }}">

                            <!-- Unified Logic Tree -->
                            <div class="adaptive-logic-tree">
                                <!-- Logic Branch: IF -->
                                <div class="logic-branch if-branch">
                                    <div class="branch-header">
                                        <span class="badge bg-gradient-info branch-label">IF</span>
                                        <div class="d-flex align-items-center ms-2">
                                            <span class="text-xs text-muted me-3">Kondisi yang harus terpenuhi</span>
                                            <x-ui.button type="button" size="xs" variant="outline" class="mb-0" data-bs-toggle="modal" data-bs-target="#addAttributeModal" icon="add">
                                                Atribut Baru
                                            </x-ui.button>
                                        </div>
                                    </div>
                                    <div class="branch-content">
                                        <div class="query-builder">
                                            <div class="query-group">
                                                <div class="group-operator">AND</div>
                                                
                                                <div id="conditionsContainer">
                                                    <!-- Initial Rule -->
                                                    <div class="query-rule" id="condition_0">
                                                        <div class="rule-input-group">
                                                            <div style="flex: 2;">
                                                                <select name="conditions[0][key]" class="rule-select w-100 attribute-select" required>
                                                                    <option value="">Select Attribute</option>
                                                                    
                                                                    @if($regularAttributes->count() > 0)
                                                                    <optgroup label="📊 Data Mahasiswa (Regular)">
                                                                        @foreach($regularAttributes as $attr)
                                                                            <option value="{{ $attr->key }}" data-type="regular">
                                                                                {{ $attr->label }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                    @endif
                                                                    
                                                                    @if($computedAttributes->count() > 0)
                                                                    <optgroup label="🧮 Nilai Terhitung (Computed)">
                                                                        @foreach($computedAttributes as $attr)
                                                                            <option value="{{ $attr->key }}" data-type="computed">
                                                                                {{ $attr->label }} ⚡
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            
                                                            <div style="flex: 1;">
                                                                <select name="conditions[0][operator]" class="rule-select w-100" required>
                                                                    <option value=">">greater than</option>
                                                                    <option value=">=">greater/equal</option>
                                                                    <option value="<">less than</option>
                                                                    <option value="<=">less/equal</option>
                                                                    <option value="==">equals</option>
                                                                    <option value="!=">not equals</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div style="flex: 1;">
                                                                <input type="text" name="conditions[0][value]" class="rule-input w-100" placeholder="Value" required>
                                                            </div>
                                                        </div>
                                                        
                                                        <x-ui.button type="button" variant="ghost" class="btn-delete-rule remove-condition" disabled icon="close" />
                                                    </div>
                                                </div>

                                                <div class="group-actions">
                                                    <x-ui.button type="button" class="btn-add-rule" id="addCondition" icon="add">
                                                         Add filter
                                                    </x-ui.button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logic Link -->
                                <div class="logic-link">
                                    <div class="link-line"></div>
                                </div>

                                <!-- Logic Branch: THEN -->
                                <div class="logic-branch then-branch">
                                    <div class="branch-header">
                                        <span class="badge bg-gradient-success branch-label">THEN</span>
                                        <span class="text-xs text-muted ms-2">Aksi yang akan dijalankan</span>
                                    </div>
                                    <div class="branch-content">
                                        <!-- Actions Query Builder UI -->
                                        <div class="query-builder">
                                            <div class="query-group then">
                                                <div class="group-operator">AND</div>
                                                
                                                <div id="actionsContainer">
                                                    <!-- Initial Action -->
                                                    <div class="query-rule" id="action_0">
                                                        <div class="rule-input-group">
                                                            <div style="flex: 2;">
                                                                <select name="actions[0][key]" class="rule-select w-100" required>
                                                                    <option value="">Select Attribute to Modify</option>
                                                                    @foreach($regularAttributes->merge($computedAttributes) as $attr)
                                                                        <option value="{{ $attr->key }}">{{ $attr->label }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            
                                                            <div style="flex: 1;">
                                                                <select name="actions[0][operator]" class="rule-select w-100" required>
                                                                    <option value="+">Tambah (+)</option>
                                                                    <option value="-">Kurang (-)</option>
                                                                    <option value="*">Kali (*)</option>
                                                                    <option value="=">Set (=)</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div style="flex: 1;">
                                                                <input type="text" name="actions[0][value]" class="rule-input w-100" placeholder="Value (e.g. 10)" required>
                                                            </div>
                                                        </div>
                                                        
                                                        <input type="hidden" name="actions[0][type]" value="update_attribute">
                                                        
                                                        <x-ui.button type="button" variant="ghost" class="btn-delete-rule remove-action" disabled icon="close" />
                                                    </div>
                                                </div>

                                                <div class="group-actions">
                                                    <x-ui.button type="button" class="btn-add-rule" id="addAction" icon="add">
                                                         Add action
                                                    </x-ui.button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-forms.form-group label="Prioritas" name="priority" required helpText="Semakin tinggi angka, semakin tinggi prioritas">
                                        <x-ui.input type="number" name="priority" value="10" min="0" required />
                                    </x-forms.form-group>
                                </div>
                                <div class="col-md-6">
                                    <div class="mt-4">
                                        <x-forms.checkbox name="is_active" label="Aktifkan Rule" checked />
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <x-ui.button variant="outline" size="lg" href="{{ route('admin.adaptive-rules.index') }}">
                                    Batal
                                </x-ui.button>
                                <x-ui.button type="submit" variant="primary" size="lg" icon="save">
                                    Simpan Rule
                                </x-ui.button>
                            </div>
                        </form>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>

    {{-- Modal untuk Tambah Atribut Baru --}}
    <div class="modal fade" id="addAttributeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-info">
                    <h5 class="modal-title text-white">Tambah Atribut Baru</h5>
                    <x-ui.button variant="link" data-bs-dismiss="modal" icon="close" />
                </div>
                <form id="addAttributeForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Key (Nama Teknis) <span class="text-danger">*</span></label>
                            <input type="text" name="key" class="form-control" placeholder="Contoh: health, mana, karma" required>
                            <small class="text-muted">Gunakan huruf kecil, tanpa spasi</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Label (Nama Tampilan) <span class="text-danger">*</span></label>
                            <input type="text" name="label" class="form-control" placeholder="Contoh: Health Points" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe Data <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="integer">Integer (Angka Bulat)</option>
                                <option value="float">Float (Angka Desimal)</option>
                                <option value="string">String (Teks)</option>
                                <option value="boolean">Boolean (True/False)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nilai Default <span class="text-danger">*</span></label>
                            <input type="text" name="default_value" class="form-control" placeholder="Contoh: 0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category" class="form-control">
                                <option value="progression">Progression (XP, Level)</option>
                                <option value="gameplay">Gameplay (Streak, Attempts)</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Jelaskan fungsi atribut ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <x-ui.button type="button" variant="secondary" data-bs-dismiss="modal">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="info">Simpan Atribut</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing adaptive rules form...');
        
        // Pre-load attributes data
        const attributesData = @json($regularAttributes->merge($computedAttributes)->map(function($attr) {
            return ['key' => $attr->key, 'label' => $attr->label];
        }));
        
        console.log('Attributes loaded:', attributesData);
        
        function buildAttributeOptions() {
            let options = '<option value="">Pilih Atribut</option>';
            attributesData.forEach(attr => {
                options += `<option value="${attr.key}">${attr.label} (${attr.key})</option>`;
            });
            return options;
        }
        
        let conditionIndex = 1;
        
        // Check if elements exist
        const addConditionBtn = document.getElementById('addCondition');
        const addActionBtn = document.getElementById('addAction');
        const conditionsContainer = document.getElementById('conditionsContainer');
        const actionsContainer = document.getElementById('actionsContainer');
        
        console.log('Elements found:', {
            addConditionBtn: !!addConditionBtn,
            addActionBtn: !!addActionBtn,
            conditionsContainer: !!conditionsContainer,
            actionsContainer: !!actionsContainer
        });
        
        // Handle attribute creation
        const addAttributeForm = document.getElementById('addAttributeForm');
        if (addAttributeForm) {
            addAttributeForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                console.log('Submitting new attribute...');
                const formData = new FormData(this);
                
                try {
                    const response = await fetch('/admin/attribute-definitions', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        console.log('Attribute created successfully');
                        location.reload();
                    } else {
                        console.error('Error creating attribute:', data);
                        alert('Error: ' + (data.message || 'Gagal menyimpan atribut'));
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    alert('Error: ' + error.message);
                }
            });
        }
        
        // Add new condition (Visual Query Builder)
        if (addConditionBtn) {
            addConditionBtn.addEventListener('click', function() {
                console.log('Add condition clicked, index:', conditionIndex);
                const container = document.getElementById('conditionsContainer');
                
                // Get options from first select to clone
                const firstSelect = document.querySelector('select[name="conditions[0][key]"]');
                const optionsHtml = firstSelect ? firstSelect.innerHTML : buildAttributeOptions();
                
                const newCondition = `
                    <div class="query-rule" id="condition_${conditionIndex}">
                        <div class="rule-input-group">
                            <div style="flex: 2;">
                                <select name="conditions[${conditionIndex}][key]" class="rule-select w-100 attribute-select" required>
                                    ${optionsHtml}
                                </select>
                            </div>
                            
                            <div style="flex: 1;">
                                <select name="conditions[${conditionIndex}][operator]" class="rule-select w-100" required>
                                    <option value=">">greater than</option>
                                    <option value=">=">greater/equal</option>
                                    <option value="<">less than</option>
                                    <option value="<=">less/equal</option>
                                    <option value="==">equals</option>
                                    <option value="!=">not equals</option>
                                </select>
                            </div>
                            
                            <div style="flex: 1;">
                                <input type="text" name="conditions[${conditionIndex}][value]" class="rule-input w-100" placeholder="Value" required>
                            </div>
                        </div>
                        
                        <x-ui.button type="button" variant="ghost" class="btn-delete-rule remove-condition" icon="close" />
                    </div>
                `;
                
                container.insertAdjacentHTML('beforeend', newCondition);
                conditionIndex++;
                updateRemoveButtons();
            });
        } else {
            console.error('addCondition button not found!');
        }
        
        // Remove condition
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-condition')) {
                const ruleRow = e.target.closest('.query-rule');
                if (document.querySelectorAll('.query-rule').length > 1) {
                    ruleRow.remove();
                    updateRemoveButtons();
                }
            }
        });
        
        function updateRemoveButtons() {
            const rules = document.querySelectorAll('.query-rule');
            rules.forEach(rule => {
                const btn = rule.querySelector('.remove-condition');
                if(btn) btn.disabled = rules.length === 1;
            });
        }
        
        // Actions Builder (Visual Query Builder)
        let actionIndex = 1;
        
        if (addActionBtn) {
            addActionBtn.addEventListener('click', function() {
                console.log('Add action clicked, index:', actionIndex);
                const container = document.getElementById('actionsContainer');
                
                // Get options from first select
                const firstSelect = document.querySelector('select[name="actions[0][key]"]');
                const optionsHtml = firstSelect ? firstSelect.innerHTML : '';

                const newAction = `
                    <div class="query-rule" id="action_${actionIndex}">
                        <div class="rule-input-group">
                            <div style="flex: 2;">
                                <select name="actions[${actionIndex}][key]" class="rule-select w-100" required>
                                    ${optionsHtml}
                                </select>
                            </div>
                            
                            <div style="flex: 1;">
                                <select name="actions[${actionIndex}][operator]" class="rule-select w-100" required>
                                    <option value="+">Tambah (+)</option>
                                    <option value="-">Kurang (-)</option>
                                    <option value="*">Kali (*)</option>
                                    <option value="=">Set (=)</option>
                                </select>
                            </div>
                            
                            <div style="flex: 1;">
                                <input type="text" name="actions[${actionIndex}][value]" class="rule-input w-100" placeholder="Value (e.g. 10)" required>
                            </div>
                        </div>
                        
                        <input type="hidden" name="actions[${actionIndex}][type]" value="update_attribute">
                        
                        <x-ui.button type="button" variant="ghost" class="btn-delete-rule remove-action" icon="close" />
                    </div>
                `;
                
                container.insertAdjacentHTML('beforeend', newAction);
                actionIndex++;
                updateActionRemoveButtons();
            });
        }
        
        // Remove action
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-action')) {
                const ruleRow = e.target.closest('.query-rule');
                if (document.querySelectorAll('#actionsContainer .query-rule').length > 1) {
                    ruleRow.remove();
                    updateActionRemoveButtons();
                }
            }
        });
        
        function updateActionRemoveButtons() {
            const actions = document.querySelectorAll('#actionsContainer .query-rule');
            actions.forEach(action => {
                const btn = action.querySelector('.remove-action');
                if(btn) btn.disabled = actions.length === 1;
            });
        }
        
        console.log('Adaptive rules form initialization complete');
    });
    </script>
    @endpush
</x-layouts.app>
