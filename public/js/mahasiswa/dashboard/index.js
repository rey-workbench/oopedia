document.addEventListener('DOMContentLoaded', function () {
    if (!sessionStorage.getItem('dashboard_tour_complete')) {
        setTimeout(startDashboardTour, 800);
    }
});

function startDashboardTour() {
    const steps = [
        {
            intro: `
                <div class="text-center">
                    <h4 class="tour-step-title">Selamat Datang di Dashboard</h4>
                    <p class="tour-step-content">Temukan semua fitur pembelajaran OOP di satu tempat!</p>
                </div>
            `,
            position: 'center'
        },
        {
            element: document.querySelector('.col-md-6:first-child .card'),
            intro: `
                <div>
                    <h5 class="tour-step-title">Materi Pembelajaran</h5>
                    <p class="tour-step-content">Lihat jumlah materi yang tersedia dan akses konten pembelajaran.</p>
                </div>
            `,
            position: 'auto'
        },
        {
            element: document.querySelector('.col-md-6:nth-child(2) .card'),
            intro: `
                <div>
                    <h5 class="tour-step-title">Latihan Soal</h5>
                    <p class="tour-step-content">Temukan berbagai level soal untuk menguji pemahaman Anda.</p>
                </div>
            `,
            position: 'auto'
        },
        {
            element: document.querySelector('.activity-timeline'),
            intro: `
                <div>
                    <h5 class="tour-step-title">Aktivitas Terbaru</h5>
                    <p class="tour-step-content">Pantau perkembangan belajar Anda melalui aktivitas terkini.</p>
                </div>
            `,
            position: 'auto'
        },
        {
            intro: `
                <div class="text-center">
                    <h4 class="tour-step-title">Mulai Petualangan Belajar!</h4>
                    <p class="tour-step-content">Anda siap menjelajahi dunia OOP. Selamat belajar!</p>
                </div>
            `,
            position: 'center'
        }
    ];

    introJs().setOptions({
        steps: steps,
        showProgress: true,
        exitOnOverlayClick: true,
        showBullets: true,
        scrollToElement: true,
        nextLabel: 'Berikutnya →',
        prevLabel: '← Sebelumnya',
        skipLabel: 'X',
        doneLabel: 'Mulai Belajar',
        tooltipClass: 'custom-tour',
        highlightClass: 'custom-highlight',
        hidePrev: true,
        exitOnEsc: true
    }).oncomplete(function () {
        sessionStorage.setItem('dashboard_tour_complete', 'true');
    }).onexit(function () {
        sessionStorage.setItem('dashboard_tour_complete', 'true');
    }).start();
}
