document.addEventListener('DOMContentLoaded', function () {
    const actionTypeSelect = document.getElementById('action_type');
    const actionValueContainer = document.getElementById('action_value_container');
    const actionValueHint = document.getElementById('action_value_hint');
    const currentActionValue = document.getElementById('current_action_value').value;

    actionTypeSelect.addEventListener('change', function () {
        const actionType = this.value;
        let html = '';
        let hint = '';

        if (actionType === 'change_difficulty') {
            html = `<select name="action_value" id="action_value" class="form-control" required>
                        <option value="">Pilih Tingkat Kesulitan</option>
                        <option value="beginner" ${currentActionValue === 'beginner' ? 'selected' : ''}>Beginner</option>
                        <option value="medium" ${currentActionValue === 'medium' ? 'selected' : ''}>Medium</option>
                        <option value="hard" ${currentActionValue === 'hard' ? 'selected' : ''}>Hard</option>
                    </select>`;
            hint = 'Pilih tingkat kesulitan yang akan diterapkan';
        } else if (actionType === 'skip_questions') {
            html = `<input type="number" name="action_value" id="action_value" class="form-control" 
                        value="${currentActionValue}" placeholder="Jumlah soal" min="1" required>`;
            hint = 'Masukkan jumlah soal yang akan dilewati';
        } else if (actionType === 'recommend_material') {
            html = `<input type="text" name="action_value" id="action_value" class="form-control" 
                        value="${currentActionValue}" placeholder="ID atau nama materi" required>`;
            hint = 'Masukkan ID atau nama materi yang akan direkomendasikan';
        } else {
            html = `<input type="text" name="action_value" id="action_value" class="form-control" 
                        value="${currentActionValue}" placeholder="Masukkan nilai aksi" required`;
            hint = 'Nilai tergantung tipe aksi yang dipilih';
        }

        actionValueContainer.innerHTML = html;
        actionValueHint.textContent = hint;
    });
});
