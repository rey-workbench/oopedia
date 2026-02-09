/**
 * Mahasiswa Quiz Module
 * Interactive quiz functionality with adaptive learning
 */

import { Http } from '../../utils/ajax.js';
import { UI } from '../../utils/ui.js';
import { DOM } from '../../utils/dom.js';
import { showPersonalizationNotification } from '../../components/notifications.js';

/**
 * Multimedia Controller
 */
const Multimedia = {
    backsound: null,
    correctSound: null,
    wrongSound: null,

    init() {
        this.backsound = DOM.$('#audioBacksound');
        this.correctSound = DOM.$('#audioCorrect');
        this.wrongSound = DOM.$('#audioWrong');

        // Set low volume for backsound
        if (this.backsound) {
            this.backsound.volume = 0.2;
        }

        // Start backsound on first interaction (browser requirement)
        const startAudio = () => {
            if (this.backsound) {
                this.backsound.play().catch(err => console.log('Autoplay blocked:', err));
            }
            document.removeEventListener('click', startAudio);
        };
        document.addEventListener('click', startAudio);
    },

    playCorrect() {
        if (this.correctSound) {
            this.correctSound.currentTime = 0;
            this.correctSound.play().catch(() => { });
        }
    },

    playWrong() {
        if (this.wrongSound) {
            this.wrongSound.currentTime = 0;
            this.wrongSound.play().catch(() => { });
        }
    }
};

/**
 * Trigger haptic feedback overlay
 * @param {boolean} isSuccess - Whether answer was correct
 */
