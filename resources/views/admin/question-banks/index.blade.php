<x-layouts.app title="Bank Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Silo Bank Soal"
            subtitle="Pusat penyimpanan dan konfigurasi butir soal latihan."
        >
            <x-ui.button href="{{ route('admin.question-banks.create') }}" variant="primary" icon="fas fa-plus">Tambah Bank Soal</x-ui.button>
        </x-ui.page-header>

        {{-- Dynamic Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <x-ui.stat-card 
                title="Total Bank Soal" 
                :value="$questionBanks->count()" 
                icon="fas fa-database" 
                variant="primary"
                footer="Silo soal tersedia"
            />
            <x-ui.stat-card 
                title="Total Soal" 
                value="{{ \App\Models\Question::count() }}" 
                icon="fas fa-question-circle" 
                variant="success"
                footer="Butir soal dalam sistem"
            />
            <x-ui.stat-card 
                title="Konfigurasi Aktif" 
                value="{{ \App\Models\QuestionBankConfig::where('is_active', true)->count() }}" 
                icon="fas fa-sliders" 
                variant="orange"
                footer="Aturan soal berjalan"
            />
            <x-ui.stat-card 
                title="Data Author" 
                value="{{ $questionBanks->pluck('created_by')->unique()->count() }}" 
                icon="fas fa-user-pen" 
                variant="indigo"
                footer="Kontributor konten"
            />
        </div>

        <x-ui.card padding="p-0" class="overflow-hidden">
            <x-slot:header>
                <div class="flex flex-col md:flex-row justify-between items-center gap-6 w-full px-6 py-4">
                    <h6 class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400">Daftar Bank Soal</h6>
                    <form method="GET" action="{{ route('admin.question-banks.index') }}" class="w-full md:w-auto">
                        <div class="relative group">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari bank soal..." 
                                value="{{ request('search') }}"
                                class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none"
                            >
                        </div>
                    </form>
                </div>
            </x-slot:header>

            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th>Bank Soal</x-ui.th>
                        <x-ui.th>Materi Terkait</x-ui.th>
                        <x-ui.th>Author</x-ui.th>
                        <x-ui.th class="text-center">Aksi</x-ui.th>
                    </tr>
                </x-slot:thead>
                @forelse($questionBanks as $bank)
                <tr class="group hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-6">
                        <div>
                            <div class="font-bold text-slate-900 tracking-tight mb-1">{{ $bank->name }}</div>
                            <p class="text-[10px] font-medium text-slate-400 line-clamp-1 max-w-sm">
                                {{ Str::limit($bank->description, 80) }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-6 font-bold text-xs text-slate-600">
                         <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            {{ $bank->material->title ?? 'Tanpa Materi' }}
                         </div>
                    </td>
                    <td class="px-6 py-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold">
                                {{ substr($bank->creator ? $bank->creator->name : 'A', 0, 1) }}
                            </div>
                            <span class="text-xs font-bold text-slate-600">{{ $bank->creator ? $bank->creator->name : 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-6">
                        <div class="flex justify-center gap-2">
                            <x-ui.button variant="ghost" size="sm" href="{{ route('admin.question-banks.show', $bank) }}" icon="fas fa-eye" />
                            <x-ui.button variant="ghost" size="sm" href="{{ route('admin.question-banks.manage-questions', $bank) }}" icon="fas fa-list-check" color="emerald" />
                            <x-ui.button variant="ghost" size="sm" href="{{ route('admin.question-banks.configure', $bank) }}" icon="fas fa-cog" color="orange" />
                            <x-ui.button variant="ghost" size="sm" href="{{ route('admin.question-banks.edit', $bank) }}" icon="fas fa-edit" color="blue" />
                            
                            <form action="{{ route('admin.question-banks.destroy', $bank) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-200 hover:text-rose-500" icon="fas fa-trash" onclick="return confirm('Hapus bank soal ini?')" />
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-database text-slate-200 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 mb-2">Bank Soal Kosong</h3>
                        <p class="text-slate-400 text-sm max-w-xs mx-auto">Silakan buat silo baru untuk menyimpan butir-butir soal latihan.</p>
                    </td>
                </tr>
                @endforelse
            </x-ui.table>
            
            @if($questionBanks->hasPages())
                <div class="p-6 border-t border-slate-100">
                    {{ $questionBanks->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>