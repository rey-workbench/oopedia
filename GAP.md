# Analisis Celah (Gap Analysis) & Justifikasi Transformasi Rule-Based Engine

Dokumen ini menjelaskan alasan akademis dan teknis di balik implementasi **Iterative Forward Chaining** dan **Inference Chain** pada sistem adaptif Oopedia.

## 1. Masalah pada Model Tradisional (Status Quo)
Sebelumnya, aturan bersifat "Silo" (berdiri sendiri), di mana data mentah langsung diubah menjadi aksi tunggal.
*   **Keterbatasan**: Tidak ada proses "berpikir" bertahap. Sistem tidak memiliki memori jangka pendek tentang diagnosa yang sudah dilakukan sebelumnya dalam satu sesi evaluasi.
*   **Redundansi**: Banyak aturan mengulang-ulang pengecekan kondisi dasar yang sama.

## 2. Solusi: Transformasi ke Inference Chain (Model Pakar)
Sistem diubah menjadi model **Multi-Stage Inference**, menyerupai cara kerja seorang pakar (guru/dokter):

### A. Tahap Diagnosa (Diagnostic Stage)
Aturan seperti **R01, R04, R07** difokuskan untuk mengubah **Data Mentah (Fakta Primer)** menjadi **Diagnosa Virtual (Deduced Fact)**.
*   *Contoh*: "Akurasi < 40%" + "Tren Turun" disimpulkan sebagai fakta baru: **V_CRISIS**.

### B. Tahap Intervensi Hibrida (Hybrid Intervention)
Aturan seperti **R02** menunjukkan kecerdasan tingkat tinggi dengan melakukan dua hal sekaligus:
1.  **Deduction (Diagnosa Lanjutan)**: Mencatat bahwa siswa memiliki masalah "Ketergantungan Bantuan" (V_DEPENDENCY).
2.  **Action (Aksi Nyata)**: Memberikan intervensi langsung berupa "Remedial" dan "Penurunan Kesulitan".

### C. Transisi ke Arsitektur Fully Database-Driven (Dinamis)
Untuk meningkatkan skalabilitas dan kemudahan pemeliharaan, sistem telah ditingkatkan dari berbasis konstanta PHP menjadi **Fully Database-Driven**:
- **Logika di Database**: Seluruh ambang batas (*threshold*) akurasi, waktu, dan bantuan kini disimpan dalam format JSON di database, bukan lagi di kodingan PHP.
- **Pembaruan Real-Time**: Dosen atau admin dapat mengubah parameter adaptivitas langsung melalui panel admin/database tanpa perlu melakukan redeploy aplikasi atau mengubah satu baris koding pun.
- **Mesin Generik**: `AdaptiveEngineService` kini bertindak sebagai mesin pengevaluasi generik yang hanya bertugas menjalankan instruksi logika yang diberikan oleh database, membuat sistem ini sangat fleksibel untuk berbagai model pedagogis lainnya.

Justifikasi ini memperkuat nilai akademis skripsi karena menunjukkan pemahaman mendalam tentang desain sistem yang *decoupled* dan adaptif.

## 3. Eksekusi Aksi Multi-Dimensi
Sistem mampu menangani beberapa aksi sekaligus dalam satu aturan (seperti pada **R02**) tanpa adanya konflik:

*   **Background State Update (Reduce Difficulty)**: Sistem secara otomatis menurunkan level kuis di database. Ini adalah persiapan untuk "masa depan" agar mahasiswa tidak frustrasi saat kembali kuis.
*   **UI Navigation Update (Remedial Review)**: Sistem melakukan navigasi ke halaman materi untuk penanganan "saat ini".
*   **Justifikasi**: Kedua aksi ini saling melengkapi. Mahasiswa diberikan waktu untuk belajar kembali (Remedial) sekaligus diringankan bebannya (Lower Difficulty) agar peluang sukses di sesi berikutnya lebih tinggi.

## 4. Keunggulan Akademis untuk Sidang Skripsi
1.  **Explainability (Transparansi Logika)**: Sistem dapat menunjukkan *Inference Path* (jalur berpikir) secara visual, membuktikan proses diagnosa tidak terjadi secara acak.
2.  **Modularitas**: Pemisahan diagnosa dan intervensi membuat sistem lebih mudah dikelola dan dikembangkan di masa depan.
3.  **Forward Chaining Engine**: Membuktikan penerapan algoritma kecerdasan buatan yang dinamis (looping) dibandingkan sekadar `IF-ELSE` statis.
4.  **Pedagogi Adaptif**: Menunjukkan penerapan teori belajar yang nyata, di mana sistem mampu beradaptasi terhadap kegagalan siswa dengan penyesuaian materi dan tingkat kesulitan secara simultan.

---
*Dokumen ini disusun untuk mendukung argumentasi dalam penelitian sistem pembelajaran adaptif berbasis Forward Chaining pada platform Oopedia.*
