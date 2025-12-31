<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="adaptive-rules" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Buat Rule Baru" />
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-lg">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="d-flex justify-content-between align-items-center px-3">
                                    <h5 class="text-white mb-0">🎯 Buat Rule Baru</h5>
                                    <a href="{{ route('admin.adaptive-rules.index') }}" class="btn btn-sm btn-light">
                                        <i class="material-icons text-sm">arrow_back</i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            
                            <form action="{{ route('admin.adaptive-rules.store') }}" method="POST" id="ruleForm">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">📝 Nama Rule</label>
                                    <input type="text" name="name" class="form-control form-control-lg" 
                                        placeholder="Contoh: Naikkan Kesulitan untuk Mahasiswa Pintar" required>
                                </div>

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
                                            <input type="text" id="valueInput" class="form-control border-0" 
                                                placeholder="Masukkan nilai (contoh: 80)">
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
                                    <a href="{{ route('admin.adaptive-rules.index') }}" class="btn btn-outline-secondary btn-lg">Batal</a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="material-icons text-sm">save</i> Simpan Rule
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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
</x-layout>

@push('css')
<style>
.rule-workspace {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
}
.rule-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
}
.if-section { border-left: 5px solid #3b82f6; }
.then-section { border-left: 5px solid #10b981; }
.rule-block {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 15px;
}
.rule-block:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.rule-block.filled {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-style: solid;
    border-color: #667eea;
}
.rule-block.filled .block-text {
    color: white;
    font-weight: 600;
}
.block-icon {
    font-size: 28px;
    flex-shrink: 0;
}
.block-text {
    flex: 1;
    font-size: 16px;
    color: #6c757d;
}
.input-block {
    cursor: default;
}
.input-block:hover {
    transform: none;
}
.option-item {
    padding: 20px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    transition: all 0.2s;
}
.option-item:hover {
    background: #f8f9fa;
    padding-left: 30px;
}
.option-item:last-child {
    border-bottom: none;
}
</style>
@endpush

@push('js')
<script>
const options = {
    condition: [
        {value: 'score_range', text: '📊 Skor Mahasiswa (%)'},
        {value: 'consecutive_correct', text: '✅ Jawaban Benar Berturut'},
        {value: 'consecutive_wrong', text: '❌ Jawaban Salah Berturut'},
        {value: 'accuracy_rate', text: '🎯 Tingkat Akurasi (%)'}
    ],
    operator: [
        {value: '>=', text: '≥ Lebih dari atau sama dengan'},
        {value: '>', text: '> Lebih dari'},
        {value: '<=', text: '≤ Kurang dari atau sama dengan'},
        {value: '<', text: '< Kurang dari'},
        {value: 'between', text: '↔️ Antara'}
    ],
    action: [
        {value: 'change_difficulty', text: '🎚️ Ubah Tingkat Kesulitan'},
        {value: 'show_hint', text: '💡 Tampilkan Petunjuk'},
        {value: 'skip_questions', text: '⏭️ Lewati Soal'},
        {value: 'end_quiz', text: '🏁 Akhiri Kuis'}
    ],
    actionValue: {
        change_difficulty: [
            {value: 'beginner', text: '🟢 Beginner (Mudah)'},
            {value: 'medium', text: '🟡 Medium (Sedang)'},
            {value: 'hard', text: '🔴 Hard (Sulit)'}
        ],
        skip_questions: [
            {value: '1', text: 'Lewati 1 soal'},
            {value: '2', text: 'Lewati 2 soal'},
            {value: '3', text: 'Lewati 3 soal'}
        ],
        default: [{value: 'true', text: 'Aktif'}]
    }
};

function showModal(type) {
    const title = document.getElementById('modalTitle');
    const body = document.getElementById('modalBody');
    
    let items = [];
    if (type === 'actionValue') {
        const actionType = document.getElementById('action_type').value;
        if (!actionType) {
            alert('⚠️ Pilih tipe aksi terlebih dahulu!');
            return;
        }
        items = options.actionValue[actionType] || options.actionValue.default;
    } else {
        items = options[type];
    }
    
    title.textContent = type === 'condition' ? 'Pilih Kondisi' : 
                       type === 'operator' ? 'Pilih Operator' :
                       type === 'action' ? 'Pilih Aksi' : 'Pilih Detail';
    
    body.innerHTML = items.map(item => 
        `<div class="option-item" onclick="selectOption('${type}', '${item.value}', \`${item.text}\`)">${item.text}</div>`
    ).join('');
    
    $('#optionModal').modal('show');
}

function selectOption(type, value, text) {
    if (type === 'condition') {
        document.getElementById('condition_type').value = value;
        document.getElementById('conditionText').textContent = text;
        document.getElementById('conditionText').parentElement.classList.add('filled');
    } else if (type === 'operator') {
        document.getElementById('condition_operator').value = value;
        document.getElementById('operatorText').textContent = text;
        document.getElementById('operatorText').parentElement.classList.add('filled');
    } else if (type === 'action') {
        document.getElementById('action_type').value = value;
        document.getElementById('actionText').textContent = text;
        document.getElementById('actionText').parentElement.classList.add('filled');
        document.getElementById('actionValueText').textContent = 'Klik untuk pilih detail';
        document.getElementById('actionValueText').parentElement.classList.remove('filled');
    } else if (type === 'actionValue') {
        document.getElementById('action_value').value = value;
        document.getElementById('actionValueText').textContent = text;
        document.getElementById('actionValueText').parentElement.classList.add('filled');
    }
    
    $('#optionModal').modal('hide');
}

document.getElementById('valueInput').addEventListener('input', function() {
    document.getElementById('condition_value').value = this.value;
});

document.getElementById('ruleForm').addEventListener('submit', function(e) {
    const required = ['condition_type', 'condition_operator', 'condition_value', 'action_type', 'action_value'];
    for (let field of required) {
        if (!document.getElementById(field).value) {
            e.preventDefault();
            alert('⚠️ Lengkapi semua bagian rule terlebih dahulu!');
            return;
        }
    }
});
</script>
@endpush
