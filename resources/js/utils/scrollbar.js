/**
 * Scrollbar Utilities Module
 * Custom scrollbar styling and initialization
 */

/**
 * Scrollbar Utilities Object
 */
export const Scrollbar = {
    /**
     * Initialize custom scrollbar styling
     * @param {string|Element|NodeList} target - Selector, element, or NodeList
     * @param {Object} options - Scrollbar styling options
     */
    init(target, options = {}) {
        const defaultOptions = {
            width: 'thin',
            trackColor: '#f1f5f9',
            thumbColor: '#cbd5e1',
            ...options
        };

        const elements = typeof target === 'string'
            ? document.querySelectorAll(target)
            : (target instanceof NodeList ? target : [target]);

        elements.forEach(el => {
            if (el) {
                el.style.scrollbarWidth = defaultOptions.width;
                el.style.scrollbarColor = `${defaultOptions.thumbColor} ${defaultOptions.trackColor}`;
            }
        });
    },

    /**
     * Scroll element to top
     * @param {Element} element - Element to scroll
     * @param {boolean} smooth - Use smooth scrolling
     */
    scrollToTop(element, smooth = true) {
        if (element) {
            element.scrollTo({
                top: 0,
                behavior: smooth ? 'smooth' : 'auto'
            });
        }
    },

    /**
     * Scroll element to bottom
     * @param {Element} element - Element to scroll
     * @param {boolean} smooth - Use smooth scrolling
     */
    scrollToBottom(element, smooth = true) {
        if (element) {
            element.scrollTo({
                top: element.scrollHeight,
                behavior: smooth ? 'smooth' : 'auto'
            });
        }
    },

    /**
     * Scroll element into view
     * @param {Element} element - Element to scroll into view
     * @param {Object} options - ScrollIntoView options
     */
    scrollIntoView(element, options = {}) {
        if (element) {
            const defaultOptions = {
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest',
                ...options
            };

            element.scrollIntoView(defaultOptions);
        }
    },

    /**
     * Check if element is scrollable
     * @param {Element} element - Element to check
     * @returns {boolean} True if element is scrollable
     */
    isScrollable(element) {
        if (!element) return false;

        return element.scrollHeight > element.clientHeight ||
            element.scrollWidth > element.clientWidth;
    },

    /**
     * Get scroll position
     * @param {Element} element - Element to get scroll position from
     * @returns {Object} Object with top and left scroll positions
     */
    getScrollPosition(element) {
        if (!element) return { top: 0, left: 0 };

        return {
            top: element.scrollTop,
            left: element.scrollLeft
        };
    },

    /**
     * Set scroll position
     * @param {Element} element - Element to set scroll position
     * @param {number} top - Top scroll position
     * @param {number} left - Left scroll position
     * @param {boolean} smooth - Use smooth scrolling
     */
    setScrollPosition(element, top, left = 0, smooth = true) {
        if (element) {
            element.scrollTo({
                top: top,
                left: left,
                behavior: smooth ? 'smooth' : 'auto'
            });
        }
    }
};

// Make globally available for backward compatibility
if (typeof window !== 'undefined') {
    window.Scrollbar = Scrollbar;
}

export default Scrollbar;
