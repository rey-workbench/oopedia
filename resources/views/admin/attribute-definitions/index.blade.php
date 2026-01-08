<x-layouts.app title="Kamus Atribut" theme="admin">
    <div class="space-y-12" x-data="{ openModal: false }">
        <x-ui.page-header
            title="Kamus Variabel Sistem"
            subtitle="Daftar parameter teknis yang digunakan untuk mengontrol perilaku adaptif instruksional."
        >
            <x-ui.button @click="openModal = true" variant="primary" icon="fas fa-plus">Register New Attribute</x-ui.button>
        </x-ui.page-header>

        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th>Atribut Parameter</x-ui.th>
                        <x-ui.th class="text-center">Tipe Data</x-ui.th>
                        <x-ui.th class="text-center">Mekanisme Sumber</x-ui.th>
                        <x-ui.th class="text-right">Deskripsi Fungsional</x-ui.th>
                    </tr>
                </x-slot:thead>
                @forelse($attributes as $groupName => $groupAttributes)
                    <tr class="bg-slate-900/[0.03]">
                        <td colspan="4" class="px-6 py-4">
                            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-500 italic">{{ strtoupper($groupName) }} System Variables</span>
                        </td>
                    </tr>
                    @foreach($groupAttributes as $attr)
                    <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-6 font-poppins">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-blue-600">
                                    <i class="fas {{ $attr->is_computed ? 'fa-calculator' : 'fa-database' }} text-xs"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 tracking-tight text-sm leading-none mb-1">{{ $attr->label }}</div>
                                    <span class="text-[9px] font-bold text-slate-400 font-mono tracking-widest uppercase">{{ $attr->key }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="px-3 py-1 bg-slate-100 rounded-lg text-[9px] font-black text-slate-600 uppercase tracking-widest">{{ $attr->type }}</span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            @if($attr->is_computed)
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100">
                                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></div>
                                    <span class="text-[9px] font-bold uppercase tracking-widest">COMPUTED LOGIC</span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-600"></div>
                                    <span class="text-[9px] font-bold uppercase tracking-widest">RAW DATA INPUT</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-6 text-right">
                            <p class="text-[10px] font-bold text-slate-400 italic leading-relaxed max-w-sm ml-auto">
                                {{ $attr->description ?: 'No formal functional description assigned to this system parameter.' }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-book-dead text-slate-200 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-black italic uppercase tracking-tighter text-slate-900 mb-2">Lexicon Empty</h3>
                            <p class="text-slate-400 text-sm max-w-xs mx-auto">Sistem belum memiliki definisi atribut terdaftar. Hubungi Tech Admin.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
        
        <x-ui.alert variant="primary" :dismissible="false">
            <div class="flex items-center gap-6">
                <div class="w-12 h-12 bg-blue-600/10 rounded-2xl flex items-center justify-center shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div class="text-[11px] font-medium text-blue-900 leading-relaxed italic font-poppins">
                    <span class="font-black uppercase tracking-widest block mb-1">Architecture Reference</span>
                    Atribut **Raw Data** ditangkap secara otomatis dari sensor aktivitas belajar mahasiswa. Atribut **Computed** dihasilkan melalui kalkulasi Forward Chaining pada Logic Engine berdasarkan formula yang didefinisikan.
                </div>
            </div>
        </x-ui.alert>

        {{-- Add Attribute Modal --}}
        <div x-show="openModal" class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="openModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                
                <div class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100" x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <div class="bg-slate-900 px-8 py-10 text-white relative">
                        <div class="absolute right-8 top-10">
                            <button @click="openModal = false" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
                        </div>
                        <h6 class="text-xl font-bold tracking-tight mb-2 uppercase">Register Attr</h6>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Initialize new system variable</p>
                    </div>
                    
                    <form action="{{ url('admin/attribute-definitions') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase text-slate-400 italic">Ident Key</label>
                                    <input type="text" name="key" placeholder="e.g. score_avg" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold outline-none focus:ring-4 focus:ring-blue-100 transition-all font-mono" required>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase text-slate-400 italic">Label</label>
                                    <input type="text" name="label" placeholder="e.g. Rata-rata Skor" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold outline-none focus:ring-4 focus:ring-blue-100 transition-all" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase text-slate-400 italic">Data Type</label>
                                    <select name="type" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer">
                                        <option value="integer">INTEGER</option>
                                        <option value="float">FLOAT</option>
                                        <option value="string">STRING</option>
                                        <option value="boolean">BOOLEAN</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase text-slate-400 italic">Default Val</label>
                                    <input type="text" name="default_value" value="0" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold outline-none focus:ring-4 focus:ring-blue-100 transition-all" required>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase text-slate-400 italic">Functional Description</label>
                                <textarea name="description" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold outline-none focus:ring-4 focus:ring-blue-100 transition-all h-24" placeholder="Purpose of this attribute..."></textarea>
                            </div>
                        </div>

                        <div class="pt-4 flex gap-4">
                            <x-ui.button type="submit" variant="primary" class="flex-1 py-4 shadow-xl shadow-blue-500/20" icon="fas fa-save">Authorize Attr</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
