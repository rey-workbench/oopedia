<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="users" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Edit Admin" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Edit Admin</h6>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-4">
                                @csrf
                                @method('PUT')
                                
                                @if($errors->any())
                                    <x-ui.alert type="warning" dismissible>
                                        @foreach($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </x-ui.alert>
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.form-group label="Nama" name="name" required>
                                            <x-ui.input name="name" value="{{ old('name', $user->name) }}" required />
                                        </x-forms.form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.form-group label="Email" name="email" required>
                                            <x-ui.input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                                        </x-forms.form-group>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.form-group label="Password (kosongkan jika tidak ingin mengubah)" name="password">
                                            <x-ui.input type="password" name="password" />
                                        </x-forms.form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.form-group label="Konfirmasi Password" name="password_confirmation">
                                            <x-ui.input type="password" name="password_confirmation" />
                                        </x-forms.form-group>
                                    </div>
                                </div>
                                @if(auth()->user()->role_id == 1)
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.form-group label="Role" name="role_id" required>
                                            <x-forms.select name="role_id" :options="$roles->pluck('role_name', 'id')" :selected="$user->role_id" required />
                                        </x-forms.form-group>
                                    </div>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <x-ui.button type="submit" variant="primary">Update</x-ui.button>
                                        <x-ui.button variant="outline" href="{{ route('admin.users.index') }}">Batal</x-ui.button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layouts.app> 