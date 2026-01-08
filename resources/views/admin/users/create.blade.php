    <div class="space-y-12">
        <x-ui.page-header
            title="Pembuatan Administrator"
            subtitle="Otorisasi entitas baru ke dalam pusat kendali sistem."
        >
            <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE DAFTAR</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl">
            <x-slot:header>Arsitektur Kredensial & Identitas</x-slot:header>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-10">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    {{-- Identity & Role --}}
                    <div class="lg:col-span-2 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-forms.form-group label="Identitas Lengkap" name="name" required>
                                <x-ui.input 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    placeholder="Nama lengkap subjek" 
                                    class="text-lg font-black italic tracking-tighter"
                                    required 
                                />
                            </x-forms.form-group>

                            <x-forms.form-group label="Alias Digital (Email)" name="email" required>
                                <x-ui.input 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    placeholder="Email elektronik subjek" 
                                    class="text-lg font-black italic tracking-tighter"
                                    required 
                                />
                            </x-forms.form-group>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-forms.form-group label="Kunci Keamanan" name="password" required>
                                <x-ui.input type="password" name="password" placeholder="Inisialisasi kata sandi" required />
                            </x-forms.form-group>

                            <x-forms.form-group label="Verifikasi Kunci Keamanan" name="password_confirmation" required>
                                <x-ui.input type="password" name="password_confirmation" placeholder="Inisialisasi ulang kata sandi" required />
                            </x-forms.form-group>
                        </div>

                        <x-forms.form-group label="Otorisasi Peran Sistem" name="role_id" required>
                            <select name="role_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ strtoupper($role->role_name) }}</option>
                                @endforeach
                            </select>
                        </x-forms.form-group>
                    </div>

                    {{-- Security Protocol --}}
                    <div class="lg:col-span-1">
                        <div class="h-full p-8 bg-slate-900 rounded-[2rem] relative overflow-hidden flex flex-col justify-center">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/10 blur-3xl"></div>
                            <div class="relative z-10 text-center">
                                <div class="w-16 h-16 mx-auto rounded-3xl bg-blue-600/20 text-blue-500 flex items-center justify-center mb-6">
                                    <i class="fas fa-user-shield text-2xl"></i>
                                </div>
                                <h4 class="text-white text-xs font-black uppercase tracking-widest mb-4 italic">Protokol Keamanan</h4>
                                <p class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-wider">
                                    Pastikan identitas dan level otorisasi sesuai dengan kebijakan keamanan data OOPEDIA.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-microchip text-xs"></i>
                        </div>
                        <div>
                            <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-900 italic mb-0">Otorisasi Utama</h6>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-0">Siap mengotorisasi entitas baru</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <x-ui.button variant="ghost" href="{{ route('admin.users.index') }}" class="text-slate-400 font-black italic uppercase text-[10px] tracking-widest">BATAL</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="lg" class="shadow-xl shadow-blue-500/30 font-black italic tracking-tighter" icon="fas fa-user-plus">OTORISASI ENTITAS</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>

    <x-admin.tutorial />
</x-layouts.app>