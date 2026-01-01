const options = {
    condition: [
        { value: 'score_range', text: '📊 Skor Mahasiswa (%)' },
        { value: 'consecutive_correct', text: '✅ Jawaban Benar Berturut' },
        { value: 'consecutive_wrong', text: '❌ Jawaban Salah Berturut' },
        { value: 'accuracy_rate', text: '🎯 Tingkat Akurasi (%)' }
    ],
    operator: [
        { value: '>=', text: '≥ Lebih dari atau sama dengan' },
        { value: '>', text: '> Lebih dari' },
        { value: '<=', text: '≤ Kurang dari atau sama dengan' },
        { value: '<', text: '< Kurang dari' },
        { value: 'between', text: '↔️ Antara' }
    ],
    action: [
        { value: 'change_difficulty', text: '🎚️ Ubah Tingkat Kesulitan' },
        { value: 'show_hint', text: '💡 Tampilkan Petunjuk' },
        { value: 'skip_questions', text: '⏭️ Lewati Soal' },
        { value: 'end_quiz', text: '🏁 Akhiri Kuis' }
    ],
    actionValue: {
        change_difficulty: [
            { value: 'beginner', text: '🟢 Beginner (Mudah)' },
            { value: 'medium', text: '🟡 Medium (Sedang)' },
            { value: 'hard', text: '🔴 Hard (Sulit)' }
        ],
        skip_questions: [
            { value: '1', text: 'Lewati 1 soal' },
            { value: '2', text: 'Lewati 2 soal' },
            { value: '3', text: 'Lewati 3 soal' }
        ],
        default: [{ value: 'true', text: 'Aktif' }]
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

document.getElementById('valueInput').addEventListener('input', function () {
    document.getElementById('condition_value').value = this.value;
});

document.getElementById('ruleForm').addEventListener('submit', function (e) {
    const required = ['condition_type', 'condition_operator', 'condition_value', 'action_type', 'action_value'];
    for (let field of required) {
        if (!document.getElementById(field).value) {
            e.preventDefault();
            alert('⚠️ Lengkapi semua bagian rule terlebih dahulu!');
            return;
        }
    }
});
