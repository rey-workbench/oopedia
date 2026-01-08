<x-layouts.app title="Progress Mahasiswa" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Wawasan Performa Siswa"
            subtitle="Analisis trajectory pembelajaran untuk entitas {{ $student->name }}."
        >
            <x-ui.button href="{{ route('admin.students.index') }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE DAFTAR</x-ui.button>
        </x-ui.page-header>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <x-ui.stat-card 
                title="Lintasan Pembelajaran" 
                value="{{ number_format($materials->avg('progress') ?? 0, 1) }}%" 
                icon="fas fa-chart-line" 
                variant="primary"
                footer="Rata-rata penyelesaian modul"
            />
            <x-ui.stat-card 
                title="Modul Berhasil Diselesaikan" 
                value="{{ $materials->where('progress', 100)->count() }} / {{ $materials->count() }}" 
                icon="fas fa-check-double" 
                variant="success"
                footer="Penyelesaian 100% tercapai"
            />
            <x-ui.stat-card 
                title="Sisa Unit Tantangan" 
                value="{{ array_sum(array_column($missingQuestionsByMaterial, 'missing_count')) }}" 
                icon="fas fa-bolt" 
                variant="danger"
                footer="Jawaban benar tertunda"
            />
        </div>

        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <x-slot:header>
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                    <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400">Matriks Penguasaan Konten</h6>
                </div>
            </x-slot:header>

            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th>Skema Modul</x-ui.th>
                        <x-ui.th>Tingkat Penguasaan</x-ui.th>
                        <x-ui.th class="text-center">Status Protokol</x-ui.th>
                        <x-ui.th class="text-right">Interaksi Terakhir</x-ui.th>
                    </tr>
                </x-slot:thead>
                @forelse($materials as $material)
                <tr class="group hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-6">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-8 bg-slate-900 rounded-full"></div>
                            <span class="text-[10px] font-black italic text-slate-900 uppercase tracking-tighter">{{ $material->title }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-6">
                        <div class="flex flex-col gap-2 min-w-[150px]">
                            <div class="flex justify-between text-[8px] font-black uppercase italic tracking-widest text-slate-400">
                                <span>Penguasaan</span>
                                <span>{{ is_numeric($material->progress) ? (int)$material->progress : 0 }}%</span>
                            </div>
                            <x-ui.progress-bar :value="is_numeric($material->progress) ? $material->progress : 0" size="xs" :showPercentage="false" />
                        </div>
                    </td>
                    <td class="px-6 py-6 text-center">
                        @if(is_numeric($material->progress) && $material->progress == 100)
                            <x-ui.badge variant="success" size="xs">STABIL</x-ui.badge>
                        @elseif(is_numeric($material->progress) && $material->progress > 0)
                            <x-ui.badge variant="warning" size="xs">PROSES</x-ui.badge>
                        @else
                            <x-ui.badge variant="secondary" size="xs">INAKTIF</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-6 py-6 text-right">
                        <span class="text-[10px] font-black italic text-slate-400 uppercase tracking-widest">
                            {{ $material->last_accessed ? $material->last_accessed->diffForHumans() : 'Tidak Ada Log Ditemukan' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-20 text-center text-slate-400 italic text-xs uppercase font-black tracking-widest">Tidak Ada Log Interaksi Ditemukan</td>
                </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if(count($missingQuestionsByMaterial) > 0)
        <x-ui.card padding="p-0" class="overflow-hidden border-rose-100 shadow-2xl bg-rose-50/5">
            <x-slot:header>
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 bg-rose-500 rounded-full"></div>
                    <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-rose-500">Unit Tantangan Belum Terpecahkan</h6>
                </div>
            </x-slot:header>

            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th>Modul Kritis</x-ui.th>
                        <x-ui.th class="text-right">Jumlah Anomali</x-ui.th>
                    </tr>
                </x-slot:thead>
                @foreach($missingQuestionsByMaterial as $item)
                <tr class="group hover:bg-rose-50/50 transition-colors">
                    <td class="px-6 py-6 font-black italic text-rose-900 text-xs tracking-tighter uppercase">{{ $item['material_title'] }}</td>
                    <td class="px-6 py-6 text-right">
                        <x-ui.badge variant="danger" size="xs">{{ $item['missing_count'] }} MENUNGGU</x-ui.badge>
                    </td>
                </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>
        @endif
    </div>
    <x-admin.tutorial />
</x-layouts.app>
