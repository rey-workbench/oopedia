# Oopedia - Platform Pembelajaran Interaktif

Oopedia adalah platform e-learning adaptif berbasis aturan (rule-based) yang menyajikan pengalaman belajar personal bagi mahasiswa. Sistem ini menggunakan mesin inferensi forward-chaining untuk menentukan intervensi pedagogis berdasarkan performa dan aktivitas belajar mahasiswa.

## Fitur Utama

- **Adaptive Learning Engine:** Penyesuaian materi dan tingkat kesulitan soal berdasarkan fakta performa mahasiswa secara real-time.
- **Inertia.js Integration:** Pembangunan Single Page Application (SPA) secara terpadu tanpa memerlukan API terpisah.
- **Svelte 5 Components:** Antarmuka pengguna yang reaktif, ringan, dan cepat.
- **Role-based Dashboard:** Halaman monitoring khusus untuk Admin (Dosen) dan Mahasiswa.
- **User Experience Evaluation:** Modul kuesioner UEQ (User Experience Questionnaire) bawaan untuk mengukur kepuasan pengguna.

## Tech Stack

- **Backend:** Laravel 12.x (PHP 8.4)
- **Frontend:** Svelte 5 + Inertia.js v3
- **Styling:** Tailwind CSS 4
- **Database:** MySQL
- **Build Tool:** Vite 7

---

## Alur Mesin Adaptif (Adaptive Engine)

Mesin adaptif menggunakan algoritma forward-chaining untuk mengevaluasi fakta teramati menjadi kesimpulan virtual, lalu mengambil aksi pedagogis yang sesuai.

### 1. Siklus Evaluasi Soal

1. **Titik Masuk:** Pengiriman jawaban diproses oleh `AdaptiveQuizFlowService`.
2. **Kalkulasi Parameter:** Sistem mencatat akurasi (`is_correct`), waktu pengerjaan (`time_spent`), penggunaan bantuan (`used_hint`), dan perolehan skor.
3. **Pembaruan State:** Menyimpan riwayat percobaan (_attempt_) ke database dan memperbarui profil gamifikasi mahasiswa.
4. **Ekstraksi Fakta:** `FactGatheringService` memetakan parameter performa ke dalam kode fakta teramati (G-Code).
5. **Evaluasi Aturan:** `AdaptiveEngineService` mengevaluasi aturan (R-Code) berdasarkan urutan prioritas tertinggi.
6. **Kebijakan Pencocokan:** Menggunakan _first-match policy_ di mana aturan pertama yang terpenuhi akan langsung dieksekusi.
7. **Resolusi Aksi:** `NextActionResolverService` menerjemahkan aturan terpilih menjadi aksi intervensi nyata (H-Code).

### 2. Kode Fakta Teramati (G-Code)

Berikut adalah daftar kode fakta yang dihasilkan dari analisis langsung terhadap state belajar mahasiswa:

| Kode | Nama Fakta             | Indikator Kondisi                          |
| :--- | :--------------------- | :----------------------------------------- |
| G01  | Akurasi Rendah         | Akurasi < 40%                              |
| G02  | Akurasi Kurang         | Akurasi 40% - 60%                          |
| G03  | Akurasi Cukup          | Akurasi 60% - 70%                          |
| G18  | Akurasi Sedang         | Akurasi 70% - 80%                          |
| G04  | Akurasi Tinggi         | Akurasi 80% - 90%                          |
| G17  | Akurasi Sangat Tinggi  | Akurasi > 90%                              |
| G05  | Tren Menurun           | `performance_metrics.trend == 'down'`      |
| G06  | Tren Stabil            | `performance_metrics.trend == 'stable'`    |
| G07  | Tren Meningkat         | `performance_metrics.trend == 'up'`        |
| G11  | Respon Sangat Cepat    | Waktu jawab < 10 detik                     |
| G13  | Respon Normal          | Waktu jawab 10 - 45 detik                  |
| G12  | Respon Lambat          | Waktu jawab > 45 detik                     |
| G08  | Ketergantungan Bantuan | Menggunakan hint > 3 kali                  |
| G09  | Bantuan Sedang         | Menggunakan hint 2 - 3 kali                |
| G10  | Bantuan Minimal        | Menggunakan hint 1 kali                    |
| G20  | Tanpa Bantuan          | Menggunakan hint 0 kali                    |
| G21  | Jawaban Benar          | `performance_metrics.last_result == true`  |
| G22  | Jawaban Salah          | `performance_metrics.last_result == false` |
| G23  | Akses Bantuan          | Mengaktifkan hint pada soal terakhir       |
| G14  | Streak Aktif           | Streak hari belajar >= 3                   |
| G15  | Streak Kuat            | Streak hari belajar >= 5                   |
| G16  | Streak Legendaris      | Streak hari belajar >= 10                  |
| G19  | Level Ahli             | Status level mahasiswa == 'Expert'         |

### 3. Kode Diagnosa Virtual (V-Code)

Fakta terinferensi yang disimpulkan oleh aturan diagnostik:

| Kode | Nama Diagnosa          | Keterangan                                          |
| :--- | :--------------------- | :-------------------------------------------------- |
| V01  | Krisis Pembelajaran    | Performa menurun drastis dan akurasi sangat rendah  |
| V02  | Sedang Kesulitan       | Akurasi kurang disertai waktu respon yang lambat    |
| V03  | Performa Optimal       | Akurasi sangat tinggi dengan pengerjaan cepat       |
| V04  | Ketergantungan Bantuan | Sering menggunakan bantuan pada waktu respon lambat |
| V05  | Potensi Menebak        | Pengerjaan sangat cepat namun akurasi rendah        |

