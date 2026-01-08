document.addEventListener('DOMContentLoaded', function () {
    // Get DOM elements
    const form = document.getElementById('ueqForm');
    const submitButton = document.querySelector('button[type="submit"]');
    const rows = document.querySelectorAll('tr.ueq-row');

    // Save answers to localStorage
    function saveAnswers() {
        const answers = {};
        rows.forEach(function (row) {
            const radios = row.querySelectorAll('input[type="radio"]');
            radios.forEach(function (radio) {
                if (radio.checked) {
                    answers[radio.name] = radio.value;
                }
            });
        });
        localStorage.setItem('ueq_survey_answers', JSON.stringify(answers));
    }

    // Load answers from localStorage
    function loadAnswers() {
        const savedAnswers = localStorage.getItem('ueq_survey_answers');
        if (savedAnswers) {
            try {
                const answers = JSON.parse(savedAnswers);
                Object.keys(answers).forEach(function (name) {
                    const value = answers[name];
                    const radio = document.querySelector(`input[name="${name}"][value="${value}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                });
            } catch (e) {
                console.error('Error loading saved answers:', e);
            }
        }
    }

    // Check for unanswered questions
    function checkUnansweredQuestions() {
        const unanswered = [];

        rows.forEach(function (row, index) {
            const name = row.querySelector('input[type="radio"]')?.name;
            if (!name) return;

            const radios = row.querySelectorAll('input[type="radio"]');
            let anyChecked = false;

            radios.forEach(function (radio) {
                if (radio.checked) {
                    anyChecked = true;
                }
            });

            if (!anyChecked) {
                unanswered.push(index);
                row.classList.add('unanswered');
            } else {
                row.classList.remove('unanswered');
            }
        });

        // Show warning if there are unanswered questions
        if (unanswered.length > 0) {
            if (!document.getElementById('unansweredWarning')) {
                const warning = document.createElement('div');
                warning.id = 'unansweredWarning';
                warning.className = 'alert alert-danger sticky-top mt-2 mb-3';
                warning.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <strong>Perhatian!</strong>
                            <p class="mb-0">Ada ${unanswered.length} pertanyaan yang belum dijawab. Silakan isi semua pertanyaan.</p>
                        </div>
                        <div class="ms-auto">
                            <button id="btnScrollToNext" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-arrow-down me-1"></i> Lihat Pertanyaan
                            </button>
                        </div>
                    </div>
                `;
                const formElement = document.getElementById('ueqForm');
                formElement.insertAdjacentElement('beforebegin', warning);

                // Add event listener to scroll to next unanswered question
                document.getElementById('btnScrollToNext').addEventListener('click', function () {
                    if (unanswered.length > 0) {
                        scrollToRow(unanswered[0]);
                    }
                });
            } else {
                document.getElementById('unansweredWarning').querySelector('p').textContent =
                    `Ada ${unanswered.length} pertanyaan yang belum dijawab. Silakan isi semua pertanyaan.`;
            }
        } else {
            const warning = document.getElementById('unansweredWarning');
            if (warning) {
                warning.remove();
            }
        }

        return unanswered;
    }

    // Update submit button status
    function updateSubmitButtonStatus() {
        const unanswered = checkUnansweredQuestions();
        if (unanswered.length > 0) {
            submitButton.disabled = true;
            submitButton.textContent = `Masih ada ${unanswered.length} pertanyaan belum dijawab`;
        } else {
            submitButton.disabled = false;
            submitButton.textContent = 'Kirim';
        }
    }

    // Scroll to specific row with animation
    function scrollToRow(rowIndex) {
        if (rowIndex >= 0 && rowIndex < rows.length) {
            const row = rows[rowIndex];
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            row.classList.add('flash-highlight');
            setTimeout(() => {
                row.classList.remove('flash-highlight');
            }, 2000);
        }
    }

    // Helper function to find row index by field name
    function findRowIndexByFieldName(fieldName) {
        for (let i = 0; i < rows.length; i++) {
            const radio = rows[i].querySelector(`input[name="${fieldName}"]`);
            if (radio) {
                return i;
            }
        }
        return -1;
    }

    // Add event listeners to all radio buttons and cells
    rows.forEach(function (row) {
        const radios = row.querySelectorAll('input[type="radio"]');
        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                row.classList.remove('unanswered');
                checkUnansweredQuestions();
                updateSubmitButtonStatus();
                saveAnswers(); // Save changes immediately
            });
        });

        // Make entire cell clickable
        const cells = row.querySelectorAll('.radio-cell');
        cells.forEach(function (cell, index) {
            cell.addEventListener('click', function (e) {
                // Prevent clicking if already clicking on the radio button itself
                if (e.target.tagName !== 'INPUT') {
                    const radio = cell.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        // Trigger change event
                        const event = new Event('change');
                        radio.dispatchEvent(event);
                    }
                }
            });
        });
    });

    // Handle form submission
    form.addEventListener('submit', function (e) {
        const unanswered = checkUnansweredQuestions();
        if (unanswered.length > 0) {
            e.preventDefault();
            scrollToRow(unanswered[0]);
            return false;
        }

        // Optional: Clear localStorage after successful submission
        // localStorage.removeItem('ueq_survey_answers');
        return true;
    });

    // Initialize on page load
    loadAnswers();
    checkUnansweredQuestions();
    updateSubmitButtonStatus();

    // Memastikan teks tombol logout tidak berubah
    const logoutButton = document.getElementById('logout-button');
    if (logoutButton) {
        logoutButton.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Logout';
    }

    // Mencegah form UEQ memengaruhi tombol logout
    if (form) {
        form.addEventListener('submit', function (e) {
            const logoutButton = document.getElementById('logout-button');
            if (logoutButton) {
                logoutButton.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Logout';
            }
        });
    }
});
