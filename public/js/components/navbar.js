function resetAllTutorials() {
    // Hapus semua tutorial keys dari localStorage
    for (let key in localStorage) {
        if (key.includes('tutorial_complete') || key === 'skip_admin_tour') {
            localStorage.removeItem(key);
        }
    }

    Swal.fire({
        title: 'Tutorial Direset',
        text: 'Tutorial akan dimulai ulang',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        // Langsung jalankan tutorial setelah reset
        // Note: we can't easily get route name here without passing it via data attr or similar variables
        // But the original code relied on Blade injection. We should make this generic or rely on global func.

        if (typeof startAdminTutorial === 'function') {
            startAdminTutorial();
        } else {
            window.location.reload();
        }
    });
}
