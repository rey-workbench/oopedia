// Personalization Notifications
export function showPersonalizationNotification(type, title, message, variant = 'info') {
    const notification = document.createElement('div');
    notification.className = `personalization-notification ${variant}`;
    notification.innerHTML = `
        <div class="notification-content">
            <h4 class="notification-title">${title}</h4>
            <p class="notification-message">${message}</p>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
    .personalization-notification {
        position: fixed;
        top: 80px;
        right: 20px;
        max-width: 400px;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideInRight 0.4s ease-out;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .personalization-notification.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .personalization-notification.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .personalization-notification.info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    
    .notification-content { flex: 1; }
    .notification-title { font-size: 1.1rem; font-weight: bold; margin: 0 0 4px 0; }
    .notification-message { font-size: 0.9rem; margin: 0; opacity: 0.95; }
    
    .notification-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    
    .notification-close:hover { background: rgba(255,255,255,0.3); }
    .personalization-notification.fade-out { animation: slideOutRight 0.3s ease-in forwards; }
    
    @keyframes slideInRight {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);
