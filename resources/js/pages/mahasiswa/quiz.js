// Mahasiswa Quiz Module
import { Http, UI } from '../../utils/index.js';
import { showPersonalizationNotification } from '../../components/notifications.js';

function redirectAfterCompletion() {
    if (document.fullscreenElement) {
        document.exitFullscreen().catch(() => { });
    }
    if (typeof redirectUrl !== 'undefined') {
        window.location.href = redirectUrl;
    }
}

function setupSecurity() {
    // Disable copy/paste/keyboard shortcuts
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
        if (
            e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'C' || e.key === 'J')) ||
            (e.ctrlKey && (e.key === 'u' || e.key === 'c' || e.key === 'v' || e.key === 's'))
        ) {
            e.preventDefault();
            UI.notify('danger', 'Fitur ini dinonaktifkan demi keamanan ujian.');
        }
    });

    // Anti-text selection
    document.body.style.userSelect = 'none';
    document.body.style.webkitUserSelect = 'none';

    // Tab switch detection
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            UI.notify('warning', 'Peringatan: Jangan meninggalkan tab ujian!');
        }
    });

    // Auto-fullscreen on first interaction
    document.addEventListener('click', function enterFS() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log('Fullscreen failed');
            });
        }
        document.removeEventListener('click', enterFS);
    }, { once: true });
}

function triggerHapticFeedback(isSuccess) {
    const feedbackOverlay = document.querySelector('.exercise-feedback');
    const hapticLayer = document.getElementById('hapticLayer');
    const contentBox = document.querySelector('.feedback-content-box');

    feedbackOverlay.classList.remove('hidden');

    // Set color based on result
    const color = isSuccess ? 'rgba(34, 197, 94, 0.4)' : 'rgba(225, 29, 72, 0.4)';
    hapticLayer.style.backgroundColor = color;
    hapticLayer.style.opacity = '1';

    // Popup animation
    setTimeout(() => {
        contentBox.classList.add('scale-100', 'opacity-100');
    }, 100);

    // Vibration feedback if available
    if (navigator.vibrate) {
        navigator.vibrate(isSuccess ? [200] : [100, 50, 100]);
    }
}

export function initializeQuestionForm(config) {
    const questionForm = document.getElementById('questionForm');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    const routes = config.routes;

    if (questionForm) {
        setupSecurity();

        // Hint Button Logic
        const hintBtn = document.getElementById('hintBtn');
        if (hintBtn) {
            hintBtn.addEventListener('click', () => {
                UI.confirm('Gunakan Hint?', 'Poin akan dikurangi jika Anda menggunakan hint.', 'Ya, Gunakan', () => {
                    UI.notify('info', 'Hint: Coba ingat kembali konsep dasar materi ini.');

                    const usedHintInput = document.getElementById('usedHintInput');
                    if (usedHintInput) usedHintInput.value = 'true';

                    const countEl = document.getElementById('hintsCount');
                    if (countEl) countEl.textContent = Math.max(0, parseInt(countEl.textContent) - 1);
                });
            });
        }

        questionForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-pulse me-2"></i>Mengevaluasi...';
            }

            Http.postForm(this.action, this)
                .then(data => {
                    UI.hideLoading();
                    showFeedback(data, routes);
                })
                .catch(error => {
                    UI.handleError(error);
                    UI.hideLoading();

                    if (checkAnswerBtn) {
                        checkAnswerBtn.disabled = false;
                        checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                    }
                });
        });
    }

    // Make answer options clickable
    const answerOptions = document.querySelectorAll('.answer-option');
    answerOptions.forEach(option => {
        option.addEventListener('click', function () {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                answerOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            }
        });
    });
}

function showFeedback(data, routes) {
    const feedbackOverlay = document.querySelector('.exercise-feedback');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const tryAgainBtn = document.getElementById('tryAgainBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const explanationBox = document.getElementById('explanationBox');
    const explanationText = document.getElementById('explanationText');
    const contentBox = document.querySelector('.feedback-content-box');

    const isSuccess = data.status === 'success';

    // Trigger haptic & background color
    triggerHapticFeedback(isSuccess);

    // Update Status
    if (feedbackStatus) {
        feedbackStatus.textContent = isSuccess ? 'Jawaban Benar!' : 'Jawaban Salah!';
        feedbackStatus.className = `text-4xl font-black mb-8 italic uppercase tracking-tight ${isSuccess ? 'text-emerald-600' : 'text-rose-600'}`;
    }

    // Update Top Stats if adaptive result is present
    if (data.adaptiveResult && data.adaptiveResult.new_state) {
        const state = data.adaptiveResult.new_state;
        const xpEl = document.querySelector('.xp-indicator h5');
        const streakEl = document.querySelector('.streak-indicator h5');
        const hintsCountEl = document.getElementById('hintsCount');
        const hintBtn = document.getElementById('hintBtn');

        if (xpEl) xpEl.innerHTML = `<i class="fas fa-star text-amber-400 text-sm"></i> ${state.xp}`;
        if (streakEl) streakEl.innerHTML = `<i class="fas fa-fire text-orange-500 text-sm"></i> ${state.current_streak}`;

        if (hintsCountEl) hintsCountEl.textContent = state.hints_available;
        if (hintBtn) {
            hintBtn.disabled = state.hints_available <= 0;
            if (state.hints_available <= 0) {
                hintBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // PERSONALIZATION NOTIFICATIONS
        if (state.fast_track_active === 1) {
            showPersonalizationNotification(
                'fast-track',
                '🚀 Fast Learner Detected!',
                'Kecepatan dan akurasi Anda luar biasa! Bonus +30 XP',
                'success'
            );
        }

        if (state.show_fatigue_warning === 1) {
            showPersonalizationNotification(
                'fatigue',
                '😴 Terdeteksi Kelelahan',
                'Sebaiknya istirahat sejenak untuk hasil lebih baik. Bonus +1 hint untuk membantu.',
                'warning'
            );
        }

        if (data.adaptiveResult.triggered_rules && data.adaptiveResult.triggered_rules.length > 0) {
            console.log('Personalization Rules Triggered:', data.adaptiveResult.triggered_rules);
        }
    }

    // Update Icon
    if (feedbackIcon) {
        feedbackIcon.innerHTML = isSuccess ? '<i class="fas fa-check-circle text-emerald-500"></i>' : '<i class="fas fa-times-circle text-rose-500"></i>';
    }

    // Explanation
    if (explanationBox && explanationText && data.explanation) {
        explanationBox.classList.remove('hidden');
        explanationText.textContent = data.explanation;
    } else {
        explanationBox?.classList.add('hidden');
    }

    // Buttons
    if (isSuccess) {
        tryAgainBtn.classList.add('hidden');
        nextQuestionBtn.classList.remove('hidden');
        if (data.hasNextQuestion) {
            nextQuestionBtn.innerHTML = 'Lanjut <i class="fas fa-arrow-right ml-2"></i>';
            nextQuestionBtn.onclick = () => window.location.href = data.nextUrl;
        } else {
            nextQuestionBtn.innerHTML = 'Tuntas <i class="fas fa-flag-checkered ml-2"></i>';
            nextQuestionBtn.onclick = () => redirectAfterCompletion();
        }
    } else {
        tryAgainBtn.classList.remove('hidden');
        nextQuestionBtn.classList.add('hidden');
        tryAgainBtn.onclick = () => {
            contentBox.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                feedbackOverlay.classList.add('hidden');
                const btn = document.getElementById('checkAnswerBtn');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            }, 300);
        };
    }
}

// Make globally available
window.initializeQuestionForm = initializeQuestionForm;
