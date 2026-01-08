// Core Utilities - Consolidated Module
// Exports: Http, UI, Tour, Scrollbar

// ============================================
// HTTP UTILITIES
// ============================================
export const Http = {
    async get(url) {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    },

    async post(url, data) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    },

    async postForm(url, formElement) {
        const formData = new FormData(formElement);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    }
};

// ============================================
// UI UTILITIES
// ============================================
export const UI = {
    notify(type, message) {
        console.log(`[${type.toUpperCase()}] ${message}`);

        if (window.Swal) {
            window.Swal.fire({
                icon: type === 'danger' ? 'error' : type,
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    },

    confirm(title, message, confirmText, onConfirm) {
        if (window.Swal) {
            window.Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: confirmText || 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed && onConfirm) {
                    onConfirm();
                }
            });
        } else if (confirm(`${title}\n${message}`)) {
            onConfirm?.();
        }
    },

    showLoading() {
        if (window.Swal) {
            window.Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    window.Swal.showLoading();
                }
            });
        }
    },

    hideLoading() {
        if (window.Swal) {
            window.Swal.close();
        }
    },

    handleError(error) {
        console.error('Error:', error);
        this.notify('danger', error.message || 'Terjadi kesalahan');
    }
};

// ============================================
// TOUR/INTRO UTILITIES
// ============================================
export const Tour = {
    init(steps) {
        if (typeof introJs === 'undefined') {
            console.warn('IntroJS not loaded');
            return null;
        }

        return introJs().setOptions({
            steps: steps,
            showProgress: true,
            showBullets: false,
            exitOnOverlayClick: false,
            doneLabel: 'Selesai',
            nextLabel: 'Lanjut',
            prevLabel: 'Kembali',
            skipLabel: 'Lewati'
        });
    },

    start(steps) {
        const tour = this.init(steps);
        if (tour) tour.start();
    }
};

// ============================================
// SCROLLBAR UTILITIES
// ============================================
export const Scrollbar = {
    init(selector) {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => {
            el.style.scrollbarWidth = 'thin';
            el.style.scrollbarColor = '#cbd5e1 #f1f5f9';
        });
    }
};

// Make globally available for backward compatibility
if (typeof window !== 'undefined') {
    window.Http = Http;
    window.UI = UI;
    window.Tour = Tour;
    window.Scrollbar = Scrollbar;
}
