    <div class="space-y-12">
        <x-ui.page-header
            title="Arsitek Bank Soal"
            subtitle="Inisialisasi repositori soal baru untuk evaluasi kompetensi mahasiswa."
        >
            <x-ui.button href="{{ route('admin.question-banks.index') }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE REPOSITORI</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl">
            <x-slot:header>Konfigurasi Registri & Domain</x-slot:header>

            <form action="{{ route('admin.question-banks.store') }}" method="POST" class="space-y-10">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    {{-- Basic Info --}}
                    <div class="lg:col-span-2 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-forms.form-group label="Penamaan Bank" name="name" required>
                                <x-ui.input 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    placeholder="e.g. UTS Pemrograman Terorientasi Objek" 
                                    class="text-lg font-black italic tracking-tighter"
                                    required 
                                />
                            </x-forms.form-group>

                            <x-forms.form-group label="Target Module Linkage" name="material_id" required>
                                <select name="material_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required>
                                    <option value="">-- HUBUNGKAN KE MODUL --</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>{{ strtoupper($material->title) }}</option>
                                    @endforeach
                                </select>
                            </x-forms.form-group>
                        </div>

                        <x-forms.form-group label="Deskripsi Operasional" name="description">
                            <x-ui.input type="textarea" name="description" rows="5" value="{{ old('description') }}" placeholder="Jelaskan cakupan soal dalam bank ini..." />
                        </x-forms.form-group>
                    </div>

                    {{-- Protocol Info --}}
                    <div class="lg:col-span-1">
                        <div class="h-full p-8 bg-slate-900 rounded-[2rem] relative overflow-hidden flex flex-col justify-center">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/10 blur-3xl"></div>
                            <div class="relative z-10">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-2xl bg-blue-600/20 text-blue-500 flex items-center justify-center">
                                        <i class="fas fa-shield-virus text-sm"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Protokol Keamanan</span>
                                </div>
                                <p class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-wider">
                                    Bank soal ini akan bertindak sebagai container data untuk pertanyaan-pertanyaan berbobot adaptif di dalam ekosistem OOPEDIA.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-database text-xs"></i>
                        </div>
                        <div>
                            <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-900 italic mb-0">Mesin Persistensi</h6>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-0">Siap sinkronisasi repositori</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <x-ui.button variant="ghost" href="{{ route('admin.question-banks.index') }}" class="text-slate-400 font-black italic uppercase text-[10px] tracking-widest">BATALKAN</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="lg" class="shadow-xl shadow-blue-500/30 font-black italic tracking-tighter" icon="fas fa-save">INISIALISASI REPOSITORI</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>

</x-layouts.app>