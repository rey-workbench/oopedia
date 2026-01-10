/**
 * DOM Utilities Module
 * Helper functions for DOM manipulation and element interactions
 */

/**
 * DOM Utilities Object
 */
export const DOM = {
    /**
     * Query selector with optional parent element
     * @param {string} selector - CSS selector
     * @param {Element} parent - Parent element (default: document)
     * @returns {Element|null} Found element or null
     */
    $(selector, parent = document) {
        return parent.querySelector(selector);
    },

    /**
     * Query selector all with optional parent element
     * @param {string} selector - CSS selector
     * @param {Element} parent - Parent element (default: document)
     * @returns {NodeList} NodeList of found elements
     */
    $$(selector, parent = document) {
        return parent.querySelectorAll(selector);
    },

    /**
     * Add event listener to element(s)
     * @param {string|Element|NodeList} target - Selector, element, or NodeList
     * @param {string} event - Event name
     * @param {Function} handler - Event handler
     * @param {Object} options - Event listener options
     */
    on(target, event, handler, options = {}) {
        const elements = typeof target === 'string'
            ? this.$$(target)
            : (target instanceof NodeList ? target : [target]);

        elements.forEach(el => {
            if (el && el.addEventListener) {
                el.addEventListener(event, handler, options);
            }
        });
    },

    /**
     * Remove event listener from element(s)
     * @param {string|Element|NodeList} target - Selector, element, or NodeList
     * @param {string} event - Event name
     * @param {Function} handler - Event handler
     */
    off(target, event, handler) {
        const elements = typeof target === 'string'
            ? this.$$(target)
            : (target instanceof NodeList ? target : [target]);

        elements.forEach(el => {
            if (el && el.removeEventListener) {
                el.removeEventListener(event, handler);
            }
        });
    },

    /**
     * Add class to element(s)
     * @param {string|Element|NodeList} target - Selector, element, or NodeList
     * @param {string} className - Class name(s) to add
     */
    addClass(target, className) {
        const elements = typeof target === 'string'
            ? this.$$(target)
            : (target instanceof NodeList ? target : [target]);

        const classes = className.split(' ');
        elements.forEach(el => {
            if (el && el.classList) {
                el.classList.add(...classes);
            }
        });
    },

    /**
     * Remove class from element(s)
     * @param {string|Element|NodeList} target - Selector, element, or NodeList
     * @param {string} className - Class name(s) to remove
     */
    removeClass(target, className) {
        const elements = typeof target === 'string'
            ? this.$$(target)
            : (target instanceof NodeList ? target : [target]);

        const classes = className.split(' ');
        elements.forEach(el => {
            if (el && el.classList) {
                el.classList.remove(...classes);
            }
        });
    },

    /**
     * Toggle class on element(s)
     * @param {string|Element|NodeList} target - Selector, element, or NodeList
     * @param {string} className - Class name to toggle
     */
    toggleClass(target, className) {
        const elements = typeof target === 'string'
            ? this.$$(target)
            : (target instanceof NodeList ? target : [target]);

        elements.forEach(el => {
            if (el && el.classList) {
                el.classList.toggle(className);
            }
        });
    },

    /**
     * Check if element has class
     * @param {Element} element - Element to check
     * @param {string} className - Class name to check
     * @returns {boolean} True if element has class
     */
    hasClass(element, className) {
        return element && element.classList && element.classList.contains(className);
    },

    /**
     * Show element(s)
     * @param {string|Element|NodeList} target - Selector, element, or NodeList
     */
    show(target) {
        const elements = typeof target === 'string'
            ? this.$$(target)
            : (target instanceof NodeList ? target : [target]);

        elements.forEach(el => {
            if (el) {
                el.style.display = '';
            }
        });
    },

    /**
     * Hide element(s)
     * @param {string|Element|NodeList} target - Selector, element, or NodeList
     */
    hide(target) {
        const elements = typeof target === 'string'
            ? this.$$(target)
            : (target instanceof NodeList ? target : [target]);

        elements.forEach(el => {
            if (el) {
                el.style.display = 'none';
            }
        });
    },

    /**
     * Get/Set attribute
     * @param {Element} element - Element
     * @param {string} name - Attribute name
     * @param {string} value - Attribute value (optional, for setter)
     * @returns {string|null} Attribute value if getter
     */
    attr(element, name, value = undefined) {
        if (!element) return null;

        if (value === undefined) {
            return element.getAttribute(name);
        } else {
            element.setAttribute(name, value);
        }
    },

    /**
     * Remove attribute
     * @param {Element} element - Element
     * @param {string} name - Attribute name
     */
    removeAttr(element, name) {
        if (element) {
            element.removeAttribute(name);
        }
    },

    /**
     * Execute callback when DOM is ready
     * @param {Function} callback - Callback function
     */
    ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }
};

// Make globally available for backward compatibility
if (typeof window !== 'undefined') {
    window.DOM = DOM;
}

export default DOM;
