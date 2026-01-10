/**
 * Auth Module
 * Login and Register form enhancements
 */

import { DOM } from '../../utils/dom.js';

/**
 * Update filled state for input group
 * @param {Element} input - Input element
 */
function updateFilledState(input) {
    const group = input.closest('.input-group');
    if (!group) return;

    if (input.value !== '') {
        group.classList.add('is-filled');
    } else {
        group.classList.remove('is-filled');
    }
}

/**
 * Initialize auth page functionality
 */
function initAuth() {
    // Input group filled state management
    const inputGroups = DOM.$$('.input-group input');

    inputGroups.forEach(input => {
        // Check on page load
        if (input.value !== '') {
            input.closest('.input-group')?.classList.add('is-filled');
        }

        // Check on input events
        input.addEventListener('focus', () => updateFilledState(input));
        input.addEventListener('blur', () => updateFilledState(input));
        input.addEventListener('input', () => updateFilledState(input));
    });

    // Register button animation
    const registerBtn = DOM.$('.register-btn');
    if (registerBtn) {
        registerBtn.addEventListener('mouseenter', () => {
            registerBtn.classList.add('btn-pulse');
        });
        registerBtn.addEventListener('mouseleave', () => {
            registerBtn.classList.remove('btn-pulse');
        });
    }
}

// Initialize on DOM ready
DOM.ready(initAuth);

export { initAuth };
