<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200" theme="admin">
    <x-navigation.sidebar activePage="formulas" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Daftar Formula" />
        <div class="container-fluid py-4">
            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('admin.formulas.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-outline my-3">
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, key, atau deskripsi..." value="{{ request('search') }}" style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline my-3 focused is-focused">
                            <select name="scope" class="form-control" style="height: 50px;">
                                <option value="">Semua Scope</option>
                                @foreach(\App\Models\Formula::SCOPES as $key => $label)
                                    <option value="{{ $key }}" {{ request('scope') == $key ? 'selected' : '' }}>
                                        filter: {{ $label }}
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
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Formula</h6>
                                <x-ui.button variant="light" size="sm" href="{{ route('admin.formulas.create') }}" icon="add" class="me-3">
                                    Buat Formula Baru
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
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Formula</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Expression</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Return Type</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Scope</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($formulas as $formula)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $formula->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $formula->key }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="text-xs">{{ Str::limit($formula->expression, 50) }}</code>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge badge-sm bg-gradient-info">{{ $formula->return_type }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge badge-sm bg-gradient-secondary">{{ $formula->scope }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($formula->is_active)
                                                    <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <x-ui.button variant="info" size="xs" href="{{ route('admin.formulas.edit', $formula) }}" icon="edit">
                                                        Edit
                                                    </x-ui.button>
                                                    <form action="{{ route('admin.formulas.toggle-status', $formula) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.button type="submit" variant="{{ $formula->is_active ? 'warning' : 'success' }}" size="xs">
                                                            {{ $formula->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                        </x-ui.button>
                                                    </form>
                                                    <form action="{{ route('admin.formulas.destroy', $formula) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus formula ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.button type="submit" variant="danger" size="xs" icon="delete">
                                                            Hapus
                                                        </x-ui.button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <p class="text-secondary mb-0">Belum ada formula</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($formulas->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $formulas->links() }}
                            </div>
                            @endif
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
</x-layouts.app>
