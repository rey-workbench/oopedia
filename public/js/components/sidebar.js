document.addEventListener('DOMContentLoaded', function () {
    // Handle all dropdown toggles within sidebar
    const dropdownToggles = document.querySelectorAll('#sidenav-main [data-bs-toggle="collapse"]');

    dropdownToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetMenu = document.querySelector(targetId);

            // Tutup semua dropdown yang terbuka kecuali yang sedang di-klik
            dropdownToggles.forEach(function (otherToggle) {
                if (otherToggle !== toggle) {
                    const otherId = otherToggle.getAttribute('href');
                    const otherMenu = document.querySelector(otherId);
                    otherToggle.setAttribute('aria-expanded', 'false');
                    if (otherMenu) otherMenu.classList.remove('show');
                }
            });

            // Toggle dropdown yang di-klik
            if (targetMenu) {
                const willExpand = !targetMenu.classList.contains('show');
                this.setAttribute('aria-expanded', willExpand);
                targetMenu.classList.toggle('show');
            }
        });
    });

    // Toggle sidebar button functionality for student sidebar
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const sidebar = document.querySelector('.sidebar');
    const sidebarBackdrop = document.querySelector('.sidebar-backdrop');

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', function () {
            sidebar.classList.add('show');
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.add('show');
            }
        });
    }

    if (sidebarCloseBtn && sidebar) {
        sidebarCloseBtn.addEventListener('click', function () {
            sidebar.classList.remove('show');
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.remove('show');
            }
        });
    }

    // Close on backdrop click
    if (sidebarBackdrop && sidebar) {
        sidebarBackdrop.addEventListener('click', function () {
            sidebar.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
        });
    }
});
x