import { tutorialState } from '@/states/ui/tutorialState.svelte';

export function registerAdminTutorials() {
    // 1. Admin Dashboard Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_dashboard',
        steps: [
            {
                element: '#sidebar-nav',
                popover: {
                    title: 'Pusat Kontrol Utama 🧭',
                    description:
                        'Halo Admin! Ini adalah Sidebar Navigasi Anda. Dari sini, Anda bisa berpindah antar departemen manajemen: Materi, Mahasiswa, hingga Analitika UEQ. Navigasi jadi super mudah!',
                    side: 'right',
                    align: 'start',
                },
            },
            {
                element: '#page-header',
                popover: {
                    title: 'Kokpit Administrasi 📂',
                    description:
                        'Setiap halaman memiliki identitas di sini. Membantu Anda tetap fokus pada apa yang sedang dikelola saat ini.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#admin-stats-overview',
                popover: {
                    title: 'Quick Stats Snapshot ⚡',
                    description:
                        'Lihat data vital sistem secara real-time! Berapa banyak mahasiswa aktif, jumlah modul, dan soal yang tersedia. Semua dalam satu pandangan!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#admin-analytics-charts',
                popover: {
                    title: 'Visualisasi Performa 📊',
                    description:
                        'Data tidak harus membosankan! Grafik ini memberikan gambaran visual tentang distribusi kemampuan mahasiswa. Hijau berarti aman, merah berarti butuh perhatian!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#admin-top-students',
                popover: {
                    title: 'Panggung Bintang Mahasiswa 🏆',
                    description:
                        'Daftar mahasiswa paling rajin dan berprestasi. Mereka adalah para pemimpin di papan skor. Berikan semangat untuk mereka!',
                    side: 'left',
                    align: 'start',
                },
            },
            {
                element: '#admin-material-stats',
                popover: {
                    title: 'Status Distribusi Materi 📖',
                    description:
                        'Pantau seberapa banyak materi yang sudah dibuat dan bagaimana status penyelesaiannya oleh mahasiswa.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#admin-recent-activity',
                popover: {
                    title: 'Jurnal Aktivitas Terbaru ⏰',
                    description:
                        'Siapa yang baru saja mengerjakan kuis? Pantau jejak aktivitas terbaru untuk memastikan ekosistem belajar tetap hidup!',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    // 2. Admin Materials Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_materials',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Manajer Kurikulum 📖',
                    description:
                        'Selamat datang di perpustakaan digital! Di sinilah Anda menyusun "menu utama" ilmu pengetahuan untuk mahasiswa.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#create-material-btn',
                popover: {
                    title: 'Inisiasi Modul Baru ➕',
                    description:
                        'Punya ide topik baru? Klik tombol ini untuk mulai meramu materi pembelajaran yang luar biasa!',
                    side: 'left',
                    align: 'center',
                },
            },
            {
                element: '#material-stats',
                popover: {
                    title: 'Ringkasan Infrastruktur Konten 🧮',
                    description:
                        'Melihat total modul dan aset media yang tersedia. Pastikan stok ilmu kita selalu terpenuhi!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#material-table',
                popover: {
                    title: 'Daftar Inventaris Materi 📋',
                    description:
                        'Semua materi Anda terdaftar rapi di sini. Gunakan ikon di kolom aksi untuk mengelola soal latihan atau mengedit detail materi.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#btn-edit-material',
                popover: {
                    title: 'Edit Materi ✏️',
                    description:
                        'Ubah konten, deskripsi, atau penanda tugas akhir materi kapan saja melalui tombol modifikasi ini.',
                    side: 'left',
                    align: 'center',
                },
            },
            {
                element: '#btn-delete-material',
                popover: {
                    title: 'Hapus Materi 🗑️',
                    description:
                        'Hati-hati! Tombol ini akan menghapus materi secara permanen beserta semua soal di dalamnya.',
                    side: 'left',
                    align: 'center',
                },
            },
        ],
    });

    // 2.1 Admin Material Create/Edit Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_material_editor',
        steps: [
            {
                element: '#material-editor-form',
                popover: {
                    title: 'Studio Kreatif Materi 🎨',
                    description:
                        'Tempat meramu konten! Berikan judul yang menarik dan deskripsi yang jelas agar mahasiswa semangat belajar.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#material-final-project-toggle',
                popover: {
                    title: 'Penanda Tugas Akhir 🏁',
                    description:
                        'Aktifkan ini jika modul ini merupakan proyek final. Mahasiswa harus menyelesaikan semua tantangan sebelumnya untuk mencapai titik ini!',
                    side: 'right',
                    align: 'center',
                },
            },
            {
                element: '#material-save-btn',
                popover: {
                    title: 'Arsip & Publikasi 💾',
                    description:
                        'Simpan perubahan Anda! Pastikan semua data sudah benar sebelum merilisnya ke publik.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });


    // 3. Admin Questions Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_questions',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Laboratorium Evaluasi 🧪',
                    description:
                        'Tempat meracik tantangan! Di sini Anda mengelola bank soal untuk mengukur kedalaman pemahaman mahasiswa.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#add-question-btn',
                popover: {
                    title: 'Racik Soal Baru 🧪',
                    description:
                        'Tambah amunisi kuis baru. Buat soal yang menantang namun tetap menyenangkan untuk dikerjakan!',
                    side: 'left',
                    align: 'center',
                },
            },
            {
                element: '#question-filter-search',
                popover: {
                    title: 'Radar Pencarian Soal 🔍',
                    description:
                        'Mencari soal tertentu? Ketikkan sepenggal teks di sini, radar akan menemukannya secepat kilat!',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#question-filter-difficulty',
                popover: {
                    title: 'Saringan Level Kesulitan 🌡️',
                    description:
                        'Saring soal berdasarkan levelnya: Beginner, Medium, atau Hard. Sangat membantu dalam menyusun kuis yang adaptif!',
                    side: 'bottom',
                    align: 'end',
                },
            },
            {
                element: '#question-table',
                popover: {
                    title: 'Gudang Instrumen Penilaian 📑',
                    description:
                        'Semua soal evaluasi dikumpulkan di sini. Anda bisa memantau tipe soal, difficulty, dan jumlah opsi jawaban yang tersedia.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#btn-edit-question',
                popover: {
                    title: 'Kalibrasi Soal ✏️',
                    description:
                        'Perbarui tingkat kesulitan atau perbaiki redaksi soal untuk meningkatkan kualitas evaluasi.',
                    side: 'left',
                    align: 'center',
                },
            },
            {
                element: '#btn-delete-question',
                popover: {
                    title: 'Hapus Soal 🗑️',
                    description:
                        'Keluarkan soal dari peredaran jika sudah tidak relevan dengan kurikulum saat ini.',
                    side: 'left',
                    align: 'center',
                },
            },
        ],
    });

    // 3.1 Admin Question Editor Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_question_editor',
        steps: [
            {
                element: '#question-editor-container',
                popover: {
                    title: 'Kanvas Pertanyaan 📝',
                    description:
                        'Tuliskan deskripsi soal Anda. Anda bisa menggunakan editor visual untuk memformat teks dengan cantik!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#algorithm-type-selector',
                popover: {
                    title: 'Mesin Interaksi ⚙️',
                    description:
                        'Pilih bagaimana mahasiswa menjawab: Pilihan Ganda, Isian Singkat, atau Seret-dan-Lepas (Drag & Drop). Setiap tipe memberikan pengalaman berbeda!',
                    side: 'right',
                    align: 'center',
                },
            },
            {
                element: '#answer-options-container',
                popover: {
                    title: 'Konfigurasi Jawaban ✅',
                    description:
                        'Tentukan kunci jawaban dan berikan feedback penjelasan. Mahasiswa akan belajar dari penjelasan yang Anda berikan jika mereka salah!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#question-metadata-panel',
                popover: {
                    title: 'Parameter Adaptif 🏷️',
                    description:
                        'Hubungkan soal dengan modul tertentu dan tentukan tingkat kesulitannya. Data ini penting untuk algoritma Forward Chaining sistem!',
                    side: 'left',
                    align: 'start',
                },
            },
        ],
    });

    // 4. Admin Students Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_students',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Direktori Mahasiswa 🎓',
                    description:
                        'Pusat pemantauan pasukan pembelajar! Anda bisa melihat siapa saja yang terdaftar dan sejauh mana mereka melangkah.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#add-student-btn',
                popover: {
                    title: 'Rekrut Mahasiswa 👤',
                    description:
                        'Daftarkan mahasiswa baru secara instan satu per satu lewat formulir pendaftaran manual ini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#import-student-btn',
                popover: {
                    title: 'Mobilisasi Massa via Excel 📂',
                    description:
                        'Punya banyak mahasiswa? Jangan input satu-satu! Gunakan fitur impor Excel untuk mendaftarkan mereka secara massal. Super efisien!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#student-table',
                popover: {
                    title: 'Monitor Dashboard Mahasiswa 📈',
                    description:
                        'Tabel ini menampilkan status real-time progres mereka. Gunakan aksi "Progress" untuk melihat detail perjalanan belajar setiap individu.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#btn-progress-student',
                popover: {
                    title: 'Detail Progres 📊',
                    description:
                        'Intip seberapa jauh mahasiswa ini melangkah. Anda dapat melihat penguasaan tiap materi secara spesifik.',
                    side: 'left',
                    align: 'center',
                },
            },
            {
                element: '#btn-delete-student',
                popover: {
                    title: 'Hapus Mahasiswa 🗑️',
                    description:
                        'Keluarkan mahasiswa ini dari sistem beserta rekam jejak progres belajarnya.',
                    side: 'left',
                    align: 'center',
                },
            },
        ],
    });

    // 4.1 Admin Student Progress Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_student_progress',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Laporan Intelijen Belajar 📂',
                    description:
                        'Di sini Anda membedah progress belajar mahasiswa secara mendalam. Mari kita lihat apa yang telah mereka capai!',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#student-progress-stats',
                popover: {
                    title: 'Indikator Vital ⚡',
                    description:
                        'Tiga angka kunci: Rata-rata progress, jumlah modul tuntas, dan tantangan yang tersisa. Jika sisa tantangan tinggi, mahasiswa mungkin butuh intervensi!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#student-certifications',
                popover: {
                    title: 'Galeri Lencana 🏅',
                    description:
                        'Jika mahasiswa sudah meraih sertifikat (Bronze/Silver/Gold), lencana kebanggaan mereka akan muncul di sini sebagai bukti penguasaan materi.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#mastery-matrix-table',
                popover: {
                    title: 'Peta Penguasaan 🗺️',
                    description:
                        'Menganalisis performa di tiap modul. Status "STABIL" menandakan penguasaan sempurna melalui algoritma adaptif.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#missing-questions-table',
                popover: {
                    title: 'Anomali Pembelajaran ⚠️',
                    description:
                        'Daftar unit yang belum berhasil ditaklukkan. Informasi ini sangat berharga untuk mengetahui di mana letak kesulitan mahasiswa tersebut.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    // 4.2 Admin Student Import Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_student_import',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Gerbang Impor Massal 📂',
                    description:
                        'Ingin memasukkan ratusan mahasiswa dalam sekejap? Anda berada di tempat yang tepat!',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#import-instructions',
                popover: {
                    title: 'Aturan Main 📋',
                    description:
                        'Baca instruksi ini dengan teliti. Pastikan format file Anda sesuai agar sistem bisa memprosesnya tanpa kendala.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#upload-zone',
                popover: {
                    title: 'Zona Drop Berkas 🏗️',
                    description:
                        'Seret file Excel Anda ke sini atau klik untuk memilih. Jangan lupa unduh "TEMPLATE FORMAL" jika Anda belum punya!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#execute-import-btn',
                popover: {
                    title: 'Luncurkan Impor 🚀',
                    description:
                        'Setelah file terunggah, klik tombol ini untuk memulai proses sinkronisasi database. Tunggu sebentar, dan voilá!',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    // 5. Admin Users Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_users',
        steps: [
            {
                element: '#page-header',
                popover: {
                    title: 'Otoritas Akses Sistem 🔑',
                    description:
                        'Halaman VIP! Di sini Anda mengelola hak akses untuk tim admin dan menjaga keamanan gerbang sistem.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#pending-requests-btn',
                popover: {
                    title: 'Antrean Rekan Baru ⏳',
                    description:
                        'Ada kolega yang mendaftar sebagai admin? Periksa dan berikan persetujuan di sini sebelum mereka bisa mengakses sistem.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#add-user-btn',
                popover: {
                    title: 'Tambah Rekan Kerja 🛡️',
                    description:
                        'Butuh bantuan mengelola sistem? Tambahkan rekan administrator baru secara langsung lewat tombol sakti ini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#user-directory-table',
                popover: {
                    title: 'Daftar Penjaga Sistem 👥',
                    description:
                        'Semua admin yang aktif terdaftar di sini. Pastikan hanya orang-orang terpercaya yang memiliki kunci akses ya!',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#btn-edit-user',
                popover: {
                    title: 'Edit Otoritas ✏️',
                    description:
                        'Ubah informasi personal atau level peran administrator ini kapan saja.',
                    side: 'left',
                    align: 'center',
                },
            },
            {
                element: '#btn-delete-user',
                popover: {
                    title: 'Cabut Akses 🗑️',
                    description:
                        'Cabut hak akses dan hapus admin ini dari sistem (Super Admin saja). Gunakan dengan bijak!',
                    side: 'left',
                    align: 'center',
                },
            },
        ],
    });

    // 5.1 Admin User Editor Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_user_editor',
        steps: [
            {
                element: '#user-identity-section',
                popover: {
                    title: 'Identitas Personal 👤',
                    description:
                        'Masukkan nama lengkap dan alamat email resmi rekan administrator baru Anda.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#user-password-section',
                popover: {
                    title: 'Kunci Pengaman 🔐',
                    description:
                        'Tentukan password awal yang kuat untuk menjaga akun ini dari akses yang tidak sah.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#user-role-selector',
                popover: {
                    title: 'Otoritas Peran 🎭',
                    description:
                        'Tentukan level akses mereka. Pastikan peran yang diberikan sesuai dengan tanggung jawab mereka di sistem.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#user-save-btn',
                popover: {
                    title: 'Konfirmasi Otorisasi ✅',
                    description:
                        'Simpan dan aktifkan akun! Sistem akan segera mengenali entitas baru ini.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    // 5.2 Admin Pending Admins Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_pending_admins',
        steps: [
            {
                element: 'h1',
                popover: {
                    title: 'Pos Pemeriksaan Akses 👮',
                    description:
                        'Selamat datang di gerbang verifikasi. Di sini Anda memutuskan siapa yang layak masuk ke pusat kendali.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#pending-admin-identity',
                popover: {
                    title: 'Verifikasi Identitas 🔍',
                    description:
                        'Periksa profil pemohon. Kenali mereka sebelum memberikan kunci akses sistem.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#pending-admin-actions',
                popover: {
                    title: 'Keputusan Otoritas ⚖️',
                    description:
                        'Klik "Setujui" jika mereka valid, atau "Tolak" jika permohonan terasa mencurigakan. Keamanan ada di tangan Anda!',
                    side: 'top',
                    align: 'end',
                },
            },
        ],
    });

    // 6. Admin UEQ Analitik Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_ueq',
        steps: [
            {
                element: 'h1',
                popover: {
                    title: 'Wawasan Kepuasan Pengguna 🧠',
                    description:
                        'Masuk ke dalam pikiran pengguna! Di sini kita menganalisis pengalaman mereka selama menggunakan platform OOPedia.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#ueq-export-btn',
                popover: {
                    title: 'Unduh Hasil Survey 📥',
                    description:
                        'Ingin mengolah data lebih lanjut untuk skripsi? Ekspor semua jawaban survey UEQ ke dalam file CSV dalam sekejap!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#ueq-stats-grid',
                popover: {
                    title: 'Kartu Skor Dimensi UX 📊',
                    description:
                        'Menganalisis 6 aspek UX (Daya Tarik, Efisiensi, dll). Hijau artinya sukses, merah artinya kita harus berimprovisasi lagi!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#ueq-class-filter',
                popover: {
                    title: 'Fokus Per Kelas 🏫',
                    description:
                        'Ingin membandingkan antar kelas? Gunakan saringan ini untuk melihat hasil survey dari kelas tertentu saja.',
                    side: 'left',
                    align: 'center',
                },
            },
            {
                element: '#ueq-log-table',
                popover: {
                    title: 'Jurnal Respon Individual 📝',
                    description:
                        'Log lengkap setiap masukan yang masuk. Klik detail untuk melihat bagaimana seorang mahasiswa menilai setiap aspek platform.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    // 6.1 Admin UEQ Detail Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_ueq_detail',
        steps: [
            {
                element: '.lg\\:grid-cols-3 > div:first-child',
                popover: {
                    title: 'Profil Responden 👤',
                    description:
                        'Informasi lengkap mengenai siapa yang mengisi survey ini, termasuk kelas dan waktu pengiriman.',
                    side: 'right',
                    align: 'start',
                },
            },
            {
                element: '.space-y-4',
                popover: {
                    title: 'Rata-Rata Dimensi 📉',
                    description:
                        'Melihat skor rata-rata yang diberikan responden ini untuk setiap dimensi UEQ secara visual.',
                    side: 'right',
                    align: 'center',
                },
            },
            {
                element: '.lg\\:col-span-2 > .space-y-6',
                popover: {
                    title: 'Suara Pengguna 🗣️',
                    description:
                        'Komentar dan saran subjektif. Ini adalah bagian paling berharga untuk mengetahui apa yang benar-benar dirasakan pengguna!',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: 'table',
                popover: {
                    title: 'Pemetaan Jawaban Spesifik 🧭',
                    description:
                        'Tabel detail pilihan responden untuk setiap pasangan kata sifat. Dari sini Anda bisa melihat pola kecenderungan penilaian mereka.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });


    // 6.2 Admin MSLQ Analitik Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_mslq',
        steps: [
            {
                element: '#mslq-header',
                popover: {
                    title: 'Analitik MSLQ 🧠',
                    description:
                        'Selamat datang di pusat analisis motivasi dan strategi belajar! Di sini kita membedah profil psikologis pembelajar mahasiswa.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '#mslq-chart-card',
                popover: {
                    title: 'Profil Kolektif 📊',
                    description:
                        'Grafik radar ini menunjukkan kekuatan dan kelemahan kolektif mahasiswa dalam 6 dimensi motivasi dan 9 dimensi strategi belajar.',
                    side: 'right',
                    align: 'center',
                },
            },
            {
                element: '#mslq-stat-motivation',
                popover: {
                    title: 'Skor Motivasi 🎯',
                    description:
                        'Rata-rata tingkat motivasi seluruh mahasiswa. Skor yang tinggi menunjukkan antusiasme belajar yang kuat.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#mslq-stat-strategy',
                popover: {
                    title: 'Skor Strategi 📋',
                    description:
                        'Rata-rata efektivitas strategi belajar yang digunakan. Membantu Anda mengidentifikasi area yang butuh bimbingan metodologi.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#mslq-table-card',
                popover: {
                    title: 'Database Responden 👥',
                    description:
                        'Daftar detail setiap mahasiswa yang telah mengisi kuesioner. Anda bisa melihat skor individu dan mendalami hasilnya.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });

    // 6.3 Admin MSLQ Detail Tutorial
    tutorialState.registerSteps({
        tourId: 'admin_mslq_detail',
        steps: [
            {
                element: '.bg-slate-900',
                popover: {
                    title: 'Biodata Responden 👤',
                    description:
                        'Identitas mahasiswa dan ringkasan skor motivasi serta strategi mereka.',
                    side: 'right',
                    align: 'start',
                },
            },
            {
                element: 'h3:contains("Motivasi")',
                popover: {
                    title: 'Detail Motivasi 🎯',
                    description:
                        'Breakdown 6 sub-skala motivasi, mulai dari orientasi intrinsik hingga kecemasan ujian.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: 'h3:contains("Strategi Belajar")',
                popover: {
                    title: 'Detail Strategi 📋',
                    description:
                        'Breakdown 9 sub-skala strategi kognitif, metakognitif, dan manajemen sumber daya.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '.divide-slate-50',
                popover: {
                    title: 'Respon Per Item 📝',
                    description:
                        'Lihat jawaban mahasiswa untuk setiap 81 butir pertanyaan MSLQ secara transparan.',
                    side: 'top',
                    align: 'center',
                },
            },
        ],
    });
}
