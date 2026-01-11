<x-layouts.app title="{{ $material->title }}" theme="mahasiswa">
    <div class="space-y-12">
        {{-- Header Section --}}
        <x-ui.page-header
            title="{{ $material->title }}"
            subtitle="Kuasai konsep fondasi hingga tingkat lanjut Pemrograman Berorientasi Objek."
        >
            <x-slot:actions>
                <x-ui.button 
                    href="{{ route('mahasiswa.materials.index') }}" 
                    variant="ghost" 
                    icon="fas fa-arrow-left"
                >
                    Kembali ke Materi
                </x-ui.button>
            </x-slot:actions>

            <div class="flex flex-wrap items-center gap-6 mt-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner">
                        <i class="fas fa-chalkboard-user"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">
                        {{ $material->creator ? $material->creator->name : 'Admin System' }}
                    </span>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">
                        {{ $material->subMaterials->count() }} Sub-Materi
                    </span>
                </div>
            </div>
        </x-ui.page-header>

        {{-- Sub-Materials Grid --}}
        <div>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 tracking-widest mb-2">Daftar Sub-Materi</h2>
                    <p class="text-slate-500 font-medium">Pilih sub-materi untuk memulai pembelajaran</p>
                </div>
            </div>

            @if($material->subMaterials->isEmpty())
                <x-ui.card class="p-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-book-open text-slate-200 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold tracking-widest text-slate-900 mb-2">Belum Ada Sub-Materi</h3>
                    <p class="text-slate-400 text-sm max-w-xs mx-auto">Sub-materi untuk topik ini sedang dalam pengembangan.</p>
                </x-ui.card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($material->subMaterials as $index => $subMaterial)
                        <x-ui.card padding="p-0" class="group hover:shadow-2xl transition-all duration-300 overflow-hidden">
                            {{-- Header with Icon --}}
                            <div class="relative h-32 bg-gradient-to-br from-{{ $subMaterial->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-500 to-{{ $subMaterial->jenis_konten === 'sintaks' ? 'teal' : 'indigo' }}-600 flex items-center justify-center">
                                <div class="absolute inset-0 bg-black/10"></div>
                                <div class="relative z-10 text-center">
                                    <div class="w-16 h-16 mx-auto bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-{{ $subMaterial->jenis_konten === 'sintaks' ? 'code' : 'book' }} text-3xl text-white"></i>
                                    </div>
                                    <div class="px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full inline-block">
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                                            {{ $subMaterial->jenis_konten === 'sintaks' ? 'Sintaks' : 'Teori' }}
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- Order Badge --}}
                                <div class="absolute top-4 left-4 w-10 h-10 bg-white rounded-xl shadow-lg flex items-center justify-center">
                                    <span class="text-lg font-bold text-{{ $subMaterial->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-600">{{ $subMaterial->order }}</span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-{{ $subMaterial->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-600 transition-colors line-clamp-2">
                                    {{ $subMaterial->title }}
                                </h3>
                                
                                <p class="text-sm text-slate-600 mb-6 line-clamp-3 leading-relaxed">
                                    {{ $subMaterial->content }}
                                </p>

                                {{-- Stats --}}
                                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                                    <div class="flex items-center gap-2 text-slate-500">
                                        <i class="fas fa-puzzle-piece text-xs"></i>
                                        <span class="text-xs font-bold">{{ $subMaterial->questions->count() }} Soal</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-500">
                                        <i class="fas fa-clock text-xs"></i>
                                        <span class="text-xs font-bold">~{{ $subMaterial->questions->count() * 2 }} menit</span>
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                <x-ui.button 
                                    href="{{ route('mahasiswa.submaterials.show', ['material' => $material->id, 'submaterial' => $subMaterial->id]) }}" 
                                    variant="primary" 
                                    class="w-full"
                                    icon="fas fa-book-open"
                                >
                                    Lihat Materi
                                </x-ui.button>
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Material Content Section (Optional) --}}
        @if($material->content)
            <x-ui.card>
                <div class="prose prose-slate max-w-none">
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Tentang Materi Ini</h3>
                    <div class="text-slate-600 leading-relaxed">
                        {!! nl2br(e($material->content)) !!}
                    </div>
                </div>
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>
