<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="students" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Tambahkan Data Mahasiswa" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Tambahkan Data Mahasiswa</h6>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            <div class="p-4">
                                @if(session('error'))
                                    <x-ui.alert type="danger" dismissible>
                                        {{ session('error') }}
                                    </x-ui.alert>
                                @endif

                                @if($errors->any())
                                    <x-ui.alert type="warning" dismissible>
                                        @foreach($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </x-ui.alert>
                                @endif

                                <div class="mb-4">
                                    <h5>Petunjuk Tambah Data:</h5>
                                    <ol>
                                        <li>File harus dalam format Excel (.xlsx, .xls) atau CSV (.csv)</li>
                                        <li>File harus memiliki kolom: name, email, password</li>
                                        <li>Mahasiswa yang ditambahkan akan otomatis disetujui</li>
                                    </ol>
                                    
                                    <div class="mt-3">
                                        <x-ui.button variant="info" size="sm" href="{{ route('admin.students.download-template') }}" icon="download">
                                            Download Template
                                        </x-ui.button>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.students.process-import') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">File Excel/CSV</label>
                                                <x-forms.input-group>
                                                    <x-ui.input type="file" name="excel_file" class="form-control" required accept=".xlsx,.xls,.csv" />
                                                </x-forms.input-group>
                                                @error('excel_file')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-ui.button type="submit" variant="primary" icon="upload_file">
                                                Tambahkan
                                            </x-ui.button>
                                            <x-ui.button variant="outline" href="{{ route('admin.students.index') }}">Batal</x-ui.button>
                                        </div>
                                    </div>
                                </form>

                                <div class="mt-4">
                                    <h5>Informasi File:</h5>
                                    <ul>
                                        <li>Maksimal Ukuran File: {{ (int)(ini_get('upload_max_filesize')) }} MB</li>
                                    </ul>
                                </div>
                                
                                @if(session('importErrors'))
                                    <div class="mt-4">
                                        <h5>Error pada baris:</h5>
                                        <x-ui.table>
                                            <thead>
                                                <tr>
                                                    <x-ui.th>Baris</x-ui.th>
                                                    <x-ui.th>Error</x-ui.th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(session('importErrors') as $error)
                                                    <tr>
                                                        <td>{{ $error['row'] }}</td>
                                                        <td>
                                                            <ul class="mb-0">
                                                                @foreach($error['errors'] as $message)
                                                                    <li>{{ $message }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </x-ui.table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />
</x-layouts.app> 