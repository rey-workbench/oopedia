<x-layouts.app title="Kelola Soal Bank" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Pusat Injeksi Soal"
            subtitle="Pindahkan soal dari repositori umum ke dalam bank soal ini secara cerdas."
        >
            <x-ui.button href="{{ route('admin.question-banks.show', $questionBank) }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE BANK</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl overflow-hidden">
            <x-slot:header>Registri Injeksi Soal</x-slot:header>

            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                <form action="{{ route('admin.question-banks.manage-questions', $questionBank) }}" method="GET" class="w-full">
                    <div class="flex flex-col lg:flex-row gap-6 items-end">
                        <div class="flex-1 space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 italic pl-1">Pencarian Kueri</label>
                            <x-ui.input name="search" value="{{ request('search') }}" placeholder="Cari teks soal atau keyword..." class="bg-white" />
                        </div>
                        <div class="w-full lg:w-64 space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 italic pl-1">Tingkat Kesulitan</label>
                            <div class="relative">
                                <select name="difficulty" class="w-full px-5 py-3.5 bg-white border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none cursor-pointer uppercase">
                                    <option value="">SEMUA TINGKAT</option>
                                    <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>BEGINNER</option>
                                    <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>MEDIUM</option>
                                    <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>HARD</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-300">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                        <x-ui.button type="submit" variant="primary" class="h-[52px] px-10 shadow-lg shadow-blue-500/20 font-black italic tracking-tighter" icon="fas fa-filter">TERAPKAN FILTER</x-ui.button>
                    </div>
                </form>
            </div>

            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th class="w-1/2">Konten Soal</x-ui.th>
                        <x-ui.th>Tautan Modul</x-ui.th>
                        <x-ui.th class="text-center">Tingkat Kesulitan</x-ui.th>
                        <x-ui.th class="text-center">Tipe Evaluasi</x-ui.th>
                        <x-ui.th class="text-right">Aksi</x-ui.th>
                    </tr>
                </x-slot:thead>
                @forelse($questions as $question)
                <tr class="group hover:bg-blue-50/30 transition-colors">
                    <td class="px-8 py-6">
                        <div class="text-[11px] font-bold text-slate-700 italic leading-relaxed line-clamp-2">
                            {!! strip_tags($question->question_text) !!}
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                            <span class="text-[9px] font-black italic text-slate-400 uppercase tracking-widest">
                                {{ $question->material->title ?? 'SYSTEM CORE' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @php
                            $variant = $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger');
                        @endphp
                        <x-ui.badge :variant="$variant" size="xs" class="font-black italic">{{ strtoupper($question->difficulty) }}</x-ui.badge>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">{{ $question->formatted_type ?? strtoupper(str_replace('_', ' ', $question->question_type)) }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex justify-end">
                            <form action="{{ route('admin.question-banks.add-question', ['questionBank' => $questionBank, 'question' => $question]) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" variant="primary" size="sm" icon="fas fa-plus" class="shadow-md shadow-blue-500/20 bg-blue-600 hover:bg-blue-700 font-black italic tracking-tighter text-[10px]">INJEKSI DATA</x-ui.button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-24 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-folder-open text-slate-200 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-black italic uppercase tracking-tighter text-slate-900 mb-2">Tidak Ada Hasil Ditemukan</h3>
                        <p class="text-slate-400 text-[10px] uppercase tracking-widest font-bold">Repositori tidak memiliki soal yang belum ditambahkan atau filter terlalu spesifik.</p>
                    </td>
                </tr>
                @endforelse
            </x-ui.table>

            @if($questions->hasPages())
                <div class="p-8 border-t border-slate-100 bg-slate-50/30">
                    {{ $questions->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>