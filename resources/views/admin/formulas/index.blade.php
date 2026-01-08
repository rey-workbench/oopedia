<x-layouts.app title="Daftar Formula" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Sistem Kalkulus Adaptif"
            subtitle="Kelola formula logika untuk komputasi atribut dinamis dan pengambilan keputusan sistem."
        >
            <x-ui.button href="{{ route('admin.formulas.create') }}" variant="primary" icon="fas fa-plus">Buat Formula Baru</x-ui.button>
        </x-ui.page-header>

        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <x-slot:header>
                <div class="flex flex-col md:flex-row justify-between items-center gap-6 w-full">
                    <h6 class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400">Formula Registry</h6>
                    <form method="GET" action="{{ route('admin.formulas.index') }}" class="w-full md:w-auto flex gap-4">
                        <div class="relative group">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari formula..." 
                                value="{{ request('search') }}"
                                class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none"
                            >
                        </div>
                        <select name="scope" onchange="this.form.submit()" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 outline-none appearance-none cursor-pointer">
                            <option value="">SEMUA SCOPE</option>
                            @foreach(\App\Models\Formula::SCOPES as $key => $label)
                                <option value="{{ $key }}" {{ request('scope') == $key ? 'selected' : '' }}>
                                    {{ strtoupper($label) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </x-slot:header>

            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th>Formula Identity</x-ui.th>
                        <x-ui.th>Logic Expression</x-ui.th>
                        <x-ui.th class="text-center">Return Type</x-ui.th>
                        <x-ui.th class="text-center">Execution Scope</x-ui.th>
                        <x-ui.th class="text-center">System Status</x-ui.th>
                        <x-ui.th class="text-right">Aksi</x-ui.th>
                    </tr>
                </x-slot:thead>
                @forelse($formulas as $formula)
                <tr class="group hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-6 border-l-4 border-transparent group-hover:border-blue-600">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:text-blue-600 transition-colors">
                                <i class="fas fa-square-root-variable text-xs"></i>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 tracking-tight text-sm">{{ $formula->name }}</div>
                                <span class="text-[9px] font-bold text-slate-400 font-mono tracking-widest uppercase">{{ $formula->key }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-6">
                        <div class="bg-slate-900 text-emerald-400 px-4 py-2 rounded-xl border border-slate-800 font-mono text-[10px] shadow-lg">
                            <span class="opacity-50 text-slate-500">f(x) =</span> {{ Str::limit($formula->expression, 40) }}
                        </div>
                    </td>
                    <td class="px-6 py-6 text-center">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-bold uppercase tracking-widest border border-blue-100">{{ $formula->return_type }}</span>
                    </td>
                    <td class="px-6 py-6 text-center">
                        <x-ui.badge variant="secondary" size="xs">{{ strtoupper($formula->scope) }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-6 text-center">
                        <div class="flex flex-col items-center gap-1">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $formula->is_active ? 'bg-emerald-500 animate-pulse outline outline-4 outline-emerald-500/20' : 'bg-slate-400' }}"></div>
                                <span class="text-[10px] font-bold uppercase tracking-widest {{ $formula->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $formula->is_active ? 'ACTIVE' : 'OFF' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-6">
                        <div class="flex justify-end gap-2">
                            <x-ui.button variant="ghost" size="sm" href="{{ route('admin.formulas.edit', $formula) }}" icon="fas fa-edit" />
                            <form action="{{ route('admin.formulas.toggle-status', $formula) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <x-ui.button type="submit" variant="ghost" size="sm" icon="{{ $formula->is_active ? 'fas fa-power-off text-rose-500' : 'fas fa-play text-emerald-500' }}" />
                            </form>
                            <form action="{{ route('admin.formulas.destroy', $formula) }}" method="POST" class="inline" onsubmit="return confirm('Hapus formula permanen?')">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-300 hover:text-rose-600" icon="fas fa-trash-alt" />
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-calculator text-slate-200 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 mb-2">Computational Void</h3>
                        <p class="text-slate-400 text-sm max-w-xs mx-auto">Sistem saat ini berjalan tanpa formula perhitungan dinamis.</p>
                    </td>
                </tr>
                @endforelse

            @if($formulas->hasPages())
                <div class="p-6 border-t border-slate-100">
                    {{ $formulas->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
