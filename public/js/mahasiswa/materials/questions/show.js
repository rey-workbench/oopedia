function redirectAfterCompletion() {
    if (document.fullscreenElement) {
        document.exitFullscreen().catch(() => { });
    }
    if (typeof redirectUrl !== 'undefined') {
        window.location.href = redirectUrl;
    }
}

/**
 * SECURITY LAYER & HAPTIC FEEDBACK
 */

function setupSecurity() {
    const quizArea = document.querySelector('main');

    // Disable copy/paste/keyboard shortcuts
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
        // Disable F12, Ctrl+Shift+I, Ctrl+U, Ctrl+C, Ctrl+V, etc.
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

function initializeQuestionForm(config) {
    const questionForm = document.getElementById('questionForm');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    const feedbackElement = document.querySelector('.exercise-feedback');
    const routes = config.routes;

    if (questionForm) {
        setupSecurity();

        // Hint Button Logic
        const hintBtn = document.getElementById('hintBtn');
        if (hintBtn) {
            hintBtn.addEventListener('click', () => {
                UI.confirm('Gunakan Hint?', 'Poin akan dikurangi jika Anda menggunakan hint.', 'Ya, Gunakan', () => {
                    // Logic to show hint (mockup for now as hint text comes from backend usually or is static)
                    // Since we don't have a dedicated API for just fetching hint text yet without submitting, 
                    // we might need to rely on what's available or just show a fast alert for now.
                    // Assuming hint is available in front-end or we just deduct count visually?
                    // For 'Secure Mode', maybe we just show a generic helpful tip or reveal first letter?
                    // Let's just show a simulated hint for now as placeholder or if backend provided it.

                    // In a real scenario, we'd hit an endpoint like /hint. 
                    // For now, let's just show a notification.
                    UI.notify('info', 'Hint: Coba ingat kembali konsep dasar materi ini.');

                    // Set hidden input flag for backend processing
                    const usedHintInput = document.getElementById('usedHintInput');
                    if (usedHintInput) usedHintInput.value = 'true';

                    // Decrement visually
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
                // Add visual feedback
                answerOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            }
        });
    });

    // Initialize Drag and Drop if elements exist
    if (document.querySelector('.draggable')) {
        initializeDragAndDrop();
    }
}

function initializeDragAndDrop() {
    const draggables = document.querySelectorAll('.draggable');
    const dropZones = document.querySelectorAll('.drop-zone');
    const hiddenInput = document.getElementById('dragAndDropAnswers');

    let draggedItem = null;

    draggables.forEach(draggable => {
        draggable.addEventListener('dragstart', () => {
            draggedItem = draggable;
            draggable.classList.add('opacity-50');
        });

        draggable.addEventListener('dragend', () => {
            draggedItem = null;
            draggable.classList.remove('opacity-50');
        });
    });

    dropZones.forEach(zone => {
        zone.addEventListener('dragover', e => {
            e.preventDefault(); // Allow drop
            zone.classList.add('bg-blue-100', 'border-blue-400');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('bg-blue-100', 'border-blue-400');
        });

        zone.addEventListener('drop', () => {
            zone.classList.remove('bg-blue-100', 'border-blue-400');
            if (draggedItem) {
                // Determine if zone is already occupied
                if (zone.hasChildNodes()) {
                    // Start swap logic or return to pool? 
                    // Simple logic: clear zone first or swap? 
                    // Let's assume replace:
                    const existing = zone.firstChild;
                    if (existing) {
                        document.querySelector('.drag-items').appendChild(existing);
                    }
                }

                zone.textContent = draggedItem.dataset.value;
                zone.dataset.userAnswer = draggedItem.dataset.value;

                // Visuals for dropped item in zone (e.g. clone styling)
                // Actually, let's just move the text content or a clone?
                // The simpler way for text drag and drop:
                zone.textContent = draggedItem.textContent.trim();
                zone.dataset.userAnswer = draggedItem.dataset.value;

                // Note: Real drag and drop often moves the element. 
                // Given the screenshot, it looks like "filling the blanks".
                // We'll update style to look "filled".
                zone.classList.add('filled', 'bg-blue-50', 'text-blue-700', 'font-bold', 'px-2', 'border', 'border-blue-300', 'rounded');

                updateDragAndDropInput();
            }
        });

        // Allow removing answer by clicking
        zone.addEventListener('click', () => {
            if (zone.textContent) {
                zone.textContent = '';
                zone.dataset.userAnswer = '';
                zone.classList.remove('filled', 'bg-blue-50', 'text-blue-700', 'font-bold', 'px-2', 'border', 'border-blue-300', 'rounded');
                updateDragAndDropInput();
                // Ideally, we should make the source option draggable again if we disabled it.
            }
        });
    });

    function updateDragAndDropInput() {
        if (!hiddenInput) return;

        const answers = {};
        dropZones.forEach(zone => {
            const index = zone.dataset.zone;
            const value = zone.dataset.userAnswer || '';
            if (value) {
                answers[index] = value;
            }
        });
        hiddenInput.value = JSON.stringify(answers);
    }
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
        explanationBox.classList.add('hidden');
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

function showErrorAlert(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    alert.style.zIndex = '10000';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}

function initializeTutorial() {
    // Tutorial implementation
}

// Add CSS animations dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInUp {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutDown {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(100px);
            opacity: 0;
        }
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0);
        }
        to {
            transform: scale(1);
        }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    @keyframes floatUp {
        0% {
            transform: translate(-50%, -50%) scale(0.5);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -100px) scale(1);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -200px) scale(0.8);
            opacity: 0;
        }
    }
    
    .reward-badge {
        display: inline-flex;
        align-items: center;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        animation: scaleIn 0.5s ease-out;
        transition: transform 0.3s;
    }
    
    .reward-badge:hover {
        transform: scale(1.05);
    }
    
    .xp-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .points-badge {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .badge-earned {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    
    .level-up-banner {
        animation: pulse 0.6s ease-in-out;
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%) !important;
    }
    
    .level-up-icon {
        animation: bounce 1s infinite;
    }
    
    .answer-option.selected {
        background-color: #e3f2fd !important;
        border-color: #2196F3 !important;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }
    
    .feedback-icon.success {
        color: #28a745;
        animation: scaleIn 0.5s ease-out;
    }
    
    .feedback-icon.error {
        color: #dc3545;
        animation: shake 0.5s;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
        20%, 40%, 60%, 80% { transform: translateX(10px); }
    }
`;
document.head.appendChild(style);
