<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="adaptive-rules" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Rule-Based Kuis (Forward Chaining)" />
        <div class="container-fluid py-4">
            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('admin.adaptive-rules.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-outline my-3">
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama rule..." value="{{ request('search') }}" style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline my-3 focused is-focused">
                            <select name="material_id" class="form-control" style="height: 50px;">
                                <option value="">Semua Materi</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>
                                        {{ $material->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-icon btn-3 btn-primary w-100" type="submit" style="height: 50px;">
                            <span class="btn-inner--icon"><i class="material-icons">search</i></span>
                            <span class="btn-inner--text">Cari</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Adaptive Rules</h6>
                                <a href="{{ route('admin.adaptive-rules.create') }}" class="btn btn-sm btn-light me-3">
                                    <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Tambah Rule Baru
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prioritas</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Rule</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kondisi (IF)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi (THEN)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rules as $rule)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <span class="badge bg-info">{{ $rule->priority }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $rule->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ Str::limit($rule->description, 40) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm mb-0">{{ $rule->material ? $rule->material->title : 'Semua Materi' }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-xs"><strong>{{ \App\Models\AdaptiveRule::CONDITION_TYPES[$rule->condition_type] ?? $rule->condition_type }}</strong></span>
                                                    <span class="text-xs text-secondary">
                                                        {{ \App\Models\AdaptiveRule::OPERATORS[$rule->condition_operator] ?? $rule->condition_operator }} 
                                                        {{ $rule->condition_value }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-xs"><strong>{{ \App\Models\AdaptiveRule::ACTION_TYPES[$rule->action_type] ?? $rule->action_type }}</strong></span>
                                                    <span class="text-xs text-secondary">{{ $rule->action_value }}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <form action="{{ route('admin.adaptive-rules.toggle-status', $rule) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-{{ $rule->is_active ? 'success' : 'secondary' }} mb-0">
                                                        {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('admin.adaptive-rules.show', $rule) }}" class="btn btn-sm btn-info mb-1" title="Detail">
                                                    <i class="material-icons text-sm">visibility</i>
                                                </a>
                                                <a href="{{ route('admin.adaptive-rules.edit', $rule) }}" class="btn btn-sm btn-primary mb-1" title="Edit">
                                                    <i class="material-icons text-sm">edit</i>
                                                </a>
                                                <form action="{{ route('admin.adaptive-rules.destroy', $rule) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus rule ini?')" title="Hapus">
                                                        <i class="material-icons text-sm">delete</i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <p class="text-sm mb-0">Belum ada adaptive rule</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $rules->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Tentang Forward Chaining</h6>
                            <p class="text-sm mb-2">Forward chaining adalah metode inferensi yang dimulai dari fakta yang diketahui, kemudian menggunakan aturan untuk menarik kesimpulan baru.</p>
                            <p class="text-sm mb-0"><strong>Cara kerja:</strong> Sistem akan mengevaluasi kondisi (IF) berdasarkan performa mahasiswa, jika kondisi terpenuhi maka aksi (THEN) akan dijalankan. Rules dengan prioritas lebih tinggi akan dievaluasi terlebih dahulu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />
</x-layout>
