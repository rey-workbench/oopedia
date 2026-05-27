# Aturan Pembelajaran Adaptif (Adaptive Learning Rules)

Dokumen ini berisi spesifikasi aturan pembelajaran adaptif yang digunakan dalam sistem e-learning Oopedia berbasis algoritma *Forward Chaining*. Aturan-aturan ini didefinisikan dalam database melalui seeder `AdaptiveRuleSeeder` dan dievaluasi secara dinamis oleh `AdaptiveEngineService`.

---

## Tabel 3.1 Interpretasi Fakta/Gejala (Input)

Tabel ini mendefinisikan gejala atau fakta (*Facts*) yang diamati langsung dari perilaku mahasiswa (*G-Codes*) maupun fakta hasil kesimpulan/diagnosa virtual (*V-Codes*).

| Kode Fakta | Nama Fakta / Gejala | Kategori | Kondisi Logika / Parameter Sensoris |
| :--- | :--- | :--- | :--- |
| **G01** | Akurasi < 40% | Primary | `accuracy < 40` |
| **G02** | Akurasi 40-60% | Primary | `accuracy >= 40` AND `accuracy <= 60` |
| **G03** | Akurasi 60-70% | Primary | `accuracy >= 60` AND `accuracy <= 70` |
| **G18** | Akurasi 70-80% | Primary | `accuracy >= 70` AND `accuracy <= 80` |
| **G04** | Akurasi 80-90% | Primary | `accuracy >= 80` AND `accuracy <= 90` |
| **G17** | Akurasi > 90% | Primary | `accuracy > 90` |
| **G05** | Tren Menurun | Primary | `performance_metrics.trend == 'down'` |
| **G06** | Tren Stabil | Primary | `performance_metrics.trend == 'stable'` |
| **G07** | Tren Meningkat | Primary | `performance_metrics.trend == 'up'` |
| **G11** | Respon Sangat Cepat (<10s) | Primary | `performance_metrics.last_response_time < 10` |
| **G13** | Respon Normal (10-45s) | Primary | `performance_metrics.last_response_time >= 10` AND `<= 45` |
| **G12** | Respon Lambat (>45s) | Primary | `performance_metrics.last_response_time > 45` |
| **G08** | Ketergantungan Hint (>3x) | Primary | `hints_used > 3` |
| **G09** | Hint Sedang (2-3x) | Primary | `hints_used >= 2` AND `hints_used <= 3` |
| **G10** | Hint Minimal (1x) | Primary | `hints_used == 1` |
| **G20** | Tanpa Bantuan (0x) | Primary | `hints_used == 0` |
| **G21** | Jawaban Terakhir Benar | Primary | `performance_metrics.last_result == true` |
| **G22** | Jawaban Terakhir Salah | Primary | `performance_metrics.last_result == false` |
| **G23** | Gunakan Hint Sekarang | Primary | `performance_metrics.last_used_hint == true` |
| **G14** | Streak Aktif (>=3 hari) | Primary | `streak >= 3` |
| **G15** | Streak Kuat (>=5 hari) | Primary | `streak >= 5` |
| **G16** | Streak Legendaris (>=10 hari) | Primary | `streak >= 10` |
| **G19** | Level Expert | Primary | `level == 'Expert'` |
| **V01** | Krisis Pembelajaran | Virtual / Diagnosa | Disimpulkan melalui Forward Chaining (Aturan Diagnosa) |
| **V02** | Sedang Kesulitan | Virtual / Diagnosa | Disimpulkan melalui Forward Chaining (Aturan Diagnosa) |
| **V03** | Performa Optimal | Virtual / Diagnosa | Disimpulkan melalui Forward Chaining (Aturan Diagnosa) |
| **V04** | Ketergantungan Bantuan | Virtual / Diagnosa | Disimpulkan melalui Forward Chaining (Aturan Diagnosa) |
| **V05** | Potensi Menebak | Virtual / Diagnosa | Disimpulkan melalui Forward Chaining (Aturan Diagnosa) |

---

## Tabel 3.2 Interpretasi Hasil/Tindakan (Output)

Tabel ini mendefinisikan tindakan atau rekomendasi pedagogis (*Actions*) yang diberikan oleh sistem kepada mahasiswa berdasarkan hasil diagnosa kognitif mereka.

