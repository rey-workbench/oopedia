<x-layouts.app title="Import Dosen" theme="admin">
  <div class="max-w-4xl mx-auto space-y-12">
    <x-ui.page-header
      title="Ingesti Fakultas"
      subtitle="Massal injeksi data dosen melalui protokol file spreadsheet."
    >
      <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE DAFTAR</x-ui.button>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      {{-- Protocol Info --}}
      <div class="space-y-6">
        <x-ui.card class="border-slate-100 bg-slate-900 text-white">
          <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-4">Protokol Fakultas</p>
          <ul class="space-y-4">
            <li class="flex gap-3">
              <i class="fas fa-file-excel text-indigo-400 mt-1"></i>
              <span class="text-xs font-bold leading-relaxed">Format: .xlsx, .xls, .csv</span>
            </li>
            <li class="flex gap-3">
              <i class="fas fa-table-list text-indigo-400 mt-1"></i>
              <span class="text-xs font-bold leading-relaxed">Kolom: name, email, password</span>
            </li>
            <li class="flex gap-3">
              <i class="fas fa-user-shield text-indigo-400 mt-1"></i>
              <span class="text-xs font-bold leading-relaxed">Akses istimewa diberikan</span>
            </li>
          </ul>
          <div class="mt-8 pt-6 border-t border-slate-800">
            <x-ui.button variant="primary" size="sm" href="{{ route('admin.users.download-template') }}" icon="fas fa-download" class="w-full">UNDUH TEMPLATE</x-ui.button>
          </div>
        </x-ui.card>

        <div class="p-6 rounded-3xl bg-indigo-50 border border-indigo-100">
          <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mb-1">Integritas Unggahan</p>
          <p class="text-xs font-bold text-indigo-900">Hanya untuk peran sistem yang terverifikasi.</p>
        </div>
      </div>

      {{-- Import Form --}}
      <div class="lg:col-span-2">
        <x-ui.card class="border-slate-100 shadow-2xl">
          <x-slot:header>
            <div class="flex items-center gap-4">
              <div class="w-1.5 h-8 bg-indigo-600 rounded-full"></div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Portal Transmisi</p>
            </div>
          </x-slot:header>

          <form method="POST" action="{{ route('admin.users.process-import') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <div class="space-y-4">
              <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Unggah Muatan Fakultas</label>
              <label class="relative flex flex-col items-center justify-center w-full h-48 px-4 transition bg-slate-50 border-2 border-slate-200 border-dashed rounded-[2rem] appearance-none cursor-pointer hover:border-indigo-400 focus:outline-none group">
                <span class="flex items-center space-x-2">
                  <i class="fas fa-upload text-slate-300 text-3xl group-hover:text-indigo-500 transition-colors"></i>
                  <span class="text-sm font-bold text-slate-400 group-hover:text-slate-900 transition-colors uppercase tracking-widest">Pilih File untuk Diinjeksi</span>
                </span>
                <input type="file" name="excel_file" class="hidden" required accept=".xlsx,.xls,.csv" onchange="document.getElementById('file-chosen').textContent = this.files[0].name">
                <span id="file-chosen" class="mt-2 text-[10px] font-bold text-indigo-600 truncate max-w-xs"></span>
              </label>
               @error('excel_file')
                <p class="text-[10px] font-bold text-rose-500 uppercase">{{ $message }}</p>
              @enderror
            </div>

            <div class="pt-4">
              <x-ui.button type="submit" variant="primary" icon="fas fa-microchip" class="w-full h-[60px] shadow-2xl shadow-indigo-500/30">
                INISIALISASI INGESTI
              </x-ui.button>
            </div>
          </form>
        </x-ui.card>
      </div>
    </div>
  </div>
  <x-admin.tutorial />
</x-layouts.app>