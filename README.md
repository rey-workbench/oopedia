# Oopedia - Platform Pembelajaran Interaktif

Oopedia: platform e-learning adaptif (rule-based) untuk pengalaman personal mahasiswa. Gunakan forward-chaining tentukan intervensi pedagogis berdasar performa belajar.

## Fitur Utama

- **Adaptive Learning Engine:** Sesuaikan materi + kesulitan soal dari performa real-time.
- **Inertia.js Integration:** SPA terpadu tanpa API terpisah.
- **Svelte 5 Components:** UI reaktif, ringan, cepat.
- **Role-based Dashboard:** Monitoring Admin (Dosen) + Mahasiswa.
- **User Experience Evaluation:** Kuesioner UEQ bawaan ukur kepuasan.

## Tech Stack

- **Backend:** Laravel 12.x (PHP 8.4)
- **Frontend:** Svelte 5 + Inertia.js v3
- **Styling:** Tailwind CSS 4
- **Database:** MySQL
- **Build Tool:** Vite 7

---

## Alur Mesin Adaptif (Adaptive Engine)

Evaluasi fakta via forward-chaining ke kesimpulan virtual -> eksekusi aksi pedagogis.

### 1. Siklus Evaluasi Soal

1. Jawaban diproses `AdaptiveQuizFlowService`.
2. Kalkulasi: akurasi (`is_correct`), waktu (`time_spent`), hint (`used_hint`), skor.
3. Simpan state percobaan + perbarui profil gamifikasi.
4. `FactGatheringService` petakan parameter ke G-Code.
5. `AdaptiveEngineService` evaluasi aturan (R-Code) berdasar prioritas.
6. _First-match policy_: aturan pertama terpenuhi dieksekusi.
7. `NextActionResolverService` terjemah aturan ke aksi intervensi (H-Code).

### 2. Kode Fakta Teramati (G-Code)

Dari analisis state mahasiswa:

| Kode | Nama Fakta | Indikator Kondisi |
| G01 | Akurasi Rendah | Akurasi < 40% |
| G02 | Akurasi Kurang | Akurasi 40% - 60% |
| G03 | Akurasi Cukup | Akurasi 60% - 70% |
| G18 | Akurasi Sedang | Akurasi 70% - 80% |
| G04 | Akurasi Tinggi | Akurasi 80% - 90% |
| G17 | Akurasi Sangat Tinggi | Akurasi > 90% |
| G05 | Tren Menurun | `performance_metrics.trend == 'down'` |
| G06 | Tren Stabil | `performance_metrics.trend == 'stable'` |
| G07 | Tren Meningkat | `performance_metrics.trend == 'up'` |
| G11 | Respon Sangat Cepat | Waktu jawab < 10 detik |
| G13 | Respon Normal | Waktu jawab 10 - 45 detik |
| G12 | Respon Lambat | Waktu jawab > 45 detik |
| G08 | Ketergantungan Bantuan | Menggunakan hint > 3 kali |
| G09 | Bantuan Sedang | Menggunakan hint 2 - 3 kali |
| G10 | Bantuan Minimal | Menggunakan hint 1 kali |
| G20 | Tanpa Bantuan | Menggunakan hint 0 kali |
| G21 | Jawaban Benar | `performance_metrics.last_result == true` |
| G22 | Jawaban Salah | `performance_metrics.last_result == false` |
| G23 | Akses Bantuan | Mengaktifkan hint soal terakhir |
| G14 | Streak Aktif | Streak hari belajar >= 3 |
| G15 | Streak Kuat | Streak hari belajar >= 5 |
| G16 | Streak Legendaris | Streak hari belajar >= 10 |
| G19 | Level Ahli | Status level == 'Expert' |

### 3. Kode Diagnosa Virtual (V-Code)

Inferensi dari aturan diagnostik:

| Kode | Nama Diagnosa | Keterangan |
| V01 | Krisis Pembelajaran | Performa turun drastis + akurasi sangat rendah |
| V02 | Sedang Kesulitan | Akurasi kurang + waktu lambat |
| V03 | Performa Optimal | Akurasi sangat tinggi + pengerjaan cepat |
| V04 | Ketergantungan Bantuan | Sering hint pada respon lambat |
| V05 | Potensi Menebak | Sangat cepat tapi akurasi rendah |

