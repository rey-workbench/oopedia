<x-layouts.app title="Import Dosen" theme="admin">
    <div class="max-w-4xl mx-auto space-y-12">
        <x-ui.page-header
            title="Faculty Ingestion"
            subtitle="Massal injeksi data dosen melalui protokol file spreadsheet."
        >
            <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost" icon="fas fa-arrow-left">BACK TO LIST</x-ui.button>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Protocol Info --}}
            <div class="space-y-6">
                <x-ui.card class="border-slate-100 bg-slate-900 text-white">
                    <h6 class="text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-4 italic">Faculty Protocol</h6>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <i class="fas fa-file-excel text-indigo-400 mt-1"></i>
                            <span class="text-xs font-bold leading-relaxed">Format: .xlsx, .xls, .csv</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-table-list text-indigo-400 mt-1"></i>
                            <span class="text-xs font-bold leading-relaxed">Columns: name, email, password</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-user-shield text-indigo-400 mt-1"></i>
                            <span class="text-xs font-bold leading-relaxed">Privileged access granted</span>
                        </li>
                    </ul>
                    <div class="mt-8 pt-6 border-t border-slate-800">
                        <x-ui.button variant="primary" size="sm" href="{{ route('admin.users.download-template') }}" icon="fas fa-download" class="w-full">DOWNLOAD TEMPLATE</x-ui.button>
                    </div>
                </x-ui.card>

                <div class="p-6 rounded-3xl bg-indigo-50 border border-indigo-100 italic">
                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Upload Integrity</p>
                    <p class="text-xs font-bold text-indigo-900">Verified system roles only.</p>
                </div>
            </div>

            {{-- Import Form --}}
            <div class="lg:col-span-2">
                <x-ui.card class="border-slate-100 shadow-2xl">
                    <x-slot:header>
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-8 bg-indigo-600 rounded-full"></div>
                            <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400">Transmission Portal</h6>
                        </div>
                    </x-slot:header>

                    <form method="POST" action="{{ route('admin.users.process-import') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Upload Faculty Payload</label>
                            <label class="relative flex flex-col items-center justify-center w-full h-48 px-4 transition bg-slate-50 border-2 border-slate-200 border-dashed rounded-[2rem] appearance-none cursor-pointer hover:border-indigo-400 focus:outline-none group">
                                <span class="flex items-center space-x-2">
                                    <i class="fas fa-upload text-slate-300 text-3xl group-hover:text-indigo-500 transition-colors"></i>
                                    <span class="text-sm font-black italic text-slate-400 group-hover:text-slate-900 transition-colors uppercase tracking-tighter">Choose File to Inject</span>
                                </span>
                                <input type="file" name="excel_file" class="hidden" required accept=".xlsx,.xls,.csv" onchange="document.getElementById('file-chosen').textContent = this.files[0].name">
                                <span id="file-chosen" class="mt-2 text-[10px] font-bold text-indigo-600 truncate max-w-xs"></span>
                            </label>
                             @error('excel_file')
                                <p class="text-[10px] font-black text-rose-500 uppercase italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4">
                            <x-ui.button type="submit" variant="primary" icon="fas fa-microchip" class="w-full h-[60px] shadow-2xl shadow-indigo-500/30">
                                INITIALIZE INGESTION
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </div>
    </div>
    <x-admin.tutorial />
</x-layouts.app>