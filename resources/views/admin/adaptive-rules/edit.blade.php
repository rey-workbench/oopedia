<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="adaptive-rules" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Edit Rule" />
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
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
                            
                            <x-forms.form-group label="Nama Rule" name="name" required class="mb-3">
                                <x-ui.input name="name" :value="$adaptiveRule->name" required />
                            </x-forms.form-group>

                            <x-forms.form-group label="Deskripsi" name="description" class="mb-3">
                                <x-ui.input type="textarea" name="description" :value="$adaptiveRule->description" rows="2" />
                            </x-forms.form-group>

                            <x-forms.form-group label="Materi (Opsional)" name="material_id" class="mb-4">
                                <select name="material_id" class="form-control">
                                    <option value="">Semua Materi</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}" {{ $adaptiveRule->material_id == $material->id ? 'selected' : '' }}>
                                            {{ $material->title }}
                                        </option>
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
                                                    @php
                                                        $existingConditions = !empty($adaptiveRule->conditions) && is_array($adaptiveRule->conditions) 
                                                            ? $adaptiveRule->conditions 
                                                            : [['key' => '', 'operator' => '', 'value' => '']];
                                                    @endphp
                    
                                                    @foreach($existingConditions as $index => $condition)
                                                        <div class="query-rule" id="condition_{{ $index }}">
                                                            <div class="rule-input-group">
                                                                <div style="flex: 2;">
                                                                    <select name="conditions[{{ $index }}][key]" class="rule-select w-100 attribute-select" required>
                                                                        <option value="">Select Attribute</option>
                                                                        
                                                                        @if($regularAttributes->count() > 0)
                                                                        <optgroup label="📊 Data Mahasiswa (Regular)">
                                                                            @foreach($regularAttributes as $attr)
                                                                                <option value="{{ $attr->key }}" data-type="regular" {{ ($condition['key'] ?? $condition['type'] ?? '') == $attr->key ? 'selected' : '' }}>
                                                                                    {{ $attr->label }}
                                                                                </option>
                                                                            @endforeach
                                                                        </optgroup>
                                                                        @endif
                                                                        
                                                                        @if($computedAttributes->count() > 0)
                                                                        <optgroup label="🧮 Nilai Terhitung (Computed)">
                                                                            @foreach($computedAttributes as $attr)
                                                                                <option value="{{ $attr->key }}" data-type="computed" {{ ($condition['key'] ?? $condition['type'] ?? '') == $attr->key ? 'selected' : '' }}>
                                                                                    {{ $attr->label }} ⚡
                                                                                </option>
                                                                            @endforeach
                                                                        </optgroup>
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                
                                                                <div style="flex: 1;">
                                                                    <select name="conditions[{{ $index }}][operator]" class="rule-select w-100" required>
                                                                        <option value=">">greater than</option>
                                                                        <option value=">=" {{ ($condition['operator'] ?? '') == '>=' ? 'selected' : '' }}>greater/equal</option>
                                                                        <option value="<" {{ ($condition['operator'] ?? '') == '<' ? 'selected' : '' }}>less than</option>
                                                                        <option value="<=" {{ ($condition['operator'] ?? '') == '<=' ? 'selected' : '' }}>less/equal</option>
                                                                        <option value="==" {{ ($condition['operator'] ?? '') == '==' ? 'selected' : '' }}>equals</option>
                                                                        <option value="!=" {{ ($condition['operator'] ?? '') == '!=' ? 'selected' : '' }}>not equals</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div style="flex: 1;">
                                                                    <input type="text" name="conditions[{{ $index }}][value]" class="rule-input w-100" value="{{ $condition['value'] ?? '' }}" placeholder="Value" required>
                                                                </div>
                                                            </div>
                                                            
                                                            <x-ui.button type="button" variant="ghost" class="btn-delete-rule remove-condition" {{ count($existingConditions) == 1 ? 'disabled' : '' }} icon="close" />
                                                        </div>
                                                    @endforeach
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
                                        <div class="query-builder">
                                            <div class="query-group then">
                                                <div class="group-operator">AND</div>
                                                
                                                <div id="actionsContainer">
                                                    @php
                                                        $existingActions = !empty($adaptiveRule->actions) && is_array($adaptiveRule->actions) 
                                                            ? $adaptiveRule->actions 
                                                            : [];
                                                        
                                                        if (empty($existingActions) && $adaptiveRule->action_type) {
                                                            if ($adaptiveRule->action_type == 'update_attribute') {
                                                                 $existingActions = [['key' => '', 'operator' => '=', 'value' => '']];
                                                            } else {
                                                                $existingActions = [['key' => '', 'operator' => '=', 'value' => '']];
                                                            }
                                                        } elseif (empty($existingActions)) {
                                                            $existingActions = [['key' => '', 'operator' => '=', 'value' => '']];
                                                        }
                                                    @endphp

                                                    @foreach($existingActions as $index => $action)
                                                        <div class="query-rule" id="action_{{ $index }}">
                                                            <div class="rule-input-group">
                                                                <div style="flex: 2;">
                                                                    <select name="actions[{{ $index }}][key]" class="rule-select w-100" required>
                                                                        <option value="">Select Attribute to Modify</option>
                                                                        @foreach($regularAttributes->merge($computedAttributes) as $attr)
                                                                            <option value="{{ $attr->key }}" {{ ($action['key'] ?? '') == $attr->key ? 'selected' : '' }}>
                                                                                {{ $attr->label }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                
                                                                <div style="flex: 1;">
                                                                    <select name="actions[{{ $index }}][operator]" class="rule-select w-100" required>
                                                                        <option value="+">Tambah (+)</option>
                                                                        <option value="-" {{ ($action['operator'] ?? '') == '-' ? 'selected' : '' }}>Kurang (-)</option>
                                                                        <option value="*" {{ ($action['operator'] ?? '') == '*' ? 'selected' : '' }}>Kali (*)</option>
                                                                        <option value="=" {{ ($action['operator'] ?? '') == '=' ? 'selected' : '' }}>Set (=)</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div style="flex: 1;">
                                                                    <input type="text" name="actions[{{ $index }}][value]" class="rule-input w-100" value="{{ $action['value'] ?? '' }}" placeholder="Value (e.g. 10)" required>
                                                                </div>
                                                            </div>
                                                            
                                                            <input type="hidden" name="actions[{{ $index }}][type]" value="update_attribute">
                                                            
                                                            <x-ui.button type="button" variant="ghost" class="btn-delete-rule remove-action" {{ count($existingActions) == 1 ? 'disabled' : '' }} icon="close" />
                                                        </div>
                                                    @endforeach
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- State Management ---
        let conditionIndex = {{ count($existingConditions) }};
        let actionIndex = {{ count($existingActions) }};

        // Attribute Options
        const regularOptions = `
            @if($regularAttributes->count() > 0)
            <optgroup label="📊 Data Mahasiswa (Regular)">
                @foreach($regularAttributes as $attr)
                    <option value="{{ $attr->key }}" data-type="regular">{{ $attr->label }}</option>
                @endforeach
            </optgroup>
            @endif
        `;

        const computedOptions = `
            @if($computedAttributes->count() > 0)
            <optgroup label="🧮 Nilai Terhitung (Computed)">
                @foreach($computedAttributes as $attr)
                    <option value="{{ $attr->key }}" data-type="computed">{{ $attr->label }} ⚡</option>
                @endforeach
            </optgroup>
            @endif
        `;
        
        const allAttributeOptions = `
            <option value="">Select Attribute</option>
            ${regularOptions}
            ${computedOptions}
        `;

        // --- Event Listeners ---

        // Add Condition
        document.getElementById('addCondition').addEventListener('click', () => {
            const container = document.getElementById('conditionsContainer');
            const newRowHtml = `
                <div class="query-rule" id="condition_${conditionIndex}">
                    <div class="rule-input-group">
                        <div style="flex: 2;">
                            <select name="conditions[${conditionIndex}][key]" class="rule-select w-100 attribute-select" required>
                                ${allAttributeOptions}
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
            
            container.insertAdjacentHTML('beforeend', newRowHtml);
            conditionIndex++;
            updateRemoveButtons('remove-condition');
        });

        // Add Action
        document.getElementById('addAction').addEventListener('click', () => {
            const container = document.getElementById('actionsContainer');
            const newRowHtml = `
                <div class="query-rule" id="action_${actionIndex}">
                    <div class="rule-input-group">
                        <div style="flex: 2;">
                            <select name="actions[${actionIndex}][key]" class="rule-select w-100" required>
                                <option value="">Select Attribute to Modify</option>
                                @foreach($regularAttributes->merge($computedAttributes) as $attr)
                                    <option value="{{ $attr->key }}">{{ $attr->label }}</option>
                                @endforeach
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
            
            container.insertAdjacentHTML('beforeend', newRowHtml);
            actionIndex++;
            updateRemoveButtons('remove-action');
        });

        // Delegate Remove Events
        document.addEventListener('click', function(e) {
            // Remove Condition
            if (e.target.closest('.remove-condition')) {
                const row = e.target.closest('.query-rule');
                // Ensure at least one remains
                if (document.querySelectorAll('#conditionsContainer .query-rule').length > 1) {
                    row.remove();
                    updateRemoveButtons('remove-condition');
                }
            }
            // Remove Action
            if (e.target.closest('.remove-action')) {
                const row = e.target.closest('.query-rule');
                if (document.querySelectorAll('#actionsContainer .query-rule').length > 1) {
                    row.remove();
                    updateRemoveButtons('remove-action');
                }
            }
        });

        // Helper: Update state of remove buttons (disable if only 1 left)
        function updateRemoveButtons(className) {
            const buttons = document.querySelectorAll('.' + className);
            const isDisabled = buttons.length === 1;
            buttons.forEach(btn => btn.disabled = isDisabled);
        }

        // Initial check
        updateRemoveButtons('remove-condition');
        updateRemoveButtons('remove-action');
    });
</script>
@endpush
