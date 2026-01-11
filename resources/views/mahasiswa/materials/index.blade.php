<x-layouts.app title="Materi Pembelajaran" theme="mahasiswa">
  @if(auth()->check() && auth()->user() === null)
  <!-- Hidden forms for guest logout and redirect -->
  <form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="redirect" value="{{ route('login') }}">
  </form>

  <form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="redirect" value="{{ route('register') }}">
  </form>
  @endif

  <div class="space-y-12">
    <x-ui.page-header
      title="Kurikulum PBO"
      subtitle="Kuasai konsep fondasi hingga tingkat lanjut Pemrograman Berorientasi Objek."
    />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
      @foreach($materials as $material)
      @php
        $isGuest = !auth()->check();

        // Calculate configured question count
        if ($isGuest) {
          $beginnerCount = min(3, $material->questions->where('difficulty', 'beginner')->count());
          $mediumCount = min(3, $material->questions->where('difficulty', 'medium')->count());
          $hardCount = min(3, $material->questions->where('difficulty', 'hard')->count());
          $configuredTotalQuestions = $beginnerCount + $mediumCount + $hardCount;
        } else {
          $configuredTotalQuestions = $material->questions->count();
        }
      @endphp

      <x-ui.card padding="p-0 flex flex-col flex-1" class="flex flex-col h-full group overflow-hidden" data-intro="Ini adalah modul pembelajaran. Setiap kartu mewakili topik PBO yang bisa kamu pelajari." data-step="6">
        {{-- Image Section --}}
        <div class="relative h-60 overflow-hidden shrink-0">
          @if($material->media && $material->media->isNotEmpty())
            <img src="{{ $material->media->first()->media_url }}" alt="{{ $material->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
          @else
            <div class="w-full h-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center">
              <i class="fas fa-code text-7xl text-white/10 group-hover:rotate-12 transition-transform"></i>
            </div>
          @endif
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
          
          <div class="absolute bottom-6 left-6 right-6 flex justify-between items-center">
            <div class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-2xl text-white text-[10px] font-bold uppercase tracking-widest border border-white/20">
              {{ $material->updated_at->format('M Y') }}
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-blue-600 rounded-2xl text-white text-[10px] font-bold uppercase tracking-widest shadow-xl shadow-blue-500/20">
              <i class="fas fa-puzzle-piece"></i>
              {{ $configuredTotalQuestions }} Tantangan
            </div>
          </div>
        </div>

        {{-- Content Section --}}
        <div class="p-8 flex-1 flex flex-col">
          <div class="mb-4 min-h-[4.5rem] flex items-start">
            <h2 class="text-2xl font-bold text-slate-900 leading-tight group-hover:text-blue-600 transition-colors uppercase tracking-widest">
              {{ $material->title }}
            </h2>
          </div>

          <div class="mb-6">
            <div class="flex items-center gap-3 text-slate-400">
              <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-xs text-slate-500 shadow-inner">
                <i class="fas fa-chalkboard-user"></i>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest">{{ $material->creator ? $material->creator->name : 'Admin System' }}</span>
            </div>
          </div>

          @if($isGuest)
            <div class="mb-8 p-5 bg-amber-50 rounded-3xl border border-amber-100 flex items-start gap-4 ring-4 ring-amber-50/50 min-h-[100px]">
              <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-amber-200">
                <i class="fas fa-ghost"></i>
              </div>
              <div>
                <span class="text-[10px] font-bold text-amber-800 uppercase tracking-widest block mb-1">Mode Tamu</span>
                <p class="text-xs text-amber-700 font-medium leading-relaxed">Akses terbatas ke materi & soal-soal pilihan.</p>
              </div>
            </div>
          @else
            {{-- Spacer to maintain alignment when guest banner is absent --}}
            <div class="mb-8 min-h-[100px]"></div>
          @endif

          <div class="mt-auto pt-6">
            <x-ui.button 
              href="{{ route('mahasiswa.materials.show', $material->id) }}" 
              variant="primary" 
              class="w-full" 
              size="lg"
              icon="fas fa-arrow-right"
              data-intro="Klik tombol ini untuk masuk ke dalam modul dan melihat materi serta tantangan di dalamnya." data-step="7"
            >
              MULAI BELAJAR
            </x-ui.button>
          </div>
        </div>
      </x-ui.card>
      @endforeach
    </div>
  </div>
</x-layouts.app>
