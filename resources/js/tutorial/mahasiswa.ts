import { tutorialState } from '@/states/ui/tutorialState.svelte';

export function registerMahasiswaTutorials() {
    tutorialState.registerSteps({
        tourId: 'mahasiswa_dashboard',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Beranda Belajar',
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
                    title: 'Petualangan Materi',
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
                    title: 'Panggung Juara',
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
                    title: 'Lemari Piagam',
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
                element: '#page-header',
                popover: {
                    title: 'Eksplorasi Sub-Materi',
                    description:
                        'Selamat datang di zona belajar! Di sini kamu akan menemukan detail materi yang mendalam.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#submaterial-content',
                popover: {
                    title: 'Pusat Ilmu',
                    description:
                        'Baca dan pahami materi ini dengan seksama. Di sinilah rahasia penguasaan PBO berada!',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#btn-start-quiz',
                popover: {
                    title: 'Area Tantangan',
                    description:
                        'Sudah merasa cukup paham? Klik tombol ini untuk mulai menguji pemahamanmu di kuis adaptif!',
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
                    title: 'Latihan Soal',
                    description: 'Saatnya mengasah otak! Pilih materi yang ingin kamu kuasai.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#module-list',
                popover: {
                    title: 'Daftar Tantangan',
                    description:
                        'Setiap kartu di sini adalah gerbang menuju penguasaan konsep PBO.',
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
                    description:
                        'Selamat datang di Arena Tantangan! Ini adalah peta perjalananmu menguasai materi ini.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#level-map',
                popover: {
                    title: 'Peta Petualangan',
                    description:
                        'Lihat progres belajarmu di sini. Setiap titik adalah langkah menuju penguasaan materi!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: 'button.border-primary-600',
                popover: {
                    title: 'Level Saat Ini',
                    description:
                        'Klik level yang aktif (berwarna biru) untuk mulai mengerjakan kuis adaptif.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#level-trophy',
                popover: {
                    title: 'Hadiah Utama',
                    description:
                        'Capai akhir peta untuk mendapatkan trofi penguasaan materi. Kamu pasti bisa!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#levels-legend',
                popover: {
                    title: 'Legenda Peta',
                    description:
                        'Gunakan panduan ini untuk memahami arti setiap simbol di peta petualanganmu.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'mahasiswa_quiz_session',
        steps: [
            {
                element: '#quiz-session-header',
                popover: {
                    title: 'Pusat Kendali Latihan',
                    description:
                        'Di sini kamu bisa melihat materi yang sedang dikerjakan dan progresmu secara keseluruhan.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#quiz-progress',
                popover: {
                    title: 'Jalur Keberhasilan',
                    description:
                        'Garis ini menunjukkan seberapa dekat kamu dengan garis finish level ini. Semangat!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#quiz-stats',
                popover: {
                    title: 'Panel Status',
                    description:
                        'Pantau tingkat kesulitan, perolehan XP, dan streak jawaban benarmu di sini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#quiz-hint-btn',
                popover: {
                    title: 'Butuh Bantuan?',
                    description:
                        'Jika merasa kesulitan, jangan ragu menggunakan petunjuk adaptif kami!',
                    side: 'left',
                    align: 'center',
                },
            },
            {
                element: '#quiz-question-area',
                popover: {
                    title: 'Arena Utama',
                    description: 'Fokus pada soal di sini. Baca pertanyaannya dengan cermat, ya!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#drag-drop-options-area',
                popover: {
                    title: 'Pilihan Jawaban',
                    description:
                        'Di tipe soal ini, kamu cukup tarik (drag) jawaban yang benar dari sini.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#drag-drop-view',
                popover: {
                    title: 'Area Penyusunan',
                    description:
                        'Letakkan jawabanmu pada kotak "···" di area soal ini untuk melengkapi kode.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#fill_in_the_blank_answer',
                popover: {
                    title: 'Input Jawaban',
                    description:
                        'Ketik jawabanmu langsung di sini. Perhatikan ejaan dan tanda baca ya!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#quiz-submit-btn',
                popover: {
                    title: 'Kirim Jawaban',
                    description:
                        'Sudah yakin? Klik tombol ini untuk memeriksa apakah jawabanmu tepat.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#feedback-result-container',
                popover: {
                    title: 'Hasil Evaluasi',
                    description: 'Lihat apakah jawabanmu benar dan berapa XP yang kamu peroleh.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#adaptive-feedback-header',
                popover: {
                    title: 'Bantuan Khusus',
                    description:
                        'Jika kamu kesulitan, sistem adaptif kami akan memberikan pesan penyemangat dan bantuan!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#adaptive-recommendation-card',
                popover: {
                    title: 'Rekomendasi Pintar',
                    description:
                        'Ini adalah materi yang disarankan sistem untuk membantu kamu memahami bagian yang sulit.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#acceleration-feedback-modal',
                popover: {
                    title: 'Status: Akselerasi',
                    description:
                        'Kamu sedang berada dalam mode percepatan karena performamu yang luar biasa!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#intervention-feedback-modal',
                popover: {
                    title: 'Bantuan Adaptif',
                    description:
                        'Sistem memberikan bantuan khusus untuk membantumu memahami bagian yang sulit.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#backtrack-feedback-modal',
                popover: {
                    title: 'Mode Penyesuaian',
                    description:
                        'Kami menyesuaikan alur belajar agar kamu bisa menguasai konsep dasar terlebih dahulu.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#acceleration-feedback-header',
                popover: {
                    title: 'Lompatan Prestasi!',
                    description:
                        'Wah, kamu hebat! Sistem mendeteksi kamu sudah master di level ini, jadi kamu langsung naik ke tingkat yang lebih menantang.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#backtrack-feedback-header',
                popover: {
                    title: 'Waktunya Review',
                    description:
                        'Tidak apa-apa, belajar butuh proses. Kami akan membantumu mengulang fondasi agar pemahamanmu lebih kuat.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#backtrack-recommendation-card',
                popover: {
                    title: 'Rekomendasi Khusus',
                    description:
                        'Sistem menyarankan kamu membaca materi ini kembali sebelum lanjut ke soal berikutnya.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#active-quiz-badge',
                popover: {
                    title: 'Status Belajar',
                    description: 'Di sini kamu bisa melihat mode belajarmu saat ini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#certificate-feedback-header',
                popover: {
                    title: 'Pencapaian Baru!',
                    description:
                        'Selamat! Kamu telah menyelesaikan tantangan dan berhak mendapatkan sertifikat ini.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#adaptive-continue-btn',
                popover: {
                    title: 'Lanjutkan Petualangan',
                    description:
                        'Klik di sini untuk mengikuti rekomendasi atau lanjut ke tantangan berikutnya.',
                    side: 'top',
                    align: 'center',
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

    tutorialState.registerSteps({
        tourId: 'mahasiswa_mslq',
        steps: [
            {
                element: '#mslq-instructions',
                popover: {
                    title: 'MSLQ Survey',
                    description:
                        'Bantu kami memahami motivasi dan strategi belajarmu dengan mengisi kuesioner ini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#mslq-identitas',
                popover: {
                    title: 'Identitas Responden',
                    description: 'Masukkan NIM dan kelas kamu untuk validasi data kuesioner.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: 'h4:contains("Bagian A")',
                popover: {
                    title: 'Motivasi Belajar',
                    description:
                        'Bagian pertama mengukur orientasi tujuan, nilai tugas, dan efikasi diri kamu.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: 'h4:contains("Bagian B")',
                popover: {
                    title: 'Strategi Belajar',
                    description:
                        'Bagian kedua mengukur bagaimana kamu mengelola waktu, upaya, dan cara berpikirmu.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: 'button[type="submit"]',
                popover: {
                    title: 'Simpan Hasil',
                    description: 'Klik tombol ini jika kamu sudah yakin telah mengisi seluruh 81 butir pertanyaan.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });
}
