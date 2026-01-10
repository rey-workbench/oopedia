/**
 * Mahasiswa Quiz Module
 * Interactive quiz functionality with adaptive learning
 */

import { Http } from '../../utils/ajax.js';
import { UI } from '../../utils/ui.js';
import { DOM } from '../../utils/dom.js';
import { showPersonalizationNotification } from '../../components/notifications.js';

/**
 * Redirect after quiz completion
 */
function redirectAfterCompletion() {
    if (document.fullscreenElement) {
        document.exitFullscreen().catch(() => { });
    }
    if (typeof redirectUrl !== 'undefined') {
        window.location.href = redirectUrl;
    }
}

/**
 * Setup security measures for quiz
 */
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
            UI.warning('Fitur ini dinonaktifkan demi keamanan ujian.');
        }
    });

    // Anti-text selection
    document.body.style.userSelect = 'none';
    document.body.style.webkitUserSelect = 'none';

    // Tab switch detection
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            UI.warning('Peringatan: Jangan meninggalkan tab ujian!');
        }
    });

    // Auto-fullscreen on first interaction
    document.addEventListener('click', function enterFS() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log('Fullscreen failed:', err);
            });
        }
        document.removeEventListener('click', enterFS);
    }, { once: true });
}

/**
 * Trigger haptic feedback overlay
 * @param {boolean} isSuccess - Whether answer was correct
 */