| Kode Aksi | Nama Tindakan | Deskripsi | Varian UI / Mekanisme |
| :--- | :--- | :--- | :--- |
| **FEEDBACK** | Lanjutkan Latihan | Navigasi normal ke soal berikutnya. | `feedback` |
| **GIVE_HINT** | Bonus Bantuan | Berikan tambahan +1 jatah Hint untuk soal selanjutnya. | `popup` |
| **REMEDIAL** | Remedial Standard | Ulangi materi dari awal untuk penguatan konsep. | `feedback` |
| **REMEDIAL_INTENSIVE** | Remedial Intensif | Remedial materi disertai pemberian soal dengan tingkat kesulitan rendah. | `feedback` |
| **REDUCE_DIFF** | Turunkan Kesulitan | Ganti soal berikutnya ke level kesulitan yang lebih rendah. | `feedback` |
| **INCREASE_DIFF** | Naikkan Kesulitan | Naikkan tingkat kesulitan soal untuk memberikan tantangan baru. | `feedback` |
| **REDUCE_HINT** | Batasi Bantuan | Batasi/kurangi ketersediaan fitur bantuan untuk memaksa kemandirian. | `popup` |
| **NEW_CHALLENGE** | Tantangan Kilat | Instruksi pengerjaan soal berikutnya dengan target waktu yang cepat. | `challenge` |
| **STREAK_BONUS** | Bonus Streak | Memberikan bonus XP (Experience Points) untuk menjaga keterlibatan belajar. | `popup` |
| **CERTIFICATION** | Berikan Sertifikat | Memberikan sertifikat pencapaian tertinggi atas penguasaan modul. | `feedback` |
| **SHOW_GUIDANCE** | Tampilkan Bimbingan | Memberikan petunjuk bimbingan kontekstual dan dorongan motivasi. | `feedback` |
| **NOTIFY_TEACHER** | Lapor Pengajar | Mengirimkan notifikasi peringatan potensi krisis belajar mahasiswa ke admin/dosen. | `background_notification` |

---

## Tabel 3.3 Rule Base Forward Chaining (Aturan)

Basis aturan (*Rule Base*) dibagi menjadi dua tahapan proses inferensi *Forward Chaining*:
1. **Aturan Diagnosa (Diagnostic Rules)**: Mendeduksi fakta baru (*Virtual Facts* / *V-Codes*) berdasarkan fakta gejala input (*G-Codes*).
2. **Aturan Intervensi (Intervention Rules)**: Menentukan tindakan pedagogis (*Adaptive Actions*) berdasarkan kombinasi diagnosa dan gejala teramati.

### 1. Aturan Diagnosa (Deducing V-Codes)

| Kode Rule | Prioritas | Nama Aturan | Kondisi Premis (IF) | Hasil Konklusi (THEN) |
| :--- | :---: | :--- | :--- | :--- |
| **R01** | 10 | Analisa Performa Optimal | Jawaban Terakhir Benar (**G21**) AND Akurasi > 90% (**G17**) AND Respon Sangat Cepat (**G11**) | Performa Optimal (**V03**) |
| **R02** | 9 | Analisa Krisis Belajar | Jawaban Terakhir Salah (**G22**) AND Akurasi < 40% (**G01**) AND Tren Menurun (**G05**) | Krisis Pembelajaran (**V01**) |
| **R03** | 8 | Analisa Kesulitan Materi | Jawaban Terakhir Salah (**G22**) AND Akurasi 40-60% (**G02**) AND Respon Lambat (**G12**) | Sedang Kesulitan (**V02**) |
| **R04** | 7 | Analisa Pola Bantuan | Ketergantungan Hint (**G08**) AND Respon Lambat (**G12**) | Ketergantungan Bantuan (**V04**) |
| **R05** | 6 | Analisa Potensi Menebak | Respon Sangat Cepat (**G11**) AND Akurasi < 40% (**G01**) | Potensi Menebak (**V05**) |

### 2. Aturan Intervensi (Firing Actions)

| Kode Rule | Prioritas | Nama Aturan | Kondisi Premis (IF) | Hasil Konklusi / Tindakan (THEN) |
| :--- | :---: | :--- | :--- | :--- |
| **R06** | 5 | Strategi Akselerasi | Performa Optimal (**V03**) AND Tanpa Bantuan (**G20**) | **INCREASE_DIFF**, **NEW_CHALLENGE**, **STREAK_BONUS** |
| **R07** | 4 | Strategi Intervensi Krisis | Krisis Pembelajaran (**V01**) | **REMEDIAL_INTENSIVE**, **NOTIFY_TEACHER** |
| **R08** | 3 | Strategi Adaptasi Kesulitan | Sedang Kesulitan (**V02**) | **REDUCE_DIFF**, **GIVE_HINT**, **SHOW_GUIDANCE** |
| **R09** | 2 | Strategi Penguatan Mandiri | Ketergantungan Bantuan (**V04**) | **REDUCE_HINT** |
| **R10** | 1 | Strategi Bimbingan Fokus | Potensi Menebak (**V05**) | **SHOW_GUIDANCE** |
| **R11** | 0 | Strategi Kelulusan Materi | Level Expert (**G19**) AND Akurasi > 90% (**G17**) | **CERTIFICATION** |
| **R00** | 100 | Progres Terjaga *(Default)* | *(Default fallback jika aturan di atas tidak terpenuhi)* | **FEEDBACK** |
