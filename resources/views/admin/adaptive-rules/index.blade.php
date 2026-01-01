<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="adaptive-rules" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Rule-Based Kuis (Forward Chaining)" />
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
                        <div class="my-3">
                            <x-ui.button type="submit" variant="primary" class="w-100" style="height: 50px;" icon="search">
                                Cari
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Adaptive Rules</h6>
                                <x-ui.button variant="light" size="sm" href="{{ route('admin.adaptive-rules.create') }}" icon="add" class="me-3">
                                    Tambah Rule Baru
                                </x-ui.button>
                            </div>
                        </x-slot:header>

                        @if(session('success'))
                            <x-ui.alert type="success" class="mx-4 mt-3" :message="session('success')" />
                        @endif
                        
                        @if(session('error'))
                            <x-ui.alert type="danger" class="mx-4 mt-3" :message="session('error')" />
                        @endif

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
                                                    <x-ui.badge variant="info">{{ $rule->priority }}</x-ui.badge>
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
                                                    <x-ui.button type="submit" variant="{{ $rule->is_active ? 'success' : 'secondary' }}" size="sm" class="mb-0">
                                                        {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </x-ui.button>
                                                </form>
                                            </td>
                                            <td class="align-middle text-center">
                                                <x-ui.button variant="info" size="sm" href="{{ route('admin.adaptive-rules.show', $rule) }}" class="mb-1" icon="visibility" />
                                                <x-ui.button variant="primary" size="sm" href="{{ route('admin.adaptive-rules.edit', $rule) }}" class="mb-1" icon="edit" />
                                                
                                                <form action="{{ route('admin.adaptive-rules.destroy', $rule) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button type="submit" variant="danger" size="sm" class="mb-1" icon="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus rule ini?')" />
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
                    </x-ui.card>
                </div>
            </div>

            <!-- Info Card -->
            <div class="row mt-4">
                <div class="col-12">
                    <x-ui.card>
                        <div class="card-body">
                            <h6 class="mb-3">Tentang Forward Chaining</h6>
                            <p class="text-sm mb-2">Forward chaining adalah metode inferensi yang dimulai dari fakta yang diketahui, kemudian menggunakan aturan untuk menarik kesimpulan baru.</p>
                            <p class="text-sm mb-0"><strong>Cara kerja:</strong> Sistem akan mengevaluasi kondisi (IF) berdasarkan performa mahasiswa, jika kondisi terpenuhi maka aksi (THEN) akan dijalankan. Rules dengan prioritas lebih tinggi akan dievaluasi terlebih dahulu.</p>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />
</x-layouts.app>
