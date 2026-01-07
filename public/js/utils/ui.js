/**
 * UI Utility for standardized interactions (Alerts, Confirmations, Loading)
 */
(function (window) {
    'use strict';

    const UI = {
        /**
         * Show Success Alert/Toast
         */
        showSuccess: function (message, title = 'Berhasil!') {
            if (typeof Swal !== 'undefined') {
                return Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            } else {
                alert(message);
                return Promise.resolve();
            }
        },

        /**
         * Show Error Alert
         */
        showError: function (message, title = 'Terjadi Kesalahan') {
            if (typeof Swal !== 'undefined') {
                return Swal.fire({
                    icon: 'error',
                    title: title,
                    text: message,
                    confirmButtonText: 'Tutup'
                });
            } else {
                alert(message);
                return Promise.resolve();
            }
        },

        /**
         * Show Confirmation Dialog
         */
        confirm: function (message, confirmText = 'Ya, Lanjutkan', title = 'Apakah Anda yakin?') {
            if (typeof Swal !== 'undefined') {
                return Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Batal'
                });
            } else {
                return Promise.resolve({ isConfirmed: confirm(message) });
            }
        },

        /**
         * Show Loading Spinner
         */
        showLoading: function (message = 'Memproses...') {
            if (window.App && window.App.showLoading) {
                window.App.showLoading(); // Use app global loader if available
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: message,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        },

        /**
         * Hide Loading Spinner
         */
        hideLoading: function () {
            if (window.App && window.App.hideLoading) {
                window.App.hideLoading();
            }
            if (typeof Swal !== 'undefined' && Swal.isVisible() && Swal.isLoading()) {
                Swal.close();
            }
        },

        /**
         * Handle Standard API Errors
         */
        handleError: function (error) {
            console.error('Operation Failed:', error);

            // Validation Errors
            if (error.status === 422 && error.data && error.data.errors) {
                const errorMessages = Object.values(error.data.errors).flat().join('\n');
                this.showError(errorMessages, 'Validasi Gagal');
                return;
            }

            // Specific message
            if (error.message) {
                this.showError(error.message);
                return;
            }

            this.showError('Terjadi kesalahan yang tidak terduga. Silakan coba lagi.');
        },

        /**
         * Preview Image (Common utility)
         */
        previewImage: function (input, previewId) {
            const preview = document.getElementById(previewId);
            if (!preview) return;

            const previewImg = preview.querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (previewImg) previewImg.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                if (previewImg) previewImg.src = '';
                preview.classList.add('d-none');
            }
        }
    };

    window.UI = UI;

})(window);
