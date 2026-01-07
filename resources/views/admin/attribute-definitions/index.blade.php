<x-layouts.app title="Kamus Atribut" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="attributes" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Kamus Atribut" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="mb-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Variabel & Atribut Sistem</h6>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Atribut</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe Data</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sumber</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attributes as $groupName => $groupAttributes)
                                            <tr class="bg-gray-100">
                                                <td colspan="4" class="text-uppercase text-secondary text-xs font-weight-bolder px-4 py-2">
                                                    {{ $groupName }}
                                                </td>
                                            </tr>
                                            @foreach($groupAttributes as $attr)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-3 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $attr->label }}</h6>
                                                            <p class="text-xs text-secondary mb-0 font-monospace">{{ $attr->key }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm bg-gradient-secondary">{{ $attr->type }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    @if($attr->is_computed)
                                                        <span class="badge badge-sm bg-gradient-info">
                                                            <i class="material-icons text-xxs position-relative" style="top: 1px;">functions</i> Formula
                                                        </span>
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-success">
                                                            <i class="material-icons text-xxs position-relative" style="top: 1px;">sensors</i> System
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $attr->description ?? 'Tidak ada deskripsi' }}
                                                    </p>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-secondary">
                                                    Belum ada definisi atribut.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </x-ui.card>
                    
                    <x-ui.alert type="info">
                        <strong>Info:</strong> Halaman ini adalah referensi (read-only). 
                        Atribut <strong>Regular</strong> berasal dari sensor sistem, sedangkan atribut <strong>Computed</strong> dihitung menggunakan Formula.
                    </x-ui.alert>
                </div>
            </div>
        </div>
    </main>
</x-layouts.app>
