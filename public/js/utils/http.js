/**
 * HTTP Utility for standardized API calls
 */
(function (window) {
    'use strict';

    const Http = {
        /**
         * Get CSRF Token
         */
        getCsrfToken: function () {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        },

        /**
         * Base Fetch Wrapper
         * @param {string} url 
         * @param {object} options 
         */
        request: async function (url, options = {}) {
            const defaults = {
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            };

            // Merge headers
            if (options.headers) {
                defaults.headers = { ...defaults.headers, ...options.headers };
                delete options.headers;
            }

            // Handle JSON body
            if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
                defaults.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(options.body);
            }

            const config = { ...defaults, ...options };

            try {
                const response = await fetch(url, config);

                // Allow handling 422 validation errors gracefully
                if (response.status === 422) {
                    const data = await response.json();
                    throw { status: 422, data, message: data.message || 'Validation Error' };
                }

                if (!response.ok) {
                    throw {
                        status: response.status,
                        statusText: response.statusText,
                        url: response.url
                    };
                }

                // Return JSON if appropriate
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return await response.json();
                }

                return await response.text();
            } catch (error) {
                console.error('HTTP Request Failed:', error);
                throw error;
            }
        },

        get: function (url, options = {}) {
            return this.request(url, { ...options, method: 'GET' });
        },

        post: function (url, data, options = {}) {
            return this.request(url, { ...options, method: 'POST', body: data });
        },

        put: function (url, data, options = {}) {
            return this.request(url, { ...options, method: 'PUT', body: data });
        },

        delete: function (url, options = {}) {
            return this.request(url, { ...options, method: 'DELETE' });
        },

        // Form Data Helper
        postForm: function (url, formElement, options = {}) {
            const formData = new FormData(formElement);
            // Content-Type header should NOT be set manually for FormData
            return this.request(url, { ...options, method: 'POST', body: formData });
        }
    };

    window.Http = Http;

})(window);