function triggerHapticFeedback(isSuccess) {
    const feedbackOverlay = DOM.$('.exercise-feedback');
    const hapticLayer = DOM.$('#hapticLayer');
    const contentBox = DOM.$('.feedback-content-box');

    if (!feedbackOverlay || !hapticLayer || !contentBox) return;

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

/**
 * Update UI with adaptive state
 * @param {Object} state - Adaptive state object
 */
function updateAdaptiveState(state) {
    const hintsCountEl = DOM.$('#hintsCount');
    const hintBtn = DOM.$('#hintBtn');

    if (hintsCountEl) {
        hintsCountEl.textContent = state.hints_available;
    }

    if (hintBtn) {
        hintBtn.disabled = state.hints_available <= 0;
        if (state.hints_available <= 0) {
            hintBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            hintBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
}

/**
 * Update debug panel with adaptive data
 * @param {Object} adaptiveResult - Adaptive result data
 */
function updateDebugPanel(adaptiveResult) {
    const debugRulesList = DOM.$('#debugRulesList');
    const debugStateJson = DOM.$('#debugStateJson');

    if (debugRulesList && adaptiveResult.triggered_rules) {
        debugRulesList.innerHTML = '';

        if (adaptiveResult.triggered_rules.length > 0) {
            adaptiveResult.triggered_rules.forEach(rule => {
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

    if (debugStateJson && adaptiveResult.new_state) {
        debugStateJson.textContent = JSON.stringify(adaptiveResult.new_state, null, 2);
    }
}

/**
 * Show feedback after answer submission
 * @param {Object} data - Response data from server
 * @param {Object} routes - Route configuration
 */
function showFeedback(data, routes) {
    const feedbackOverlay = DOM.$('.exercise-feedback');
    const feedbackStatus = DOM.$('#feedbackStatus');
    const feedbackIcon = DOM.$('#feedbackIcon');
    const tryAgainBtn = DOM.$('#tryAgainBtn');
    const nextQuestionBtn = DOM.$('#nextQuestionBtn');
    const explanationBox = DOM.$('#explanationBox');
    const explanationText = DOM.$('#explanationText');
    const contentBox = DOM.$('.feedback-content-box');

    const isSuccess = data.status === 'success';

    // Trigger haptic & background color
    triggerHapticFeedback(isSuccess);

    // Update Status
    if (feedbackStatus) {
        feedbackStatus.textContent = isSuccess ? 'Jawaban Benar!' : 'Jawaban Salah!';
        feedbackStatus.className = `text-4xl font-black mb-8 italic uppercase tracking-tight ${isSuccess ? 'text-emerald-600' : 'text-rose-600'}`;
    }

    // Update adaptive state if present
    if (data.adaptiveResult && data.adaptiveResult.new_state) {
        const state = data.adaptiveResult.new_state;
        updateAdaptiveState(state);

        // Show personalization notifications
        if (state.fast_track_active === 1) {
            showPersonalizationNotification(
                'fast-track',
                '🚀 Fast Learner Detected!',
                'Kecepatan dan akurasi Anda luar biasa! Bonus +30 XP',
                'success'
            );
        }

        // Update debug panel
        updateDebugPanel(data.adaptiveResult);
    }

    // Update Icon
    if (feedbackIcon) {
        feedbackIcon.innerHTML = isSuccess
            ? '<i class="fas fa-check-circle text-emerald-500"></i>'
            : '<i class="fas fa-times-circle text-rose-500"></i>';
    }

    // Show explanation if available
    if (explanationBox && explanationText && data.explanation) {
        explanationBox.classList.remove('hidden');
        explanationText.textContent = data.explanation;
    } else if (explanationBox) {
        explanationBox.classList.add('hidden');
    }

    // Configure action buttons
    const nextAction = data.adaptiveResult?.new_state?.next_action_data;
    const hasSpecificRecommendation = nextAction && nextAction.type !== 'question';

    if (isSuccess || hasSpecificRecommendation) {
        if (tryAgainBtn) tryAgainBtn.style.display = isSuccess ? 'none' : 'inline-flex';
        if (nextQuestionBtn) {
            nextQuestionBtn.style.display = 'inline-flex';

            const btnLabel = nextAction?.label || 'Lanjut';
            const btnIcon = nextAction?.type === 'material' ? 'fa-book-open' :
                nextAction?.type === 'navigation' ? 'fa-home' :
                    nextAction?.type === 'certificate' ? 'fa-medal' : 'fa-arrow-right';

            if (data.hasNextQuestion || nextAction?.url) {
                nextQuestionBtn.innerHTML = `${btnLabel} <i class="fas ${btnIcon} ml-2"></i>`;
                nextQuestionBtn.onclick = () => window.location.href = data.nextUrl;
            } else {
                nextQuestionBtn.innerHTML = 'Tuntas <i class="fas fa-flag-checkered ml-2"></i>';
                nextQuestionBtn.onclick = () => redirectAfterCompletion();
            }
        }
    } else {
        if (tryAgainBtn) tryAgainBtn.style.display = 'inline-flex';
        if (nextQuestionBtn) nextQuestionBtn.style.display = 'none';

        if (tryAgainBtn) {
            tryAgainBtn.onclick = () => {
                contentBox.classList.remove('scale-100', 'opacity-100');
                setTimeout(() => {
                    feedbackOverlay.classList.add('hidden');
                    const btn = DOM.$('#checkAnswerBtn');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                    }
                }, 300);
            };
        }
    }
}

/**
 * Initialize question form
 * @param {Object} config - Configuration object with routes
 */
export function initializeQuestionForm(config) {
    const questionForm = DOM.$('#questionForm');
    const checkAnswerBtn = DOM.$('#checkAnswerBtn');
    const timeSpentInput = DOM.$('#timeSpentInput');
    const routes = config.routes;

    if (!questionForm) return;

    // Timer Logic
    let startTime = Date.now();

    // Security setup (disabled for debugging)
    // setupSecurity();

    // Hint Button Logic
    const hintBtn = DOM.$('#hintBtn');
    if (hintBtn) {
        hintBtn.addEventListener('click', () => {
            const hintText = hintBtn.getAttribute('data-hint') || 'Coba ingat kembali konsep dasar materi ini.';

            // Show hint overlay with same style as feedback
            showHintOverlay(hintText);

            // Mark hint as used
            const usedHintInput = DOM.$('#usedHintInput');
            if (usedHintInput) usedHintInput.value = 'true';

            // Decrease hint count
            const countEl = DOM.$('#hintsCount');
            if (countEl) {
                countEl.textContent = Math.max(0, parseInt(countEl.textContent) - 1);
            }

            // Disable button if no hints left
            const newCount = parseInt(countEl?.textContent || 0);
            if (newCount <= 0) {
                hintBtn.disabled = true;
                hintBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    }

    // Form submission
    questionForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Update time spent
        if (timeSpentInput) {
            const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
            timeSpentInput.value = elapsedSeconds;
        }

        // Disable submit button
        if (checkAnswerBtn) {
            checkAnswerBtn.disabled = true;
            checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-pulse me-2"></i>Mengevaluasi...';
        }

        // Submit form via AJAX
        Http.postForm(this.action, this)
            .then(data => {
                UI.hideLoading();
                showFeedback(data, routes);
            })
            .catch(error => {
                UI.handleError(error);
                UI.hideLoading();

                // Re-enable submit button
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            });
    });

    // Make answer options clickable
    const answerOptions = DOM.$$('.answer-option');
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

/**
 * Show hint overlay with consistent styling
 * @param {string} hintText - Hint text to display
 */
function showHintOverlay(hintText) {
    // Create or get hint overlay
    let hintOverlay = DOM.$('#hintOverlay');

    if (!hintOverlay) {
        hintOverlay = document.createElement('div');
        hintOverlay.id = 'hintOverlay';
        hintOverlay.className = 'fixed inset-0 z-[100] flex items-center justify-center pointer-events-none transition-all duration-300 hidden';
        hintOverlay.innerHTML = `
            <div class="absolute inset-0 bg-amber-500/20 opacity-0 transition-opacity duration-300" id="hintHapticLayer"></div>
            <div class="relative z-10 p-12 rounded-[3rem] text-center bg-white shadow-2xl scale-90 opacity-0 transition-all duration-500 pointer-events-auto max-w-2xl mx-4" id="hintContentBox">
                <div class="text-8xl mb-6">💡</div>
                <div class="text-4xl font-black mb-8 italic uppercase tracking-tight text-amber-600">Hint!</div>
                
                <div class="max-w-lg mx-auto mt-6 p-6 bg-amber-50 rounded-2xl border-l-8 border-amber-500 text-left">
                    <h5 class="font-bold text-lg mb-3 text-amber-900 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-500"></i>
                        Petunjuk
                    </h5>
                    <p id="hintTextContent" class="text-gray-700 leading-relaxed text-base"></p>
                </div>

                <div class="flex justify-center mt-10">
                    <button id="closeHintBtn" class="px-10 py-4 rounded-2xl font-black italic uppercase tracking-widest text-sm bg-amber-600 hover:bg-amber-700 text-white transition-colors shadow-lg hover:shadow-xl">
                        <i class="fas fa-check mr-2"></i> Mengerti
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(hintOverlay);

        // Add close button handler
        DOM.$('#closeHintBtn').addEventListener('click', () => {
            closeHintOverlay();
        });
    }

    // Update hint text
    const hintTextEl = DOM.$('#hintTextContent');
    if (hintTextEl) {
        hintTextEl.textContent = hintText;
    }

    // Show overlay with animation
    hintOverlay.classList.remove('hidden');
    const hapticLayer = DOM.$('#hintHapticLayer');
    const contentBox = DOM.$('#hintContentBox');

    // Haptic feedback
    setTimeout(() => {
        if (hapticLayer) {
            hapticLayer.style.opacity = '1';
        }
    }, 10);

    // Content animation
    setTimeout(() => {
        if (contentBox) {
            contentBox.classList.add('scale-100', 'opacity-100');
            contentBox.classList.remove('scale-90', 'opacity-0');
        }
    }, 100);

    // Vibration if available
    if (navigator.vibrate) {
        navigator.vibrate([100, 50, 100]);
    }
}

/**
 * Close hint overlay
 */
function closeHintOverlay() {
    const hintOverlay = DOM.$('#hintOverlay');
    const contentBox = DOM.$('#hintContentBox');
    const hapticLayer = DOM.$('#hintHapticLayer');

    if (contentBox) {
        contentBox.classList.remove('scale-100', 'opacity-100');
        contentBox.classList.add('scale-90', 'opacity-0');
    }

    if (hapticLayer) {
        hapticLayer.style.opacity = '0';
    }

    setTimeout(() => {
        if (hintOverlay) {
            hintOverlay.classList.add('hidden');
        }
    }, 300);
}

// Make globally available
if (typeof window !== 'undefined') {
    window.initializeQuestionForm = initializeQuestionForm;
}
