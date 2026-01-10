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
    const timeSpentInput = document.getElementById('timeSpentInput');
    const routes = config.routes;

    // Timer Logic
    let startTime = Date.now();

    // Reset timer when coming back or checking next question
    // Actually, this function runs once per page load/module init? 
    // If SPA navigation (AJAX) re-calls this, we need to reset.
    // For now, let's assume simple start.

    if (questionForm) {
        // setupSecurity(); // Disabled for debugging

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
            if (timeSpentInput) {
                const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
                timeSpentInput.value = elapsedSeconds;
            }

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
        const hintsCountEl = document.getElementById('hintsCount');
        const hintBtn = document.getElementById('hintBtn');

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

        // Update Debug Panel
        const debugRulesList = document.getElementById('debugRulesList');
        const debugStateJson = document.getElementById('debugStateJson');
        const debugContent = document.getElementById('debugContent');

        if (debugRulesList && data.adaptiveResult.triggered_rules) {
            debugRulesList.innerHTML = '';
            if (data.adaptiveResult.triggered_rules.length > 0) {
                data.adaptiveResult.triggered_rules.forEach(rule => {
                    const li = document.createElement('li');
                    li.className = 'flex items-start gap-2 text-xs mb-1';
                    li.innerHTML = `
                        <span class="bg-rose-900 text-rose-300 px-1 rounded text-[10px] font-bold mt-0.5">RULE</span>
                        <span class="text-slate-300">${rule.rule_name || rule}</span>
                    `;
                    debugRulesList.appendChild(li);
                });
            } else {
                debugRulesList.innerHTML = '<li class="text-slate-500 italic">No rules triggered in this step.</li>';
            }
        }

        if (debugStateJson && data.adaptiveResult.new_state) {
            // Format JSON for readability
            debugStateJson.textContent = JSON.stringify(data.adaptiveResult.new_state, null, 2);
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
    // Buttons
    if (isSuccess) {
        tryAgainBtn.style.display = 'none';
        nextQuestionBtn.style.display = 'inline-flex';

        if (data.hasNextQuestion) {
            nextQuestionBtn.innerHTML = 'Lanjut <i class="fas fa-arrow-right ml-2"></i>';
            nextQuestionBtn.onclick = () => window.location.href = data.nextUrl;
        } else {
            nextQuestionBtn.innerHTML = 'Tuntas <i class="fas fa-flag-checkered ml-2"></i>';
            nextQuestionBtn.onclick = () => redirectAfterCompletion();
        }
    } else {
        tryAgainBtn.style.display = 'inline-flex';
        nextQuestionBtn.style.display = 'none';

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
