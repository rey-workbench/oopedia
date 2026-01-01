/**
 * Main Application JavaScript - Entry Point
 * 
 * This is the main JS file that initializes the application
 * and provides global utilities.
 */

(function () {
    'use strict';

    // ===================================
    // GLOBAL APP CONFIGURATION
    // ===================================

    window.App = {
        baseUrl: window.location.origin,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        theme: document.body.dataset.theme || 'default',

        /**
         * Initialize the application
         */
        init: function () {
            console.log('🚀 App initialized');
            this.initTooltips();
            this.initPopovers();
            this.initModals();
            this.initLoadingOverlay();
            this.setupAjaxDefaults();
        },

        /**
         * Initialize Loading Overlay Triggers
         */
        initLoadingOverlay: function () {
            const loadingOverlay = document.getElementById('loading-overlay');
            if (!loadingOverlay) return;

            // Hide on load
            window.addEventListener('load', () => this.hideLoading());

            // Click triggers
            document.addEventListener('click', (event) => {
                const link = event.target.closest('a');
                if (link &&
                    link.href &&
                    !link.target &&
                    link.hostname === window.location.hostname &&
                    !link.hasAttribute('data-bs-toggle') &&
                    !link.classList.contains('no-loading')) {
                    this.showLoading();
                }
            });

            // Form submit triggers
            document.addEventListener('submit', (event) => {
                if (!event.target.classList.contains('ajax-form')) {
                    this.showLoading();
                }
            });

            // Timeout safety
            this.loadingTimeout = null;
        },

        /**
         * Initialize Bootstrap tooltips
         */
        initTooltips: function () {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );

            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        },

        /**
         * Initialize Bootstrap popovers
         */
        initPopovers: function () {
            const popoverTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="popover"]')
            );

            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        },

        /**
         * Initialize modals
         */
        initModals: function () {
            // Auto-hide modals after successful operations
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function () {
                    // Clean up modal backdrop if any
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                });
            });
        },

        /**
         * Setup AJAX defaults
         */
        setupAjaxDefaults: function () {
            // Add CSRF token to all AJAX requests
            if (window.jQuery) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });
            }
        },
        /**
         * Show loading overlay
         */
        showLoading: function () {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                overlay.classList.add('show');
                clearTimeout(this.loadingTimeout);
                this.loadingTimeout = setTimeout(() => {
                    this.hideLoading();
                }, 10000);
            }
        },

        /**
         * Hide loading overlay
         */
        hideLoading: function () {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                clearTimeout(this.loadingTimeout);
                overlay.classList.remove('show');
            }
        },

        /**
         * Show toast notification
         */
        showToast: function (message, type = 'info') {
            // Simple toast implementation
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            // Append to body or toast container
            const container = document.querySelector('.toast-container') || document.body;
            container.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove after hidden
            toast.addEventListener('hidden.bs.toast', function () {
                toast.remove();
            });
        }
    };

    // ===================================
    // DOM READY
    // ===================================

    document.addEventListener('DOMContentLoaded', function () {
        App.init();
    });

    // ===================================
    // EXPORT
    // ===================================

    // Expose global helpers
    window.showLoading = () => App.showLoading();
    window.hideLoading = () => App.hideLoading();

    // Make App globally available
    window.App = App;

})();
