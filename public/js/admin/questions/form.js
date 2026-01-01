// Global variables assumed to be set in the view:
// - initialAnswerCount (number)
// - isEditMode (boolean)

let answerCount = (typeof initialAnswerCount !== 'undefined') ? initialAnswerCount : 1;

function handleQuestionTypeChange() {
    const questionType = document.querySelector('[name="question_type"]').value;
    const answerContainer = document.getElementById('answers-container');
    const addAnswerBtn = document.getElementById('add-answer-btn');

    // In edit mode, we might not want to reset answers immediately on load, 
    // but the original logic for create was to reset. 
    // For a shared script, we can check if we are explicitly changing the type vs loading.
    // However, for simplicity and matching existing logic:

    // Check if called from event listener (change) or explicit call

    // To replicate existing specific behavior:
    // Create view resets UI on type change. 
    // Edit view should mostly preserve unless type changes explicitly. 

    // We will separate the "Reset" logic from "Update UI" logic.

    updateAnswerUI(questionType);
}

function resetAnswersForNewType() {
    const questionType = document.querySelector('[name="question_type"]').value;
    const answerContainer = document.getElementById('answers-container');
    const addAnswerBtn = document.getElementById('add-answer-btn');

    // Reset container except heading if present, or just use innerHTML
    // Preserving heading `h6`
    const heading = answerContainer.querySelector('h6');
    answerContainer.innerHTML = '';
    if (heading) answerContainer.appendChild(heading);
    else {
        const h6 = document.createElement('h6');
        h6.className = 'mb-3';
        h6.innerText = 'Jawaban';
        answerContainer.appendChild(h6);
    }

    // Reset counter
    answerCount = 0; // Will increment in addAnswer

    // Add answers based on type
    if (questionType === 'fill_in_the_blank') {
        addAnswer();
        if (addAnswerBtn) addAnswerBtn.style.display = 'none';
    } else {
        addAnswer();
        addAnswer();
        if (addAnswerBtn) addAnswerBtn.style.display = 'block';
    }
}

function updateAnswerUI(questionType) {
    const answerEntries = document.querySelectorAll('.answer-entry');
    const addAnswerBtn = document.getElementById('add-answer-btn');

    if (questionType === 'fill_in_the_blank') {
        if (addAnswerBtn) addAnswerBtn.style.display = 'none';
    } else {
        if (addAnswerBtn) addAnswerBtn.style.display = 'block';
    }

    answerEntries.forEach((entry, index) => {
        const radioInput = entry.querySelector('input[type="radio"]');
        const isCorrectInput = entry.querySelector('input[name$="[is_correct]"]');

        if (questionType === 'fill_in_the_blank' && index === 0) {
            if (radioInput) radioInput.checked = true;
            if (isCorrectInput) isCorrectInput.value = '1';
        }
    });
}

function addAnswer() {
    const container = document.getElementById('answers-container');
    const questionType = document.querySelector('[name="question_type"]').value;

    if (questionType === 'fill_in_the_blank' && container.getElementsByClassName('answer-entry').length >= 1) {
        return;
    }

    const newAnswer = document.createElement('div');
    newAnswer.className = 'answer-entry mb-3';

    // Note: 'answerCount' is used as index. 
    // In edit mode with existing answers, answerCount should start from the last index + 1.

    let innerHTML = '';

    if (questionType === 'radio_button') {
        innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group input-group-outline">
                        <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input correct-radio" type="radio" name="correct_answer" value="${answerCount}">
                        <label class="form-check-label">Jawaban Benar</label>
                        <input type="hidden" name="answers[${answerCount}][is_correct]" value="0">
                    </div>
                </div>
            </div>
        `;
    } else if (questionType === 'drag_and_drop') {
        innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group input-group-outline">
                        <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="answers[${answerCount}][is_correct]" value="1">
                        <label class="form-check-label">Jawaban Benar</label>
                    </div>
                </div>
            </div>
        `;
    } else if (questionType === 'fill_in_the_blank') {
        innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group input-group-outline">
                        <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input correct-radio" type="radio" name="correct_answer" value="${answerCount}" checked>
                        <label class="form-check-label">Jawaban Benar</label>
                        <input type="hidden" name="answers[${answerCount}][is_correct]" value="1">
                    </div>
                </div>
            </div>
        `;
    }

    newAnswer.innerHTML = innerHTML;
    container.appendChild(newAnswer);
    answerCount++;

    setupRadioButtonListeners();
}

function setupRadioButtonListeners() {
    const correctRadios = document.querySelectorAll('.correct-radio');
    correctRadios.forEach(radio => {
        // Remove existing listeners to avoid duplication is hard without named function reference, 
        // but adding same listener multiple times to same element is ignored if using same function ref.
        // Here we use anonymous function, so it duplicates. 
        // Better:
        radio.removeEventListener('change', updateAllHiddenInputs);
        radio.addEventListener('change', updateAllHiddenInputs);
    });
}

function updateAllHiddenInputs() {
    const container = document.getElementById('answers-container');
    const entries = container.getElementsByClassName('answer-entry');
    // For radio buttons only
    const selectedRadio = document.querySelector('input[name="correct_answer"]:checked');

    if (!selectedRadio) return;

    // We update based on the radio checked state in the entry
    Array.from(entries).forEach((entry) => {
        const hiddenInput = entry.querySelector('input[type="hidden"]'); // For is_correct
        const radio = entry.querySelector('input[type="radio"][name="correct_answer"]');

        if (hiddenInput && radio) {
            hiddenInput.value = radio.checked ? '1' : '0';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const questionTypeSelect = document.querySelector('[name="question_type"]');
    const form = document.getElementById('questionForm');

    // If we are in CREATE mode (no existing answers implied or we want to reset)
    // Or if checking logic for type change:

    questionTypeSelect.addEventListener('change', function () {
        // Reset Logic
        resetAnswersForNewType();
    });

    // Initial setup
    setupRadioButtonListeners();
    // Also listen to existing radios (for Edit mode)
    const existingRadios = document.querySelectorAll('input[name="correct_answer"]');
    existingRadios.forEach(radio => {
        radio.addEventListener('change', updateAllHiddenInputs);
    });

    // Initial UI update based on type (for Edit mode to show/hide add button)
    handleQuestionTypeChange();

    // Form submit validation
    if (form) {
        form.addEventListener('submit', function (e) {

            // TinyMCE
            const questionText = tinymce.get('content-editor').getContent();
            if (!questionText) {
                alert('Pertanyaan tidak boleh kosong!');
                e.preventDefault();
                return;
            }

            const questionType = questionTypeSelect.value;
            // Validasi Radio Button
            if (questionType === 'radio_button') {
                const selectedRadio = document.querySelector('input[name="correct_answer"]:checked');
                if (!selectedRadio) {
                    alert('Pilih satu jawaban yang benar untuk tipe soal Radio Button');
                    e.preventDefault();
                    return;
                }

                // Double check hidden inputs? 
                // updateAllHiddenInputs(); // ensure sync
            }

            // Check validation passed...
            // Prevent double submit
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                // setTimeout to allow submit to process
                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
                }, 0);
            }
        });
    }
});
