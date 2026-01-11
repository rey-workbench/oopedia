<x-layouts.app :title="$subMaterial->title" theme="mahasiswa">
    <div class="space-y-12">
        {{-- Breadcrumb Navigation --}}
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('mahasiswa.materials.index') }}" class="text-slate-400 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-home"></i> Materi
            </a>
            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
            <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="text-slate-400 hover:text-blue-600 font-bold transition-colors">
                {{ $material->title }}
            </a>
            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-900 font-bold">{{ $subMaterial->title }}</span>
        </div>

        <x-ui.page-header
            title="{{ $subMaterial->title }}"
            subtitle="Bagian {{ $subMaterial->order }} dari modul {{ $material->title }}."
        >
            <x-slot:actions>
                <div class="flex items-center gap-4">
                    <div class="px-4 py-2 bg-{{ $subMaterial->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-50 border border-{{ $subMaterial->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-100 rounded-2xl">
                        <span class="text-[10px] font-bold text-{{ $subMaterial->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-600 uppercase tracking-widest">
                            {{ $subMaterial->jenis_konten === 'sintaks' ? 'Sintaks' : 'Teori' }}
                        </span>
                    </div>
                </div>
            </x-slot:actions>

            <div class="flex flex-wrap items-center gap-6 mt-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner">
                        <i class="fas fa-puzzle-piece"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">
                        {{ $subMaterial->questions->count() }} Soal Latihan
                    </span>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">
                        ~{{ $subMaterial->questions->count() * 2 }} Menit
                    </span>
                </div>
            </div>
        </x-ui.page-header>

        {{-- Content Section --}}
        <x-ui.card class="p-10 md:p-16">
            <article class="prose prose-lg prose-slate max-w-none">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">Materi Pembelajaran</h2>
                    <div class="h-1 w-20 bg-gradient-to-r from-{{ $subMaterial->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-600 to-{{ $subMaterial->jenis_konten === 'sintaks' ? 'teal' : 'indigo' }}-600 rounded-full"></div>
                </div>
                
                <div class="text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $subMaterial->content }}
                </div>
            </article>

            {{-- Action Footer --}}
            <div class="mt-16 pt-10 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-4 text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Siap untuk latihan?</p>
                        <p class="text-sm font-bold text-slate-600">{{ $subMaterial->questions->count() }} soal menanti Anda</p>
                    </div>
                </div>
                
                <x-ui.button 
                    href="{{ route('mahasiswa.materials.questions.show', ['material' => $material->id, 'sub_material' => $subMaterial->id]) }}" 
                    variant="primary" 
                    size="xl"
                    icon="fas fa-play"
                    class="w-full md:w-auto shadow-2xl shadow-{{ $subMaterial->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-500/20"
                >
                    Mulai Latihan Soal
                </x-ui.button>
            </div>
        </x-ui.card>

        {{-- Navigation to Other Sub-Materials --}}
        @if($material->subMaterials->count() > 1)
            <x-ui.card>
                <h3 class="text-2xl font-bold text-slate-900 mb-6">Sub-Materi Lainnya</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($material->subMaterials->where('id', '!=', $subMaterial->id) as $otherSub)
                        <a href="{{ route('mahasiswa.submaterials.show', ['material' => $material->id, 'submaterial' => $otherSub->id]) }}" 
                           class="group p-6 rounded-2xl border-2 border-slate-100 hover:border-{{ $otherSub->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-500 hover:shadow-lg transition-all">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-{{ $otherSub->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-50 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                    <span class="text-lg font-bold text-{{ $otherSub->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-600">{{ $otherSub->order }}</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-900 mb-1 group-hover:text-{{ $otherSub->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-600 transition-colors">
                                        {{ $otherSub->title }}
                                    </h4>
                                    <p class="text-xs text-slate-500 uppercase tracking-wider font-bold">
                                        {{ $otherSub->jenis_konten === 'sintaks' ? 'Sintaks' : 'Teori' }} • {{ $otherSub->questions->count() }} Soal
                                    </p>
                                </div>
                                <i class="fas fa-arrow-right text-slate-300 group-hover:text-{{ $otherSub->jenis_konten === 'sintaks' ? 'emerald' : 'blue' }}-600 group-hover:translate-x-1 transition-all"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>
