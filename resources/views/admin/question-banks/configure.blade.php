<x-layouts.app title="Konfigurasi Bank Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Distribution Engine Config"
            subtitle="Atur rasio kemunculan soal berdasarkan tingkat kesulitan untuk engine adaptif."
        >
            <x-ui.button href="{{ route('admin.question-banks.show', $questionBank) }}" variant="ghost" icon="fas fa-arrow-left">BACK TO INSIGHT</x-ui.button>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Form Side --}}
            <div class="space-y-8">
                <x-ui.card class="border-slate-100 shadow-2xl relative overflow-hidden">
                    <x-slot:header>Architect Logic Builder</x-slot:header>
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/5 blur-3xl"></div>
                    
                    <form action="{{ route('admin.question-banks.store-config', $questionBank) }}" method="POST" class="space-y-8 p-4">
                        @csrf
                        @if(isset($editConfig))
                            <input type="hidden" name="config_id" value="{{ $editConfig->id }}">
                        @endif

                        <x-forms.form-group label="Target Curriculum Module" name="material_id" required>
                            @if(isset($editConfig))
                                <div class="px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-4">
                                    <i class="fas fa-link text-blue-600"></i>
                                    <span class="text-sm font-black italic tracking-tighter text-slate-900 uppercase">{{ $editConfig->material->title }}</span>
                                    <input type="hidden" name="material_id" value="{{ $editConfig->material_id }}">
                                </div>
                            @else
                                <select name="material_id" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer" required>
                                    <option value="">-- ATTACH TO MODULE --</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>{{ strtoupper($material->title) }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </x-forms.form-group>

                        <div class="grid grid-cols-3 gap-6">
                            <div class="p-6 bg-emerald-50/50 rounded-3xl border border-emerald-100 group transition-all hover:bg-emerald-50">
                                <label class="text-[9px] font-black uppercase tracking-widest text-emerald-600 mb-2 block">Beginner</label>
                                <input 
                                    type="number" 
                                    name="beginner_count" 
                                    id="input_beginner"
                                    min="0" 
                                    value="{{ old('beginner_count', isset($editConfig) ? $editConfig->beginner_count : 0) }}" 
                                    class="w-full bg-transparent border-0 p-0 text-2xl font-black italic tracking-tighter text-slate-900 outline-none placeholder:text-slate-200" 
                                    onchange="updateTotal()"
                                    onkeyup="updateTotal()"
                                    required 
                                />
                            </div>
                            <div class="p-6 bg-amber-50/50 rounded-3xl border border-amber-100 group transition-all hover:bg-amber-50">
                                <label class="text-[9px] font-black uppercase tracking-widest text-amber-600 mb-2 block">Medium</label>
                                <input 
                                    type="number" 
                                    name="medium_count" 
                                    id="input_medium"
                                    min="0" 
                                    value="{{ old('medium_count', isset($editConfig) ? $editConfig->medium_count : 0) }}" 
                                    class="w-full bg-transparent border-0 p-0 text-2xl font-black italic tracking-tighter text-slate-900 outline-none placeholder:text-slate-200" 
                                    onchange="updateTotal()"
                                    onkeyup="updateTotal()"
                                    required 
                                />
                            </div>
                            <div class="p-6 bg-rose-50/50 rounded-3xl border border-rose-100 group transition-all hover:bg-rose-50">
                                <label class="text-[9px] font-black uppercase tracking-widest text-rose-600 mb-2 block">Hard</label>
                                <input 
                                    type="number" 
                                    name="hard_count" 
                                    id="input_hard"
                                    min="0" 
                                    value="{{ old('hard_count', isset($editConfig) ? $editConfig->hard_count : 0) }}" 
                                    class="w-full bg-transparent border-0 p-0 text-2xl font-black italic tracking-tighter text-slate-900 outline-none placeholder:text-slate-200" 
                                    onchange="updateTotal()"
                                    onkeyup="updateTotal()"
                                    required 
                                />
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-6 bg-slate-900 rounded-[2.5rem] shadow-xl">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white">
                                    <i class="fas fa-calculator text-xs"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 block">Quota Aggregation</span>
                                    <span class="text-xl font-black italic text-white tracking-tighter"><span id="total_display">0</span> QUESTIONS</span>
                                </div>
                            </div>
                            <x-forms.checkbox name="is_active" label="Operational" :checked="(isset($editConfig) && $editConfig->is_active) || old('is_active')" class="text-white font-black italic uppercase text-[10px] tracking-widest" />
                        </div>

                        <div class="flex gap-4 pt-4">
                            <x-ui.button type="submit" variant="primary" size="lg" class="flex-1 shadow-2xl shadow-blue-500/20" icon="fas fa-save">
                                {{ isset($editConfig) ? 'UPDATE LOGIC' : 'LEGALIZE LOGIC' }}
                            </x-ui.button>
                            <x-ui.button variant="ghost" href="{{ route('admin.question-banks.configure', $questionBank) }}" class="px-8 font-black italic text-slate-400 uppercase text-[10px] tracking-widest">RESET</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>

            {{-- Registry Side --}}
            <div class="space-y-8">
                <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
                    <x-slot:header>Active Distribution Registry</x-slot:header>
                    <x-ui.table>
                        <x-slot:thead>
                            <tr>
                                <x-ui.th>Target Module</x-ui.th>
                                <x-ui.th class="text-center">Complexity Quota (B/M/H)</x-ui.th>
                                <x-ui.th class="text-center">Status</x-ui.th>
                                <x-ui.th class="text-right">Aksi</x-ui.th>
                            </tr>
                        </x-slot:thead>
                        @forelse($configs as $config)
                        <tr class="group hover:bg-slate-50 transition-colors {{ isset($editConfig) && $editConfig->id == $config->id ? 'bg-blue-50/50 border-l-4 border-blue-600' : '' }}">
                            <td class="px-6 py-6 font-black italic text-slate-900 uppercase tracking-tighter text-sm">
                                {{ $config->material ? $config->material->title : 'Global Access' }}
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="flex justify-center gap-2">
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-black">{{ $config->beginner_count }}</span>
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-[10px] font-black">{{ $config->medium_count }}</span>
                                    <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded-lg text-[10px] font-black">{{ $config->hard_count }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="w-2 h-2 rounded-full {{ $config->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }} mx-auto"></div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex justify-end gap-2">
                                    <x-ui.button variant="ghost" size="sm" href="{{ route('admin.question-banks.configure', ['questionBank' => $questionBank, 'edit' => $config->id]) }}" icon="fas fa-edit" />
                                    <form action="{{ route('admin.question-bank-configs.delete', $config) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" onclick="return confirm('Hapus konfigurasi?')" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-16 text-center italic text-slate-400 text-xs">No active distribution configurations established.</td>
                        </tr>
                        @endforelse
                    </x-ui.table>
                </x-ui.card>

                <x-ui.alert variant="primary" :dismissible="false">
                    <div class="flex gap-4">
                        <i class="fas fa-microchip text-blue-600 mt-1"></i>
                        <p class="text-[10px] font-bold text-blue-900 uppercase tracking-widest leading-loose">
                            **Engine Protocol:** Konfigurasi ini menentukan jumlah butir soal yang ditarik secara cerdas oleh sistem saat mahasiswa menempuh evaluasi pada materi terkait.
                        </p>
                    </div>
                </x-ui.alert>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateTotal() {
            const b = parseInt(document.getElementById('input_beginner').value) || 0;
            const m = parseInt(document.getElementById('input_medium').value) || 0;
            const h = parseInt(document.getElementById('input_hard').value) || 0;
            document.getElementById('total_display').innerText = b + m + h;
        }
        document.addEventListener('DOMContentLoaded', updateTotal);
    </script>
    @endpush
</x-layouts.app>