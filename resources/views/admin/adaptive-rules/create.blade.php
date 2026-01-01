<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.sidebar activePage="adaptive-rules" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Buat Rule Baru" />
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <x-ui.card class="shadow-lg">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="d-flex justify-content-between align-items-center px-3">
                                    <h5 class="text-white mb-0">🎯 Buat Rule Baru</h5>
                                    <x-ui.button variant="light" size="sm" href="{{ route('admin.adaptive-rules.index') }}" icon="arrow_back">
                                        Kembali
                                    </x-ui.button>
                                </div>
                            </div>
                        </x-slot:header>
                        
                        <x-ui.alert type="danger" :message="session('error')" />
                            
                        <form action="{{ route('admin.adaptive-rules.store') }}" method="POST" id="ruleForm">
                            @csrf
                            
                            <x-forms.form-group label="📝 Nama Rule" name="name" required class="mb-4">
                                <x-ui.input name="name" class="form-control-lg" placeholder="Contoh: Naikkan Kesulitan untuk Mahasiswa Pintar" required />
                            </x-forms.form-group>

                            <div class="rule-workspace p-4 mb-4">
                                <div class="rule-section if-section mb-4">
                                    <h6 class="text-info mb-3"><i class="material-icons text-sm">arrow_forward</i> JIKA</h6>
                                    <div class="rule-block mb-2" onclick="showModal('condition')">
                                        <span class="block-icon">📊</span>
                                        <span id="conditionText" class="block-text">Klik untuk pilih kondisi</span>
                                    </div>
                                    <div class="rule-block mb-2" onclick="showModal('operator')">
                                        <span class="block-icon">⚖️</span>
                                        <span id="operatorText" class="block-text">Klik untuk pilih operator</span>
                                    </div>
                                    <div class="rule-block input-block">
                                        <span class="block-icon">🔢</span>
                                        <x-ui.input id="valueInput" class="border-0" placeholder="Masukkan nilai (contoh: 80)" />
                                    </div>
                                </div>

                                <div class="rule-section then-section">
                                    <h6 class="text-success mb-3"><i class="material-icons text-sm">check_circle</i> MAKA</h6>
                                    <div class="rule-block mb-2" onclick="showModal('action')">
                                        <span class="block-icon">⚡</span>
                                        <span id="actionText" class="block-text">Klik untuk pilih aksi</span>
                                    </div>
                                    <div class="rule-block" onclick="showModal('actionValue')">
                                        <span class="block-icon">🎯</span>
                                        <span id="actionValueText" class="block-text">Klik untuk pilih detail</span>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="condition_type" id="condition_type">
                            <input type="hidden" name="condition_operator" id="condition_operator">
                            <input type="hidden" name="condition_value" id="condition_value">
                            <input type="hidden" name="action_type" id="action_type">
                            <input type="hidden" name="action_value" id="action_value">
                            <input type="hidden" name="priority" value="10">
                            <input type="hidden" name="is_active" value="1">

                            <div class="d-flex justify-content-end gap-2">
                                <x-ui.button variant="outline" size="lg" href="{{ route('admin.adaptive-rules.index') }}">
                                    Batal
                                </x-ui.button>
                                <x-ui.button type="submit" variant="primary" size="lg" icon="save">
                                    Simpan Rule
                                </x-ui.button>
                            </div>
                        </form>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="optionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Pilih</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="modalBody"></div>
            </div>
        </div>
    </div>
</x-layouts.app>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/adaptive-rules/create.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/admin/adaptive-rules/create.js') }}"></script>
@endpush
