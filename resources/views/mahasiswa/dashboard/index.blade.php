<x-layouts.app title="Dashboard" theme="mahasiswa">
    <div id="dashboard-container" class="space-y-12">
        <x-ui.page-header
            title="Dashboard"
            subtitle="Selamat datang di pusat kendali belajar Anda."
        />

        {{-- Welcome Banner --}}
        <div class="relative overflow-hidden rounded-[3rem] bg-slate-900 p-12 text-white shadow-2xl shadow-slate-200">
            {{-- Background Elements --}}
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px]"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                <div class="w-32 h-32 bg-white rounded-[2.5rem] flex items-center justify-center shadow-2xl rotate-3">
                    <img src="{{ asset('images/logo.png') }}" alt="User" class="w-20 h-auto">
                </div>
                
                <div class="text-center md:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-3">Selamat Datang Kembali</p>
                    <h2 class="text-5xl font-extrabold tracking-tight mb-4 text-white">{{ auth()->user()->name }}</h2>
                    <p class="text-slate-400 font-medium text-lg max-w-xl">Lanjutkan perjalanan belajar Anda hari ini dan kuasai konsep <span class="text-white">Object-Oriented Programming</span> dengan cara yang menyenangkan!</p>
                </div>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <x-ui.stat-card 
                title="Materi Tersedia" 
                :value="$totalMaterials" 
                icon="fas fa-book-open" 
                variant="primary"
                footer="Konsep PBO dari Dasar"
            />
            <x-ui.stat-card 
                title="Total Soal" 
                :value="$totalQuestions" 
                icon="fas fa-brain" 
                variant="success"
                footer="Latihan & Tantangan"
            />
            <x-ui.stat-card 
                title="Level Hard" 
                :value="$hardQuestions" 
                icon="fas fa-fire" 
                variant="danger"
                footer="Tingkat Kesulitan Tinggi"
            />
            <x-ui.stat-card 
                title="Peringkat Anda" 
                value="#12" 
                icon="fas fa-trophy" 
                variant="warning"
                footer="Terus tingkatkan skor!"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Quick Actions --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold tracking-tight text-slate-900">Aktivitas Terbaru</h3>
                    <x-ui.button variant="ghost" size="sm">Lihat Semua</x-ui.button>
                </div>

                <div class="space-y-6">
                    @forelse($recentActivities as $activity)
                        <x-ui.card :hover="true" padding="p-0">
                            <div class="p-8 flex gap-8 items-center">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0
                                    @if($activity->type === 'achievement') bg-emerald-50 text-emerald-500
                                    @elseif($activity->type === 'milestone') bg-amber-50 text-amber-500
                                    @else bg-blue-50 text-blue-500 @endif border border-black/5 shadow-inner">
                                    <i class="fas @if($activity->type === 'achievement') fa-trophy @elseif($activity->type === 'milestone') fa-star @else fa-tasks @endif text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-slate-900 tracking-tight text-sm uppercase">
                                                @if($activity->type === 'achievement') Pencapaian Baru! @elseif($activity->type === 'milestone') Milestone Tercapai! @else Progres Belajar @endif
                                            </h4>
                                            <p class="text-slate-500 text-sm font-medium mt-1 leading-relaxed">
                                                @if($activity->type === 'achievement')
                                                    Menyelesaikan <span class="text-emerald-500 font-bold">{{ $activity->total_correct }} soal</span> di materi <span class="text-slate-900">{{ $activity->material_title }}</span>
                                                @elseif($activity->type === 'milestone')
                                                    Berhasil menyelesaikan soal <span class="text-amber-500 font-bold">level hard</span> di materi <span class="text-slate-900">{{ $activity->material_title }}</span>
                                                @else
                                                    Mengerjakan soal <span class="capitalize font-bold text-blue-500">{{ $activity->difficulty }}</span> di materi <span class="text-slate-900">{{ $activity->material_title }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </x-ui.card>
                    @empty
                        <x-ui.empty-state />
                    @endforelse
                </div>
            </div>

            {{-- Sidebar Dashboard --}}
            <div class="space-y-8">
                <h3 class="text-xl font-bold tracking-tight text-slate-900">Materi Unggulan</h3>
                
                <div class="space-y-6">
                    <x-ui.card padding="p-8" class="bg-gradient-to-br from-blue-600 to-blue-700 text-white border-none shadow-xl">
                        <i class="fas fa-code text-3xl mb-6 opacity-50"></i>
                        <h4 class="text-lg font-bold tracking-tight mb-2">PBO Dasar: Class & Object</h4>
                        <p class="text-blue-100 text-sm font-medium mb-8 leading-relaxed">Pahami anatomi dasar dari pemrograman berorientasi objek.</p>
                        <x-ui.button variant="secondary" size="sm" class="w-full" :href="route('mahasiswa.materials.index')">Pelajari Sekarang</x-ui.button>
                    </x-ui.card>

                    <x-ui.card padding="p-8" class="border-2 border-dashed border-slate-200 shadow-none hover:border-blue-200 transition-colors">
                        <div class="flex flex-col items-center text-center">
                            <i class="fas fa-plus text-slate-300 text-2xl mb-4"></i>
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Eksplor Materi Lain</h4>
                            <a href="{{ route('mahasiswa.materials.index') }}" class="mt-4 text-xs font-bold text-blue-600 hover:underline">Lihat Katalog Lengkap</a>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
