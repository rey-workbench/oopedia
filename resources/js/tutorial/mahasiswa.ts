import { tutorialState } from '@/states/ui/tutorialState.svelte';

export function registerMahasiswaTutorials() {
    tutorialState.registerSteps({
        tourId: 'mahasiswa_dashboard',
        steps: [
            {
                element: '#student-progress-overview',
                popover: {
                    title: 'Status Belajar',
                    description:
                        'Pantau kemajuan level, XP, dan penguasaan sub-materi kamu secara real-time.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#active-materials-list',
                popover: {
                    title: 'Lanjutkan Belajar',
                    description:
                        'Di sini kamu bisa melihat materi yang sudah terbuka dan mulai mempelajarinya.',
                    side: 'top',
                    align: 'start',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_materials',
        steps: [
            {
                element: '#material-exploration-grid',
                popover: {
                    title: 'Eksplorasi Modul',
                    description: 'Pilih modul yang tersedia sesuai dengan urutan level kamu.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#material-item-0',
                popover: {
                    title: 'Materi Pertama',
                    description:
                        'Klik tombol Pelajari untuk masuk ke dalam konten pembelajaran dan kuis adaptif.',
                    side: 'bottom',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_leaderboard',
        steps: [
            {
                element: '#leaderboard-top-three',
                popover: {
                    title: 'Sang Juara',
                    description: 'Lihat 3 mahasiswa dengan skor tertinggi dalam platform!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#leaderboard-full-list',
                popover: {
                    title: 'Peringkat Global',
                    description:
                        'Lihat posisi kamu dibandingkan mahasiswa lainnya berdasarkan XP yang dikumpulkan.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_ueq',
        steps: [
            {
                element: '#ueq-instructions',
                popover: {
                    title: 'Panduan Survey',
                    description: 'Bacalah petunjuk pengisian UEQ agar evaluasi kamu akurat.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#ueq-survey-form',
                popover: {
                    title: 'Kuesioner UEQ',
                    description:
                        'Pilih skala yang paling mewakili pengalaman kamu menggunakan platform Oopedia.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_certificates',
        steps: [
            {
                element: '#certificate-inventory',
                popover: {
                    title: 'Koleksi Sertifikat',
                    description:
                        'Unduh sertifikat Bronze, Silver, atau Gold yang telah kamu raih di sini.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_profile',
        steps: [
            {
                element: '#profile-personal-info',
                popover: {
                    title: 'Biodata Diri',
                    description:
                        'Pastikan nama dan email kamu sudah sesuai untuk pencetakan sertifikat.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#learning-profile-analysis',
                popover: {
                    title: 'Analisis Belajar',
                    description:
                        'Lihat gaya belajar dominan kamu dan performa rata-rata dalam kuis.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_materials_show',
        steps: [
            {
                element: '#material-header',
                popover: {
                    title: 'Detail Materi',
                    description:
                        'Ini adalah halaman detail materi yang berisi sub-topik yang dapat kamu pelajari.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#sub-material-section',
                popover: {
                    title: 'Sub-Materi',
                    description:
                        'Pilih salah satu sub-materi untuk memulai pembelajaran dan latihan soal.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#material-content',
                popover: {
                    title: 'Konten Tambahan',
                    description: 'Baca materi tambahan untuk memperkuat pemahaman konsep.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_submaterials_show',
        steps: [
            {
                element: '#submaterial-content',
                popover: {
                    title: 'Konten Pembelajaran',
                    description:
                        'Baca materi teori di section ini dengan seksama sebelum mengerjakan soal.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#submaterial-quiz-section',
                popover: {
                    title: 'Latihan Kuis',
                    description:
                        'Setelah memahami materi, kerjakan kuis adaptif untuk menguji pemahamanmu.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_quiz_index',
        steps: [
            {
                element: '#quiz-header',
                popover: {
                    title: 'Pilih Materi',
                    description: 'Pilih materi yang ingin kamu kerjakan untuk menguji pemahamanmu.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#module-list',
                popover: {
                    title: 'Daftar Modul',
                    description: 'Klik tombol mulai untuk masuk ke level soal yang tersedia.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_quiz_levels',
        steps: [
            {
                element: '#levels-header',
                popover: {
                    title: 'Pilih Level',
                    description: 'Pilih level kesulitan soal yang ingin kamu kerjakan.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#levels-legend',
                popover: {
                    title: 'Panduan Level',
                    description:
                        'Easy (Mudah), Medium (Sedang), Hard (Sulit). Sistem akan menyesuaikan tingkat kesulitan secara adaptif.',
                    side: 'top',
                    align: 'start',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_quiz_session',
        steps: [
            {
                element: '#quiz-progress',
                popover: {
                    title: 'Progress Kuis',
                    description:
                        'Track progres menjawab soal. Sistem adaptif akan menyesuaikan tingkat kesulitan berdasarkan performamu.',
                    side: 'bottom',
                    align: 'start',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_quiz_review',
        steps: [
            {
                element: '#review-results',
                popover: {
                    title: 'Hasil Kuis',
                    description:
                        'Lihat hasil jawabanmu, benar atau salah, beserta penjelasan setiap soal.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_dashboard_inprogress',
        steps: [
            {
                element: '#inprogress-header',
                popover: {
                    title: 'Materi Aktif',
                    description: 'Materi yang sedang kamu pelajari dan belum selesai.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#inprogress-materials-grid',
                popover: {
                    title: 'Kartu Progres',
                    description: 'Lihat progres belajar kamu di setiap materi. Tingkatkan terus!',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_dashboard_completed',
        steps: [
            {
                element: '#completed-header',
                popover: {
                    title: 'Hall of Fame',
                    description: 'Selamat! Kamu telah menyelesaikan modul-modul ini.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#completed-materials-grid',
                popover: {
                    title: 'Materi Selesai',
                    description:
                        'Kartu-kartu ini menunjukkan materi yang sudah kamu kuasai sepenuhnya.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_ueq_thankyou',
        steps: [
            {
                element: '#thankyou-container',
                popover: {
                    title: 'Terima Kasih!',
                    description: 'Evaluasi kamu sangat berharga untuk peningkatan platform.',
                    side: 'bottom',
                    align: 'center',
                },
            },
        ],
    });
}
