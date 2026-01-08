/* ========================================
   ADMIN CONSOLIDATED JS
   Menggabungkan fungsi-fungsi JS untuk role admin
   ======================================== */

// ========== ADAPTIVE RULES MODULE ==========
const AdaptiveRules = {
    init() {
        this.bindActionTypeChange();
        this.initTour();
    },

    initTour() {
        if (typeof window.OopediaTour === 'undefined' || !document.querySelector('#ruleForm')) return;

        const steps = [
            {
                title: 'Logic Architect 🧠',
                element: document.querySelector('.page-header'),
                intro: 'Halaman ini digunakan untuk merancang "otak" adaptif sistem.',
                position: 'bottom'
            },
            {
                title: 'IF Condition 🔍',
                element: document.querySelector('.bg-blue-600.text-white'),
                intro: 'Tentukan pemicu atau syarat. Misal: Jika skor mahasiswa > 80.',
                position: 'top'
            },
            {
                title: 'THEN Action ⚡',
                element: document.querySelector('.bg-emerald-500.text-white'),
                intro: 'Tentukan aksi yang akan dijalankan sistem jika syarat di atas terpenuhi.',
                position: 'top'
            },
            {
                title: 'Priority & Deploy 🚀',
                element: document.querySelector('.bg-slate-900.border-slate-800'),
                intro: 'Atur prioritas eksekusi dan aktifkan rule untuk diterapkan ke sistem.',
                position: 'top'
            }
        ];

        window.OopediaTour.init({ steps }).start();
    },
    // ... rest of AdaptiveRules ...

    bindActionTypeChange() {
        const actionTypeSelect = document.getElementById('action_type');
        const actionValueContainer = document.getElementById('action_value_container');
        const actionValueHint = document.getElementById('action_value_hint');
        const currentActionValueEl = document.getElementById('current_action_value');
        const currentActionValue = currentActionValueEl ? currentActionValueEl.value : '';

        if (actionTypeSelect) {
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
                                value="${currentActionValue}" placeholder="Masukkan nilai aksi" required>`;
                    hint = 'Nilai tergantung tipe aksi yang dipilih';
                }

                if (actionValueContainer) actionValueContainer.innerHTML = html;
                if (actionValueHint) actionValueHint.textContent = hint;
            });
        }
    }
};

// ========== MATERIALS MODULE ==========
const MaterialsAdmin = {
    init() {
        this.bindImagePreview();
        this.initTour();
    },

    initTour() {
        if (typeof window.OopediaTour === 'undefined' || !document.querySelector('.Kurikulum')) return;

        const steps = [
            {
                title: 'Pusat Kurikulum 📚',
                element: document.querySelector('.page-header'),
                intro: 'Manajemen seluruh modul pembelajaran PBO tersedia di halaman ini.',
                position: 'bottom'
            },
            {
                title: 'Statistik Modul 📊',
                element: document.querySelector('.grid-cols-3'),
                intro: 'Pantau total modul, materi baru, dan aset multimedia yang tersinkronisasi.',
                position: 'bottom'
            },
            {
                title: 'Tambah Modul ➕',
                element: document.querySelector('.btn-primary'),
                intro: 'Klik di sini untuk menginjeksi modul atau topik pembelajaran baru ke sistem.',
                position: 'left'
            }
        ];

        window.OopediaTour.init({ steps }).start();
    },
    // ... rest of MaterialsAdmin ...

    bindImagePreview() {
        const imageInputs = document.querySelectorAll('input[type="file"][data-preview]');
        imageInputs.forEach(input => {
            input.addEventListener('change', function () {
                const previewId = this.getAttribute('data-preview');
                MaterialsAdmin.previewImage(this, previewId);
            });
        });
    },

    previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById(previewId);
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
};

// ========== QUESTION BANKS MODULE ==========
const QuestionBanks = {
    init() {
        this.bindDifficultyDistribution();
    },

    bindDifficultyDistribution() {
        const form = document.querySelector('form[data-question-bank-config]');
        if (!form) return;

        const inputs = form.querySelectorAll('input[data-difficulty]');
        const totalDisplay = document.getElementById('total-questions');

        inputs.forEach(input => {
            input.addEventListener('input', function () {
                QuestionBanks.updateTotal(inputs, totalDisplay);
            });
        });
    },

    updateTotal(inputs, totalDisplay) {
        let total = 0;
        inputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });

        if (totalDisplay) {
            totalDisplay.textContent = total;
        }
    }
};

// ========== USERS MODULE ==========
const UsersAdmin = {
    init() {
        this.bindPendingActions();
        this.initTour();
    },

    initTour() {
        if (typeof window.OopediaTour === 'undefined' || !document.querySelector('.Direktori')) return;

        const steps = [
            {
                title: 'Manajemen Akses 🔐',
                element: document.querySelector('.page-header'),
                intro: 'Kelola seluruh akun administrator dan dosen pembimbing di sini.',
                position: 'bottom'
            },
            {
                title: 'Pending Approvals ⏳',
                element: document.querySelector('.btn-outline-danger'),
                intro: 'Perhatikan tombol ini jika ada permintaan akun admin baru yang perlu disetujui.',
                position: 'left'
            },
            {
                title: 'Direktori Pengguna 📋',
                element: document.querySelector('table'),
                intro: 'Daftar user beserta role (Superadmin/Dosen) dan status akses mereka.',
                position: 'top'
            }
        ];

        window.OopediaTour.init({ steps }).start();
    },
    // ... rest of UsersAdmin ...

    bindPendingActions() {
        const approveButtons = document.querySelectorAll('.btn-approve-user');
        const rejectButtons = document.querySelectorAll('.btn-reject-user');

        approveButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                UsersAdmin.approveUser(userId);
            });
        });

        rejectButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                UsersAdmin.rejectUser(userId);
            });
        });
    },

    approveUser(userId) {
        if (confirm('Apakah Anda yakin ingin menyetujui pendaftaran user ini?')) {
            // Submit approval form
            const form = document.getElementById(`approve-form-${userId}`);
            if (form) form.submit();
        }
    },

    rejectUser(userId) {
        if (confirm('Apakah Anda yakin ingin menolak pendaftaran user ini?')) {
            // Submit rejection form
            const form = document.getElementById(`reject-form-${userId}`);
            if (form) form.submit();
        }
    }
};

// ========== DASHBOARD MODULE ==========
const DashboardAdmin = {
    init() {
        this.initCharts();
        this.updateStats();
        this.initTour();
    },

    initTour() {
        if (typeof window.OopediaTour !== 'undefined') {
            window.OopediaTour.init().start();
        }
    },

    initCharts() {
        // Placeholder for chart initialization
        // Can be extended with Chart.js or other charting libraries
        console.log('Dashboard charts initialized');
    },

    updateStats() {
        // Animate stat numbers
        const statValues = document.querySelectorAll('.stat-value');
        statValues.forEach(stat => {
            const finalValue = parseInt(stat.textContent);
            let currentValue = 0;
            const increment = Math.ceil(finalValue / 30);

            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                stat.textContent = currentValue;
            }, 30);
        });
    }
};

// ========== TABLE UTILITIES ==========
const TableUtils = {
    init() {
        this.bindDeleteConfirmations();
        this.bindBulkActions();
    },

    bindDeleteConfirmations() {
        const deleteButtons = document.querySelectorAll('.btn-delete, [data-action="delete"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    },

    bindBulkActions() {
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
    }
};

// ========== AUTO INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function () {
    // Initialize based on page context
    if (document.querySelector('[data-adaptive-rules]')) {
        AdaptiveRules.init();
    }

    if (document.querySelector('[data-materials-form]')) {
        MaterialsAdmin.init();
    }

    if (document.querySelector('[data-question-banks]')) {
        QuestionBanks.init();
    }

    if (document.querySelector('[data-users-pending]')) {
        UsersAdmin.init();
    }

    if (document.querySelector('[data-admin-dashboard]')) {
        DashboardAdmin.init();
    }

    // Initialize table utilities on all admin pages
    TableUtils.init();
});

// Export for use in other scripts
window.AdminApp = {
    AdaptiveRules,
    MaterialsAdmin,
    QuestionBanks,
    UsersAdmin,
    DashboardAdmin,
    TableUtils
};

// Global helper function for image preview (backward compatibility)
function previewImage(input, previewId) {
    MaterialsAdmin.previewImage(input, previewId);
}
