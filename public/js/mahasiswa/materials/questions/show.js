function redirectAfterCompletion() {
    // This function might be called by legacy code or partials
    if (typeof redirectUrl !== 'undefined') {
        window.location.href = redirectUrl;
    }
}

function initializeQuestionForm(config) {
    const questionForm = document.getElementById('questionForm');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    const feedbackElement = document.querySelector('.exercise-feedback');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const tryAgainBtn = document.getElementById('tryAgainBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');

    // Config values passed from blade
    const routes = config.routes; // Object containing route URLs

    if (questionForm) {
        questionForm.addEventListener('submit', function (e) {
            // Prevent default form submission
            // Note: drag-and-drop validation listener in question.js runs too.
            // If it calls e.preventDefault(), this might still run or not depending on order?
            // Usually, we want to stop if validation failed.
            // But we can't easily check that here without shared state.
            // However, fetch submission should happen.

            e.preventDefault();

            // Basic validation for drag and drop is handled in question.js
            // If question.js prevented default, we should check?
            if (e.defaultPrevented) return;

            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
            }

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    showFeedback(data, routes);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memeriksa jawaban. Silakan coba lagi.');
                    if (checkAnswerBtn) {
                        checkAnswerBtn.disabled = false;
                        checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                    }
                });
        });
    }

    // Make the entire answer option clickable
    const answerOptions = document.querySelectorAll('.answer-option');
    answerOptions.forEach(option => {
        option.addEventListener('click', function () {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });
}

function showFeedback(data, routes) {
    const feedbackElement = document.querySelector('.exercise-feedback');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const tryAgainBtn = document.getElementById('tryAgainBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const questionForm = document.getElementById('questionForm');
    const explanationBox = document.getElementById('explanationBox');
    const explanationText = document.getElementById('explanationText');

    // Set status dan icon
    if (feedbackStatus) feedbackStatus.innerHTML = `<h3 class="${data.status === 'success' ? 'text-success' : 'text-danger'}">${data.message}</h3>`;
    if (feedbackIcon) {
        feedbackIcon.className = `feedback-icon ${data.status === 'success' ? 'success' : 'error'}`;
        feedbackIcon.innerHTML = `<i class="fas ${data.status === 'success' ? 'fa-check-circle' : 'fa-times-circle'} fa-3x"></i>`;
    }

    // Show explanation if available
    if (explanationBox && explanationText && data.explanation) {
        explanationBox.style.display = 'block';
        explanationText.innerHTML = data.explanation;
    } else if (explanationBox) {
        explanationBox.style.display = 'none';
    }

    if (data.status === 'success') {
        // Penanganan khusus untuk guest dengan redirect langsung
        if (data.redirect_url) {
            // Tampilkan feedback sebentar lalu redirect
            if (feedbackElement) {
                feedbackElement.style.display = 'block';
                feedbackElement.classList.remove('alert-danger');
                feedbackElement.classList.add('alert-success');
            }
            if (feedbackIcon) feedbackIcon.className = 'fas fa-check-circle';
            if (feedbackStatus) feedbackStatus.textContent = data.message;

            // Redirect setelah 1.5 detik
            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 1500);

            return;
        }

        // Kode penanganan normal lainnya
        if (tryAgainBtn) tryAgainBtn.style.display = 'none';
        if (nextQuestionBtn) {
            nextQuestionBtn.style.display = 'inline-block';
            nextQuestionBtn.innerHTML = '<i class="fas fa-list-ol me-2"></i>Kembali ke Level';
            nextQuestionBtn.onclick = () => {
                const levelUrl = data.levelUrl || routes.levels;
                window.location.href = levelUrl + (levelUrl.includes('?') ? '&' : '?') + 'scroll=true';
            };
        }
    } else {
        if (tryAgainBtn) tryAgainBtn.style.display = 'inline-block';
        if (nextQuestionBtn) nextQuestionBtn.style.display = 'none';

        if (tryAgainBtn) {
            tryAgainBtn.onclick = () => {
                if (feedbackElement) feedbackElement.style.display = 'none';
                if (questionForm) questionForm.style.display = 'block';

                const checkAnswerBtn = document.getElementById('checkAnswerBtn');
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            };
        }
    }

    if (feedbackElement) feedbackElement.style.display = 'block';
    if (questionForm) questionForm.style.display = 'none';
}

function initializeTutorial() {
    // Check if this is first time visiting a question
    if (!sessionStorage.getItem('question_answer_tutorial_complete')) {
        setTimeout(startQuestionAnswerTutorial, 1000);
    }
}

function startQuestionAnswerTutorial() {
    const questionText = document.querySelector('.question-text');
    const optionsContainer = document.querySelector('.answers-container') || document.querySelector('.options-container');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');

    if (!questionText || !optionsContainer || !checkAnswerBtn) {
        return;
    }

    const steps = [
        {
            intro: "Sekarang Anda berada di halaman soal. Mari kita pelajari cara menjawab soal."
        },
        {
            element: questionText,
            intro: "Ini adalah teks pertanyaan yang harus Anda jawab."
        },
        {
            element: optionsContainer,
            intro: "Pilih salah satu jawaban yang menurut Anda benar."
        },
        {
            element: checkAnswerBtn,
            intro: "Setelah memilih jawaban, klik tombol ini untuk memeriksa jawaban Anda."
        },
        {
            intro: "Jika jawaban benar, Anda dapat melanjutkan ke soal berikutnya. Jika salah, Anda dapat mencoba lagi."
        }
    ];

    try {
        if (typeof introJs !== 'undefined') {
            const tour = introJs().setOptions({
                steps: steps,
                showProgress: true,
                exitOnOverlayClick: true,
                showBullets: false,
                scrollToElement: true,
                nextLabel: 'Berikutnya',
                prevLabel: 'Sebelumnya',
                doneLabel: 'Mulai Menjawab'
            });

            tour.oncomplete(function () {
                sessionStorage.setItem('question_answer_tutorial_complete', 'true');
            });

            tour.start();
        }
    } catch (error) {
        console.error("Error starting tutorial:", error);
    }
}

// Fix for TinyMCE content display
document.addEventListener('DOMContentLoaded', function () {
    const questionTextElements = document.querySelectorAll('.question-text');

    questionTextElements.forEach(element => {
        // Only apply if the content appears to be raw HTML but escaped
        if (element.textContent.includes('&lt;') ||
            element.textContent.includes('<p class="whitespace-pre-wrap') ||
            element.textContent.includes('<div class="relative group/copy')) {

            let rawHtml = element.textContent;
            element.innerHTML = rawHtml;
        }

        const codeBlocks = element.querySelectorAll('pre');
        codeBlocks.forEach(block => {
            block.classList.add('language-java', 'formatted');
            block.style.backgroundColor = '#f1f3f5';
            block.style.padding = '1rem';
            block.style.borderRadius = '4px';
            block.style.fontFamily = 'monospace';
            block.style.overflow = 'auto';
            block.style.whiteSpace = 'pre-wrap';
        });
    });
});

function redirectToLevelWithScroll(levelUrl) {
    localStorage.setItem('questionCompleted', 'true');

    if (typeof redirectUrl !== 'undefined' && !levelUrl) {
        // fallback
    }

    if (levelUrl) {
        const separator = levelUrl.includes('?') ? '&' : '?';
        window.location.href = `${levelUrl}${separator}scroll=true`;
    }
}
