/**
 * UI Utilities Module
 * User interface helper functions for notifications, confirmations, and loading states
 */

/**
 * UI Utilities Object
 */
export const UI = {
    /**
     * Show notification message
     * @param {string} type - Notification type: 'success', 'error', 'warning', 'info', 'danger'
     * @param {string} message - Message to display
     * @param {Object} options - Additional SweetAlert2 options
     */
    notify(type, message, options = {}) {
        console.log(`[${type.toUpperCase()}] ${message}`);

        if (window.Swal) {
            window.Swal.fire({
                icon: type === 'danger' ? 'error' : type,
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                ...options
            });
        } else {
            // Fallback to browser alert
            alert(message);
        }
    },

    /**
     * Show success notification
     * @param {string} message - Success message
     * @param {Object} options - Additional options
     */
    success(message, options = {}) {
        this.notify('success', message, options);
    },

    /**
     * Show error notification
     * @param {string} message - Error message
     * @param {Object} options - Additional options
     */
    error(message, options = {}) {
        this.notify('error', message, options);
    },

    /**
     * Show warning notification
     * @param {string} message - Warning message
     * @param {Object} options - Additional options
     */
    warning(message, options = {}) {
        this.notify('warning', message, options);
    },

    /**
     * Show info notification
     * @param {string} message - Info message
     * @param {Object} options - Additional options
     */
    info(message, options = {}) {
        this.notify('info', message, options);
    },

    /**
     * Show confirmation dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     * @param {string} confirmText - Confirm button text
     * @param {Function} onConfirm - Callback when confirmed
     * @param {Function} onCancel - Callback when cancelled
     * @param {Object} options - Additional SweetAlert2 options
     */
    confirm(title, message, confirmText, onConfirm, onCancel = null, options = {}) {
        if (window.Swal) {
            window.Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: confirmText || 'Ya',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                ...options
            }).then((result) => {
                if (result.isConfirmed && onConfirm) {
                    onConfirm();
                } else if (result.isDismissed && onCancel) {
                    onCancel();
                }
            });
        } else {
            // Fallback to browser confirm
            if (confirm(`${title}\n${message}`)) {
                onConfirm?.();
            } else {
                onCancel?.();
            }
        }
    },

    /**
     * Show loading indicator
     * @param {string} title - Loading message
     */
    showLoading(title = 'Loading...') {
        if (window.Swal) {
            window.Swal.fire({
                title: title,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    window.Swal.showLoading();
                }
            });
        }
    },

    /**
     * Hide loading indicator
     */
    hideLoading() {
        if (window.Swal) {
            window.Swal.close();
        }
    },

    /**
     * Handle and display error
     * @param {Error|string} error - Error object or message
     * @param {string} defaultMessage - Default message if error is empty
     */
    handleError(error, defaultMessage = 'Terjadi kesalahan') {
        console.error('Error:', error);

        let message = defaultMessage;

        if (typeof error === 'string') {
            message = error;
        } else if (error && error.message) {
            message = error.message;
        }

        // If error has validation errors, show them
        if (error && error.errors) {
            const errorList = Object.values(error.errors).flat().join('\n');
            message = `${message}\n\n${errorList}`;
        }

        this.notify('danger', message);
    },

    /**
     * Show modal dialog
     * @param {string} title - Modal title
     * @param {string} content - Modal content (HTML supported)
     * @param {Object} options - Additional SweetAlert2 options
     */
    modal(title, content, options = {}) {
        if (window.Swal) {
            window.Swal.fire({
                title: title,
                html: content,
                showCloseButton: true,
                focusConfirm: false,
                ...options
            });
        } else {
            alert(`${title}\n\n${content}`);
        }
    }
};

// Make globally available for backward compatibility
if (typeof window !== 'undefined') {
    window.UI = UI;
}

export default UI;
