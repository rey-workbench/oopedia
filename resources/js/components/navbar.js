/**
 * Navbar Component
 * Handles navbar interactions and tutorial reset functionality
 */

import { UI } from '../utils/ui.js';

/**
 * Reset all tutorial progress
 */
function resetAllTutorials() {
    // Remove all tutorial keys from localStorage
    for (let key in localStorage) {
        if (key.includes('tutorial_complete') || key === 'skip_admin_tour') {
            localStorage.removeItem(key);
        }
    }

    if (window.Swal) {
        window.Swal.fire({
            title: 'Tutorial Direset',
            text: 'Tutorial akan dimulai ulang',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            // Start tutorial if function exists
            if (typeof startAdminTutorial === 'function') {
                startAdminTutorial();
            } else {
                window.location.reload();
            }
        });
    } else {
        UI.success('Tutorial direset. Halaman akan dimuat ulang.');
        setTimeout(() => window.location.reload(), 1000);
    }
}

// Make globally available if needed
if (typeof window !== 'undefined') {
    window.resetAllTutorials = resetAllTutorials;
}

export { resetAllTutorials };
