<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="pending-users" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Admin Pending" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Admin Menunggu Persetujuan</h6>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            <div class="p-4">
                                @if(session('success'))
                                    <x-ui.alert type="success" dismissible>
                                        {{ session('success') }}
                                    </x-ui.alert>
                                @endif
                                
                                @if(session('error'))
                                    <x-ui.alert type="danger" dismissible>
                                        {{ session('error') }}
                                    </x-ui.alert>
                                @endif
                                
                                @if(count($pendingAdmins) > 0)
                                    <x-ui.table>
                                        <thead>
                                            <tr>
                                                <x-ui.th>Nama</x-ui.th>
                                                <x-ui.th class="ps-2">Email</x-ui.th>
                                                <x-ui.th class="ps-2">Tanggal Daftar</x-ui.th>
                                                <x-ui.th class="text-center">Aksi</x-ui.th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pendingAdmins as $admin)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $admin->name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $admin->email }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $admin->created_at->format('d M Y H:i') }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <form action="{{ route('admin.users.approve', $admin->id) }}" method="POST" class="me-2">
                                                            @csrf
                                                            <x-ui.button type="submit" variant="success" size="sm">
                                                                Setujui
                                                            </x-ui.button>
                                                        </form>
                                                        <form action="{{ route('admin.users.reject', $admin->id) }}" method="POST">
                                                            @csrf
                                                            <x-ui.button type="submit" variant="danger" size="sm" onclick="return confirm('Apakah Anda yakin ingin menolak admin ini? Akun akan diubah menjadi mahasiswa.')">
                                                                Tolak
                                                            </x-ui.button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </x-ui.table>
                                @else
                                    <x-ui.alert type="info">
                                        Tidak ada admin yang menunggu persetujuan.
                                    </x-ui.alert>
                                @endif
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-slot:scripts>
        <script src="{{ asset('js/admin/users/pending.js') }}"></script>
    </x-slot:scripts>
    <x-admin.tutorial />

</x-layouts.app> 