### 4. Kode Aksi Intervensi (H-Code)

Aksi dieksekusi sistem:

| Kode | Nama Aksi | Deskripsi Intervensi |
| feedback | Lanjutkan Latihan | Navigasi normal soal berikutnya |
| give_hint | Bonus Bantuan | Beri tambahan 1 kuota hint |
| remedial | Remedial Standard | Baca kembali materi |
| remedial_intensive | Remedial Intensif | Remedial materi + soal mudah |
| reduce_diff | Turunkan Kesulitan | Turunkan level soal berikutnya |
| increase_diff | Naikkan Kesulitan | Tingkatkan level soal berikutnya |
| reduce_hint | Batasi Bantuan | Hilangkan hint latih kemandirian |
| new_challenge | Tantangan Kilat | Beri tantangan batas waktu |
| streak_bonus | Bonus Streak | Beri bonus XP tambahan |
| certification | Berikan Sertifikat | Terbitkan sertifikat kelulusan |
| show_guidance | Tampilkan Bimbingan | Tampilkan pop-up petunjuk |
| notify_teacher | Lapor Pengajar | Kirim notif krisis ke Admin |

### 5. Aturan Adaptif (R-Code)

Struktur forward-chaining terdaftar:

| Aturan | Nama Aturan | Syarat Input | Output / Aksi |
| R00 | Progres Terjaga | Tanpa syarat | Lanjutkan Latihan (`feedback`) |
| R01 | Analisa Performa Optimal | G21, G17, G11 | Diagnosa Performa Optimal (`V03`) |
| R02 | Analisa Krisis Belajar | G22, G01, G05 | Diagnosa Krisis Pembelajaran (`V01`) |
| R03 | Analisa Kesulitan Materi | G22, G02, G12 | Diagnosa Sedang Kesulitan (`V02`) |
| R04 | Analisa Pola Bantuan | G08, G12 | Diagnosa Ketergantungan Bantuan (`V04`) |
| R05 | Analisa Potensi Menebak | G11, G01 | Diagnosa Potensi Menebak (`V05`) |
| R06 | Strategi Akselerasi | V03, G20 | Naikkan Kesulitan, Tantangan Kilat, Bonus Streak |
| R07 | Strategi Intervensi Krisis | V01 | Remedial Intensif, Lapor Pengajar |
| R08 | Strategi Adaptasi Kesulitan | V02 | Turunkan Kesulitan, Bonus Bantuan, Tampilkan Bimbingan |
| R09 | Strategi Penguatan Mandiri | V04 | Batasi Bantuan |
| R10 | Strategi Bimbingan Fokus | V05 | Tampilkan Bimbingan |
| R11 | Strategi Kelulusan Materi | G19, G17 | Berikan Sertifikat |

---

## Panduan Instalasi

Jalankan proyek lokal:

1. **Dependensi Backend:**
    ```bash
    composer install
    ```

2. **Dependensi Frontend:**
    ```bash
    pnpm install
    ```

3. **Konfigurasi Environment:**
    Salin `.env.example` ke `.env`, sesuaikan DB:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Migrasi Database & Seeding:**
    Buat tabel + data aturan:
    ```bash
    php artisan migrate --seed
    ```

5. **Server Pengembangan:**
    Vite + Artisan:
    ```bash
    # Terminal 1: Frontend
    pnpm run dev

    # Terminal 2: Backend
    php artisan serve
    ```

## Evaluasi User Experience (UEQ)

Integrasi 26 item kuesioner UEQ untuk ukur:
- **Daya Tarik (Attractiveness):** Kesan umum.
- **Kejelasan (Perspicuity):** Mudah dipahami.
- **Efisiensi (Efficiency):** Kecepatan respon.
- **Ketepatan (Dependability):** Kontrol konsistensi.
- **Stimulasi (Stimulation):** Ketertarikan guna kembali.
- **Kebaruan (Novelty):** Desain kreatif unik.

Hasil disajikan via grafik statistik di Dashboard Admin.
