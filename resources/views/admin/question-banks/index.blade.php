<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="question-banks" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Bank Soal" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.question-banks.index') }}" class="mb-3">
                <div class="input-group input-group-outline my-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama..." value="{{ request('search') }}" style="height: 50px;">
                    <button class="btn btn-icon btn-3 btn-primary" type="submit" style="height: 50px;">
                        <span class="btn-inner--icon"><i class="material-icons">search</i></span>
                        <span class="btn-inner--text">Cari</span>
                    </button>
                </div>
            </form>

            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Bank Soal</h6>
                                <x-ui.button variant="light" size="sm" href="{{ route('admin.question-banks.create') }}" icon="add" class="me-3">
                                    Tambah Bank Soal
                                </x-ui.button>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            @if(session('success'))
                                <div class="px-4">
                                    <x-ui.alert type="success" dismissible>
                                        {{ session('success') }}
                                    </x-ui.alert>
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="px-4">
                                    <x-ui.alert type="danger" dismissible>
                                        {{ session('error') }}
                                    </x-ui.alert>
                                </div>
                            @endif

                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Bank Soal</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dibuat Oleh</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Dibuat</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($questionBanks as $bank)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $bank->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ Str::limit($bank->description, 50) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm mb-0">{{ $bank->material->title ?? 'Tidak ada materi' }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $bank->creator ? $bank->creator->name : 'Unknown' }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $bank->created_at->format('d/m/Y') }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <!-- Action buttons -->
                                                <div class="d-flex justify-content-center gap-1">
                                                    <x-ui.button variant="info" size="sm" href="{{ route('admin.question-banks.show', $bank) }}" icon="visibility" title="Detail" />
                                                    
                                                    <x-ui.button variant="success" size="sm" href="{{ route('admin.question-banks.manage-questions', $bank) }}" icon="question_answer" title="Kelola Soal" />
                                                    
                                                    <x-ui.button variant="warning" size="sm" href="{{ route('admin.question-banks.configure', $bank) }}" icon="settings" title="Konfigurasi" />
                                                    
                                                    <x-ui.button variant="primary" size="sm" href="{{ route('admin.question-banks.edit', $bank) }}" icon="edit" title="Edit" />
                                                    
                                                    <form action="{{ route('admin.question-banks.destroy', $bank) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.button type="submit" variant="danger" size="sm" icon="delete" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus bank soal ini?')" />
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <p class="text-sm mb-0">Belum ada bank soal</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $questionBanks->links() }}
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layouts.app>

@push('js')
@endpush 