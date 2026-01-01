document.addEventListener('DOMContentLoaded', function () {
    if (!sessionStorage.getItem('question_index_tour_complete')) {
        setTimeout(startQuestionsIndexTour, 500);
    }
});

function startQuestionsIndexTour() {
    const steps = [
        {
            intro: `
                <div class="text-center">
                    <h4 style="margin-bottom: 10px; color: var(--color-1);">Selamat datang di Latihan Soal OOPEDIA!</h4>
                    <p>Temukan berbagai latihan soal untuk menguji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi</p>
                </div>
            `
        },
        {
            element: document.querySelector('.material-question-card'),
            intro: `
                <div>
                    <h5 style="margin-bottom: 8px; color: var(--color-1);">Kartu Materi</h5>
                    <p>Setiap kartu mewakili satu materi yang bisa Anda pelajari. Pilih materi untuk mulai berlatih.</p>
                </div>
            `,
            position: 'bottom'
        },
        {
            element: document.querySelector('.progress-section'),
            intro: `
                <div>
                    <h5 style="margin-bottom: 8px; color: var(--color-1);">Progress Belajar</h5>
                    <p>Pantau perkembangan Anda melalui indikator progress ini.</p>
                </div>
            `,
            position: 'bottom'
        },
        {
            element: document.querySelector('.btn-start-exercise'),
            intro: `
                <div>
                    <h5 style="margin-bottom: 8px; color: var(--color-1);">Mulai Berlatih</h5>
                    <p>Klik tombol ini untuk mengakses soal-soal latihan dari materi yang dipilih.</p>
                </div>
            `,
            position: 'top'
        },
        {
            intro: `
                <div class="text-center">
                    <h4 style="margin-bottom: 10px; color: var(--color-1);">Siap Berlatih!</h4>
                    <p>Selamat mengasah kemampuan Pemrograman Berorientasi Objek Anda!</p>
                </div>
            `
        }
    ];

    const intro = introJs();

    intro.setOptions({
        steps: steps,
        showProgress: true,
        exitOnOverlayClick: true,
        scrollToElement: true,
        nextLabel: 'Berikutnya',
        prevLabel: 'Sebelumnya',
        doneLabel: 'Mulai Berlatih',
        skipLabel: 'X',
        showSkipButton: true,
        tooltipClass: 'custom-introjs-tooltip',
        hidePrev: true
    })
        .oncomplete(function () {
            sessionStorage.setItem('question_index_tour_complete', 'true');
        })
        .onexit(function () {
            sessionStorage.setItem('question_index_tour_complete', 'true');
        })
        .start();
}
