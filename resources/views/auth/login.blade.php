@php
    $title = 'Login - OOPedia';
@endphp

<x-layouts.guest :title="$title">
    <div class="relative min-h-screen flex items-center justify-center p-6 overflow-hidden bg-slate-50">
        {{-- Decorative Background --}}
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-600/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative w-full max-w-lg animate-in fade-in zoom-in duration-700">
            {{-- Logo --}}
            <div class="flex flex-col items-center mb-10">
                <a href="{{ url('/') }}" class="flex items-center gap-4 group">
                    <div class="w-16 h-16 bg-white rounded-[2rem] flex items-center justify-center shadow-2xl shadow-slate-200 group-hover:rotate-12 transition-transform duration-500">
                        <img src="{{ asset('images/logo.png') }}" alt="OOPedia" class="w-10 h-auto">
                    </div>
                    <div>
                        <h2 class="text-3xl font-black italic tracking-tighter text-slate-900 leading-none">OOPEDIA</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-1">Learning System</p>
                    </div>
                </a>
            </div>

            <x-ui.card padding="p-10" :hover="false">
                <x-slot:header>
                    <div class="text-center w-full" data-intro="Selamat datang di OOPedia! Masuk ke akun Anda untuk memulai petualangan belajar PBO." data-step="1">
                        <h3 class="text-xl font-black italic tracking-tight text-slate-900">MASUK KE AKUN</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Gunakan akun OOPedia Anda</p>
                    </div>
                </x-slot:header>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    @if (Session::has('status'))
                        <x-ui.alert variant="success" dismissible="true">
                            {{ Session::get('status') }}
                        </x-ui.alert>
                    @endif

                    <div data-intro="Masukkan alamat email yang telah terdaftar." data-step="2">
                        <x-forms.form-group label="Alamat Email" name="email" :required="true">
                            <x-ui.input type="email" name="email" placeholder="nama@email.com" value="{{ old('email') }}" required />
                        </x-forms.form-group>
                    </div>

                    <div data-intro="Dan masukkan kata sandi Anda." data-step="3">
                        <x-forms.form-group label="Kata Sandi" name="password" :required="true">
                            <x-ui.input type="password" name="password" placeholder="••••••••" required />
                        </x-forms.form-group>
                    </div>

                    <div class="pt-2" data-intro="Klik tombol ini untuk masuk." data-step="4">
                        <x-ui.button type="submit" variant="primary" class="w-full" size="lg">
                            MASUK SEKARANG
                            <i class="fas fa-arrow-right ml-3"></i>
                        </x-ui.button>
                    </div>

                    <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                        <div class="relative flex justify-center text-[10px] uppercase font-black tracking-widest italic"><span class="bg-white px-4 text-slate-400">Atau</span></div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <p class="text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Tidak memiliki akun? 
                            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Daftar Gratis</a>
                        </p>
                        
                        <a href="{{ route('guest.login') }}" class="flex items-center justify-center gap-2 text-[10px] font-black italic uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors">
                            <i class="fas fa-ghost"></i>
                            Masuk Sebagai Tamu
                        </a>
                    </div>
                </form>
            </x-ui.card>

            <p class="text-center mt-10 text-[10px] font-bold text-slate-300 uppercase tracking-[0.3em]">
                &copy; {{ date('Y') }} OOPEDIA TEAM. ALL RIGHTS RESERVED.
            </p>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.OopediaTour) {
                window.OopediaTour.init().start();
            }
        });
    </script>
    @endpush
</x-layouts.guest>
