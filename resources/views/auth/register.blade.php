@php
  $title = 'Daftar - OOPedia';
@endphp

<x-layouts.guest :title="$title">
  <div class="relative min-h-screen flex items-center justify-center p-6 overflow-hidden bg-slate-50">
    {{-- Decorative Background --}}
    <div class="absolute top-0 right-0 w-full h-full">
      <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-600/5 rounded-full blur-[120px]"></div>
      <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-600/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative w-full max-w-xl animate-in fade-in zoom-in duration-700">
      {{-- Logo --}}
      <div class="flex flex-col items-center mb-10">
        <a href="{{ url('/') }}" class="flex items-center gap-4 group">
          <div class="w-16 h-16 bg-white rounded-[2rem] flex items-center justify-center shadow-2xl shadow-slate-200 group-hover:rotate-12 transition-transform duration-500">
            <img src="{{ asset('images/logo.png') }}" alt="OOPedia" class="w-10 h-auto">
          </div>
          <div>
            <h2 class="text-3xl font-bold tracking-widest text-slate-900 leading-none">OOPEDIA</h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Learning System</p>
          </div>
        </a>
      </div>

      <x-ui.card padding="p-10" :hover="false">
        <x-slot:header>
          <div class="text-center w-full" data-intro="Belum punya akun? Daftarkan diri Anda untuk mulai belajar Pemrograman Berorientasi Objek secara interaktif." data-step="1">
            <h3 class="text-xl font-bold tracking-widest text-slate-900">BUAT AKUN BARU</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Bergabunglah dengan komunitas OOPedia</p>
          </div>
        </x-slot:header>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
          @csrf

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-forms.form-group label="Nama Lengkap" name="name" :required="true">
              <x-ui.input type="text" name="name" placeholder="John Doe" value="{{ old('name') }}" required />
            </x-forms.form-group>

            <x-forms.form-group label="Alamat Email" name="email" :required="true">
              <x-ui.input type="email" name="email" placeholder="john@example.com" value="{{ old('email') }}" required />
            </x-forms.form-group>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-forms.form-group label="Kata Sandi" name="password" :required="true">
              <x-ui.input type="password" name="password" placeholder="••••••••" required />
            </x-forms.form-group>

            <x-forms.form-group label="Konfirmasi" name="password_confirmation" :required="true">
              <x-ui.input type="password" name="password_confirmation" placeholder="••••••••" required />
            </x-forms.form-group>
          </div>

          <div data-intro="Centang opsi ini jika Anda ingin mendaftar sebagai Pengajar/Dosen." data-step="2">
            <x-forms.checkbox 
              name="register_as_admin" 
              id="register_as_admin" 
              value="1" 
              :checked="old('register_as_admin')"
              label="Daftar sebagai Dosen (Perlu Approval)" 
            />
          </div>

          <div class="pt-2">
            <x-ui.button type="submit" variant="primary" class="w-full" size="lg">
              DAFTAR SEKARANG
              <i class="fas fa-user-plus ml-3"></i>
            </x-ui.button>
          </div>

          <div class="relative py-4">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
            <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-widest"><span class="bg-white px-4 text-slate-400">Sudah punya akun?</span></div>
          </div>

          <div class="text-center">
            <x-ui.button href="{{ route('login') }}" variant="secondary" class="w-full">
              MASUK KE AKUN
            </x-ui.button>
          </div>
        </form>
      </x-ui.card>

      <p class="text-center mt-10 text-[10px] font-bold text-slate-300 uppercase tracking-widest">
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