<x-layouts.app title="Adaptive Rules" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Logic Engine"
            subtitle="Konfigurasi sistem pakar Forward Chaining untuk adaptivitas kuis."
        >
            <x-ui.button href="{{ route('admin.adaptive-rules.create') }}" variant="primary" icon="fas fa-plus">Tambah Rule</x-ui.button>
        </x-ui.page-header>

        {{-- Filter Section --}}
        <x-ui.card class="bg-slate-900 border-slate-800 shadow-2xl">
            <form method="GET" action="{{ route('admin.adaptive-rules.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic ml-4">Cari Nama Rule</label>
                        <div class="relative group">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors"></i>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search rules..." 
                                value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-sm font-bold text-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"
                            >
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic ml-4">Filter Materi</label>
                        <select 
                            name="material_id" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-sm font-bold text-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer"
                        >
                            <option value="">Semua Materi</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>
                                    {{ $material->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <x-ui.button type="submit" variant="primary" class="w-full py-3.5 shadow-lg shadow-blue-500/20">Apply Filter</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0" class="overflow-hidden">
            <x-slot:header>
                <h6 class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400">Daftar Aturan Adaptif</h6>
            </x-slot:header>

            <x-ui.table>
                <thead>
                    <tr>
                        <x-ui.th>ID / Pri</x-ui.th>
                        <x-ui.th>Logic Rule</x-ui.th>
                        <x-ui.th>Materi</x-ui.th>
                        <x-ui.th>Condition (IF)</x-ui.th>
                        <x-ui.th>Action (THEN)</x-ui.th>
                        <x-ui.th class="text-center">Aksi</x-ui.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-8">
                            <div class="flex flex-col gap-1 items-center">
                                <span class="text-[10px] font-black text-slate-300 italic">#{{ $rule->id }}</span>
                                <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs font-bold shadow-lg shadow-slate-200">
                                    {{ $rule->priority }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-8">
                            <div>
                                <div class="font-bold text-slate-900 tracking-tight mb-1">{{ $rule->name }}</div>
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-1.5 rounded-full {{ $rule->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest {{ $rule->is_active ? 'text-emerald-500' : 'text-slate-400' }}">
                                        {{ $rule->is_active ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-8">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ $rule->material ? $rule->material->title : 'GLOBAL SYSTEM' }}
                            </span>
                        </td>
                        <td class="px-6 py-8">
                            <div class="space-y-2">
                                @if(!empty($rule->conditions) && is_array($rule->conditions))
                                    @foreach($rule->conditions as $index => $condition)
                                        <div class="flex items-center gap-2 text-[10px] font-bold">
                                            @if($index > 0)
                                                <span class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-400 font-black italic">AND</span>
                                            @endif
                                            <span class="text-blue-600 uppercase">{{ $condition['key'] ?? $condition['type'] ?? 'ATTR' }}</span>
                                            <span class="text-slate-400 font-black italic">{{ $condition['operator'] ?? '' }}</span>
                                            <span class="px-2 py-0.5 bg-slate-900 text-white rounded-md">{{ $condition['value'] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-[10px] font-black italic text-slate-300">NO CONDITION</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-8">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-rose-500">{{ \App\Models\AdaptiveRule::ACTION_TYPES[$rule->action_type] ?? $rule->action_type }}</span>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-arrow-turn-down text-slate-300 text-xs ml-2"></i>
                                    <span class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-bold uppercase tracking-tight">{{ $rule->action_value }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-8">
                            <div class="flex justify-center gap-2">
                                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.adaptive-rules.show', $rule) }}" icon="fas fa-search" />
                                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.adaptive-rules.edit', $rule) }}" icon="fas fa-pen-nib" />
                                
                                <form action="{{ route('admin.adaptive-rules.destroy', $rule) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="danger" size="sm" icon="fas fa-trash-can" onclick="return confirm('Hapus aturan ini?')" />
                                </form>
                                
                                <form action="{{ route('admin.adaptive-rules.toggle-status', $rule) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.button type="submit" variant="ghost" size="sm" icon="{{ $rule->is_active ? 'fas fa-toggle-on' : 'fas fa-toggle-off' }}" color="{{ $rule->is_active ? 'emerald' : 'slate' }}" />
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <i class="fas fa-robot text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-black italic uppercase tracking-tighter text-slate-900 mb-2">No Passive Logic Found</h3>
                            <p class="text-slate-400 text-xs max-w-xs mx-auto">Sistem belum memiliki aturan inferensi. Tambahkan rule untuk mengaktifkan adaptivitas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            @if($rules->hasPages())
                <div class="p-6 border-t border-slate-100">
                    {{ $rules->links() }}
                </div>
            @endif
        </x-ui.card>

        {{-- Info Panel --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <x-ui.card padding="p-8" class="bg-gradient-to-br from-indigo-600 to-blue-700 text-white border-0 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-20 -bottom-20 opacity-10">
                    <i class="fas fa-brain text-[250px]"></i>
                </div>
                <h6 class="text-xl font-bold tracking-tight mb-4 flex items-center gap-3">
                    <i class="fas fa-code-merge"></i>
                    Forward Chaining Logic
                </h6>
                <p class="text-sm font-medium leading-relaxed opacity-90 mb-6">
                    Metode inferensi yang dimulai dari fakta yang diketahui untuk menarik kesimpulan baru. Dalam OOPedia, sistem mengevaluasi atribut performa mahasiswa secara real-time.
                </p>
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center">1</div>
                        <span>Evaluasi Atribut (IF Condition)</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center">2</div>
                        <span>Eksekusi Perintah (THEN Action)</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center">3</div>
                        <span>Prioritas menentukan urutan evaluasi rule.</span>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card padding="p-8" class="bg-slate-900 text-white border-0 shadow-2xl">
                <h6 class="text-xl font-bold tracking-tight mb-4 text-emerald-400">System Parameters</h6>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Active Rules</span>
                        <div class="text-3xl font-black italic tracking-tighter">{{ $rules->where('is_active', true)->count() }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Global Handlers</span>
                        <div class="text-3xl font-black italic tracking-tighter">{{ $rules->where('material_id', null)->count() }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Total Triggers</span>
                        <div class="text-3xl font-black italic tracking-tighter">{{ $rules->sum(fn($r) => count($r->conditions ?? [])) }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Engine Status</span>
                        <div class="text-xs font-black uppercase text-emerald-500 bg-emerald-500/10 px-3 py-1 rounded-lg w-fit mt-2">Nominal</div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
