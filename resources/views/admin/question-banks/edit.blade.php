    <div class="space-y-12">
        <x-ui.page-header
            title="Perbarui Repositori Data"
            subtitle="Modifikasi spesifikasi dan dokumentasi bank soal yang terdaftar."
        >
            <x-ui.button href="{{ route('admin.question-banks.index') }}" variant="ghost" icon="fas fa-arrow-left">BATALKAN PERUBAHAN</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl">
            <x-slot:header>Registri & Logika Optimasi</x-slot:header>

            <form action="{{ route('admin.question-banks.update', $questionBank) }}" method="POST" class="space-y-10">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    {{-- General Info --}}
                    <div class="lg:col-span-2 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-forms.form-group label="Penamaan Bank" name="name" required>
                                <x-ui.input 
                                    name="name" 
                                    value="{{ old('name', $questionBank->name) }}" 
                                    placeholder="e.g. UTS Pemrograman Terorientasi Objek" 
                                    class="text-lg font-black italic tracking-tighter"
                                    required 
                                />
                            </x-forms.form-group>

                            <x-forms.form-group label="Attached Core Module" name="material_id">
                                <div class="px-6 py-4 bg-indigo-50 border border-indigo-100 rounded-2xl text-xs font-black italic text-indigo-900 border-l-4 border-l-indigo-600 uppercase flex items-center justify-between">
                                    <span>{{ $questionBank->material->title }}</span>
                                    <i class="fas fa-lock text-[10px] opacity-30"></i>
                                </div>
                                <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-widest pl-1">Tautan modul tidak dapat diubah setelah inisialisasi</p>
                            </x-forms.form-group>
                        </div>

                        <x-forms.form-group label="Operational Description" name="description">
                            <x-ui.input type="textarea" name="description" rows="5" value="{{ old('description', $questionBank->description) }}" placeholder="Jelaskan cakupan soal dalam bank ini..." />
                        </x-forms.form-group>
                    </div>

                    {{-- Technical Status --}}
                    <div class="lg:col-span-1">
                        <div class="h-full p-8 bg-indigo-950 rounded-[2rem] relative overflow-hidden flex flex-col justify-center">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-600/10 blur-3xl"></div>
                            <div class="relative z-10">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 rounded-2xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center">
                                        <i class="fas fa-microchip text-sm"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-300 italic">Spesifikasi Teknis</span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                        <span class="text-[8px] font-black uppercase tracking-widest text-indigo-300 block mb-1">TOTAL Q'S</span>
                                        <span class="text-xl font-black italic text-white tracking-tighter">{{ $questionBank->questions_count ?? $questionBank->questions->count() }}</span>
                                    </div>
                                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                        <span class="text-[8px] font-black uppercase tracking-widest text-indigo-300 block mb-1">STATUS</span>
                                        <span class="text-[9px] font-black italic text-white uppercase tracking-tighter">LIVE</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i class="fas fa-sync-alt text-xs animate-spin-slow"></i>
                        </div>
                        <div>
                            <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-900 italic mb-0">Sinkronisasi Persistensi</h6>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-0">Pembaruan langsung dan perversi aktif</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <x-ui.button variant="ghost" href="{{ route('admin.question-banks.index') }}" class="text-slate-400 font-black italic uppercase text-[10px] tracking-widest">BATALKAN</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="lg" class="shadow-xl shadow-indigo-500/30 bg-indigo-600 hover:bg-indigo-700 font-black italic tracking-tighter" icon="fas fa-cloud-upload-alt">SINKRONISASI DATA</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>

    <style>
        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

</x-layouts.app>
