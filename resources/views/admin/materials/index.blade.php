<x-layouts.app title="Kelola Materi" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Kurikulum Materi"
            subtitle="Otoritas manajemen konten dan modul pembelajaran Pemrograman Berorientasi Objek."
        >
            <x-ui.button href="{{ route('admin.materials.create') }}" variant="primary" icon="fas fa-plus">Tambah Modul Baru</x-ui.button>
        </x-ui.page-header>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <x-ui.stat-card 
                title="Total Modul" 
                :value="$materials->count()" 
                icon="fas fa-layer-group" 
                variant="primary"
                footer="Modul instruksional aktif"
            />
            <x-ui.stat-card 
                title="Materi Baru" 
                value="{{ $materials->where('created_at', '>=', now()->subDays(30))->count() }}" 
                icon="fas fa-calendar-check" 
                variant="success"
                footer="Penambahan 30 hari terakhir"
            />
            <x-ui.stat-card 
                title="Korpus Media" 
                value="{{ $materials->sum(fn($m) => $m->media->count()) }}" 
                icon="fas fa-photo-film" 
                variant="indigo"
                footer="Total aset multimedia"
            />
        </div>

        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <x-slot:header>
                <div class="flex flex-col md:flex-row justify-between items-center gap-6 w-full px-6 py-4">
                    <h6 class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400">Inventaris Konten</h6>
                    <form method="GET" action="{{ route('admin.materials.index') }}" class="w-full md:w-auto">
                        <div class="relative group">
                            <i class="fas fa-dna absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Pindai materi..." 
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
                        <x-ui.th>Pratinjau Visual</x-ui.th>
                        <x-ui.th>Identitas Modul</x-ui.th>
                        <x-ui.th>Penulis Utama</x-ui.th>
                        <x-ui.th class="text-center">Sinkronisasi Awal</x-ui.th>
                        <x-ui.th class="text-right">Operasi</x-ui.th>
                    </tr>
                </x-slot:thead>
                <tbody>
                    @forelse($materials as $material)
                    <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-6 border-l-4 border-transparent group-hover:border-blue-600">
                            @if($material->media && $material->media->isNotEmpty())
                                <div class="w-20 h-14 rounded-xl overflow-hidden shadow-lg shadow-slate-200 group-hover:scale-105 transition-transform">
                                    <img src="{{ asset($material->media->first()->media_url) }}" alt="{{ $material->title }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-20 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-300 border border-dashed border-slate-200">
                                    <i class="fas fa-cube text-xl opacity-30"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-6 border-l-4 border-transparent group-hover:border-blue-600">
                            <div>
                                <div class="font-bold text-slate-900 tracking-tight mb-1">{{ $material->title }}</div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full uppercase tracking-widest">MOD-{{ str_pad($material->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    <p class="text-[10px] font-medium text-slate-400 line-clamp-1 max-w-sm">
                                        {{ Str::limit(strip_tags($material->content), 60) }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold shadow-lg shadow-slate-200">
                                    {{ substr($material->creator ? $material->creator->name : 'S', 0, 1) }}
                                </div>
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">{{ $material->creator ? $material->creator->name : 'System Admin' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="text-[10px] font-black italic text-slate-400 uppercase tracking-widest">
                                {{ $material->created_at->format('d.m.Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex justify-end gap-3">
                                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.materials.questions.index', $material->id) }}" icon="fas fa-vial" class="text-indigo-500 hover:text-indigo-600" />
                                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.materials.edit', $material->id) }}" icon="fas fa-pen-nib" />
                                <form action="{{ route('admin.materials.destroy', $material->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-300 hover:text-rose-500" icon="fas fa-trash-can" onclick="return confirm('Hapus materi ini secara permanen dari basis data?')" />
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-24 text-center">
                            <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                                <i class="fas fa-box-open text-slate-200 text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Kurikulum Kosong</h3>
                            <p class="text-slate-400 text-sm max-w-xs mx-auto mb-8">Basis data materi instruksional kosong. Lakukan injeksi modul baru untuk memulai siklus pembelajaran.</p>
                            <x-ui.button href="{{ route('admin.materials.create') }}" variant="primary" icon="fas fa-plus">Inisialisasi Kurikulum</x-ui.button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    </div>
    <x-admin.tutorial />
</x-layouts.app>
