<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="materials" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Materi" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.materials.index') }}" class="mb-3">
                <x-forms.input-group class="my-3">
                    <x-ui.input name="search" placeholder="Search" value="{{ request('search') }}" style="height: 42px;" />
                    <x-ui.button type="submit" variant="primary" icon="search" style="height: 42px; margin-bottom: 0;">
                        Cari
                    </x-ui.button>
                </x-forms.input-group>
            </form>
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Materi</h6>
                                <x-ui.button variant="light" size="sm" href="{{ route('admin.materials.create') }}" icon="add" class="me-3">
                                    Tambah Materi
                                </x-ui.button>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cover Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dibuat Oleh</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materials as $material)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $material->title }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ Str::limit(strip_tags($material->content), 50) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($material->media && $material->media->isNotEmpty())
                                                    <div class="material-thumbnail-container">
                                                        <img src="{{ asset($material->media->first()->media_url) }}" 
                                                             alt="{{ $material->title }}" 
                                                             class="material-cover-thumbnail">
                                                    </div>
                                                @else
                                                    <div class="no-image-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $material->creator ? $material->creator->name : 'Admin' }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    {{ $material->created_at->format('d M Y') }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <x-ui.button variant="info" size="sm" href="{{ route('admin.materials.edit', $material->id) }}" icon="edit" />
                                                
                                                <form action="{{ route('admin.materials.destroy', $material->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button type="submit" variant="danger" size="sm" icon="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini?')" />
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
<style>
    /* Perbaikan ukuran gambar di daftar materi admin */
    .material-cover-thumbnail {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e6ed;
    }
    
    /* Untuk container gambar */
    .material-thumbnail-container {
        width: 120px;
        height: 80px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 6px;
    }
    
    /* Untuk placeholder jika tidak ada gambar */
    .no-image-placeholder {
        width: 120px;
        height: 80px;
        background-color: #f0f7ff;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0057B8;
        font-size: 24px;
        border: 1px solid #e0e6ed;
    }
</style>
