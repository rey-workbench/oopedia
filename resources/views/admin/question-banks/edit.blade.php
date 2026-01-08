<x-layouts.app title="Edit Bank Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Update Data Repository"
            subtitle="Modifikasi spesifikasi dan dokumentasi bank soal yang terdaftar."
        >
            <x-ui.button href="{{ route('admin.question-banks.index') }}" variant="ghost" icon="fas fa-arrow-left">ABORT UPDATES</x-ui.button>
        </x-ui.page-header>

        <form action="{{ route('admin.question-banks.update', $questionBank) }}" method="POST" class="space-y-12">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <div class="lg:col-span-2 space-y-8">
                    <x-ui.card class="border-slate-100 shadow-2xl">
                        <x-slot:header>General Metadata</x-slot:header>
                        <div class="space-y-8">
                            <x-forms.form-group label="Bank Designation" name="name" required>
                                <x-ui.input 
                                    name="name" 
                                    value="{{ old('name', $questionBank->name) }}" 
                                    placeholder="e.g. UTS Pemrograman Terorientasi Objek" 
                                    class="text-lg font-black italic tracking-tighter"
                                    required 
                                />
                            </x-forms.form-group>

                            <x-forms.form-group label="Operational Description" name="description">
                                <x-ui.input type="textarea" name="description" rows="8" value="{{ old('description', $questionBank->description) }}" placeholder="Jelaskan cakupan soal dalam bank ini..." />
                            </x-forms.form-group>
                        </div>
                    </x-ui.card>
                </div>

                <div class="lg:col-span-1 space-y-8">
                    <x-ui.card class="bg-indigo-600 border-0 shadow-2xl overflow-hidden relative">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 blur-3xl"></div>
                        <x-slot:header class="border-indigo-500/30">
                            <span class="text-white font-black italic tracking-widest text-[10px] uppercase">Technical Specs</span>
                        </x-slot:header>
                        
                        <div class="space-y-8">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-200 block mb-2">Attached Core Module</span>
                                <div class="flex items-center gap-3 p-4 bg-white/10 rounded-2xl border border-white/10">
                                    <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white">
                                        <i class="fas fa-layer-group text-xs"></i>
                                    </div>
                                    <span class="text-xs font-black italic text-white uppercase">{{ $questionBank->material->title }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                    <span class="text-[8px] font-black uppercase tracking-widest text-indigo-300 block mb-1">TOTAL Q'S</span>
                                    <span class="text-2xl font-black italic text-white tracking-tighter">{{ $questionBank->questions_count ?? $questionBank->questions->count() }}</span>
                                </div>
                                <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                    <span class="text-[8px] font-black uppercase tracking-widest text-indigo-300 block mb-1">DATA'S STATUS</span>
                                    <span class="text-[10px] font-black italic text-white uppercase tracking-tighter">IMMUTABLE</span>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card class="border-slate-100 shadow-xl p-8 bg-slate-50">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-[1.5rem] bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <i class="fas fa-sync-alt text-xs animate-spin-slow"></i>
                            </div>
                            <div>
                                <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-900 italic">Persistence Sync</h6>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Live updates active</p>
                            </div>
                        </div>
                        <x-ui.button type="submit" variant="primary" size="lg" class="w-full bg-indigo-600 hover:bg-indigo-700 shadow-2xl shadow-indigo-500/40" icon="fas fa-cloud-upload-alt">SYNCHRONIZE DATA</x-ui.button>
                        <x-ui.button variant="ghost" href="{{ route('admin.question-banks.index') }}" class="w-full mt-4 text-slate-400 font-black italic uppercase text-[10px] tracking-widest hover:text-rose-500">TERMINATE CHANGES</x-ui.button>
                    </x-ui.card>
                </div>
            </div>
        </form>
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
