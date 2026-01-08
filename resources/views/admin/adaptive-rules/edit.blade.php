<x-layouts.app title="Edit Aturan Adaptif" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Update Logic Architect"
            subtitle="Modifikasi aturan adaptif yang sudah ada untuk optimasi performa sistem."
        >
            <x-ui.button href="{{ route('admin.adaptive-rules.index') }}" variant="ghost" icon="fas fa-arrow-left">BATALKAN PERUBAHAN</x-ui.button>
        </x-ui.page-header>

        <form action="{{ route('admin.adaptive-rules.update', $adaptiveRule) }}" method="POST" id="ruleForm" class="space-y-12">
            @csrf
            @method('PUT')
            
            <x-ui.card class="border-slate-100 shadow-2xl">
                <x-slot:header>System Identification Update</x-slot:header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <x-forms.form-group label="Rule Designator" name="name" required>
                            <x-ui.input name="name" :value="$adaptiveRule->name" required />
                        </x-forms.form-group>

                        <x-forms.form-group label="Target Modul (Scope)" name="material_id">
                            <select name="material_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none appearance-none">
                                <option value="">Global Enforcement (Semua Materi)</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}" {{ $adaptiveRule->material_id == $material->id ? 'selected' : '' }}>
                                        {{ $material->title }}
                                    </option>
                                @endforeach
                            </select>
                        </x-forms.form-group>
                    </div>

                    <div class="space-y-6">
                        <x-forms.form-group label="Logic Rationale" name="description">
                            <x-ui.input type="textarea" name="description" rows="5" :value="$adaptiveRule->description" />
                        </x-forms.form-group>
                    </div>
                </div>
            </x-ui.card>

            {{-- Logic Engine Builder --}}
            <div class="relative">
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-1 h-full bg-gradient-to-b from-blue-500/20 via-blue-500/40 to-emerald-500/20 rounded-full"></div>
                </div>

                <div class="space-y-24 relative z-10">
                    {{-- IF BRANCH --}}
                    <div class="flex flex-col items-center">
                        <div class="px-8 py-3 bg-blue-600 text-white rounded-full text-sm font-black italic tracking-widest shadow-xl shadow-blue-500/20 mb-8 border-4 border-white uppercase">Modify Conditions</div>
                        
                        <div class="w-full max-w-4xl">
                            <x-ui.card padding="p-0" class="overflow-hidden border-blue-100 shadow-2xl">
                                <div class="p-4 bg-blue-50/50 border-b border-blue-100 flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 italic">IF Triggers</span>
                                    <x-ui.button type="button" size="xs" variant="ghost" data-bs-toggle="modal" data-bs-target="#addAttributeModal" icon="fas fa-plus-circle">NEW VARIABLE</x-ui.button>
                                </div>
                                
                                <div id="conditionsContainer" class="p-8 space-y-4">
                                    @php
                                        $existingConditions = !empty($adaptiveRule->conditions) && is_array($adaptiveRule->conditions) 
                                            ? $adaptiveRule->conditions 
                                            : [['key' => '', 'operator' => '>', 'value' => '']];
                                    @endphp
                                    
                                    @foreach($existingConditions as $index => $condition)
                                    <div class="query-rule flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center group transition-all" id="condition_{{ $index }}">
                                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <select name="conditions[{{ $index }}][key]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-blue-100 outline-none" required>
                                                <option value="">Select Attribute</option>
                                                @if($regularAttributes->count() > 0)
                                                <optgroup label="📊 REGULAR DATA">
                                                    @foreach($regularAttributes as $attr)
                                                        <option value="{{ $attr->key }}" {{ ($condition['key'] ?? $condition['type'] ?? '') == $attr->key ? 'selected' : '' }}>
                                                            {{ $attr->label }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                @endif
                                                @if($computedAttributes->count() > 0)
                                                <optgroup label="🧮 COMPUTED VALUES">
                                                    @foreach($computedAttributes as $attr)
                                                        <option value="{{ $attr->key }}" {{ ($condition['key'] ?? $condition['type'] ?? '') == $attr->key ? 'selected' : '' }}>
                                                            {{ $attr->label }} ⚡
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                @endif
                                            </select>

                                            <select name="conditions[{{ $index }}][operator]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black italic uppercase focus:ring-4 focus:ring-blue-100 outline-none" required>
                                                <option value=">" {{ ($condition['operator'] ?? '') == '>' ? 'selected' : '' }}>GREATER THAN</option>
                                                <option value=">=" {{ ($condition['operator'] ?? '') == '>=' ? 'selected' : '' }}>GREATER OR EQUAL</option>
                                                <option value="<" {{ ($condition['operator'] ?? '') == '<' ? 'selected' : '' }}>LESS THAN</option>
                                                <option value="<=" {{ ($condition['operator'] ?? '') == '<=' ? 'selected' : '' }}>LESS OR EQUAL</option>
                                                <option value="==" {{ ($condition['operator'] ?? '') == '==' ? 'selected' : '' }}>EQUALS</option>
                                                <option value="!=" {{ ($condition['operator'] ?? '') == '!=' ? 'selected' : '' }}>NOT EQUALS</option>
                                            </select>

                                            <input type="text" name="conditions[{{ $index }}][value]" value="{{ $condition['value'] ?? '' }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-blue-100 outline-none" placeholder="Threshold Value" required>
                                        </div>
                                        <x-ui.button type="button" variant="ghost" size="sm" class="remove-condition opacity-0 group-hover:opacity-100 transition-opacity text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" :disabled="count($existingConditions) == 1" />
                                    </div>
                                    @endforeach
                                </div>

                                <div class="p-6 border-t border-slate-50 bg-slate-50/30 flex justify-center">
                                    <x-ui.button type="button" id="addCondition" variant="ghost" size="sm" class="text-blue-600 font-black italic" icon="fas fa-plus">APPEND NEW CRITERIA</x-ui.button>
                                </div>
                            </x-ui.card>
                        </div>
                    </div>

                    {{-- THEN BRANCH --}}
                    <div class="flex flex-col items-center">
                        <div class="px-8 py-3 bg-emerald-500 text-white rounded-full text-sm font-black italic tracking-widest shadow-xl shadow-emerald-500/20 mb-8 border-4 border-white uppercase">Modify Actions</div>
                        
                        <div class="w-full max-w-4xl">
                            <x-ui.card padding="p-0" class="overflow-hidden border-emerald-100 shadow-2xl">
                                <div class="p-4 bg-emerald-50/50 border-b border-emerald-100">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 italic">THEN Executions</span>
                                </div>
                                
                                <div id="actionsContainer" class="p-8 space-y-4">
                                    @php
                                        $existingActions = !empty($adaptiveRule->actions) && is_array($adaptiveRule->actions) 
                                            ? $adaptiveRule->actions 
                                            : [['key' => '', 'operator' => '=', 'value' => '']];
                                    @endphp

                                    @foreach($existingActions as $index => $action)
                                    <div class="query-rule flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center group transition-all" id="action_{{ $index }}">
                                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <select name="actions[{{ $index }}][key]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-emerald-100 outline-none" required>
                                                <option value="">Attribute to Modify</option>
                                                @foreach($regularAttributes->merge($computedAttributes) as $attr)
                                                    <option value="{{ $attr->key }}" {{ ($action['key'] ?? '') == $attr->key ? 'selected' : '' }}>
                                                        {{ $attr->label }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <select name="actions[{{ $index }}][operator]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black italic uppercase focus:ring-4 focus:ring-emerald-100 outline-none" required>
                                                <option value="+" {{ ($action['operator'] ?? '') == '+' ? 'selected' : '' }}>INCREMENT (+)</option>
                                                <option value="-" {{ ($action['operator'] ?? '') == '-' ? 'selected' : '' }}>DECREMENT (-)</option>
                                                <option value="*" {{ ($action['operator'] ?? '') == '*' ? 'selected' : '' }}>MULTIPLY (*)</option>
                                                <option value="=" {{ ($action['operator'] ?? '') == '=' ? 'selected' : '' }}>SET VALUE (=)</option>
                                            </select>

                                            <input type="text" name="actions[{{ $index }}][value]" value="{{ $action['value'] ?? '' }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-emerald-100 outline-none" placeholder="Target Value" required>
                                        </div>
                                        <input type="hidden" name="actions[{{ $index }}][type]" value="update_attribute">
                                        <x-ui.button type="button" variant="ghost" size="sm" class="remove-action opacity-0 group-hover:opacity-100 transition-opacity text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" :disabled="count($existingActions) == 1" />
                                    </div>
                                    @endforeach
                                </div>

                                <div class="p-6 border-t border-slate-50 bg-slate-50/30 flex justify-center">
                                    <x-ui.button type="button" id="addAction" variant="ghost" size="sm" class="text-emerald-600 font-black italic" icon="fas fa-bolt">APPEND EXECUTION</x-ui.button>
                                </div>
                            </x-ui.card>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Controls --}}
            <x-ui.card class="bg-slate-900 border-slate-800 text-white overflow-hidden relative">
                <div class="absolute right-0 top-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 relative z-10">
                    <div class="space-y-6">
                        <x-forms.form-group label="Priority Index" name="priority" required>
                            <x-ui.input type="number" name="priority" :value="$adaptiveRule->priority" min="0" required class="bg-slate-800 border-slate-700 text-white" />
                        </x-forms.form-group>
                    </div>
                    <div class="flex flex-col justify-center gap-6">
                        <div class="flex items-center gap-4 p-6 bg-slate-800/50 rounded-[2rem] border border-slate-700">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                                <i class="fas fa-power-off"></i>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-black uppercase tracking-widest italic mb-1 block">Live Engagement</label>
                                <x-forms.checkbox name="is_active" label="Keep this logic active" :checked="$adaptiveRule->is_active" class="text-emerald-500" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 flex justify-end gap-4">
                    <x-ui.button variant="ghost" href="{{ route('admin.adaptive-rules.index') }}" class="text-slate-400 font-black">CANCEL UPDATE</x-ui.button>
                    <x-ui.button type="submit" variant="primary" size="lg" class="px-12 shadow-2xl shadow-blue-500/40" icon="fas fa-sync">SYNCHRONIZE ENGINE</x-ui.button>
                </div>
            </x-ui.card>
        </form>
    </div>

    {{-- Modal Reused from Create --}}
    <div class="modal fade" id="addAttributeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-[2.5rem] shadow-2xl overflow-hidden">
                <div class="p-8 bg-slate-900 text-white relative">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/20 blur-2xl"></div>
                    <h5 class="text-xl font-black italic tracking-tighter uppercase relative z-10">Register System Attribute</h5>
                </div>
                <form id="addAttributeForm" class="p-8 space-y-6 bg-white">
                    <div class="grid grid-cols-2 gap-4">
                        <x-forms.form-group label="Key" name="key" required><x-ui.input name="key" required /></x-forms.form-group>
                        <x-forms.form-group label="Label" name="label" required><x-ui.input name="label" required /></x-forms.form-group>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <x-forms.form-group label="Type" name="type" required>
                            <select name="type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none"><option value="integer">Integer</option><option value="float">Float</option><option value="string">String</option></select>
                        </x-forms.form-group>
                        <x-forms.form-group label="Default" name="default_value" required><x-ui.input name="default_value" value="0" required /></x-forms.form-group>
                    </div>
                    <x-ui.button type="submit" variant="primary" class="w-full">REGISTER NEW VARIABLE</x-ui.button>
                    <x-ui.button type="button" variant="ghost" data-bs-dismiss="modal" class="w-full">CLOSE</x-ui.button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let conditionIndex = {{ count($existingConditions) }};
        let actionIndex = {{ count($existingActions) }};
        
        const addConditionBtn = document.getElementById('addCondition');
        const addActionBtn = document.getElementById('addAction');
        const conditionsContainer = document.getElementById('conditionsContainer');
        const actionsContainer = document.getElementById('actionsContainer');

        const attributeOptions = `
            <option value="">Select Attribute</option>
            @if($regularAttributes->count() > 0)
            <optgroup label="📊 REGULAR DATA">
                @foreach($regularAttributes as $attr)
                    <option value="{{ $attr->key }}">{{ $attr->label }}</option>
                @endforeach
            </optgroup>
            @endif
            @if($computedAttributes->count() > 0)
            <optgroup label="🧮 COMPUTED VALUES">
                @foreach($computedAttributes as $attr)
                    <option value="{{ $attr->key }}">{{ $attr->label }} ⚡</option>
                @endforeach
            </optgroup>
            @endif
        `;

        if (addConditionBtn) {
            addConditionBtn.addEventListener('click', () => {
                const html = `
                    <div class="query-rule flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center group animate-in slide-in-from-left-4 duration-300" id="condition_${conditionIndex}">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select name="conditions[${conditionIndex}][key]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-blue-100 outline-none" required>${attributeOptions}</select>
                            <select name="conditions[${conditionIndex}][operator]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black italic uppercase outline-none">
                                <option value=">">GREATER THAN</option><option value=">=">GREATER OR EQUAL</option><option value="<">LESS THAN</option><option value="<=">LESS OR EQUAL</option><option value="==">EQUALS</option>
                            </select>
                            <input type="text" name="conditions[${conditionIndex}][value]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold outline-none" required>
                        </div>
                        <x-ui.button type="button" variant="ghost" size="sm" class="remove-condition text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" />
                    </div>
                `;
                conditionsContainer.insertAdjacentHTML('beforeend', html);
                conditionIndex++;
                updateState('remove-condition');
            });
        }

        if (addActionBtn) {
            addActionBtn.addEventListener('click', () => {
                const firstActionKeySelect = document.querySelector('select[name="actions[0][key]"]');
                const actionOpts = firstActionKeySelect ? firstActionKeySelect.innerHTML : attributeOptions;
                const html = `
                    <div class="query-rule flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center group animate-in slide-in-from-left-4 duration-300" id="action_${actionIndex}">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select name="actions[${actionIndex}][key]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold outline-none" required>${actionOpts}</select>
                            <select name="actions[${actionIndex}][operator]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black italic uppercase outline-none">
                                <option value="+">INCREMENT (+)</option><option value="-">DECREMENT (-)</option><option value="=">SET VALUE (=)</option>
                            </select>
                            <input type="text" name="actions[${actionIndex}][value]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold" required>
                        </div>
                        <input type="hidden" name="actions[${actionIndex}][type]" value="update_attribute">
                        <x-ui.button type="button" variant="ghost" size="sm" class="remove-action text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" />
                    </div>
                `;
                actionsContainer.insertAdjacentHTML('beforeend', html);
                actionIndex++;
                updateState('remove-action');
            });
        }

        document.addEventListener('click', (e) => {
            if (e.target.closest('.remove-condition')) {
                const row = e.target.closest('.query-rule');
                if (document.querySelectorAll('#conditionsContainer .query-rule').length > 1) {
                    row.remove();
                    updateState('remove-condition');
                }
            }
            if (e.target.closest('.remove-action')) {
                const row = e.target.closest('.query-rule');
                if (document.querySelectorAll('#actionsContainer .query-rule').length > 1) {
                    row.remove();
                    updateState('remove-action');
                }
            }
        });

        function updateState(cl) {
            const btns = document.querySelectorAll('.' + cl);
            btns.forEach(b => b.disabled = btns.length === 1);
        }
    });
    </script>
    @endpush
</x-layouts.app>
