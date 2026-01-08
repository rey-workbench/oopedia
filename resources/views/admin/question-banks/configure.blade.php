<x-layouts.app title="Konfigurasi Bank Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Konfigurasi Mesin Distribusi"
            subtitle="Atur rasio kemunculan soal berdasarkan tingkat kesulitan untuk engine adaptif."
        >
            <x-ui.button href="{{ route('admin.question-banks.show', $questionBank) }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE WAWASAN</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl overflow-hidden p-0">
            <x-slot:header>Pusat Komando Optimasi</x-slot:header>

            <div class="grid grid-cols-1 lg:grid-cols-12">
                {{-- Command Input Section --}}
                <div class="lg:col-span-5 border-r border-slate-50 bg-slate-50/20 p-8 space-y-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-blue-600/10 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-terminal text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 italic">Arsitek Logika</h4>
                    </div>

                    <form action="{{ route('admin.question-banks.store-config', $questionBank) }}" method="POST" class="space-y-8">
                        @csrf
                        @if(isset($editConfig))
                            <input type="hidden" name="config_id" value="{{ $editConfig->id }}">
                        @endif

                        <x-forms.form-group label="Modul Kurikulum Target" name="material_id" required>
                            @if(isset($editConfig))
                                <div class="px-5 py-4 bg-white border border-slate-200 rounded-2xl flex items-center gap-4">
                                    <i class="fas fa-link text-blue-600 text-[10px]"></i>
                                    <span class="text-xs font-black italic tracking-tighter text-slate-900 uppercase">{{ $editConfig->material->title }}</span>
                                    <input type="hidden" name="material_id" value="{{ $editConfig->material_id }}">
                                </div>
                            @else
                                <div class="relative">
                                    <select name="material_id" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none cursor-pointer uppercase" required>
                                        <option value="">-- HUBUNGKAN KE MODUL --</option>
                                        @foreach($materials as $material)
                                            <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>{{ strtoupper($material->title) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-300">
                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            @endif
                        </x-forms.form-group>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex flex-col items-center">
                                <label class="text-[8px] font-black uppercase tracking-widest text-emerald-600 mb-2">Beginner</label>
                                <input 
                                    type="number" 
                                    name="beginner_count" 
                                    id="input_beginner"
                                    min="0" 
                                    value="{{ old('beginner_count', isset($editConfig) ? $editConfig->beginner_count : 0) }}" 
                                    class="w-full bg-transparent border-0 text-center text-xl font-black italic tracking-tighter text-slate-900 outline-none p-0" 
                                    onchange="updateTotal()"
                                    onkeyup="updateTotal()"
                                    required 
                                />
                            </div>
                            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex flex-col items-center">
                                <label class="text-[8px] font-black uppercase tracking-widest text-amber-600 mb-2">Medium</label>
                                <input 
                                    type="number" 
                                    name="medium_count" 
                                    id="input_medium"
                                    min="0" 
                                    value="{{ old('medium_count', isset($editConfig) ? $editConfig->medium_count : 0) }}" 
                                    class="w-full bg-transparent border-0 text-center text-xl font-black italic tracking-tighter text-slate-900 outline-none p-0" 
                                    onchange="updateTotal()"
                                    onkeyup="updateTotal()"
                                    required 
                                />
                            </div>
                            <div class="p-4 bg-rose-50 rounded-2xl border border-rose-100 flex flex-col items-center">
                                <label class="text-[8px] font-black uppercase tracking-widest text-rose-600 mb-2">Hard</label>
                                <input 
                                    type="number" 
                                    name="hard_count" 
                                    id="input_hard"
                                    min="0" 
                                    value="{{ old('hard_count', isset($editConfig) ? $editConfig->hard_count : 0) }}" 
                                    class="w-full bg-transparent border-0 text-center text-xl font-black italic tracking-tighter text-slate-900 outline-none p-0" 
                                    onchange="updateTotal()"
                                    onkeyup="updateTotal()"
                                    required 
                                />
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-6 bg-slate-900 rounded-[2rem] shadow-xl">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white">
                                    <i class="fas fa-calculator text-[10px]"></i>
                                </div>
                                <div>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-500 block mb-0.5">Agregasi Kuota</span>
                                    <span class="text-lg font-black italic text-white tracking-tighter"><span id="total_display">0</span> SOAL</span>
                                </div>
                            </div>
                            <x-forms.checkbox name="is_active" label="OPERASIONAL" :checked="(isset($editConfig) && $editConfig->is_active) || old('is_active')" class="text-white font-black italic uppercase text-[9px] tracking-[0.2em]" />
                        </div>

                        <div class="flex gap-4 pt-4">
                            <x-ui.button type="submit" variant="primary" size="lg" class="flex-1 shadow-xl shadow-blue-500/20 font-black italic tracking-tighter" icon="fas fa-save">
                                {{ isset($editConfig) ? 'SINKRONISASI LOGIKA' : 'LEGALKAN LOGIKA' }}
                            </x-ui.button>
                            <x-ui.button variant="ghost" href="{{ route('admin.question-banks.configure', $questionBank) }}" class="px-6 text-slate-400 font-black italic uppercase text-[9px] tracking-widest">RESET</x-ui.button>
                        </div>
                    </form>

                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3 px-4 py-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <i class="fas fa-info-circle text-blue-600 text-[10px] mt-0.5"></i>
                            <p class="text-[9px] font-bold text-blue-900 uppercase tracking-widest leading-relaxed">
                                Konfigurasi ini menentukan algoritma penarikan soal saat evaluasi materi.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Registry Table Section --}}
                <div class="lg:col-span-7 bg-white">
                    <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-indigo-600/10 text-indigo-600 flex items-center justify-center">
                                <i class="fas fa-list-ul text-xs"></i>
                            </div>
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 italic">Registri Aktif</h4>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <x-ui.table>
                            <x-slot:thead>
                                <tr>
                                    <x-ui.th class="px-8">Modul Target</x-ui.th>
                                    <x-ui.th class="text-center">Kuota (P/S/S)</x-ui.th>
                                    <x-ui.th class="text-center">Status</x-ui.th>
                                    <x-ui.th class="text-right px-8">Aksi</x-ui.th>
                                </tr>
                            </x-slot:thead>
                            @forelse($configs as $config)
                            <tr class="group hover:bg-slate-50 transition-colors {{ isset($editConfig) && $editConfig->id == $config->id ? 'bg-blue-50/50 border-l-4 border-blue-600' : '' }}">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black italic text-slate-900 uppercase tracking-tighter">
                                            {{ $config->material ? $config->material->title : 'Global Access' }}
                                        </span>
                                        <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest mt-1">MODULE ID: #{{ $config->material_id ?? 'GLB' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex justify-center gap-1.5">
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-md text-[9px] font-black italic">{{ $config->beginner_count }}</span>
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-md text-[9px] font-black italic">{{ $config->medium_count }}</span>
                                        <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded-md text-[9px] font-black italic">{{ $config->hard_count }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-2 h-2 rounded-full {{ $config->is_active ? 'bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)] animate-pulse' : 'bg-slate-300' }}"></div>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic">{{ $config->is_active ? 'AKTIF' : 'PASIF' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2">
                                        <x-ui.button variant="ghost" size="sm" href="{{ route('admin.question-banks.configure', ['questionBank' => $questionBank, 'edit' => $config->id]) }}" icon="fas fa-edit" class="text-slate-300 hover:text-blue-600" />
                                        <form action="{{ route('admin.question-bank-configs.delete', $config) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-200 hover:text-rose-500" icon="fas fa-trash-alt" onclick="return confirm('Hapus konfigurasi?')" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-20 text-center">
                                    <div class="text-slate-200 mb-4">
                                        <i class="fas fa-database text-3xl opacity-20"></i>
                                    </div>
                                    <p class="text-[10px] font-black italic text-slate-300 uppercase tracking-widest">Tidak ada konfigurasi distribusi aktif yang dibuat.</p>
                                </td>
                            </tr>
                            @endforelse
                        </x-ui.table>
                    </div>
                </div>
            </div>
        </x-ui.card>
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