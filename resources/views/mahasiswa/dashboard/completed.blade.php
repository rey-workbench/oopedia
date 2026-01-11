<x-layouts.app theme="mahasiswa">
  <x-slot:title>Materi Selesai</x-slot:title>

    <x-ui.page-header
      title="Materi Selesai"
      subtitle="Semua materi yang telah Anda kuasai sepenuhnya."
    >
      <x-slot:actions>
        <x-ui.button href="{{ route('mahasiswa.dashboard') }}" variant="ghost" icon="fas fa-arrow-left">
          Dashboard
        </x-ui.button>
      </x-slot:actions>
    </x-ui.page-header>

      <div class="mt-10">
        @if(count($materialsWithStats) == 0)
          <div class="text-center py-24 bg-white rounded-[2.5rem] shadow-sm border border-gray-100">
            <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner">
              <i class="fas fa-check-circle text-5xl"></i>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-4 uppercase tracking-widest">Belum Ada Materi Selesai</h3>
            <p class="text-gray-500 mb-10 max-w-md mx-auto font-medium">
              Ayo tantang diri Anda dan selesaikan semua tingkatan soal untuk melihat progres Anda di sini!
            </p>
            <x-ui.button href="{{ route('mahasiswa.materials.index') }}" variant="primary" class="px-10 py-4 rounded-2xl font-bold uppercase transition-all shadow-xl shadow-blue-100" icon="fa-book">
              Cari Materi Baru
            </x-ui.button>
          </div>
        @else
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            @foreach($materialsWithStats as $materialData)
              @php
                $material = $materialData['material'];
                $stats = $materialData['stats'];
              @endphp
              <div class="group h-full">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 h-full flex flex-col">
                  <div class="p-8 bg-gradient-to-br from-emerald-500 to-teal-600 relative overflow-hidden text-white">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                      <i class="fas fa-trophy text-8xl"></i>
                    </div>
                    <div class="relative z-10 flex items-center gap-6">
                      <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner border border-white/30 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-check-double text-2xl"></i>
                      </div>
                      <div class="min-h-[4.5rem] flex flex-col justify-center">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-100 mb-1">Mastered Material</div>
                        <h4 class="text-2xl font-bold tracking-widest leading-tight">{{ $material->title }}</h4>
                      </div>
                    </div>
                  </div>

                  <div class="p-8 flex-1 flex flex-col">
                    <!-- Overall Stats Header -->
                    <div class="flex items-end justify-between mb-8">
                      <div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Soal Terjawab</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['overall']['correct'] }}<span class="text-sm text-gray-400 not- ml-2">/ {{ $stats['overall']['total'] }} SOAL</span></div>
                      </div>
                      <div class="text-right">
                        <div class="text-4xl font-bold text-emerald-600">100%</div>
                      </div>
                    </div>

                    <div class="space-y-4 mb-10">
                      {{-- Beginner --}}
                      <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 group-hover:bg-blue-50/50 group-hover:border-blue-100 transition-colors">
                        <div class="flex justify-between items-center mb-2">
                          <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-blue-600">Beginner Mastery</span>
                          <span class="text-xs font-bold text-gray-900">{{ $stats['beginner']['correct'] }}/{{ $stats['beginner']['configured_total'] }}</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                          <div class="h-full bg-blue-500 rounded-full" style="width: 100%"></div>
                        </div>
                      </div>

                      {{-- Medium --}}
                      <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 group-hover:bg-amber-50/50 group-hover:border-amber-100 transition-colors">
                        <div class="flex justify-between items-center mb-2">
                          <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-amber-600">Medium Mastery</span>
                          <span class="text-xs font-bold text-gray-900">{{ $stats['medium']['correct'] }}/{{ $stats['medium']['configured_total'] }}</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                          <div class="h-full bg-amber-500 rounded-full" style="width: 100%"></div>
                        </div>
                      </div>

                      {{-- Hard --}}
                      <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 group-hover:bg-rose-50/50 group-hover:border-rose-100 transition-colors">
                        <div class="flex justify-between items-center mb-2">
                          <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-rose-600">Hard Mastery</span>
                          <span class="text-xs font-bold text-gray-900">{{ $stats['hard']['correct'] }}/{{ $stats['hard']['configured_total'] }}</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                          <div class="h-full bg-rose-500 rounded-full" style="width: 100%"></div>
                        </div>
                      </div>
                    </div>

                    <div class="mt-auto">
                      <x-ui.button href="{{ route('mahasiswa.materials.show', $material->id) }}" variant="secondary" class="w-full py-4 rounded-2xl font-bold uppercase outline outline-2 outline-gray-100 group-hover:outline-emerald-200 group-hover:bg-emerald-50 transition-all" icon="fa-book">
                        Review Materi
                      </x-ui.button>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

  <x-slot:styles>
  </x-slot:styles>
</x-layouts.app>
