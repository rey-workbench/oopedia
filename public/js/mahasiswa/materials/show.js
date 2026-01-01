document.addEventListener('DOMContentLoaded', function () {
    // Initialize Lightbox
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': "Gambar %1 dari %2"
        });
    }
});

function initializeQuestionForm(config) {
    const questionForm = document.getElementById('questionForm');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    const feedbackElement = document.querySelector('.exercise-feedback');
    const routes = config && config.routes ? config.routes : {};

    if (questionForm) {
        questionForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Disable button to prevent multiple submissions
            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
            }

            // Validasi untuk fill in the blank
            const fillInBlankInput = document.getElementById('fill_in_the_blank_answer');
            if (fillInBlankInput && fillInBlankInput.value.trim() === '') {
                alert('Jawaban tidak boleh kosong');
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
                return;
            }

            const formData = new FormData(this);

            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(JSON.stringify(errorData));
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    const feedbackStatus = document.getElementById('feedbackStatus');
                    const feedbackIcon = document.getElementById('feedbackIcon');
                    const explanationBox = document.getElementById('explanationBox');
                    const explanationText = document.getElementById('explanationText');
                    const tryAgainBtn = document.getElementById('tryAgainBtn');
                    const nextQuestionBtn = document.getElementById('nextQuestionBtn');

                    // Set status dan icon
                    if (feedbackStatus) feedbackStatus.innerHTML = `<h3 class="${data.status === 'success' ? 'text-success' : 'text-danger'}">${data.message}</h3>`;
                    if (feedbackIcon) {
                        feedbackIcon.className = `feedback-icon ${data.status === 'success' ? 'success' : 'error'}`;
                        feedbackIcon.innerHTML = `<i class="fas ${data.status === 'success' ? 'fa-check-circle' : 'fa-times-circle'} fa-3x"></i>`;
                    }

                    // Tampilkan penjelasan
                    if (data.explanation && explanationText) {
                        explanationText.innerHTML = data.explanation;
                        if (explanationBox) explanationBox.style.display = 'block';
                    } else if (data.selectedExplanation && explanationText) {
                        explanationText.innerHTML = data.selectedExplanation;
                        if (explanationBox) explanationBox.style.display = 'block';
                    } else {
                        if (explanationBox) explanationBox.style.display = 'none';
                    }

                    // Tampilkan tombol yang sesuai
                    if (data.status === 'success') {
                        if (tryAgainBtn) tryAgainBtn.style.display = 'none';
                        if (nextQuestionBtn) nextQuestionBtn.style.display = 'inline-block';

                        if (config.isGuest && data.answeredCount >= config.maxQuestionsForGuest) {
                            nextQuestionBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selesai';
                            nextQuestionBtn.onclick = () => window.location.reload();
                        } else if (config.isGuest && !data.hasNextQuestion) {
                            nextQuestionBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selesai';
                            nextQuestionBtn.onclick = () => window.location.reload();
                        } else if (!data.hasNextQuestion) {
                            nextQuestionBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selesai';
                            nextQuestionBtn.onclick = () => {
                                if (routes.levelsUrl) window.location.href = routes.levelsUrl;
                            };
                        } else {
                            nextQuestionBtn.innerHTML = 'Lanjut ke Soal Berikutnya <i class="fas fa-arrow-right ms-2"></i>';
                            nextQuestionBtn.onclick = () => {
                                // Logic to load next question - requires full page reload functionality fallback or more complex AJAX update
                                // Using data.nextUrl if available
                                if (data.nextUrl) {
                                    window.location.href = data.nextUrl;
                                } else {
                                    window.location.reload();
                                }
                            };
                        }
                    } else {
                        if (tryAgainBtn) tryAgainBtn.style.display = 'inline-block';
                        if (nextQuestionBtn) nextQuestionBtn.style.display = 'none';
                        if (tryAgainBtn) {
                            tryAgainBtn.onclick = () => {
                                if (feedbackElement) feedbackElement.style.display = 'none';
                                if (questionForm) questionForm.style.display = 'block';
                                if (checkAnswerBtn) {
                                    checkAnswerBtn.disabled = false;
                                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                                }
                            };
                        }
                    }

                    if (feedbackElement) feedbackElement.style.display = 'block';
                    if (questionForm) questionForm.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    try {
                        const errorData = JSON.parse(error.message);
                        if (errorData.message) {
                            alert(errorData.message);
                        } else {
                            alert('Terjadi kesalahan saat memeriksa jawaban.');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan saat memeriksa jawaban.');
                    }

                    if (checkAnswerBtn) {
                        checkAnswerBtn.disabled = false;
                        checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                    }
                });
        });
    }

    // Initialize clickable answer options
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
