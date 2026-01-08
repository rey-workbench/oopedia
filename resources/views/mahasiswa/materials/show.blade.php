<x-layouts.app :title="$material->title" theme="mahasiswa">
    <div class="space-y-12">
        {{-- Custom Page Header --}}
        <div>
            <a href="{{ route('mahasiswa.materials.index') }}" class="group inline-flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-blue-600 transition-all mb-8 bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-100">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Katalog Materi
            </a>
            
            <div class="relative">
                <div class="absolute -top-10 left-0 opacity-[0.03] text-[120px] font-bold select-none pointer-events-none uppercase tracking-tight">MODUL</div>
                <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight mb-6 max-w-4xl leading-[0.9]">{{ $material->title }}</h1>
                <div class="flex items-center gap-4">
                    <div class="h-2 w-24 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Durasi Baca ~10 Min</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <x-ui.alert variant="success" :message="session('success')" />
        @endif

        @if(session('error'))
            <x-ui.alert variant="danger" :message="session('error')" />
        @endif

        @guest
            <x-ui.card class="bg-gradient-to-br from-amber-500 to-orange-600 border-0 shadow-2xl shadow-amber-200">
                <div class="flex flex-col md:flex-row items-center gap-8 p-4">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md text-white rounded-[2rem] flex items-center justify-center shrink-0 border border-white/30 shadow-xl">
                        <i class="fas fa-lock-open text-3xl"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h4 class="text-white font-bold tracking-tight text-2xl mb-2">Guest Access Mode</h4>
                        <p class="text-white/80 font-bold text-sm leading-relaxed">
                            Anda sedang membaca dalam mode terbatas. Harap login untuk mengakses seluruh submateri, video penjelasan, dan bank soal adaptif.
                        </p>
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-6">
                            <x-ui.button variant="white" size="sm" onclick="document.getElementById('guest-logout-login-form').submit();">Login Mahasiswa</x-ui.button>
                            <x-ui.button variant="ghost" size="sm" class="text-white border-white/40 hover:bg-white/10" onclick="document.getElementById('guest-logout-register-form').submit();">Daftar Baru</x-ui.button>
                        </div>
                    </div>
                </div>

                <form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ route('login') }}">
                </form>

                <form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ route('register') }}">
                </form>
            </x-ui.card>
        @endguest

        {{-- Content Area --}}
        <div class="max-w-5xl mx-auto">
            <x-ui.card padding="p-10 md:p-20" class="shadow-2xl border-slate-50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full -mr-32 -mt-32 pointer-events-none"></div>
                
                <article class="prose prose-lg prose-slate max-w-none prose-headings:font-bold prose-headings:tracking-tight prose-p:font-medium prose-p:text-slate-600 prose-p:leading-loose prose-img:rounded-[2rem] prose-img:shadow-2xl relative z-10">
                    {!! $material->content !!}
                </article>

                {{-- Action Footer --}}
                <div class="mt-20 pt-10 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex items-center gap-4 text-slate-400">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center">
                            <i class="fas fa-check-double text-xs"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Selesai Mempelajari Materi?</span>
                    </div>
                    
                    <x-ui.button 
                        href="{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id]) }}" 
                        variant="primary" 
                        size="xl"
                        icon="fas fa-arrow-right"
                        class="w-full md:w-auto shadow-2xl shadow-blue-500/20"
                    >
                        Ke Latihan Soal
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
