    <div class="space-y-12">
        <x-ui.page-header
            title="Rekonfigurasi Administrator"
            subtitle="Modifikasi parameter akses dan profil untuk entitas {{ $user->name }}."
        >
            <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE DAFTAR</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl">
            <x-slot:header>Registri & Logika Optimasi</x-slot:header>

            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-10">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    {{-- Identity & Rank --}}
                    <div class="lg:col-span-2 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-forms.form-group label="Identitas Global" name="name" required>
                                <x-ui.input 
                                    name="name" 
                                    value="{{ old('name', $user->name) }}" 
                                    placeholder="Nama lengkap subjek" 
                                    class="text-lg font-bold tracking-widest"
                                    required 
                                />
                            </x-forms.form-group>

                            <x-forms.form-group label="Auth Alias (Email)" name="email" required>
                                <x-ui.input 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email', $user->email) }}" 
                                    placeholder="Email elektronik subjek" 
                                    class="text-lg font-bold tracking-widest"
                                    required 
                                />
                            </x-forms.form-group>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-forms.form-group label="Kunci Keamanan Baru (Opsional)" name="password">
                                <x-ui.input type="password" name="password" placeholder="Kosongkan untuk tetap menggunakan kunci saat ini" />
                            </x-forms.form-group>

                            <x-forms.form-group label="Verifikasi Kunci Baru" name="password_confirmation">
                                <x-ui.input type="password" name="password_confirmation" placeholder="Verifikasi inisialisasi kunci keamanan" />
                            </x-forms.form-group>
                        </div>

                        @if(auth()->user()->role_id == 1)
                        <x-forms.form-group label="Peringkat Otorisasi" name="role_id" required>
                            <select name="role_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold tracking-widest outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ strtoupper($role->role_name) }}</option>
                                @endforeach
                            </select>
                        </x-forms.form-group>
                        @endif
                    </div>

                    {{-- Security Parameters --}}
                    <div class="lg:col-span-1">
                        <div class="h-full p-8 bg-indigo-950 rounded-[2rem] relative overflow-hidden flex flex-col justify-center">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-600/10 blur-3xl"></div>
                            <div class="relative z-10 text-center">
                                <div class="w-16 h-16 mx-auto rounded-3xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center mb-6">
                                    <i class="fas fa-fingerprint text-2xl font-bold"></i>
                                </div>
                                <h4 class="text-white text-xs font-bold uppercase tracking-widest mb-4 ">Parameter Keamanan</h4>
                                <p class="text-[10px] font-bold text-indigo-300 leading-relaxed uppercase tracking-wider">
                                    Perubahan parameter keamanan akan segera di sinkronisasi dengan database otentikasi utama.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i class="fas fa-sync-alt text-xs"></i>
                        </div>
                        <div>
                            <h6 class="text-[10px] font-bold uppercase tracking-widest text-slate-900  mb-0">Sinkronisasi Persistensi</h6>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-0">Siap merekonfigurasi status entitas</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <x-ui.button variant="ghost" href="{{ route('admin.users.index') }}" class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">BATALKAN</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="lg" class="shadow-xl shadow-indigo-500/30 bg-indigo-600 hover:bg-indigo-700 font-bold tracking-widest" icon="fas fa-cloud-upload-alt">REKONFIGURASI ENTITAS</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>

    <x-admin.tutorial />
</x-layouts.app>