### 4. Kode Aksi Intervensi (H-Code)

Aksi yang dieksekusi oleh sistem berdasarkan hasil evaluasi aturan:

| Kode               | Nama Aksi           | Deskripsi Intervensi                                |
| :----------------- | :------------------ | :-------------------------------------------------- |
| feedback           | Lanjutkan Latihan   | Navigasi normal ke soal berikutnya                  |
| give_hint          | Bonus Bantuan       | Memberikan tambahan 1 kuota bantuan                 |
| remedial           | Remedial Standard   | Mengarahkan mahasiswa membaca kembali materi        |
| remedial_intensive | Remedial Intensif   | Remedial materi disertai pemberian soal mudah       |
| reduce_diff        | Turunkan Kesulitan  | Menurunkan tingkat kesulitan soal berikutnya        |
| increase_diff      | Naikkan Kesulitan   | Meningkatkan tingkat kesulitan soal berikutnya      |
| reduce_hint        | Batasi Bantuan      | Menghilangkan tombol hint untuk melatih kemandirian |
| new_challenge      | Tantangan Kilat     | Memberikan tantangan batas waktu pengerjaan         |
| streak_bonus       | Bonus Streak        | Memberikan bonus XP tambahan                        |
| certification      | Berikan Sertifikat  | Menerbitkan sertifikat kelulusan modul              |
| show_guidance      | Tampilkan Bimbingan | Menampilkan pop-up petunjuk pengerjaan              |
| notify_teacher     | Lapor Pengajar      | Mengirimkan notifikasi krisis belajar ke Admin      |

### 5. Aturan Adaptif (R-Code)

Struktur aturan forward-chaining yang terdaftar pada sistem:

| Aturan | Nama Aturan                 | Syarat Input Fakta     | Output / Aksi yang Dihasilkan                          |
| :----- | :-------------------------- | :--------------------- | :----------------------------------------------------- |
| R00    | Progres Terjaga             | Tanpa syarat (Default) | Lanjutkan Latihan (`feedback`)                         |
| R01    | Analisa Performa Optimal    | G21, G17, G11          | Diagnosa Performa Optimal (`V03`)                      |
| R02    | Analisa Krisis Belajar      | G22, G01, G05          | Diagnosa Krisis Pembelajaran (`V01`)                   |
| R03    | Analisa Kesulitan Materi    | G22, G02, G12          | Diagnosa Sedang Kesulitan (`V02`)                      |
| R04    | Analisa Pola Bantuan        | G08, G12               | Diagnosa Ketergantungan Bantuan (`V04`)                |
| R05    | Analisa Potensi Menebak     | G11, G01               | Diagnosa Potensi Menebak (`V05`)                       |
| R06    | Strategi Akselerasi         | V03, G20               | Naikkan Kesulitan, Tantangan Kilat, Bonus Streak       |
| R07    | Strategi Intervensi Krisis  | V01                    | Remedial Intensif, Lapor Pengajar                      |
| R08    | Strategi Adaptasi Kesulitan | V02                    | Turunkan Kesulitan, Bonus Bantuan, Tampilkan Bimbingan |
| R09    | Strategi Penguatan Mandiri  | V04                    | Batasi Bantuan                                         |
| R10    | Strategi Bimbingan Fokus    | V05                    | Tampilkan Bimbingan                                    |
| R11    | Strategi Kelulusan Materi   | G19, G17               | Berikan Sertifikat                                     |

---

## Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di lingkungan pengembangan lokal:

1. **Persiapan Dependensi Backend:**

    ```bash
    composer install
    ```

2. **Persiapan Dependensi Frontend:**

    ```bash
    pnpm install
    ```

3. **Konfigurasi Environment:**
   Salin berkas konfigurasi `.env.example` menjadi `.env` lalu sesuaikan kredensial database Anda:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Migrasi Database & Seeding:**
   Jalankan migrasi untuk membuat tabel beserta data awal aturan adaptif:

    ```bash
    php artisan migrate --seed
    ```

5. **Menjalankan Server Pengembangan:**
   Jalankan compiler frontend (Vite) dan server backend (Artisan) pada terminal terpisah:

    ```bash
    # Terminal 1: Frontend compiler
    pnpm run dev

    # Terminal 2: PHP Development Server
    php artisan serve
    ```

## Evaluasi User Experience (UEQ)

Sistem ini mengintegrasikan kuesioner UEQ (User Experience Questionnaire) yang terdiri dari 26 item penilaian untuk mengukur aspek:

- **Daya Tarik (Attractiveness):** Kesan umum pengguna terhadap sistem.
- **Kejelasan (Perspicuity):** Kemudahan untuk memahami cara kerja sistem.
- **Efisiensi (Efficiency):** Kecepatan dan efisiensi sistem dalam merespon pengguna.
- **Ketepatan (Dependability):** Kontrol dan konsistensi sistem.
- **Stimulasi (Stimulation):** Ketertarikan pengguna untuk menggunakan sistem kembali.
- **Kebaruan (Novelty):** Desain kreatif dan keunikan sistem.

Hasil jawaban mahasiswa diakumulasikan dan disajikan dalam bentuk grafik analisis statistik pada Dashboard Admin.
