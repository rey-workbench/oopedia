<x-layouts.app title="Daftar Soal" theme="admin">
  <div class="space-y-12">
    <x-ui.page-header
      title="{{ $material ? 'Modul: ' . $material->title : 'Repositori Soal Global' }}"
      subtitle="Pusat penyimpanan data butir soal evaluasi yang terdaftar dalam ekosistem sistem."
    >
      <x-ui.button 
        href="{{ $material ? route('admin.materials.questions.create', $material) : route('admin.questions.create') }}" 
        variant="primary" 
        icon="fas fa-plus"
        class="shadow-xl shadow-blue-500/30 font-bold tracking-widest"
      >
        TAMBAH SOAL BARU
      </x-ui.button>
    </x-ui.page-header>

    <x-ui.card class="border-slate-100 shadow-2xl overflow-hidden">
      <x-slot:header>Pusat Pencarian & Filter Registri</x-slot:header>

      <div class="p-8 border-b border-slate-50 bg-slate-50/30">
        <form method="GET" action="{{ $material ? route('admin.materials.questions.index', $material) : route('admin.questions.index') }}">
          <div class="flex flex-col lg:flex-row gap-6 items-end">
            <div class="flex-1 space-y-2">
              <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Pencarian Kueri</label>
              <x-ui.input name="search" placeholder="Cari teks soal, tipe, atau pembuat..." value="{{ request('search') }}" class="bg-white" />
            </div>
            <div class="w-full lg:w-64 space-y-2">
              <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Tingkat Kesulitan</label>
              <div class="relative">
                <select name="difficulty" class="w-full px-5 py-3.5 bg-white border border-slate-200 rounded-2xl text-xs font-bold tracking-widest outline-none focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none cursor-pointer uppercase">
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
            <x-ui.button type="submit" variant="primary" class="h-[52px] px-10 shadow-lg shadow-blue-500/20 font-bold tracking-widest" icon="fas fa-search">JALANKAN PENCARIAN</x-ui.button>
          </div>
        </form>
      </div>

      <x-ui.table>
        <x-slot:thead>
          <tr>
            <x-ui.th class="px-8">Modul Asal</x-ui.th>
            <x-ui.th class="w-1/2">Wawasan Soal</x-ui.th>
            <x-ui.th class="text-center">Tipe Evaluasi</x-ui.th>
            <x-ui.th class="text-center">Tingkat Kesulitan</x-ui.th>
            <x-ui.th class="text-right px-8">Aksi</x-ui.th>
          </tr>
        </x-slot:thead>
        @forelse($questions as $question)
        <tr class="group hover:bg-blue-50/30 transition-colors">
          <td class="px-8 py-8 align-top">
            <div class="flex items-center gap-3">
              <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
              <div>
                <span class="text-[10px] font-bold text-slate-900 uppercase tracking-widest block">{{ $question->material->title }}</span>
                @if($question->subMaterial)
                  <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block mt-1">SUB: {{ $question->subMaterial->title }}</span>
                @endif
              </div>
            </div>
          </td>
          <td class="px-8 py-8">
            <div class="space-y-6">
              <div class="text-[11px] font-bold text-slate-700 leading-relaxed line-clamp-3">
                {!! strip_tags($question->question_text) !!}
              </div>
              
              {{-- Integrated Answers Insight --}}
              <div class="p-5 bg-slate-950 rounded-[1.5rem] border border-slate-800 shadow-inner">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 block mb-3">Registri Kunci Jawaban</span>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  @foreach($question->answers as $answer)
                    <div class="flex items-center gap-2">
                      <div class="w-1.5 h-1.5 rounded-full {{ $answer->is_correct ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-700' }}"></div>
                      <span class="text-[10px] font-bold {{ $answer->is_correct ? 'text-emerald-400' : 'text-slate-400' }} truncate uppercase tracking-widest">{{ $answer->answer_text }}</span>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </td>
          <td class="px-8 py-8 text-center align-top">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">{{ $question->formatted_type }}</span>
          </td>
          <td class="px-8 py-8 text-center align-top">
            @php
              $variant = $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger');
            @endphp
            <x-ui.badge :variant="$variant" size="xs" class="font-bold px-3 py-1">{{ strtoupper($question->difficulty) }}</x-ui.badge>
          </td>
          <td class="px-8 py-8 align-top">
            <div class="flex justify-end gap-2">
              @php
                $editRoute = $material ? route('admin.materials.questions.edit', ['material' => $material, 'question' => $question]) : route('admin.questions.edit', $question);
                $deleteRoute = $material ? route('admin.materials.questions.destroy', ['material' => $material, 'question' => $question]) : route('admin.questions.destroy', $question);
              @endphp
              <x-ui.button variant="ghost" size="sm" href="{{ $editRoute }}" icon="fas fa-edit" class="text-slate-300 hover:text-blue-600" />
              <form action="{{ $deleteRoute }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-200 hover:text-rose-500" icon="fas fa-trash-alt" onclick="return confirm('Hapus permanen soal ini?')" />
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
            <h3 class="text-lg font-bold uppercase tracking-widest text-slate-900 mb-2">Tidak Ada Soal Ditemukan</h3>
            <p class="text-slate-400 text-[10px] uppercase tracking-widest font-bold">Basis data soal kosong atau filter terlalu spesifik.</p>
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
