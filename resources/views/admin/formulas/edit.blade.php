<x-layouts.app title="Edit Formula" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Logic Formula Update"
            subtitle="Modifikasi ekspresi matematika untuk optimasi metrik adaptif."
        >
            <x-ui.button href="{{ route('admin.formulas.index') }}" variant="ghost" icon="fas fa-arrow-left">BATALKAN UPDATE</x-ui.button>
        </x-ui.page-header>

        <form action="{{ route('admin.formulas.update', $formula) }}" method="POST" class="space-y-12">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                {{-- Configuration Left --}}
                <div class="lg:col-span-1 space-y-8">
                    <x-ui.card class="border-slate-100 shadow-2xl">
                        <x-slot:header>Identity Specs</x-slot:header>
                        <div class="space-y-6">
                            <x-forms.form-group label="Formula Name" name="name" required>
                                <x-ui.input name="name" :value="old('name', $formula->name)" required />
                            </x-forms.form-group>
                            
                            <x-forms.form-group label="Technical Key" name="key" required>
                                <x-ui.input name="key" :value="old('key', $formula->key)" pattern="[a-z_]+" required />
                            </x-forms.form-group>

                            <x-forms.form-group label="Operational Scope" name="scope" required>
                                <select name="scope" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold outline-none ring-offset-2 focus:ring-4 focus:ring-blue-100 transition-all">
                                    @foreach(\App\Models\Formula::SCOPES as $key => $label)
                                        <option value="{{ $key }}" {{ old('scope', $formula->scope) == $key ? 'selected' : '' }}>{{ strtoupper($label) }}</option>
                                    @endforeach
                                </select>
                            </x-forms.form-group>

                            <x-forms.form-group label="Return Signature" name="return_type" required>
                                <select name="return_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold outline-none ring-offset-2 focus:ring-4 focus:ring-blue-100 transition-all">
                                    @foreach(\App\Models\Formula::RETURN_TYPES as $key => $label)
                                        <option value="{{ $key }}" {{ old('return_type', $formula->return_type) == $key ? 'selected' : '' }}>{{ strtoupper($label) }}</option>
                                    @endforeach
                                </select>
                            </x-forms.form-group>
                        </div>
                    </x-ui.card>

                    <x-ui.card class="bg-slate-900 border-0 shadow-2xl overflow-hidden relative group">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/20 blur-3xl group-hover:bg-blue-600/40 transition-colors"></div>
                        <div class="relative z-10 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                                <i class="fas fa-power-off"></i>
                            </div>
                            <div class="flex-1">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-1">Operational State</label>
                                <x-forms.checkbox name="is_active" label="Keep this logic active" :checked="old('is_active', $formula->is_active)" class="text-white" />
                            </div>
                        </div>
                    </x-ui.card>
                </div>

                {{-- Expression Central --}}
                <div class="lg:col-span-2 space-y-8">
                    <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl flex flex-col h-full">
                        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                            <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Expression Auditor</h6>
                            <div class="flex gap-2">
                                <div class="w-2 h-2 rounded-full bg-rose-400"></div>
                                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                            </div>
                        </div>
                        <div class="flex-1 min-h-[400px] flex flex-col">
                            <textarea 
                                name="expression" 
                                class="flex-1 w-full p-8 bg-slate-900 text-emerald-400 font-mono text-lg outline-none selection:bg-emerald-400/20" 
                                required>{{ old('expression', $formula->expression) }}</textarea>
                        </div>
                        <div class="p-6 bg-slate-100 border-t border-slate-200">
                             <div class="flex items-center gap-4">
                                <i class="fas fa-info-circle text-blue-600"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Functional Doc:</span>
                                <x-ui.input name="description" :value="old('description', $formula->description)" placeholder="Analisa teknis formula ini..." class="flex-1 border-0 bg-transparent py-0 text-slate-900 font-bold italic" />
                            </div>
                        </div>
                    </x-ui.card>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($attributes as $category => $attrs)
                            <x-ui.card class="bg-slate-50 border-slate-200">
                                <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">{{ $category }} VARIABLES</h6>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($attrs as $attr)
                                        <div class="px-2 py-1 bg-white border border-slate-200 rounded-md text-[9px] font-bold text-slate-600 font-mono hover:bg-blue-50 hover:border-blue-200 cursor-help transition-all" title="{{ $attr->label }}">
                                            {{ $attr->key }}
                                        </div>
                                    @endforeach
                                </div>
                            </x-ui.card>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-12">
                <x-ui.button type="submit" variant="primary" size="lg" class="px-16 shadow-2xl shadow-blue-500/40" icon="fas fa-sync">SYNCHRONIZE FORMULA</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.app>
