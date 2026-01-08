// Auth Module - Consolidated
// Login + Register form enhancements

document.addEventListener('DOMContentLoaded', () => {
    // Input group filled state management
    const inputGroups = document.querySelectorAll('.input-group input');

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

    function updateFilledState(input) {
        const group = input.closest('.input-group');
        if (!group) return;

        if (input.value !== '') {
            group.classList.add('is-filled');
        } else {
            group.classList.remove('is-filled');
        }
    }

    // Register button animation
    const registerBtn = document.querySelector('.register-btn');
    if (registerBtn) {
        registerBtn.addEventListener('mouseenter', () => {
            registerBtn.classList.add('btn-pulse');
        });
        registerBtn.addEventListener('mouseleave', () => {
            registerBtn.classList.remove('btn-pulse');
        });
    }
});
