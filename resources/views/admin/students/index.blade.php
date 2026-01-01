<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="students" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Data Mahasiswa" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.students.index') }}" class="mb-3">
                <x-forms.input-group class="my-3">
                    <x-ui.input name="search" placeholder="Cari berdasarkan nama..." value="{{ request('search') }}" style="height: 50px;" />
                    <x-ui.button type="submit" variant="primary" icon="search" style="height: 50px;">
                        Cari
                    </x-ui.button>
                </x-forms.input-group>
            </form>

            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
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
                                    {{ session('error') }}
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

                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Data Mahasiswa</h6>
                                <div class="d-flex me-3">
                                    <x-ui.button variant="success" size="sm" href="{{ route('admin.students.import') }}" icon="upload_file" class="me-2">
                                        Tambah dengan Excel
                                    </x-ui.button>
                                </div>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Soal Dijawab</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progress Keseluruhan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $student->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $student->email }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold">{{ $student->total_answered_questions ?? 0 }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="progress" style="height: 8px; width: 80%; margin: 0 auto;">
                                                    <div class="progress-bar bg-gradient-info" role="progressbar" 
                                                         style="width: {{ $student->overall_progress }}%" 
                                                         aria-valuenow="{{ $student->overall_progress }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <span class="text-xs font-weight-bold">{{ $student->overall_progress }}%</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <x-ui.button variant="info" size="sm" href="{{ route('admin.students.progress', $student) }}" icon="visibility">
                                                        Detail
                                                    </x-ui.button>
                                                    <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.button type="submit" variant="danger" size="sm" icon="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?')">
                                                            Hapus
                                                        </x-ui.button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <p class="text-sm mb-0">Belum ada data mahasiswa</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $students->links() }}
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layouts.app>