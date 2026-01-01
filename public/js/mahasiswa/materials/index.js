document.addEventListener('DOMContentLoaded', function () {
    if (!sessionStorage.getItem('material_index_tour_complete')) {
        setTimeout(startMaterialsIndexTour, 800);
    }
});

function startMaterialsIndexTour() {
    // Check if elements exist before starting tour
    if (!document.querySelector('.material-card:first-child')) return;

    const steps = [
        {
            intro: `
                <div class="text-center">
                    <h4 style="margin-bottom: 10px; color: var(--color-1);">Selamat Datang</h4>
                    <p>Di halaman Materi OOPEDIA!</p>
                </div>
            `,
            position: 'center'
        },
        {
            element: document.querySelector('.material-card:first-child'),
            intro: `
                <div>
                    <h5 style="margin-bottom: 8px; color: var(--color-1);">Kartu Materi</h5>
                    <p>Ini adalah kartu materi pembelajaran. Pilih salah satu materi untuk mulai belajar.</p>
                </div>
            `,
            position: 'auto'
        },
        {
            element: document.querySelector('.material-actions .btn-read-material') || document.querySelector('.material-link'),
            intro: `
                <div>
                    <h5 style="margin-bottom: 8px; color: var(--color-1);">Tombol Baca</h5>
                    <p>Klik tombol ini untuk mulai mempelajari materi yang dipilih.</p>
                </div>
            `,
            position: 'auto'
        },
        {
            intro: `
                <div class="text-center">
                    <h4 style="margin-bottom: 10px; color: var(--color-1);">Selamat Belajar!</h4>
                    <p>Mari eksplorasi dunia Pemrograman Berorientasi Objek bersama OOPEDIA.</p>
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
        nextLabel: 'Berikutnya',
        prevLabel: 'Sebelumnya',
        skipLabel: 'Lewati',
        doneLabel: 'Selesai',
        tooltipClass: 'custom-tour',
        highlightClass: 'custom-highlight',
        hidePrev: true,
        exitOnEsc: true
    }).oncomplete(function () {
        sessionStorage.setItem('material_index_tour_complete', 'true');
    }).onexit(function () {
        sessionStorage.setItem('material_index_tour_complete', 'true');
    }).start();

}
