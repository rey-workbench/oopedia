# Adaptive Learning Engine - Rule Documentation

## Arsitektur 4 Layer

Engine ini menggunakan pendekatan **Forward Chaining** dengan alur 4 lapis:

1. **Input (Fakta / Raw Data)**
   Sistem membaca data performa siswa pada akhir sesi kuis:
    - `accuracy` (Akurasi jawaban, % dari total pertanyaan dijawab benar)
    - `time_spent` (Total waktu yang dihabiskan untuk menjawab)
    - `hints_used` (Jumlah bantuan yang digunakan)
    - `streak` (Konsistensi hari berturut-turut belajar - _Daily Streak_)
    - `stagnant_count` (Jumlah sesi beruntun di mana skor siswa tidak berubah lebih dari ±5%)
    - `level` (Level gamifikasi siswa: Beginner, Ahli, dsb)
    - `trend` (Tren grafik 3 sesi terakhir: `up`, `down`, atau `stable`)

2. **Kondisi / Threshold (Layer Klasifikasi)**
   Raw data dikonversi menjadi fakta boolean (`G-Codes`):
    - **G01 - G04, G17** (Akurasi): `<40%`, `40-60%`, `60-70%`, `>80%`, `>85%`
    - **G05 - G07** (Tren Performa): `Turun`, `Stabil`, `Naik`
    - **G08, G09, G20** (Penggunaan Bantuan): `>3x`, `2-3x`, `0x`
    - **G11 - G13** (Kecepatan Respon): `Cepat`, `Lambat`, `Normal`
    - **G14 - G16** (Streak Harian): `≥3 Hari`, `≥5 Hari`, `≥7 Hari`
    - **G19** (Gamifikasi): `Level Ahli`

3. **Diagnosis (V-Codes)**
   Kombinasi _G-Codes_ menyimpulkan status psikologis / kognitif siswa (`V-Codes`):
    - **V_CRISIS (Krisis Pembelajaran):** Siswa gagal memahami konsep secara fatal.
    - **V_STRUGGLING (Sedang Kesulitan):** Siswa kesulitan tapi masih mau berjuang.
    - **V_OPTIMAL (Performa Optimal):** Siswa menguasai materi dengan baik.
    - **V_DEPENDENCY (Ketergantungan Bantuan):** Siswa punya skor bagus hanya karena mengandalkan _hint_.
    - **V_BOREDOM (Potensi Kebosanan):** Siswa terus-menerus mendapat skor sama (stagnan), butuh tantangan baru.

4. **Rekomendasi (Aksi Sistem)**
   Sistem melontarkan _Action Constants_ berdasarkan diagnosis dan kriteria spesifik:
    - `REDUCE_DIFF` (Turunkan Kesulitan)
    - `INCREASE_DIFF` (Naikkan Kesulitan)
    - `REMEDIAL` (Ulangi Materi / Latihan Ekstra)
    - `SCAFFOLD_REDUCTION` (Kurangi jatah bantuan di sesi selanjutnya)
    - `NEW_CHALLENGE` (Format tantangan / soal baru)
    - `STREAK_BONUS` (Beri ekstra XP/Poin)
    - `CERTIFICATION` (Lulus dengan predikat sangat baik)
    - `FEEDBACK` (Berikan umpan balik / motivasi umum)

---

## Matriks Rule (R01 - R15)

Evaluasi berjalan berurutan. Rule pertama yang kondisinya **TRUE** akan langsung dieksekusi, mengabaikan rule di bawahnya.

| ID      | Diagnosis (V-Code) | Kondisi (Syarat G-Code)                                                                                                  | Rekomendasi Aksi                      |
| ------- | ------------------ | ------------------------------------------------------------------------------------------------------------------------ | ------------------------------------- |
| **R01** | `V_CRISIS`         | Akurasi <40% **AND** Tren Turun **AND** Bantuan >3x                                                                      | Remedial Review, Reduce Difficulty    |
| **R02** | `V_CRISIS`         | Akurasi <40% **AND** Tren Turun **AND** Bantuan ≤3x                                                                      | Remedial Review                       |
| **R03** | `V_CRISIS`         | Akurasi <40% **AND** Tren Stabil/Naik **AND** Bantuan >3x                                                                | Reduce Difficulty, Scaffold Reduction |
| **R04** | `V_STRUGGLING`     | Akurasi 40-60% **AND** Respons Lambat **AND** Bantuan ≤3x                                                                | Reduce Difficulty                     |
| **R05** | `V_STRUGGLING`     | Akurasi 40-60% **AND** Respons Normal **AND** Bantuan 2-3x                                                               | Remedial Review                       |
| **R06** | `V_STRUGGLING`     | Akurasi 60-70% **AND** Tren Stabil **AND** Bantuan ≤2x                                                                   | General Feedback                      |
| **R07** | `V_OPTIMAL`        | Akurasi >80% **AND** Tren Naik **AND** Level < Ahli                                                                      | Increase Difficulty                   |
| **R08** | `V_OPTIMAL`        | Akurasi >80% **AND** Tren Naik **AND** Level = Ahli                                                                      | New Challenge                         |
| **R09** | `V_OPTIMAL`        | Akurasi >80% **AND** Respons Cepat **AND** Streak ≥3 Hari                                                                | Increase Difficulty, Streak Bonus     |
| **R10** | `V_DEPENDENCY`     | Bantuan >3x **AND** Akurasi <50% tanpa bantuan **AND** Tren Stabil                                                       | Scaffold Reduction, Remedial Review   |
| **R11** | `V_DEPENDENCY`     | Bantuan >3x **AND** Akurasi >60% dengan bantuan **AND** Tren Naik                                                        | Scaffold Reduction                    |
| **R12** | `V_BOREDOM`        | Akurasi >80% **AND** Skor stagnan ≥3 sesi **AND** Streak ≥5 hari                                                         | New Challenge, Streak Bonus           |
| **R13** | `V_BOREDOM`        | Akurasi >80% **AND** Respons Cepat **AND** Skor stagnan ≥3 sesi                                                          | Increase Difficulty                   |
| **R14** | `-`                | _DEFAULT FALLBACK_ (Tidak masuk kondisi apa pun)                                                                         | General Feedback                      |
| **R15** | `V_OPTIMAL`        | Level = Ahli **AND** Bantuan = 0 **AND** Streak ≥7 Hari **AND** _Akumulasi akurasi rata-rata 3 sesi berturut-turut >85%_ | Grant Certification                   |

---

## Technical Edge Cases

1. **Daily Login Streak:**
   Streak bukan sekadar bertambah setiap kali menjawab kuis. Sistem mengecek `last_active_at`. Jika rentang aktivitas terakhir adalah kemarin (`Carbon::isYesterday()`), _streak_ naik. Jika hari yang sama, _streak_ tetap. Jika bolong, kembali ke 1.
2. **Hitungan Stagnansi:**
   Di-handle via `PerformanceService::calculateStagnantCount()`. Menggunakan perbandingan _sliding window_ dari sejarah sesi ke belakang untuk mencari pola konsisten di margin ±5%.
3. **Penyusutan Bantuan (Scaffold Reduction):**
   Sistem tidak serta-merta menganggap siswa pintar kalau mereka banyak buka _hint_. Justru bantuan (`hints_available`) akan dikurangi bertahap menjadi 2, 1, 0 di sesi berikutnya untuk memaksa kemandirian.
