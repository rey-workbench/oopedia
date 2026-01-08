<x-layouts.app title="Tambah Bank Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Question Bank Architect"
            subtitle="Inisialisasi repositori soal baru untuk evaluasi kompetensi mahasiswa."
        >
            <x-ui.button href="{{ route('admin.question-banks.index') }}" variant="ghost" icon="fas fa-arrow-left">BACK TO REPOSITORY</x-ui.button>
        </x-ui.page-header>

        <form action="{{ route('admin.question-banks.store') }}" method="POST" class="space-y-12">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <div class="lg:col-span-2 space-y-8">
                    <x-ui.card class="border-slate-100 shadow-2xl">
                        <x-slot:header>Registry Configuration</x-slot:header>
                        <div class="space-y-8">
                            <x-forms.form-group label="Bank Designation" name="name" required>
                                <x-ui.input name="name" value="{{ old('name') }}" placeholder="e.g. UTS Pemrograman Terorientasi Objek" required />
                            </x-forms.form-group>

                            <x-forms.form-group label="Operational Description" name="description">
                                <x-ui.input type="textarea" name="description" rows="5" value="{{ old('description') }}" placeholder="Jelaskan cakupan soal dalam bank ini..." />
                            </x-forms.form-group>
                        </div>
                    </x-ui.card>
                </div>

                <div class="lg:col-span-1 space-y-8">
                    <x-ui.card class="bg-slate-900 border-0 shadow-2xl overflow-hidden relative">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/10 blur-3xl"></div>
                        <x-slot:header class="border-slate-800">
                            <span class="text-white font-black italic tracking-widest text-[10px] uppercase">Domain Scope</span>
                        </x-slot:header>
                        
                        <div class="space-y-6">
                            <x-forms.form-group label="Target Module Linkage" name="material_id" required>
                                <select name="material_id" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-sm font-bold text-white focus:ring-4 focus:ring-blue-500/20 transition-all outline-none" required>
                                    <option value="">-- ATTACH TO MODULE --</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>{{ strtoupper($material->title) }}</option>
                                    @endforeach
                                </select>
                            </x-forms.form-group>

                            <div class="p-6 bg-slate-800 rounded-[2rem] border border-slate-700 shadow-inner">
                                <div class="flex items-center gap-3 mb-4">
                                    <i class="fas fa-shield-virus text-blue-500"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 italic">Security Protocol</span>
                                </div>
                                <p class="text-[9px] font-bold text-slate-500 leading-relaxed uppercase tracking-wider">
                                    Bank soal ini akan bertindak sebagai container data untuk pertanyaan-pertanyaan berbobot adaptif.
                                </p>
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card class="border-slate-100 shadow-xl p-8 bg-slate-50">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-[1.5rem] bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                                <i class="fas fa-database text-xs"></i>
                            </div>
                            <div>
                                <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-900 italic">Data Persistence</h6>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Ready to sync</p>
                            </div>
                        </div>
                        <x-ui.button type="submit" variant="primary" size="lg" class="w-full shadow-2xl shadow-blue-500/40" icon="fas fa-save">INITIALIZE REPOSITORY</x-ui.button>
                        <x-ui.button variant="ghost" href="{{ route('admin.question-banks.index') }}" class="w-full mt-4 text-slate-400 font-black italic">ABORT OPERATION</x-ui.button>
                    </x-ui.card>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>