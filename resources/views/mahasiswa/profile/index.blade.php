<x-layouts.app title="Profil Mahasiswa" theme="mahasiswa">
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Profile Header --}}
            <div class="relative mb-12">
                <div class="h-64 w-full bg-gradient-to-br from-indigo-600 via-blue-700 to-blue-800 rounded-[2.5rem] shadow-xl overflow-hidden relative">
                    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1557683316-973673baf926?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80')] opacity-20 bg-cover bg-center"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>

                <div class="absolute -bottom-6 left-8 right-8" data-intro="Di sini kamu bisa melihat ringkasan identitas dan status akunmu." data-step="6">
                    <div class="bg-white/80 backdrop-blur-xl rounded-[2rem] p-6 shadow-2xl border border-white/50 flex flex-col md:flex-row items-center gap-6">
                        <div class="relative">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg shrink-0">
                                <img src="{{ asset('images/accountinfo.gif') }}" alt="Profile Avatar" class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white border-2 border-white shadow-lg">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                        </div>
                        <div class="text-center md:text-left flex-1">
                            <h3 class="text-[10px] font-bold text-blue-600 uppercase tracking-[0.2em] mb-1">Akun Terverifikasi</h3>
                            <h2 class="text-3xl font-bold text-gray-900  tracking-widest mb-1">{{ auth()->user()->name }}</h2>
                            <p class="text-sm font-bold text-gray-500  uppercase">Mahasiswa Terdaftar</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Section --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 md:p-12 mb-10">
                @if (session('success'))
                    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-100">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <p class="text-emerald-800 font-bold ">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('mahasiswa.profile.update') }}" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <x-forms.form-group label="Nama Lengkap" name="name" required="true">
                            <x-ui.input 
                                name="name" 
                                value="{{ old('name', auth()->user()->name) }}" 
                                class="rounded-2xl border-gray-100 bg-gray-50/50 py-4 font-bold"
                                required />
                        </x-forms.form-group>

                        <x-forms.form-group label="Alamat Email" name="email" required="true">
                            <x-ui.input 
                                type="email" 
                                name="email" 
                                value="{{ old('email', auth()->user()->email) }}" 
                                class="rounded-2xl border-gray-100 bg-gray-50/50 py-4 font-bold"
                                required />
                        </x-forms.form-group>
                    </div>

                    <div class="pt-8 border-t border-gray-100">
                        <div class="flex items-center gap-3 mb-8" data-intro="Gunakan bagian ini untuk memperbarui kata sandi jika diperlukan." data-step="7">
                            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shadow-inner">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900  tracking-widest uppercase">Keamanan Akun</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <x-forms.form-group label="Password Baru" name="password">
                                <x-ui.input 
                                    type="password" 
                                    name="password" 
                                    class="rounded-2xl border-gray-100 bg-gray-50/50 py-4 font-bold"
                                    placeholder="••••••••" />
                                <x-slot:helpText>
                                    <span class="text-[10px] font-bold text-gray-400 ">Kosongkan jika tidak ingin mengubah password</span>
                                </x-slot:helpText>
                            </x-forms.form-group>

                            <x-forms.form-group label="Konfirmasi Password" name="password_confirmation">
                                <x-ui.input 
                                    type="password" 
                                    name="password_confirmation" 
                                    class="rounded-2xl border-gray-100 bg-gray-50/50 py-4 font-bold"
                                    placeholder="••••••••" />
                            </x-forms.form-group>
                        </div>
                    </div>

                    <div class="pt-8 flex justify-end">
                        <button type="submit" class="w-full md:w-auto px-12 py-4 bg-gray-900 text-white rounded-2xl font-bold  uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-gray-200 hover:shadow-blue-200 flex items-center justify-center gap-3">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>