function triggerFeedbackUI(isSuccess, data, routes) {
    const feedbackOverlay = DOM.$('.exercise-feedback');
    const hapticLayer = DOM.$('#hapticLayer');
    const contentBox = DOM.$('.feedback-content-box');
    const feedbackStatus = DOM.$('#feedbackStatus');
    const feedbackIcon = DOM.$('#feedbackIcon');
    const tryAgainBtn = DOM.$('#tryAgainBtn');
    const nextQuestionBtn = DOM.$('#nextQuestionBtn');
    const explanationBox = DOM.$('#explanationBox');
    const explanationText = DOM.$('#explanationText');

    if (!feedbackOverlay || !hapticLayer || !contentBox) return;

    feedbackOverlay.classList.remove('hidden');

    // Set color based on result
    const color = isSuccess ? 'rgba(34, 197, 94, 0.4)' : 'rgba(225, 29, 72, 0.4)';
    hapticLayer.style.backgroundColor = color;
    hapticLayer.style.opacity = '1';

    // Update Status & Icon
    if (feedbackStatus) {
        feedbackStatus.textContent = isSuccess ? 'Jawaban Benar!' : 'Jawaban Salah!';
        feedbackStatus.className = `text-4xl font-bold mb-8 uppercase tracking-widest ${isSuccess ? 'text-emerald-600' : 'text-rose-600'}`;
    }
    if (feedbackIcon) {
        feedbackIcon.innerHTML = isSuccess
            ? '<i class="fas fa-check-circle text-emerald-500"></i>'
            : '<i class="fas fa-times-circle text-rose-500"></i>';
    }

    // Update adaptive state if present
    if (data.adaptiveResult && data.adaptiveResult.new_state) {
        const state = data.adaptiveResult.new_state;
        updateAdaptiveState(state);

        if (state.fast_track_active === 1) {
            showPersonalizationNotification('fast-track', '🚀 Fast Learner!', 'Kecepatan luar biasa! Bonus +30 XP', 'success');
        }
        updateDebugPanel(data.adaptiveResult);
    }

    // Explanation
    if (explanationBox && explanationText && data.explanation) {
        explanationBox.classList.remove('hidden');
        explanationText.textContent = data.explanation;
    } else if (explanationBox) {
        explanationBox.classList.add('hidden');
    }

    // Buttons logic
    const nextAction = data.adaptiveResult?.new_state?.next_action_data;
    const hasSpecificRecommendation = nextAction && nextAction.type !== 'question';

    if (isSuccess || hasSpecificRecommendation) {
        if (tryAgainBtn) tryAgainBtn.style.display = 'none';
        if (nextQuestionBtn) {
            nextQuestionBtn.style.display = 'inline-flex';
            const btnLabel = nextAction?.label || 'Lanjut';
            const btnIcon = nextAction?.type === 'material' ? 'fa-book-open' : 'fa-arrow-right';
            nextQuestionBtn.innerHTML = `${btnLabel} <i class="fas ${btnIcon} ml-2"></i>`;
            nextQuestionBtn.onclick = () => window.location.href = data.nextUrl || routes.dashboard;
        }
    } else {
        if (tryAgainBtn) {
            tryAgainBtn.style.display = 'inline-flex';
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
        if (nextQuestionBtn) nextQuestionBtn.style.display = 'none';
    }

    // Popup animation
    setTimeout(() => {
        contentBox.classList.add('scale-100', 'opacity-100');
    }, 100);

    // Vibration
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

    // Update triggered rules section
    if (debugRulesList) {
        debugRulesList.innerHTML = '';

        if (adaptiveResult.triggered_rule) {
            const rule = adaptiveResult.triggered_rule;
            const li = document.createElement('li');
            li.className = 'space-y-2';
            li.innerHTML = `
                <div class="flex items-start gap-2">
                    <span class="bg-rose-900 text-rose-300 px-2 py-0.5 rounded text-[10px] font-bold mt-0.5">${rule.id}</span>
                    <div class="flex-1">
                        <div class="text-slate-200 font-bold text-sm">${rule.name}</div>
                        <div class="text-slate-400 text-xs mt-1">
                            <span class="text-emerald-400">Action:</span> ${rule.action}
                            <span class="text-slate-600 mx-2">|</span>
                            <span class="text-amber-400">Priority:</span> ${rule.priority}
                        </div>
                    </div>
                </div>
            `;
            debugRulesList.appendChild(li);

            // Add facts section
            if (adaptiveResult.facts && adaptiveResult.facts.length > 0) {
                const factsLi = document.createElement('li');
                factsLi.className = 'mt-3 pt-3 border-t border-slate-700';
                factsLi.innerHTML = `
                    <div class="text-xs">
                        <div class="text-slate-500 font-bold uppercase tracking-widest mb-2">Facts Gathered</div>
                        <div class="flex flex-wrap gap-1">
                            ${adaptiveResult.facts.map(fact => `
                                <span class="bg-blue-900 text-blue-300 px-2 py-0.5 rounded text-[10px] font-mono">${fact}</span>
                            `).join('')}
                        </div>
                    </div>
                `;
                debugRulesList.appendChild(factsLi);
            }
        } else {
            debugRulesList.innerHTML = '<li class="text-slate-500 italic text-xs">No rules triggered (fallback used)</li>';
        }
    }

    // Update state JSON section
    if (debugStateJson && adaptiveResult.new_state) {
        const state = adaptiveResult.new_state;
        const stateDisplay = {
            xp: state.global_xp,
            level: state.current_level,
            streak: state.current_streak,
            wrong_streak: state.wrong_streak,
            questions_answered: state.total_questions_answered,
            correct: state.correct_count,
            wrong: state.wrong_count,
            hints_available: state.hints_available,
            fast_track: state.fast_track_active,
            next_action: state.next_action,
            recommendation: state.recommendation,
            certification: state.certification
        };

        debugStateJson.textContent = JSON.stringify(stateDisplay, null, 2);
    }
}


/**
 * Show feedback after answer submission
 * @param {Object} data - Response data from server
 * @param {Object} routes - Route configuration
 */
function showFeedback(data, routes) {
    const isSuccess = data.status === 'success';

    // Multimedia Feedback
    if (isSuccess) {
        Multimedia.playCorrect();
    } else {
        Multimedia.playWrong();
    }

    // Show UI immediately
    triggerFeedbackUI(isSuccess, data, routes);
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

    // Initialize Multimedia
    Multimedia.init();

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
