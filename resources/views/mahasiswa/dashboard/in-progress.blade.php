<x-layouts.app title="Materi Sedang Dipelajari" theme="mahasiswa">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-ui.page-header
                title="Materi Sedang Dipelajari"
                subtitle="Terus asah kemampuan Anda dan selesaikan tantangan yang ada."
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
                        <div class="w-24 h-24 bg-blue-50 text-blue-500 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner">
                            <i class="fas fa-book-open text-5xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 mb-4  uppercase tracking-widest">Belum Ada Progres</h3>
                        <p class="text-gray-500 mb-10 max-w-md mx-auto font-medium">
                            Anda belum memulai materi apapun. Pilih materi yang Anda minati dan mulai petualangan belajar Anda sekarang!
                        </p>
                        <x-ui.button href="{{ route('mahasiswa.materials.index') }}" variant="primary" class="px-10 py-4 rounded-2xl font-bold  uppercase transition-all shadow-xl shadow-blue-100" icon="fa-rocket">
                            Mulai Belajar
                        </x-ui.button>
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        @foreach($materialsWithStats as $materialData)
                            @php
                                $material = $materialData['material'];
                                $stats = $materialData['stats'];
                                $overallPercent = $stats['overall']['total'] > 0 ? round(($stats['overall']['correct'] / $stats['overall']['total']) * 100) : 0;
                            @endphp
                            <div class="group">
                                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                                    <div class="p-8 bg-gradient-to-br from-blue-600 to-indigo-700 relative overflow-hidden text-white">
                                        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                                            <i class="fas fa-running text-8xl"></i>
                                        </div>
                                        <div class="relative z-10 flex items-center gap-6">
                                            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner border border-white/30 group-hover:scale-110 transition-transform duration-500">
                                                <i class="fas fa-spinner animate-spin-slow text-2xl"></i>
                                            </div>
                                            <div>
                                                <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 mb-1">Learning in Progress</div>
                                                <h4 class="text-2xl font-bold  tracking-widest">{{ $material->title }}</h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-8">
                                        <!-- Overall Stats Header -->
                                        <div class="flex items-end justify-between mb-8">
                                            <div>
                                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status Progres</div>
                                                <div class="text-3xl font-bold text-gray-900 ">{{ $stats['overall']['correct'] }}<span class="text-sm text-gray-400 not- ml-2">/ {{ $stats['overall']['total'] }} SELESAI</span></div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-4xl font-bold text-blue-600 ">{{ $overallPercent }}%</div>
                                            </div>
                                        </div>

                                        <div class="space-y-4 mb-10">
                                            {{-- Beginner --}}
                                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-colors">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Beginner Level</span>
                                                    <span class="text-xs font-bold text-gray-900 ">{{ $stats['beginner']['correct'] }}/{{ $stats['beginner']['configured_total'] }}</span>
                                                </div>
                                                <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" style="width: {{ $stats['beginner']['percentage'] }}%"></div>
                                                </div>
                                            </div>

                                            {{-- Medium --}}
                                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-colors">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Medium Level</span>
                                                    <span class="text-xs font-bold text-gray-900 ">{{ $stats['medium']['correct'] }}/{{ $stats['medium']['configured_total'] }}</span>
                                                </div>
                                                <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full bg-amber-500 rounded-full transition-all duration-1000" style="width: {{ $stats['medium']['percentage'] }}%"></div>
                                                </div>
                                            </div>

                                            {{-- Hard --}}
                                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-colors">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hard Level</span>
                                                    <span class="text-xs font-bold text-gray-900 ">{{ $stats['hard']['correct'] }}/{{ $stats['hard']['configured_total'] }}</span>
                                                </div>
                                                <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full bg-rose-500 rounded-full transition-all duration-1000" style="width: {{ $stats['hard']['percentage'] }}%"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="flex items-center justify-center gap-2 py-4 rounded-2xl font-bold  uppercase text-xs border-2 border-gray-100 hover:bg-gray-50 transition-all text-gray-600">
                                                <i class="fas fa-book"></i>
                                                Materi
                                            </a>
                                            <a href="{{ route('mahasiswa.materials.questions.levels', $material->id) }}" class="flex items-center justify-center gap-2 py-4 rounded-2xl font-bold  uppercase text-xs bg-gray-900 text-white hover:bg-blue-600 transition-all shadow-lg shadow-gray-200 hover:shadow-blue-200">
                                                <i class="fas fa-play"></i>
                                                Lanjut
                                            </a>
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
</x-layouts.app>
