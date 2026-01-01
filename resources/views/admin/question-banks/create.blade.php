<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="question-banks" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Tambah Bank Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <x-ui.card class="my-4">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Tambah Bank Soal Baru</h6>
                            </div>
                        </x-slot:header>

                        <div class="card-body px-0 pb-2">
                            <form method="POST" action="{{ route('admin.question-banks.store') }}" class="p-4">
                                @csrf
                                
                                @if($errors->any())
                                    <div class="mb-4">
                                        <x-ui.alert type="warning" dismissible>
                                            @foreach($errors->all() as $error)
                                                {{ $error }}<br>
                                            @endforeach
                                        </x-ui.alert>
                                    </div>
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <x-forms.form-group label="Nama Bank Soal" name="name" required>
                                            <x-ui.input name="name" value="{{ old('name') }}" required />
                                        </x-forms.form-group>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <x-forms.form-group label="Deskripsi" name="description">
                                            <x-ui.input type="textarea" name="description" rows="4" value="{{ old('description') }}" />
                                        </x-forms.form-group>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @php
                                            $materialOptions = $materials->pluck('title', 'id')->toArray();
                                        @endphp
                                        <x-forms.form-group label="Materi" name="material_id" required>
                                            <x-forms.select name="material_id" :options="$materialOptions" selected="{{ old('material_id') }}" required placeholder="-- Pilih Materi --" />
                                        </x-forms.form-group>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 mt-3">
                                        <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                                        <x-ui.button variant="outline" href="{{ route('admin.question-banks.index') }}">Batal</x-ui.button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layouts.app>