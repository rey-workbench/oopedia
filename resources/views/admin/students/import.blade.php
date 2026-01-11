<x-layouts.app title="Import Mahasiswa" theme="admin">
  <div class="max-w-4xl mx-auto space-y-12">
    <x-ui.page-header
      title="Batch Student Ingestion"
      subtitle="Massal injeksi data mahasiswa melalui protokol file spreadsheet."
    >
      <x-ui.button href="{{ route('admin.students.index') }}" variant="ghost" icon="fas fa-arrow-left">BACK TO LIST</x-ui.button>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      {{-- Protocol Info --}}
      <div class="space-y-6">
        <x-ui.card class="border-slate-100 bg-slate-900 text-white">
          <h6 class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-4">Ingestion Protocol</h6>
          <ul class="space-y-4">
            <li class="flex gap-3">
              <i class="fas fa-file-excel text-blue-400 mt-1"></i>
              <span class="text-xs font-bold leading-relaxed">Format: .xlsx, .xls, .csv</span>
            </li>
            <li class="flex gap-3">
              <i class="fas fa-table-list text-blue-400 mt-1"></i>
              <span class="text-xs font-bold leading-relaxed">Columns: name, email, password</span>
            </li>
            <li class="flex gap-3">
              <i class="fas fa-shield-check text-blue-400 mt-1"></i>
              <span class="text-xs font-bold leading-relaxed">Auto-approval enabled</span>
            </li>
          </ul>
          <div class="mt-8 pt-6 border-t border-slate-800">
            <x-ui.button variant="primary" size="sm" href="{{ route('admin.students.download-template') }}" icon="fas fa-download" class="w-full">DOWNLOAD TEMPLATE</x-ui.button>
          </div>
        </x-ui.card>

        <div class="p-6 rounded-3xl bg-blue-50 border border-blue-100">
          <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">Max Payload</p>
          <p class="text-xs font-bold text-blue-900">{{ (int)(ini_get('upload_max_filesize')) }} Megabytes</p>
        </div>
      </div>

      {{-- Import Form --}}
      <div class="lg:col-span-2">
        <x-ui.card class="border-slate-100 shadow-2xl">
          <x-slot:header>
            <div class="flex items-center gap-4">
              <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
              <h6 class="mb-0 font-bold uppercase tracking-widest text-xs text-slate-400">Transmission Portal</h6>
            </div>
          </x-slot:header>

          <form method="POST" action="{{ route('admin.students.process-import') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <div class="space-y-4">
              <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Upload Source File</label>
              <label class="relative flex flex-col items-center justify-center w-full h-48 px-4 transition bg-slate-50 border-2 border-slate-200 border-dashed rounded-[2rem] appearance-none cursor-pointer hover:border-blue-400 focus:outline-none group">
                <span class="flex items-center space-x-2">
                  <i class="fas fa-cloud-arrow-up text-slate-300 text-3xl group-hover:text-blue-500 transition-colors"></i>
                  <span class="text-sm font-bold text-slate-400 group-hover:text-slate-900 transition-colors uppercase tracking-widest">Choose File to Inject</span>
                </span>
                <input type="file" name="excel_file" class="hidden" required accept=".xlsx,.xls,.csv" onchange="document.getElementById('file-chosen').textContent = this.files[0].name">
                <span id="file-chosen" class="mt-2 text-[10px] font-bold text-blue-600 truncate max-w-xs"></span>
              </label>
               @error('excel_file')
                <p class="text-[10px] font-bold text-rose-500 uppercase">{{ $message }}</p>
              @enderror
            </div>

            <div class="pt-4">
              <x-ui.button type="submit" variant="primary" icon="fas fa-upload" class="w-full h-[60px] shadow-2xl shadow-blue-500/30">
                START INGESTION
              </x-ui.button>
            </div>
          </form>

          @if(session('importErrors'))
            <div class="mt-12 pt-8 border-t border-slate-100">
              <div class="flex items-center gap-4 mb-6">
                <div class="w-1.5 h-8 bg-rose-500 rounded-full"></div>
                <h6 class="mb-0 font-bold uppercase tracking-widest text-xs text-rose-500">Transmission Anomalies</h6>
              </div>
              
              <x-ui.table>
                <x-slot:thead>
                  <tr>
                    <x-ui.th>Row ID</x-ui.th>
                    <x-ui.th>Anomaly Report</x-ui.th>
                  </tr>
                </x-slot:thead>
                @foreach(session('importErrors') as $error)
                  <tr class="bg-rose-50/30">
                    <td class="px-6 py-4 font-bold text-rose-900 text-xs">{{ $error['row'] }}</td>
                    <td class="px-6 py-4">
                      <ul class="space-y-1">
                        @foreach($error['errors'] as $message)
                          <li class="text-[10px] font-bold text-rose-600">{{ $message }}</li>
                        @endforeach
                      </ul>
                    </td>
                  </tr>
                @endforeach
              </x-ui.table>
            </div>
          @endif
        </x-ui.card>
      </div>
    </div>
  </div>
  <x-admin.tutorial />
</x-layouts.app>