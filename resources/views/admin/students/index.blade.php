<x-layouts.app title="Data Mahasiswa" theme="admin">
  <div class="space-y-12" x-data="{ openModal: false }">
    <x-ui.page-header
      title="Database Mahasiswa"
      subtitle="Pantau progres dan aktivitas belajar seluruh mahasiswa terdaftar."
    >
      <div class="flex flex-wrap items-center gap-4">
        <x-ui.button @click="openModal = true" variant="primary" icon="fas fa-plus">Daftarkan Mahasiswa</x-ui.button>
        <x-ui.button href="{{ route('admin.students.import') }}" variant="success" icon="fas fa-file-excel">Impor Excel</x-ui.button>
      </div>
    </x-ui.page-header>

    <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
      <x-slot:header>
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 w-full px-6 py-4">
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Registri Subjek</p>
          <form method="GET" action="{{ route('admin.students.index') }}" class="w-full md:w-auto">
            <div class="relative group">
              <i class="fas fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
              <input 
                type="text" 
                name="search" 
                placeholder="Cari mahasiswa..." 
                value="{{ request('search') }}"
                class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none"
              >
            </div>
          </form>
        </div>
      </x-slot:header>

      <x-ui.table>
        <x-slot:thead>
          <tr>
            <x-ui.th>Identitas Mahasiswa</x-ui.th>
            <x-ui.th>Akses Email</x-ui.th>
            <x-ui.th class="text-center">Aktivitas Soal</x-ui.th>
            <x-ui.th class="text-center">Integrasi Progres</x-ui.th>
            <x-ui.th class="text-right">Aksi</x-ui.th>
          </tr>
        </x-slot:thead>
        <tbody>
          @forelse($students as $student)
          <tr class="group hover:bg-slate-50 transition-colors">
            <td class="px-6 py-6 border-l-4 border-transparent group-hover:border-blue-600">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold shadow-lg shadow-slate-200 uppercase text-xs">
                  {{ substr($student->name, 0, 1) }}
                </div>
                <div class="font-bold text-slate-900 tracking-widest">{{ $student->name }}</div>
              </div>
            </td>
            <td class="px-6 py-6">
              <span class="text-xs font-bold text-slate-400 underline decoration-slate-200 underline-offset-4">{{ $student->email }}</span>
            </td>
            <td class="px-6 py-6 text-center">
              <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full">
                <i class="fas fa-terminal text-[10px] text-blue-500"></i>
                <span class="text-[10px] font-bold text-slate-700">{{ $student->total_answered_questions ?? 0 }}</span>
              </div>
            </td>
            <td class="px-6 py-6">
              <div class="w-40 mx-auto space-y-2">
                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-slate-400 px-1">
                  <span>Sinkronisasi Progres</span>
                  <span>{{ $student->overall_progress }}%</span>
                </div>
                <x-ui.progress-bar :value="$student->overall_progress" size="xs" :showPercentage="false" variant="primary" />
              </div>
            </td>
            <td class="px-6 py-6">
              <div class="flex justify-end gap-2">
                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.students.progress', $student) }}" icon="fas fa-chart-line" />
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline">
                  @csrf
                  @method('DELETE')
                  <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-300 hover:text-rose-500" icon="fas fa-user-minus" onclick="return confirm('Hapus data mahasiswa ini?')" />
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="p-20 text-center">
              <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-user-graduate text-slate-200 text-3xl"></i>
              </div>
              <h3 class="text-xl font-bold uppercase tracking-widest text-slate-900 mb-2">Tidak Ada Mahasiswa Terdaftar</h3>
              <p class="text-slate-400 text-sm max-w-xs mx-auto mb-8">Silakan daftarkan mahasiswa secara manual atau impor melalui protokol Excel.</p>
              <div class="flex justify-center gap-4">
                <x-ui.button @click="openModal = true" variant="primary" icon="fas fa-plus">Daftar Individu</x-ui.button>
                <x-ui.button href="{{ route('admin.students.import') }}" variant="outline" icon="fas fa-file-excel">Unggah Dataset</x-ui.button>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </x-ui.table>

      @if($students->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/30">
          {{ $students->links() }}
        </div>
      @endif
    </x-ui.card>

    {{-- Register Student Modal --}}
    <div x-show="openModal" class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">
      <div class="flex items-center justify-center min-h-screen p-4">
        <div @click="openModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        
        <div class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100" x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
          <div class="bg-blue-600 px-8 py-10 text-white relative">
            <div class="absolute right-8 top-10">
              <button @click="openModal = false" class="text-blue-200 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <h6 class="text-xl font-bold tracking-widest mb-1 uppercase">Inisialisasi Otentikasi</h6>
            <p class="text-[10px] font-bold text-blue-100/60 uppercase tracking-widest">Daftarkan entitas mahasiswa individu</p>
          </div>
          
          <form action="{{ route('admin.students.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div class="space-y-4">
              <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase text-slate-400 font-poppins">Identitas Lengkap</label>
                <x-ui.input name="name" placeholder="Nama lengkap subjek" required />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase text-slate-400 font-poppins">Email Elektronik</label>
                <x-ui.input type="email" name="email" placeholder="mahasiswa@example.com" required />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label class="text-[10px] font-bold uppercase text-slate-400 font-poppins">Kunci Keamanan</label>
                  <x-ui.input type="password" name="password" placeholder="Minimal 8 karakter" required />
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-bold uppercase text-slate-400 font-poppins">Konfirmasi Kunci</label>
                  <x-ui.input type="password" name="password_confirmation" placeholder="Verifikasi kunci" required />
                </div>
              </div>
            </div>

            <div class="pt-4 flex gap-4">
              <x-ui.button type="submit" variant="primary" class="flex-1 py-4 shadow-xl shadow-blue-500/20" icon="fas fa-user-plus">Otorisasi Mahasiswa</x-ui.button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <x-admin.tutorial />
</x-layouts.app>