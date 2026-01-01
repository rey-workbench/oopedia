document.addEventListener('DOMContentLoaded', function () {
    // Auto refresh setelah form submit
    const approveForms = document.querySelectorAll('form[action*="approve"]');
    const rejectForms = document.querySelectorAll('form[action*="reject"]');

    const handleFormSubmit = function (e) {
        const form = e.target;
        const originalButton = form.querySelector('button[type="submit"]');

        if (originalButton) {
            originalButton.disabled = true;
            originalButton.innerHTML = 'Memproses...';
        }
    };

    approveForms.forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });

    rejectForms.forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });

    // Jika ada pesan sukses (di-pass via data attribute atau variable global jika perlukan, 
    // tapi disini kita cek elemen yang mungkin di-render blade)
    // Note: Since this is an external JS file, we can't use blade syntax directly.
    // We will rely on a data attribute on the body or a specific element to trigger reload.
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function () {
            window.location.reload();
        }, 2000);
    }
});
