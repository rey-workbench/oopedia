/**
 * Tour/Tutorial Utilities Module
 * Interactive tour functionality using IntroJS
 */

/**
 * Tour Utilities Object
 */
export const Tour = {
    /**
     * Initialize tour with steps
     * @param {Array} steps - Array of tour steps
     * @param {Object} options - Additional IntroJS options
     * @returns {Object|null} IntroJS instance or null if not available
     */
    init(steps, options = {}) {
        if (typeof introJs === 'undefined') {
            console.warn('IntroJS not loaded');
            return null;
        }

        const defaultOptions = {
            steps: steps,
            showProgress: true,
            showBullets: false,
            exitOnOverlayClick: false,
            doneLabel: 'Selesai',
            nextLabel: 'Lanjut',
            prevLabel: 'Kembali',
            skipLabel: 'Lewati',
            ...options
        };

        return introJs().setOptions(defaultOptions);
    },

    /**
     * Start tour immediately
     * @param {Array} steps - Array of tour steps
     * @param {Object} options - Additional IntroJS options
     */
    start(steps, options = {}) {
        const tour = this.init(steps, options);
        if (tour) {
            tour.start();
        }
    },

    /**
     * Check if tour has been completed
     * @param {string} tourKey - Unique key for the tour
     * @returns {boolean} True if tour completed
     */
    isCompleted(tourKey) {
        return localStorage.getItem(`tutorial_complete_${tourKey}`) === 'true';
    },

    /**
     * Mark tour as completed
     * @param {string} tourKey - Unique key for the tour
     */
    markCompleted(tourKey) {
        localStorage.setItem(`tutorial_complete_${tourKey}`, 'true');
    },

    /**
     * Reset tour completion status
     * @param {string} tourKey - Unique key for the tour (optional, resets all if not provided)
     */
    reset(tourKey = null) {
        if (tourKey) {
            localStorage.removeItem(`tutorial_complete_${tourKey}`);
        } else {
            // Reset all tutorials
            for (let key in localStorage) {
                if (key.includes('tutorial_complete') || key === 'skip_admin_tour') {
                    localStorage.removeItem(key);
                }
            }
        }
    },

    /**
     * Start tour with completion tracking
     * @param {string} tourKey - Unique key for the tour
     * @param {Array} steps - Array of tour steps
     * @param {Object} options - Additional IntroJS options
     */
    startWithTracking(tourKey, steps, options = {}) {
        // Check if already completed
        if (this.isCompleted(tourKey)) {
            console.log(`Tour "${tourKey}" already completed`);
            return;
        }

        const tour = this.init(steps, options);

        if (tour) {
            // Mark as completed when tour finishes
            tour.oncomplete(() => {
                this.markCompleted(tourKey);
            });

            // Also mark as completed if skipped
            tour.onexit(() => {
                this.markCompleted(tourKey);
            });

            tour.start();
        }
    }
};

// Make globally available for backward compatibility
if (typeof window !== 'undefined') {
    window.Tour = Tour;
}

export default Tour;
