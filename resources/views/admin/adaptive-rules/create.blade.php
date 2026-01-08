<x-layouts.app title="Buat Aturan Adaptif" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Engine Logic Architect"
            subtitle="Definisikan kecerdasan sistem untuk menyesuaikan pengalaman belajar secara dinamis."
        >
            <x-ui.button href="{{ route('admin.adaptive-rules.index') }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE LIST</x-ui.button>
        </x-ui.page-header>

        <form action="{{ route('admin.adaptive-rules.store') }}" method="POST" id="ruleForm" class="space-y-12">
            @csrf
            
            <x-ui.card class="border-slate-100 shadow-2xl">
                <x-slot:header>General Identification</x-slot:header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <x-forms.form-group label="Rule Designator" name="name" required>
                            <x-ui.input name="name" placeholder="Misal: Akselerasi Kesulitan Mahasiswa High-Perform" required />
                        </x-forms.form-group>

                        <x-forms.form-group label="Target Modul (Scope)" name="material_id">
                            <select name="material_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none appearance-none">
                                <option value="">Global Enforcement (Semua Materi)</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->title }}</option>
                                @endforeach
                            </select>
                        </x-forms.form-group>
                    </div>

                    <div class="space-y-6">
                        <x-forms.form-group label="Logic Rationale" name="description">
                            <x-ui.input type="textarea" name="description" rows="5" placeholder="Jelaskan secara teknis tujuan dari rule ini..." />
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
                        <div class="px-8 py-3 bg-blue-600 text-white rounded-full text-sm font-black italic tracking-widest shadow-xl shadow-blue-500/20 mb-8 border-4 border-white">IF CONDITION</div>
                        
                        <div class="w-full max-w-4xl">
                            <x-ui.card padding="p-0" class="overflow-hidden border-blue-100 shadow-2xl">
                                <div class="p-4 bg-blue-50/50 border-b border-blue-100 flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 italic">Conditional Triggers</span>
                                    <x-ui.button type="button" size="xs" variant="ghost" data-bs-toggle="modal" data-bs-target="#addAttributeModal" icon="fas fa-plus-circle">NEW ATTRIBUTE</x-ui.button>
                                </div>
                                
                                <div id="conditionsContainer" class="p-8 space-y-4">
                                    <div class="query-rule flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center group transition-all" id="condition_0">
                                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <select name="conditions[0][key]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-blue-100 outline-none" required>
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
                                            </select>

                                            <select name="conditions[0][operator]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black italic uppercase italic focus:ring-4 focus:ring-blue-100 outline-none" required>
                                                <option value=">">GREATER THAN</option>
                                                <option value=">=">GREATER OR EQUAL</option>
                                                <option value="<">LESS THAN</option>
                                                <option value="<=">LESS OR EQUAL</option>
                                                <option value="==">EQUALS</option>
                                                <option value="!=">NOT EQUALS</option>
                                            </select>

                                            <input type="text" name="conditions[0][value]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-blue-100 outline-none" placeholder="Threshold Value" required>
                                        </div>
                                        <x-ui.button type="button" variant="ghost" size="sm" class="remove-condition opacity-0 group-hover:opacity-100 transition-opacity text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" disabled />
                                    </div>
                                </div>

                                <div class="p-6 border-t border-slate-50 bg-slate-50/30 flex justify-center">
                                    <x-ui.button type="button" id="addCondition" variant="ghost" size="sm" class="text-blue-600 font-black italic" icon="fas fa-plus">ADD FILTER CRITERIA</x-ui.button>
                                </div>
                            </x-ui.card>
                        </div>
                    </div>

                    {{-- THEN BRANCH --}}
                    <div class="flex flex-col items-center">
                        <div class="px-8 py-3 bg-emerald-500 text-white rounded-full text-sm font-black italic tracking-widest shadow-xl shadow-emerald-500/20 mb-8 border-4 border-white">THEN ACTION</div>
                        
                        <div class="w-full max-w-4xl">
                            <x-ui.card padding="p-0" class="overflow-hidden border-emerald-100 shadow-2xl">
                                <div class="p-4 bg-emerald-50/50 border-b border-emerald-100">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 italic">Engine Execution Plan</span>
                                </div>
                                
                                <div id="actionsContainer" class="p-8 space-y-4">
                                    <div class="query-rule flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center group transition-all" id="action_0">
                                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <select name="actions[0][key]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-emerald-100 outline-none" required>
                                                <option value="">Attribute to Modify</option>
                                                @foreach($regularAttributes->merge($computedAttributes) as $attr)
                                                    <option value="{{ $attr->key }}">{{ $attr->label }}</option>
                                                @endforeach
                                            </select>

                                            <select name="actions[0][operator]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black italic uppercase focus:ring-4 focus:ring-emerald-100 outline-none" required>
                                                <option value="+">INCREMENT (+)</option>
                                                <option value="-">DECREMENT (-)</option>
                                                <option value="*">MULTIPLY (*)</option>
                                                <option value="=">SET VALUE (=)</option>
                                            </select>

                                            <input type="text" name="actions[0][value]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-emerald-100 outline-none" placeholder="Target Value" required>
                                        </div>
                                        <input type="hidden" name="actions[0][type]" value="update_attribute">
                                        <x-ui.button type="button" variant="ghost" size="sm" class="remove-action opacity-0 group-hover:opacity-100 transition-opacity text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" disabled />
                                    </div>
                                </div>

                                <div class="p-6 border-t border-slate-50 bg-slate-50/30 flex justify-center">
                                    <x-ui.button type="button" id="addAction" variant="ghost" size="sm" class="text-emerald-600 font-black italic" icon="fas fa-bolt">ADD EXECUTION STEP</x-ui.button>
                                </div>
                            </x-ui.card>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Controls --}}
            <x-ui.card class="bg-slate-900 border-slate-800 text-white overflow-hidden relative">
                <div class="absolute right-0 top-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 relative z-10">
                    <div class="space-y-6">
                        <x-forms.form-group label="Priority Index" name="priority" required>
                            <x-ui.input type="number" name="priority" value="10" min="0" required class="bg-slate-800 border-slate-700 text-white" />
                            <p class="text-[10px] text-slate-500 italic mt-2">Semakin tinggi angka, semakin awal antrian eksekusi.</p>
                        </x-forms.form-group>
                    </div>
                    <div class="flex flex-col justify-center gap-6">
                        <div class="flex items-center gap-4 p-6 bg-slate-800/50 rounded-[2rem] border border-slate-700">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                                <i class="fas fa-power-off"></i>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-black uppercase tracking-widest italic mb-1 block">Operational Status</label>
                                <x-forms.checkbox name="is_active" label="Aktifkan Logic Engine" checked class="text-emerald-500" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 flex justify-end gap-4">
                    <x-ui.button variant="ghost" href="{{ route('admin.adaptive-rules.index') }}" class="text-slate-400">CANCEL ARCHITECTURE</x-ui.button>
                    <x-ui.button type="submit" variant="primary" size="lg" class="px-12 shadow-2xl shadow-blue-500/40" icon="fas fa-microchip">DEPLOY LOGIC RULE</x-ui.button>
                </div>
            </x-ui.card>
        </form>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="addAttributeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-[2.5rem] shadow-2xl overflow-hidden">
                <div class="p-8 bg-slate-900 text-white relative">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/20 blur-2xl"></div>
                    <h5 class="text-xl font-black italic tracking-tighter uppercase relative z-10">Register System Attribute</h5>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2">Menambah variabel baru ke database adaptif</p>
                </div>
                <form id="addAttributeForm" class="p-8 space-y-6 bg-white">
                    <div class="grid grid-cols-2 gap-4">
                        <x-forms.form-group label="Key (Technical Name)" name="key" required>
                            <x-ui.input name="key" placeholder="e.g. mastery_level" required />
                        </x-forms.form-group>
                        <x-forms.form-group label="Label (Display)" name="label" required>
                            <x-ui.input name="label" placeholder="e.g. Tingkat Penguasaan" required />
                        </x-forms.form-group>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <x-forms.form-group label="Data Type" name="type" required>
                            <select name="type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none" required>
                                <option value="integer">Integer</option>
                                <option value="float">Float</option>
                                <option value="string">String</option>
                                <option value="boolean">Boolean</option>
                            </select>
                        </x-forms.form-group>
                        <x-forms.form-group label="Default Value" name="default_value" required>
                            <x-ui.input name="default_value" value="0" required />
                        </x-forms.form-group>
                    </div>
                    <x-forms.form-group label="Functional Category" name="category">
                        <select name="category" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none">
                            <option value="progression">Progression</option>
                            <option value="gameplay">Gameplay</option>
                            <option value="general">General</option>
                        </select>
                    </x-forms.form-group>
                    <div class="flex flex-col gap-4 pt-4 border-t border-slate-100">
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">REGISTER ATTRIBUTE</x-ui.button>
                        <x-ui.button type="button" variant="ghost" data-bs-dismiss="modal">CLOSE</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const attributesData = @json($regularAttributes->merge($computedAttributes)->map(function($attr) {
            return ['key' => $attr->key, 'label' => $attr->label];
        }));
        
        function buildAttributeOptions() {
            let options = '<option value="">Select Attribute</option>';
            attributesData.forEach(attr => {
                options += `<option value="${attr.key}">${attr.label}</option>`;
            });
            return options;
        }
        
        let conditionIndex = 1;
        let actionIndex = 1;

        const addConditionBtn = document.getElementById('addCondition');
        const addActionBtn = document.getElementById('addAction');
        const conditionsContainer = document.getElementById('conditionsContainer');
        const actionsContainer = document.getElementById('actionsContainer');
        
        // Add Condition
        if (addConditionBtn) {
            addConditionBtn.addEventListener('click', function() {
                const firstSelect = document.querySelector('select[name="conditions[0][key]"]');
                const optionsHtml = firstSelect ? firstSelect.innerHTML : buildAttributeOptions();
                
                const newCondition = `
                    <div class="query-rule flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center group animate-in slide-in-from-left-4 duration-300" id="condition_${conditionIndex}">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select name="conditions[${conditionIndex}][key]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-blue-100 outline-none" required>
                                ${optionsHtml}
                            </select>
                            <select name="conditions[${conditionIndex}][operator]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black italic uppercase focus:ring-4 focus:ring-blue-100 outline-none" required>
                                <option value=">">GREATER THAN</option>
                                <option value=">=">GREATER OR EQUAL</option>
                                <option value="<">LESS THAN</option>
                                <option value="<=">LESS OR EQUAL</option>
                                <option value="==">EQUALS</option>
                                <option value="!=">NOT EQUALS</option>
                            </select>
                            <input type="text" name="conditions[${conditionIndex}][value]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-blue-100 outline-none" placeholder="Threshold Value" required>
                        </div>
                        <x-ui.button type="button" variant="ghost" size="sm" class="remove-condition opacity-0 group-hover:opacity-100 transition-opacity text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" />
                    </div>
                `;
                conditionsContainer.insertAdjacentHTML('beforeend', newCondition);
                conditionIndex++;
                updateRemoveButtons('remove-condition');
            });
        }

        // Add Action
        if (addActionBtn) {
            addActionBtn.addEventListener('click', function() {
                const firstSelect = document.querySelector('select[name="actions[0][key]"]');
                const optionsHtml = firstSelect ? firstSelect.innerHTML : '';
                
                const newAction = `
                    <div class="query-rule flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center group animate-in slide-in-from-left-4 duration-300" id="action_${actionIndex}">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select name="actions[${actionIndex}][key]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-emerald-100 outline-none" required>
                                ${optionsHtml}
                            </select>
                            <select name="actions[${actionIndex}][operator]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black italic uppercase focus:ring-4 focus:ring-emerald-100 outline-none" required>
                                <option value="+">INCREMENT (+)</option>
                                <option value="-">DECREMENT (-)</option>
                                <option value="*">MULTIPLY (*)</option>
                                <option value="=">SET VALUE (=)</option>
                            </select>
                            <input type="text" name="actions[${actionIndex}][value]" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-emerald-100 outline-none" placeholder="Target Value" required>
                        </div>
                        <input type="hidden" name="actions[${actionIndex}][type]" value="update_attribute">
                        <x-ui.button type="button" variant="ghost" size="sm" class="remove-action opacity-0 group-hover:opacity-100 transition-opacity text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" />
                    </div>
                `;
                actionsContainer.insertAdjacentHTML('beforeend', newAction);
                actionIndex++;
                updateRemoveButtons('remove-action');
            });
        }

        // Remove handling
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-condition')) {
                const row = e.target.closest('.query-rule');
                if (document.querySelectorAll('.remove-condition').length > 1) {
                    row.remove();
                    updateRemoveButtons('remove-condition');
                }
            }
            if (e.target.closest('.remove-action')) {
                const row = e.target.closest('.query-rule');
                if (document.querySelectorAll('.remove-action').length > 1) {
                    row.remove();
                    updateRemoveButtons('remove-action');
                }
            }
        });

        function updateRemoveButtons(className) {
            const buttons = document.querySelectorAll('.' + className);
            buttons.forEach(btn => btn.disabled = buttons.length === 1);
        }

        // Attribute Creation Form
        const addAttributeForm = document.getElementById('addAttributeForm');
        if (addAttributeForm) {
            addAttributeForm.addEventListener('submit', async function(e) {
                e.preventDefault();
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
                    if (response.ok) location.reload();
                    else alert('Gagal menyimpan atribut. Periksa kembali inputan Anda.');
                } catch (error) {
                    alert('Terjadi kesalahan koneksi.');
                }
            });
        }
    });
    </script>
    @endpush
</x-layouts.app>
