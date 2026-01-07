function redirectAfterCompletion() {
    if (typeof redirectUrl !== 'undefined') {
        window.location.href = redirectUrl;
    }
}

// Confetti animation for celebrations
function triggerConfetti() {
    const duration = 3 * 1000;
    const animationEnd = Date.now() + duration;
    const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

    function randomInRange(min, max) {
        return Math.random() * (max - min) + min;
    }

    const interval = setInterval(function () {
        const timeLeft = animationEnd - Date.now();

        if (timeLeft <= 0) {
            return clearInterval(interval);
        }

        const particleCount = 50 * (timeLeft / duration);

        confetti(Object.assign({}, defaults, {
            particleCount,
            origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
        }));
        confetti(Object.assign({}, defaults, {
            particleCount,
            origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
        }));
    }, 250);
}

// Animated number counting
function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.textContent = value;
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Create floating XP/Points animation
function createFloatingReward(text, color, delay = 0) {
    setTimeout(() => {
        const reward = document.createElement('div');
        reward.className = 'floating-reward';
        reward.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            font-weight: bold;
            color: ${color};
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            z-index: 10000;
            animation: floatUp 2s ease-out forwards;
            pointer-events: none;
        `;
        reward.textContent = text;
        document.body.appendChild(reward);

        setTimeout(() => reward.remove(), 2000);
    }, delay);
}

function initializeQuestionForm(config) {
    const questionForm = document.getElementById('questionForm');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    const feedbackElement = document.querySelector('.exercise-feedback');
    const routes = config.routes;

    if (questionForm) {
        questionForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-pulse me-2"></i>Memeriksa...';
            }

            Http.postForm(this.action, this)
                .then(data => {
                    UI.hideLoading(); // Ensure any global loader is closed
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
}

function showFeedback(data, routes) {
    const feedbackElement = document.querySelector('.exercise-feedback');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const tryAgainBtn = document.getElementById('tryAgainBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const dashboardBtn = document.getElementById('dashboardBtn');
    const questionForm = document.getElementById('questionForm');
    const explanationBox = document.getElementById('explanationBox');
    const explanationText = document.getElementById('explanationText');
    const adaptiveFeedback = document.getElementById('adaptiveFeedback');

    // Close SweetAlert loader if present
    UI.hideLoading();

    // Add entrance animation to feedback
    if (feedbackElement) {
        feedbackElement.style.display = 'block';
        feedbackElement.style.animation = 'slideInUp 0.5s ease-out';
    }

    // Update Stats Bar if adaptive result exists
    if (data.adaptiveResult) {
        const ar = data.adaptiveResult;

        // Update Stats Values with animation
        const levelEl = document.querySelector('.level-indicator h5');
        const xpEl = document.querySelector('.xp-indicator h5');
        const streakEl = document.querySelector('.streak-indicator h5');

        if (levelEl && ar.level_changed !== 'none') {
            levelEl.textContent = ar.new_level.charAt(0).toUpperCase() + ar.new_level.slice(1);
            levelEl.className = 'mb-0 fw-bold ' +
                (ar.new_level === 'hard' ? 'text-danger' : (ar.new_level === 'medium' ? 'text-warning' : 'text-success'));
            levelEl.style.animation = 'pulse 0.6s ease-in-out';
        }

        if (xpEl) {
            const currentXP = parseInt(xpEl.textContent);
            const newXP = ar.total_xp;
            animateValue(xpEl, currentXP, newXP, 1000);
            xpEl.innerHTML = `<span>${newXP}</span> XP`;
        }

        if (streakEl) {
            streakEl.innerHTML = `<i class="fas fa-fire text-danger"></i> ${ar.current_streak}`;
            if (ar.current_streak > 0) {
                streakEl.style.animation = 'bounce 0.5s ease';
            }
        }

        // Show Enhanced Adaptive Feedback
        if (adaptiveFeedback) {
            let feedbackHtml = '<div class="adaptive-rewards">';

            // Level Change with special effects
            if (ar.level_changed === 'up') {
                feedbackHtml += `
                    <div class="level-up-banner alert alert-success border-0 shadow-lg mb-3">
                        <div class="d-flex align-items-center">
                            <div class="level-up-icon me-3">
                                <i class="fas fa-trophy fa-3x text-warning"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold"><i class="fas fa-arrow-up me-2"></i>LEVEL UP!</h4>
                                <p class="mb-0">${ar.message}</p>
                                <small class="text-muted">${ar.previous_level} → ${ar.new_level}</small>
                            </div>
                        </div>
                    </div>
                `;
                // Trigger confetti for level up!
                if (typeof confetti !== 'undefined') {
                    triggerConfetti();
                }
            } else if (ar.level_changed === 'down') {
                feedbackHtml += `
                    <div class="alert alert-warning border-0 shadow mb-3">
                        <i class="fas fa-arrow-down me-2"></i><strong>${ar.message}</strong>
                    </div>
                `;
            }

            // XP & Points with animation
            feedbackHtml += '<div class="rewards-container d-flex flex-wrap gap-2 mb-3">';

            if (ar.xp_earned > 0) {
                feedbackHtml += `
                    <div class="reward-badge xp-badge">
                        <i class="fas fa-star me-1"></i>
                        <span class="reward-value">+${ar.xp_earned}</span> XP
                    </div>
                `;
                createFloatingReward(`+${ar.xp_earned} XP`, '#FFD700', 200);
            }

            if (ar.points_earned > 0) {
                feedbackHtml += `
                    <div class="reward-badge points-badge">
                        <i class="fas fa-coins me-1"></i>
                        <span class="reward-value">+${ar.points_earned}</span> Points
                    </div>
                `;
                createFloatingReward(`+${ar.points_earned} Points`, '#FF6B6B', 400);
            }

            // Badges with special styling
            if (ar.badges_earned && ar.badges_earned.length > 0) {
                ar.badges_earned.forEach((badge, index) => {
                    feedbackHtml += `
                        <div class="reward-badge badge-earned" style="animation-delay: ${index * 0.2}s">
                            <i class="fas fa-medal me-1"></i>${badge}
                        </div>
                    `;
                });
            }

            feedbackHtml += '</div></div>';

            adaptiveFeedback.innerHTML = feedbackHtml;
            adaptiveFeedback.className = 'adaptive-feedback-box mt-3';
            adaptiveFeedback.style.display = 'block';
        }
    }

    // Enhanced Status Display
    if (feedbackStatus) {
        const statusClass = data.status === 'success' ? 'text-success' : 'text-danger';
        const icon = data.status === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle';
        feedbackStatus.innerHTML = `
            <div class="feedback-message">
                <i class="${icon} me-2"></i>
                <span>${data.message}</span>
            </div>
        `;
        feedbackStatus.className = statusClass;
    }

    if (feedbackIcon) {
        const isSuccess = data.status === 'success';
        feedbackIcon.className = `feedback-icon ${isSuccess ? 'success' : 'error'}`;
        feedbackIcon.innerHTML = `
            <i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-times-circle'} fa-4x"></i>
        `;
        feedbackIcon.style.animation = 'scaleIn 0.5s ease-out';
    }

    // Show explanation with better styling
    if (explanationBox && explanationText && data.explanation) {
        explanationBox.style.display = 'block';
        explanationBox.className = 'explanation-box mt-4 p-4 bg-light border-start border-4 border-info rounded shadow-sm';
        explanationText.innerHTML = `
            <h5 class="mb-3"><i class="fas fa-lightbulb me-2 text-warning"></i>Penjelasan</h5>
            <p class="mb-0">${data.explanation}</p>
        `;
    } else if (explanationBox) {
        explanationBox.style.display = 'none';
    }

    // Button handling
    if (data.status === 'success') {
        if (tryAgainBtn) tryAgainBtn.style.display = 'none';
        if (nextQuestionBtn) {
            if (data.hasNextQuestion) {
                nextQuestionBtn.style.display = 'inline-block';
                nextQuestionBtn.className = 'btn btn-success btn-lg shadow';
                nextQuestionBtn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Soal Berikutnya';
                nextQuestionBtn.onclick = () => window.location.href = data.nextUrl;
            } else {
                nextQuestionBtn.style.display = 'none';
                if (dashboardBtn) {
                    dashboardBtn.style.display = 'inline-block';
                    dashboardBtn.className = 'btn btn-primary btn-lg shadow';
                }
            }
        }
    } else {
        if (tryAgainBtn) {
            tryAgainBtn.style.display = 'inline-block';
            tryAgainBtn.className = 'btn btn-warning btn-lg shadow';
            tryAgainBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Coba Lagi';
        }
        if (nextQuestionBtn) nextQuestionBtn.style.display = 'none';
        if (dashboardBtn) dashboardBtn.style.display = 'none';

        if (tryAgainBtn) {
            tryAgainBtn.onclick = () => {
                if (feedbackElement) {
                    feedbackElement.style.animation = 'slideOutDown 0.3s ease-in';
                    setTimeout(() => feedbackElement.style.display = 'none', 300);
                }
                if (questionForm) questionForm.style.display = 'block';
                if (adaptiveFeedback) adaptiveFeedback.style.display = 'none';

                const checkAnswerBtn = document.getElementById('checkAnswerBtn');
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            };
        }
    }

    if (questionForm) questionForm.style.display = 'none';
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
