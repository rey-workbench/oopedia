<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="users" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Manajemen Admin" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
                <x-forms.input-group class="my-3">
                    <x-ui.input name="search" placeholder="Cari berdasarkan nama atau email..." value="{{ request('search') }}" style="height: 50px;" />
                    <x-ui.button type="submit" variant="primary" icon="search" style="height: 50px;">
                        Cari
                    </x-ui.button>
                </x-forms.input-group>
            </form>

            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">
                                    @if(auth()->user()->role_id == 1)
                                        Daftar Pengguna
                                    @else
                                        Daftar Dosen
                                    @endif
                                </h6>
                                @if(auth()->user()->role_id == 1)
                                <div class="d-flex me-3">
                                    <x-ui.button variant="warning" size="sm" href="{{ route('admin.pending-admins') }}" icon="pending" class="me-2 d-flex align-items-center">
                                        Dosen Pending
                                        @php
                                            $pendingAdminsCount = \App\Models\User::where('role_id', 2)->where('is_approved', false)->count();
                                        @endphp
                                        @if($pendingAdminsCount > 0)
                                            <span class="badge bg-danger ms-1">{{ $pendingAdminsCount }}</span>
                                        @endif
                                    </x-ui.button>
                                    <x-ui.button variant="success" size="sm" href="{{ route('admin.users.import') }}" icon="upload_file" class="me-2">
                                        Tambah dengan Excel
                                    </x-ui.button>
                                    <x-ui.button variant="light" size="sm" href="{{ route('admin.users.create') }}" icon="add">
                                        Tambah Pengguna
                                    </x-ui.button>
                                </div>
                                @endif
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            @if(session('success'))
                                <div class="pt-4 px-4">
                                    <x-ui.alert type="success" dismissible>
                                        {{ session('success') }}
                                    </x-ui.alert>
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="pt-4 px-4">
                                    <x-ui.alert type="danger" dismissible>
                                        <strong>Error:</strong> {{ session('error') }}
                                    </x-ui.alert>
                                </div>
                            @endif
                            
                            @if(session('importErrors'))
                                <div class="pt-4 px-4">
                                    <x-ui.alert type="warning" dismissible>
                                        <p>Beberapa baris tidak dapat diimpor:</p>
                                        <ul>
                                            @foreach(session('importErrors') as $error)
                                                <li>Baris {{ $error['row'] }}: {{ implode(', ', $error['errors']) }}</li>
                                            @endforeach
                                        </ul>
                                    </x-ui.alert>
                                </div>
                            @endif
                            
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(auth()->user()->role_id == 1)
                                            <!-- Tampilkan superadmin (role_id = 1) -->
                                            @php
                                                $superadmins = \App\Models\User::where('role_id', 1)->get();
                                            @endphp
                                            @foreach($superadmins as $superadmin)
                                            <tr class="bg-light">
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $superadmin->name }}</h6>
                                                            <x-ui.badge variant="dark">Superadmin</x-ui.badge>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $superadmin->email }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $superadmin->role->role_name }}</p>
                                                </td>
                                                <td>
                                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if(auth()->id() == $superadmin->id)
                                                        <x-ui.button variant="info" size="sm" href="{{ route('admin.users.edit', $superadmin->id) }}">
                                                            Edit
                                                        </x-ui.button>
                                                        <x-ui.button variant="secondary" size="sm" disabled>
                                                            Akun Anda
                                                        </x-ui.button>
                                                    @else
                                                        <x-ui.button variant="info" size="sm" href="{{ route('admin.users.edit', $superadmin->id) }}">
                                                            Edit
                                                        </x-ui.button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif

                                        <!-- Tampilkan dosen (role_id = 2) -->
                                        @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->role->role_name }}</p>
                                            </td>
                                            <td>
                                                @if($user->is_approved)
                                                    <x-ui.badge variant="success">Disetujui</x-ui.badge>
                                                @else
                                                    <x-ui.badge variant="warning">Pending</x-ui.badge>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <x-ui.button variant="info" size="sm" href="{{ route('admin.users.edit', $user->id) }}">
                                                    Edit
                                                </x-ui.button>
                                                @if(auth()->user()->role_id == 1 && auth()->id() != $user->id)
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button type="submit" variant="danger" size="sm" onclick="return confirm('Apakah Anda yakin ingin menghapus dosen ini?')">
                                                        Hapus
                                                    </x-ui.button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-3">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layouts.app>

@push('scripts')
@endpush 