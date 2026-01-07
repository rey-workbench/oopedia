<x-layouts.app title="Profil Mahasiswa" theme="mahasiswa">
<div class="container-fluid px-2 px-md-4">
    <!-- Header background dengan gambar -->
    <div class="page-header min-height-300 border-radius-xl mt-4"
        style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
        <span class="mask bg-gradient-primary opacity-6"></span>
    </div>

    <x-ui.card class="mx-3 mx-md-4 mt-n6">
        <!-- Profile info section -->
        <div class="row gx-4 mb-2">
            <div class="col-auto">
                <div class="avatar avatar-xl position-relative">
                    <img src="{{ asset('images/accountinfo.gif') }}" alt="Profile Avatar"
                        class="w-100 border-radius-lg shadow-sm">
                </div>
            </div>
            <div class="col-auto my-auto">
                <div class="h-100">
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="mb-0 font-weight-normal text-sm">Mahasiswa</p>
                </div>
            </div>
        </div>

        <!-- Alert section -->
        @if (session('success'))
            <x-ui.alert type="success" dismissible="true">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('mahasiswa.profile.update') }}">
            @csrf
            @method('PUT')
            
            <x-forms.form-group label="Nama" name="name" required="true">
                <x-ui.input 
                    name="name" 
                    value="{{ old('name', auth()->user()->name) }}" 
                    required />
            </x-forms.form-group>

            <x-forms.form-group label="Email" name="email" required="true">
                <x-ui.input 
                    type="email" 
                    name="email" 
                    value="{{ old('email', auth()->user()->email) }}" 
                    required />
            </x-forms.form-group>

            <div class="password-section mt-4">
                <h5 class="mb-4">Ubah Password</h5>
                
                <x-forms.form-group label="Password Baru" name="password" help-text="Kosongkan jika tidak ingin mengubah password">
                    <x-ui.input 
                        type="password" 
                        name="password" />
                </x-forms.form-group>

                <x-forms.form-group label="Konfirmasi Password Baru" name="password_confirmation">
                    <x-ui.input 
                        type="password" 
                        name="password_confirmation" />
                </x-forms.form-group>
            </div>

            <div class="text-center mt-4">
                <x-ui.button type="submit" variant="primary">
                    <i class="fas fa-save me-2"></i>
                    Simpan Perubahan
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
</x-layouts.app>