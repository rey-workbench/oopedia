import { tutorialState } from '@/states/ui/tutorialState.svelte';

export function registerAdminTutorials() {
    tutorialState.registerSteps({
        tourId: 'admin_dashboard',
        steps: [
            {
                element: '#admin-stats-overview',
                popover: {
                    title: 'Ringkasan Sistem',
                    description: 'Pantau total pengguna, materi, dan aktivitas kuis secara cepat.',
                    side: 'bottom',
                    align: 'center'
                }
            },
            {
                element: '#admin-analytics-charts',
                popover: {
                    title: 'Tren Aktivitas',
                    description: 'Visualisasi aktivitas belajar mahasiswa dalam skala waktu tertentu.',
                    side: 'top',
                    align: 'center'
                }
            },
            {
                element: '#admin-popular-materials',
                popover: {
                    title: 'Materi Terpopuler',
                    description: 'Lihat materi mana yang paling sering diakses oleh mahasiswa.',
                    side: 'left',
                    align: 'start'
                }
            }
        ]
    });

    tutorialState.registerSteps({
        tourId: 'admin_materials',
        steps: [
            {
                element: '#material-stats',
                popover: {
                    title: 'Statistik Materi',
                    description: 'Ringkasan distribusi materi dan sub-materi dalam sistem.',
                    side: 'bottom',
                    align: 'center'
                }
            },
            {
                element: '#material-table',
                popover: {
                    title: 'Manajemen Konten',
                    description: 'Edit, hapus, atau kelola sub-materi dan kuis untuk setiap modul di sini.',
                    side: 'top',
                    align: 'center'
                }
            },
            {
                element: '#create-material-btn',
                popover: {
                    title: 'Tambah Materi Baru',
                    description: 'Gunakan tombol ini untuk membuat modul pembelajaran baru.',
                    side: 'bottom',
                    align: 'end'
                }
            }
        ]
    });

    tutorialState.registerSteps({
        tourId: 'admin_students',
        steps: [
            {
                element: '#student-table',
                popover: {
                    title: 'Data Mahasiswa',
                    description: 'Seluruh data mahasiswa yang terdaftar di platform dapat dikelola di sini.',
                    side: 'top',
                    align: 'center'
                }
            },
            {
                element: '#add-student-btn',
                popover: {
                    title: 'Registrasi Manual',
                    description: 'Daftarkan mahasiswa satu per satu secara manual.',
                    side: 'bottom',
                    align: 'end'
                }
            },
            {
                element: '#import-student-btn',
                popover: {
                    title: 'Impor Massal',
                    description: 'Gunakan template Excel untuk mendaftarkan banyak mahasiswa sekaligus.',
                    side: 'bottom',
                    align: 'end'
                }
            }
        ]
    });

    tutorialState.registerSteps({
        tourId: 'admin_users',
        steps: [
            {
                element: '#user-directory-table',
                popover: {
                    title: 'Akses Kontrol',
                    description: 'Kelola akun Administrator dan Dosen pembimbing sistem.',
                    side: 'top',
                    align: 'center'
                }
            },
            {
                element: '#pending-requests-btn',
                popover: {
                    title: 'Permintaan Akses',
                    description: 'Setujui atau tolak permintaan registrasi admin baru di sini.',
                    side: 'bottom',
                    align: 'end'
                }
            },
            {
                element: '#add-user-btn',
                popover: {
                    title: 'Tambah Administrator',
                    description: 'Daftarkan admin baru untuk mengelola platform ini.',
                    side: 'bottom',
                    align: 'end'
                }
            }
        ]
    });

    tutorialState.registerSteps({
        tourId: 'admin_ueq',
        steps: [
            {
                element: '#ueq-stats-grid',
                popover: {
                    title: 'Skor UEQ',
                    description: 'Lihat rata-rata skor pada 6 dimensi User Experience (Daya Tarik, Efisiensi, dll).',
                    side: 'bottom',
                    align: 'center'
                }
            },
            {
                element: '#ueq-log-table',
                popover: {
                    title: 'Log Responden',
                    description: 'Detil jawaban survey dari setiap mahasiswa yang telah berpartisipasi.',
                    side: 'top',
                    align: 'center'
                }
            },
            {
                element: '#ueq-export-btn',
                popover: {
                    title: 'Unduh Hasil',
                    description: 'Ekspor seluruh data survey ke format CSV untuk analisis lebih lanjut.',
                    side: 'bottom',
                    align: 'end'
                }
            }
        ]
    });

    tutorialState.registerSteps({
        tourId: 'admin_questions',
        steps: [
            {
                element: '#question-table',
                popover: {
                    title: 'Bank Soal',
                    description: 'Kumpulan seluruh instrumen evaluasi yang digunakan dalam kuis adaptif.',
                    side: 'top',
                    align: 'center'
                }
            },
            {
                element: '#question-filter-search',
                popover: {
                    title: 'Pencarian Cepat',
                    description: 'Cari soal berdasarkan teks atau kata kunci tertentu.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#add-question-btn',
                popover: {
                    title: 'Tambah Soal',
                    description: 'Buat soal Multiple Choice, Drag & Drop, atau Fill in the Blank baru.',
                    side: 'bottom',
                    align: 'end'
                }
            }
        ]
    });

    tutorialState.registerSteps({
        tourId: 'admin_question_editor',
        steps: [
            {
                element: '#algorithm-type-selector',
                popover: {
                    title: 'Pilih Mekanisme',
                    description: 'Tentukan tipe soal: Pilihan Ganda, Isian, atau Drag & Drop. Setiap pilihan memiliki logika penilaian yang berbeda.',
                    side: 'left',
                    align: 'center'
                }
            },
            {
                element: '#question-editor-container',
                popover: {
                    title: 'Kanvas Pertanyaan',
                    description: 'Tuliskan inti persoalan di sini. Gunakan editor visual untuk penjelasan yang lebih kaya.',
                    side: 'bottom',
                    align: 'center'
                }
            },
            {
                element: '#answer-options-container',
                popover: {
                    title: 'Konfigurasi Jawaban',
                    description: 'Atur jawaban benar dan berikan penjelasan (feedback) untuk membantu mahasiswa belajar dari kesalahan.',
                    side: 'top',
                    align: 'center'
                }
            },
            {
                element: '.drag-handle-item',
                popover: {
                    title: 'Handle Interaktif',
                    description: 'Tarik ikon ini dan masukkan ke dalam area teks soal untuk menandai di mana jawaban tersebut harus diletakkan.',
                    side: 'right',
                    align: 'center'
                }
            },
            {
                element: '#drag-drop-guide',
                popover: {
                    title: 'Modus Drag & Drop',
                    description: 'Khusus untuk tipe Drag & Drop, ikuti panduan ini: Drag handle jawaban ke dalam kotak soal untuk membuat "lubang" jawaban.',
                    side: 'bottom',
                    align: 'center'
                }
            },
            {
                element: '#drag-drop-view',
                popover: {
                    title: 'Editor Spesialis',
                    description: 'Di sini Anda merancang soal interaktif. Area ini mendukung penyematan item yang dapat ditarik.',
                    side: 'top',
                    align: 'center'
                }
            },
            {
                element: '#question-metadata-panel',
                popover: {
                    title: 'Parameter Cerdas',
                    description: 'Hubungkan soal dengan modul yang tepat dan tentukan tingkat kesulitan untuk mesin adaptif.',
                    side: 'left',
                    align: 'start'
                }
            }
        ]
    });
}
