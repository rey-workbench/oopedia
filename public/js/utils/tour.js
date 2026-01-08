/**
 * TourManager Utility for Oopedia
 * Handles Intro.js tours globally across pages.
 */
class TourManager {
    constructor() {
        this.intro = typeof introJs !== 'undefined' ? introJs() : null;
        this.isInitialized = false;
        this.pageId = window.location.pathname.replace(/\//g, '_') || 'home';
    }

    /**
     * Initialize the tour with options
     * @param {Object} options Intro.js options
     */
    init(options = {}) {
        if (!this.intro) {
            console.warn('Intro.js not loaded. Skipping tour initialization.');
            return;
        }

        const defaultOptions = {
            showProgress: true,
            exitOnOverlayClick: true,
            showBullets: true,
            scrollToElement: true,
            nextLabel: 'Berikutnya →',
            prevLabel: '← Sebelumnya',
            skipLabel: 'X',
            doneLabel: 'Selesai',
            tooltipClass: 'custom-tour',
            highlightClass: 'custom-highlight',
            hidePrev: true,
            exitOnEsc: true
        };

        this.intro.setOptions({ ...defaultOptions, ...options });
        this.isInitialized = true;

        this.intro.oncomplete(() => this.markAsCompleted());
        this.intro.onexit(() => this.markAsCompleted());

        return this;
    }

    /**
     * Start the tour if not completed or if forced
     * @param {boolean} force Force start the tour
     */
    start(force = false) {
        if (!this.isInitialized) this.init();

        if (force || !this.isCompleted()) {
            // Short delay to ensure components are rendered
            setTimeout(() => {
                this.intro.start();
            }, 800);
        }
    }

    /**
     * Check if tour is completed for current page
     */
    isCompleted() {
        return localStorage.getItem(`tour_completed_${this.pageId}`) === 'true';
    }

    /**
     * Mark tour as completed for current page
     */
    markAsCompleted() {
        localStorage.setItem(`tour_completed_${this.pageId}`, 'true');
    }

    /**
     * Reset tour status (useful for development)
     */
    reset() {
        localStorage.removeItem(`tour_completed_${this.pageId}`);
    }
}

// Global instance
window.OopediaTour = new TourManager();

// Auto-trigger manual button if it exists
document.addEventListener('DOMContentLoaded', () => {
    const tourBtn = document.getElementById('start-page-tour');
    if (tourBtn) {
        tourBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.OopediaTour.start(true);
        });
    }
});
