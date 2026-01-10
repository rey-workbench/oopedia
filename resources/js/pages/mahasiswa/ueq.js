/**
 * UEQ Survey Module
 * User Experience Questionnaire form handling
 */

import { DOM } from '../../utils/dom.js';

/**
 * Save answers to localStorage
 * @param {NodeList} rows - Survey rows
 */
function saveAnswers(rows) {
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

/**
 * Load answers from localStorage
 */
function loadAnswers() {
    const savedAnswers = localStorage.getItem('ueq_survey_answers');

    if (savedAnswers) {
        try {
            const answers = JSON.parse(savedAnswers);
            Object.keys(answers).forEach(function (name) {
                const value = answers[name];
                const radio = DOM.$(`input[name="${name}"][value="${value}"]`);
                if (radio) {
                    radio.checked = true;
                }
            });
        } catch (e) {
            console.error('Error loading saved answers:', e);
        }
    }
}

/**
 * Scroll to specific row with animation
 * @param {NodeList} rows - All survey rows
 * @param {number} rowIndex - Index of row to scroll to
 */
function scrollToRow(rows, rowIndex) {
    if (rowIndex >= 0 && rowIndex < rows.length) {
        const row = rows[rowIndex];
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        row.classList.add('flash-highlight');
        setTimeout(() => {
            row.classList.remove('flash-highlight');
        }, 2000);
    }
}

/**
 * Check for unanswered questions
 * @param {NodeList} rows - Survey rows
 * @returns {Array} Array of unanswered row indices
 */
function checkUnansweredQuestions(rows) {
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

    // Show/update warning if there are unanswered questions
    if (unanswered.length > 0) {
        showUnansweredWarning(unanswered, rows);
    } else {
        removeUnansweredWarning();
    }

    return unanswered;
}

/**
 * Show warning for unanswered questions
 * @param {Array} unanswered - Array of unanswered indices
 * @param {NodeList} rows - Survey rows
 */
function showUnansweredWarning(unanswered, rows) {
    let warning = DOM.$('#unansweredWarning');

    if (!warning) {
        warning = document.createElement('div');
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

        const formElement = DOM.$('#ueqForm');
        if (formElement) {
            formElement.insertAdjacentElement('beforebegin', warning);
        }

        // Add event listener to scroll to next unanswered question
        const scrollBtn = DOM.$('#btnScrollToNext');
        if (scrollBtn) {
            scrollBtn.addEventListener('click', function () {
                if (unanswered.length > 0) {
                    scrollToRow(rows, unanswered[0]);
                }
            });
        }
    } else {
        // Update existing warning
        const messageEl = warning.querySelector('p');
        if (messageEl) {
            messageEl.textContent = `Ada ${unanswered.length} pertanyaan yang belum dijawab. Silakan isi semua pertanyaan.`;
        }
    }
}

/**
 * Remove unanswered warning
 */
function removeUnansweredWarning() {
    const warning = DOM.$('#unansweredWarning');
    if (warning) {
        warning.remove();
    }
}

/**
 * Update submit button status
 * @param {Element} submitButton - Submit button element
 * @param {NodeList} rows - Survey rows
 */
function updateSubmitButtonStatus(submitButton, rows) {
    const unanswered = checkUnansweredQuestions(rows);

    if (unanswered.length > 0) {
        submitButton.disabled = true;
        submitButton.textContent = `Masih ada ${unanswered.length} pertanyaan belum dijawab`;
    } else {
        submitButton.disabled = false;
        submitButton.textContent = 'Kirim';
    }
}

/**
 * Initialize UEQ form
 */
function initUEQForm() {
    const form = DOM.$('#ueqForm');
    const submitButton = DOM.$('button[type="submit"]');
    const rows = DOM.$$('tr.ueq-row');

    if (!form || !submitButton || rows.length === 0) return;

    // Add event listeners to all radio buttons and cells
    rows.forEach(function (row) {
        const radios = row.querySelectorAll('input[type="radio"]');

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                row.classList.remove('unanswered');
                checkUnansweredQuestions(rows);
                updateSubmitButtonStatus(submitButton, rows);
                saveAnswers(rows);
            });
        });

        // Make entire cell clickable
        const cells = row.querySelectorAll('.radio-cell');
        cells.forEach(function (cell) {
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
        const unanswered = checkUnansweredQuestions(rows);
        if (unanswered.length > 0) {
            e.preventDefault();
            scrollToRow(rows, unanswered[0]);
            return false;
        }

        // Optional: Clear localStorage after successful submission
        // localStorage.removeItem('ueq_survey_answers');
        return true;
    });

    // Initialize on page load
    loadAnswers();
    checkUnansweredQuestions(rows);
    updateSubmitButtonStatus(submitButton, rows);

    // Ensure logout button text doesn't change
    const logoutButton = DOM.$('#logout-button');
    if (logoutButton) {
        logoutButton.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Logout';
    }

    // Prevent form UEQ from affecting logout button
    form.addEventListener('submit', function () {
        const logoutButton = DOM.$('#logout-button');
        if (logoutButton) {
            logoutButton.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Logout';
        }
    });
}

// Initialize on DOM ready
DOM.ready(initUEQForm);

export { initUEQForm };
