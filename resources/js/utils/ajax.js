/**
 * AJAX Utilities Module
 * Centralized HTTP request handling using Fetch API
 */

/**
 * Get CSRF token from meta tag
 * @returns {string} CSRF token
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

/**
 * Default headers for AJAX requests
 * @returns {Object} Headers object
 */
function getDefaultHeaders() {
    return {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    };
}

/**
 * Handle HTTP response
 * @param {Response} response - Fetch API response
 * @returns {Promise} JSON parsed response
 */
async function handleResponse(response) {
    if (!response.ok) {
        const error = new Error(`HTTP error! status: ${response.status}`);
        error.status = response.status;
        error.response = response;

        // Try to parse error message from response
        try {
            const errorData = await response.json();
            error.message = errorData.message || error.message;
            error.errors = errorData.errors;
        } catch (e) {
            // If JSON parsing fails, use status text
            error.message = response.statusText || error.message;
        }

        throw error;
    }

    return await response.json();
}

/**
 * HTTP Utilities Object
 */
export const Http = {
    /**
     * Perform GET request
     * @param {string} url - Request URL
     * @param {Object} options - Additional fetch options
     * @returns {Promise} JSON response
     */
    async get(url, options = {}) {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                ...getDefaultHeaders(),
                ...options.headers
            },
            ...options
        });

        return handleResponse(response);
    },

    /**
     * Perform POST request with JSON data
     * @param {string} url - Request URL
     * @param {Object} data - Data to send
     * @param {Object} options - Additional fetch options
     * @returns {Promise} JSON response
     */
    async post(url, data = {}, options = {}) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                ...getDefaultHeaders(),
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        });

        return handleResponse(response);
    },

    /**
     * Perform POST request with FormData
     * @param {string} url - Request URL
     * @param {HTMLFormElement|FormData} formElementOrData - Form element or FormData object
     * @param {Object} options - Additional fetch options
     * @returns {Promise} JSON response
     */
    async postForm(url, formElementOrData, options = {}) {
        const formData = formElementOrData instanceof FormData
            ? formElementOrData
            : new FormData(formElementOrData);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                ...getDefaultHeaders(),
                'X-CSRF-TOKEN': getCsrfToken(),
                ...options.headers
            },
            body: formData,
            ...options
        });

        return handleResponse(response);
    },

    /**
     * Perform PUT request
     * @param {string} url - Request URL
     * @param {Object} data - Data to send
     * @param {Object} options - Additional fetch options
     * @returns {Promise} JSON response
     */
    async put(url, data = {}, options = {}) {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                ...getDefaultHeaders(),
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        });

        return handleResponse(response);
    },

    /**
     * Perform PATCH request
     * @param {string} url - Request URL
     * @param {Object} data - Data to send
     * @param {Object} options - Additional fetch options
     * @returns {Promise} JSON response
     */
    async patch(url, data = {}, options = {}) {
        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                ...getDefaultHeaders(),
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        });

        return handleResponse(response);
    },

    /**
     * Perform DELETE request
     * @param {string} url - Request URL
     * @param {Object} options - Additional fetch options
     * @returns {Promise} JSON response
     */
    async delete(url, options = {}) {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                ...getDefaultHeaders(),
                'X-CSRF-TOKEN': getCsrfToken(),
                ...options.headers
            },
            ...options
        });

        return handleResponse(response);
    }
};

// Make globally available for backward compatibility
if (typeof window !== 'undefined') {
    window.Http = Http;
}

export default Http;
