import { tutorialState } from '@/states/ui/tutorialState.svelte';

export function registerMahasiswaTutorials() {
    tutorialState.registerSteps({
        tourId: 'mahasiswa_dashboard',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Beranda Belajar 🏠',
                    description:
                        'Waktunya naik pangkat! Di sini kamu bisa memantau semua progres dan pencapaian hebatmu.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#dashboard-hero',
                popover: {
                    title: 'Area Sambutan',
                    description:
                        'Sapaan personal yang menyemangimu untuk terus belajar setiap hari.',
                    side: 'right',
                    align: 'start',
                },
            },
            {
                element: '#dashboard-certificates',
                popover: {
                    title: 'Sertifikat',
                    description:
                        'Koleksi sertifikat yang telah kamu raih. Klik untuk melihat detail dan unduh.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#student-progress-overview',
                popover: {
                    title: 'Statistik Pembelajaran',
                    description:
                        'Pantau jumlah materi, total soal, level hardest, dan rankingmu di sini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#activity-feed-header',
                popover: {
                    title: 'Riwayat Aktivitas',
                    description:
                        'Lihat jejak pembelajaran terakhirmu, termasuk pencapaian dan milestone.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#active-materials-list',
                popover: {
                    title: 'Materi Rekomendasi',
                    description:
                        'Materi Unggulan yang direkomendasikan untukmu. Klik untuk langsung belajar!',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#btn-learn-now',
                popover: {
                    title: 'Mulai Belajar',
                    description: 'Klik tombol ini untuk langsung menuju halaman materi.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#btn-view-all-activities',
                popover: {
                    title: 'Lihat Semua Aktivitas',
                    description: 'Melihat riwayat lengkap aktivitas pembelajaranmu.',
                    side: 'left',
                    align: 'start',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_materials',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Kurikulum PBO',
                    description:
                        'Daftar modul pembelajaran Pemrograman Berorientasi Objek dari dasar hingga lanjut.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#material-exploration-grid',
                popover: {
                    title: 'Petualangan Materi 🗺️',
                    description:
                        'Pilih modul yang menantang bagimu. Setiap modul menyimpan ilmu PBO yang berharga!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#material-item-0',
                popover: {
                    title: 'Mulai Belajar',
                    description:
                        'Klik tombol "MULAI BELAJAR" untuk masuk ke halaman detail materi dan sub-topik.',
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
                element: '#page-header',
                popover: {
                    title: 'Leaderboard',
                    description: 'Peringkat terbaik mahasiswa berdasarkan progres pembelajaran.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#leaderboard-podium',
                popover: {
                    title: 'Panggung Juara 🏆',
                    description:
                        'Lihat siapa yang sedang memimpin! Apakah namamu akan ada di sana selanjutnya?',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#leaderboard-top-three',
                popover: {
                    title: '3 Besar Prestasi',
                    description:
                        'Juara 1, 2, dan 3 dengan skor tertinggi. Siapa yang akan jadi yang terbaik?',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#leaderboard-full-list',
                popover: {
                    title: 'Tabel Peringkat',
                    description:
                        'Lihat posisi rankingmu dibandingkan mahasiswa lain berdasarkan XP dan progress.',
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
                element: '#page-header',
                popover: {
                    title: 'Sertifikat Saya',
                    description:
                        'Koleksi sertifikat yang telah kamu raih dari menyelesaikan Final Project.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#certificate-inventory',
                popover: {
                    title: 'Lemari Piagam 🎖️',
                    description:
                        'Koleksi medals dan sertifikatmu! Bukti nyata bahwa kamu telah menguasai PBO.',
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
                    title: 'UEQ Survey',
                    description:
                        'Bantu kami improve platform dengan mengisi kuesioner pengalaman pengguna ini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#ueq-identitas',
                popover: {
                    title: 'Identitas Responden',
                    description: 'Masukkan NIM dan kelas kamu untuk validasi data survey.',
                    side: 'right',
                    align: 'start',
                },
            },
            {
                element: '#ueq-matriks',
                popover: {
                    title: 'Skala Penilaian',
                    description:
                        'Pilih angka 1-5 yang paling sesuai dengan pengalamanmu (1=sangat negatif, 5=sangat positif).',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#ueq-survey-form',
                popover: {
                    title: 'Form UEQ',
                    description:
                        'Isi semua pertanyaan dengan jujur untuk membantu kami meningkatkan kualitas platform.',
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
                element: '#page-header',
                popover: {
                    title: 'Profil Saya',
                    description: 'Kelola informasi akun dan keamananmu di sini.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#profile-hero',
                popover: {
                    title: 'Profil Saya',
                    description: 'Ini halaman profil akun. Semua info penting ada di sini.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#profile-stats',
                popover: {
                    title: 'Data Personalisasi',
                    description: 'Lihat gaya belajar, level, akurasi, dan streak belajarmu.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#learning-profile-analysis',
                popover: {
                    title: 'Analisis Pembelajaran',
                    description: 'Statistik lengkap: gaya belajar, level, akurasi, dan streak.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#profile-certificates',
                popover: {
                    title: 'Sertifikat Profil',
                    description: 'Sertifikat yang sudah kamu raih dari setiap materi.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#profile-settings',
                popover: {
                    title: 'Pengaturan Akun',
                    description: 'Ubah nama, email, atau password akunmu di sini.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#profile-personal-info',
                popover: {
                    title: 'Form Biodata',
                    description: 'Pastikan nama dan email benar untuk pencetakan sertifikat.',
                    side: 'bottom',
                    align: 'start',
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
                    description: 'Judul dan info materi yang akan kamu pelajari ada di sini.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#adaptive-recommendation',
                popover: {
                    title: 'Rekomendasi Sistem',
                    description: 'Sistem adaptif mungkin menyarankan materi untuk diulas ulang.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#sub-material-section',
                popover: {
                    title: 'Daftar Sub-Materi',
                    description: 'Pilih sub-materi untuk mulai belajar teori dan mengerjakan kuis.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#sub-material-grid',
                popover: {
                    title: 'Kartu Sub-Materi',
                    description:
                        'Setiap kartu mewakili satu sub-topik dengan jumlah soal tertentu.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#material-content',
                popover: {
                    title: 'Konten Tambahan',
                    description: 'Baca materi tambahan untuk memperkuat pemahaman konsep PBO.',
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
                    title: 'Konten Teori',
                    description: 'Baca materi teori dengan seksama sebelum mengerjakan kuis.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#submaterial-quiz-section',
                popover: {
                    title: 'Mulai Kuis',
                    description:
                        'Setelah paham materi, klik "Mulai Latihan Soal" untuk menguji pemahaman.',
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
                element: '#page-header',
                popover: {
                    title: 'Latihan soal PBO',
                    description: 'Pilih materi yang ingin kamu kerjakan untuk menguji pemahaman.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#module-list',
                popover: {
                    title: 'Daftar Modul',
                    description: 'Pilih materi dan klik "Mulai" untuk mengerjakan soal.',
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
                element: '#page-header',
                popover: {
                    title: 'Peta Tantangan',
                    description: 'Pilih level kesulitan yang sesuai dengan kemampuanmu.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#levels-legend',
                popover: {
                    title: 'Panduan Level',
                    description:
                        'Easy: dasar, Medium: menengah, Hard: tantangan. Sistem adaptif akan menyesuaikan.',
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
                        'Track progres menjawab soal. Sistem adaptif menyesuaikan kesulitan berdasarkan performamu.',
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
                        'Lihat hasil: jawaban benar/salah, skor, dan penjelasan setiap soal.',
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
                element: '#page-header',
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
                element: '#page-header',
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
