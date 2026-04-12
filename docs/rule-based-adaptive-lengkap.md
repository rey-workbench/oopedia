# Dokumentasi Sistem Adaptif Rule-Based (Forward Chaining) - Oopedia

## 1. Pendahuluan

Sistem pembelajaran oopedia menerapkan mesin adaptif berbasis aturan (**Rule-Based Engine**) yang menggunakan algoritma **Forward Chaining**. Sistem ini bekerja dengan mengumpulkan fakta-fakta (**Facts**) dari aktivitas belajar siswa, menyocokkannya dengan kumpulan aturan (**Rules**) yang sudah didefinisikan, dan mengeksekusi aksi (**Actions**) yang sesuai untuk menyesuaikan jalur pembelajaran secara real-time.

---

## 2. Arsitektur Mesin Adaptif

Mesin ini terdiri dari beberapa komponen inti yang saling bekerja sama:

1.  **FactGatheringService**: Bertugas mengonversi metrik performa mentah siswa (skor, waktu, jenis kesalahan) menjadi kode fakta atomik (**G-Codes**).
2.  **RuleRegistry**: Pusat pendaftaran seluruh aturan. Aturan diurutkan berdasarkan **Prioritas** (angka lebih kecil berarti prioritas lebih tinggi).
3.  **AdaptiveEngineService**: Orkesrator utama yang menjalankan loop *forward chaining*. Ia mencari aturan pertama yang memenuhi syarat fakta saat ini, mengeksekusinya, dan segera menghentikan pencarian (*First Match Policy*).
4.  **NextActionResolverService**: Menerjemahkan hasil keputusan mesin menjadi navigasi konkret di frontend (URL materi atau soal).

---

## 3. Kamus Fakta (G-Codes)

Fakta adalah input atau sinyal yang dibaca oleh sistem.

### 3.1 Performa & Skor
| Kode | Nama Fakta | Deskripsi |
| :--- | :--- | :--- |
| **G01** | Score Critical | Skor sangat rendah (< 40%) |
| **G02** | Score Remedial | Skor perlu perbaikan (40% - 69%) |
| **G03** | Score Standard | Skor memenuhi standar (70% - 89%) |
| **G04** | Score Mastery  | Skor mahir/sempurna (90% - 100%) |

### 3.2 Waktu & Interaksi
| Kode | Nama Fakta | Deskripsi |
| :--- | :--- | :--- |
| **G05** | Time Fast | Penyelesaian < 70% dari alokasi waktu default |
| **G06** | Time Slow | Penyelesaian melebihi alokasi waktu default |
| **G12** | Hint Used | Menggunakan bantuan (*hint*) saat menjawab |

### 3.3 Gaya Belajar (Learning Styles)
| Kode | Nama Fakta | Deskripsi |
| :--- | :--- | :--- |
| **G07** | Style Visual | Kecenderungan gaya belajar Visual |
| **G08** | Style Textual | Kecenderungan gaya belajar Tekstual |
| **G27** | Style Mixed | Gaya belajar campuran (Visual & Tekstual) |

### 3.4 Jenis Kesalahan (Error Types)
| Kode | Nama Fakta | Deskripsi |
| :--- | :--- | :--- |
| **G09** | Error Syntax | Kesalahan pada penulisan/sintaksis kode |
| **G10** | Error Logic | Kesalahan pada alur logika program |
| **G11** | No Error | Jawaban benar tanpa kesalahan |

### 3.5 Konteks & Progres
| Kode | Nama Fakta | Deskripsi |
| :--- | :--- | :--- |
| **G13** | In Module | Siswa sedang berada di tengah modul |
| **G14** | Module Started | Modul baru saja dimulai |
| **G15** | Diff Beginner | Sedang mengerjakan tingkat *Easy* |
| **G16** | Diff Medium | Sedang mengerjakan tingkat *Medium* |
| **G17** | Diff Hard | Sedang mengerjakan tingkat *Advanced* |
| **G18** | Final Project | Sedang mengerjakan Proyek Akhir Modul |
| **G19** | Is Practice | Soal latihan biasa (bukan proyek akhir) |
| **G20** | Next Unlocked | Materi berikutnya sudah terbuka |
| **G21** | Prev Unlocked | Materi sebelumnya sudah terbuka |
| **G22** | Persistent Fail | Gagal menjawab benar >= 2 kali berturut-turut |
| **G23** | Completed Module | Modul saat ini telah diselesaikan |
| **G24** | Completed All Modules | Seluruh modul dalam sistem telah selesai |
| **G25** | High Engagement | Tingkat keterlibatan siswa tinggi |
| **G26** | Satisfactory Progress | Progres materi memadai (>60%) |

---

## 4. Kamus Aksi (H-Codes)

Aksi adalah keputusan atau output yang dihasilkan oleh sistem.

| Kode | Nama Aksi | Efek Terhadap Siswa |
| :--- | :--- | :--- |
| **H01** | Visual Crisis Intervention | Intervensi segera ke materi visual karena gagal kritis |
| **H02** | Textual Crisis Intervention | Intervensi segera ke materi tekstual karena gagal kritis |
| **H03** | Syntax Recovery | Diarahkan ke pemulihan pemahaman sintaksis |
| **H04** | Logic Recovery | Diarahkan ke pemulihan pemahaman logika |
| **H05** | Standard Promotion | Promosi normal ke soal berikutnya dalam tingkat yang sama |
| **H06** | Accelerated Jump | Melompati tingkat (misal Easy langsung ke Hard) |
| **H07** | Critical Backtracking | Penurunan tingkat kesulitan karena performa buruk |
| **H08** | Module Graduation | Kelulusan dari modul saat ini |
| **H09** | Gold Certificate | Pemberian Sertifikat Emas (Peforma Sempurna) |
| **H10** | Silver Certificate | Pemberian Sertifikat Perak (Peforma Baik) |
| **H11** | Bronze Certificate | Pemberian Sertifikat Perunggu (Lulus Standar) |
| **H12** | Visual Project Revision | Revisi khusus materi visual pada proyek akhir |
| **H13** | Textual Project Revision | Revisi khusus materi tekstual pada proyek akhir |
| **H14** | Persistent Visual Net | Jaring pengaman untuk kegagalan berulang (Visual) |
| **H15** | Persistent Textual Net | Jaring pengaman untuk kegagalan berulang (Tekstual) |
| **H16** | Accel. Material Promotion | Loncatan antar modul materi (Fast Track) |

---

## 5. Daftar Aturan Lengkap (20 Aturan Terimplementasi)

Aturan diurutkan berdasarkan prioritas eksekusi (1 - 100).

### 5.1 Kategori: Crisis & Safety Net (Prioritas 3 - 15)
Kategori ini menangani kegagalan berat atau kegagalan berulang agar siswa mendapatkan bantuan/intervensi segera.

1.  **Rule ID: RULE_18 - Final Project Visual Persistent Fail**
    *   **Kondisi**: G01/G02 (Gagal) + G22 (Berulang) + G07 (Visual) + G18 (Proyek Akhir)
    *   **Aksi**: H12 (Revisi Proyek Visual)
    *   **Prioritas**: 3

2.  **Rule ID: RULE_19 - Final Project Textual Persistent Fail**
    *   **Kondisi**: G01/G02 (Gagal) + G22 (Berulang) + G08 (Tekstual) + G18 (Proyek Akhir)
    *   **Aksi**: H13 (Revisi Proyek Tekstual)
    *   **Prioritas**: 3

3.  **Rule ID: RULE_14 - Persistent Visual Safety Net**
    *   **Kondisi**: G01/G02 (Gagal) + G22 (Berulang) + G07 (Visual)
    *   **Aksi**: H14 (Safety Net Visual)
    *   **Prioritas**: 5

4.  **Rule ID: RULE_15 - Persistent Textual Safety Net**
    *   **Kondisi**: G01/G02 (Gagal) + G22 (Berulang) + G08 (Tekstual)
    *   **Aksi**: H15 (Safety Net Tekstual)
    *   **Prioritas**: 5

5.  **Rule ID: RULE_01 - Visual Crisis Intervention**
    *   **Kondisi**: G01 (Kritis) + G07 (Visual) + G15 (Easy) + Bukan Proyek Akhir
    *   **Aksi**: H01 (Intervensi Visual)
    *   **Prioritas**: 10

6.  **Rule ID: RULE_02 - Textual Crisis Intervention**
    *   **Kondisi**: G01 (Kritis) + G08 (Tekstual) + G15 (Easy) + Bukan Proyek Akhir
    *   **Aksi**: H02 (Intervensi Tekstual)
    *   **Prioritas**: 10

7.  **Rule ID: RULE_12 - Visual Project Revision**
    *   **Kondisi**: G01/G02 (Gagal) + G07 (Visual) + G18 (Proyek Akhir) + Bukan Gagal Berulang
    *   **Aksi**: H12 (Revisi Visual)
    *   **Prioritas**: 15

8.  **Rule ID: RULE_13 - Textual Project Revision**
    *   **Kondisi**: G01/G02 (Gagal) + G08 (Tekstual) + G18 (Proyek Akhir) + Bukan Gagal Berulang
    *   **Aksi**: H13 (Revisi Tekstual)
    *   **Prioritas**: 15

### 5.2 Kategori: Recovery (Prioritas 24 - 48)
Kategori ini menangani siswa yang mengalami kesulitan spesifik (sintaksis atau logika) pada tingkat medium.

9.  **Rule ID: RULE_03 - Syntax Recovery**
    *   **Kondisi**: G02 (Remedial) + G09 (Error Sintaks) + G16 (Medium) + G12 (Pakai Hint)
    *   **Aksi**: H03 (Pelajari Sintaks)
    *   **Prioritas**: 24

10. **Rule ID: RULE_04 - Logic Recovery**
    *   **Kondisi**: G02 (Remedial) + G10 (Error Logika) + G16 (Medium) + G12 (Pakai Hint)
    *   **Aksi**: H04 (Pahami Konsep)
    *   **Prioritas**: 25

11. **Rule ID: RULE_17 - Remedial Independent**
    *   **Kondisi**: G02 (Remedial) + Tidak Pakai Hint + Bukan Proyek Akhir
    *   **Aksi**: STUDY_MIXED (Review Materi Campuran)
    *   **Prioritas**: 48

### 5.3 Kategori: Progression (Prioritas 27 - 50)
Kategori ini mengatur alur naik/turunnya tingkat kesulitan berdasarkan performa.

12. **Rule ID: RULE_07 - Critical Backtracking**
    *   **Kondisi**: G01 (Kritis) + (G16/G17 - Medium/Hard) + Bukan Gagal Berulang + Bukan Proyek Akhir
    *   **Aksi**: H07 (Turun ke Easy)
    *   **Prioritas**: 27

13. **Rule ID: RULE_16 - Mastery Medium**
    *   **Kondisi**: G04 (Mahir) + G05 (Cepat) + G16 (Medium) + Tanpa Hint + Bukan Proyek Akhir
    *   **Aksi**: H05 (Promosi ke Hard)
    *   **Prioritas**: 35

14. **Rule ID: RULE_20 - Accelerated Material Promotion**
    *   **Kondisi**: G04 (Mahir) + G05 (Cepat) + G20 (Materi Berikutnya Ada) + Tanpa Hint
    *   **Aksi**: H16 (Loncatan Materi)
    *   **Prioritas**: 35

15. **Rule ID: RULE_06 - Accelerated Jump**
    *   **Kondisi**: G04 (Mahir) + G05 (Cepat) + G15 (Easy) + Tanpa Hint
    *   **Aksi**: H06 (Lompat ke Hard/Medium)
    *   **Prioritas**: 40

16. **Rule ID: RULE_05 - Standard Promotion**
    *   **Kondisi**: G03/G04 (Lulus) + Bukan Proyek Akhir
    *   **Aksi**: H05 (Soal Berikutnya)
    *   **Prioritas**: 50

### 5.4 Kategori: Achievement (Prioritas 21 - 30)
Kategori ini menangani kelulusan modul dan pemberian sertifikasi.

17. **Rule ID: RULE_09 - Gold Certificate**
    *   **Kondisi**: G04 (Mastery) + G05 (Cepat) + Tanpa Hint + G18 (Proyek Akhir) + G26 (Progres Oke)
    *   **Aksi**: H09 (Sertifikat Emas)
    *   **Prioritas**: 21

18. **Rule ID: RULE_10 - Silver Certificate**
    *   **Kondisi**: G03/G04 (Lulus) + Tanpa Hint + G18 (Proyek Akhir) + G26 (Progres Oke)
    *   **Aksi**: H10 (Sertifikat Perak)
    *   **Prioritas**: 22

19. **Rule ID: RULE_11 - Bronze Certificate**
    *   **Kondisi**: G03/G04 (Lulus) + Pakai Hint + G18 (Proyek Akhir) + G26 (Progres Oke)
    *   **Aksi**: H11 (Sertifikat Perunggu)
    *   **Prioritas**: 23

20. **Rule ID: RULE_08 - Module Graduation**
    *   **Kondisi**: G04 (Mastery) + G05 (Cepat) + Tanpa Hint + G17 (Hard) + G26 (Progres Oke) + G13 (In Module)
    *   **Aksi**: H08 (Lulus Modul)
    *   **Prioritas**: 30

---

## 6. Prosedur Eksekusi (Evaluation Loop)

Setiap kali siswa mengirimkan jawaban, sistem menjalankan alur berikut:

1.  **Pengumpulan Fakta**: Sistem memeriksa database dan konteks jawaban untuk membangun *array* fakta (Misal: `['G02', 'G09', 'G16', 'G12']`).
2.  **Iterasi Aturan**: Mesin mengambil daftar aturan yang sudah terdaftar di `RuleRegistry`, diurutkan dari prioritas 3 sampai 50.
3.  **Evaluasi Terurut**:
    *   Apakah Rule P3 (Final Project) cocok? Tidak.
    *   Apakah Rule P5 (Safety Net) cocok? Tidak.
    *   ...
    *   Apakah Rule P24 (Syntax Recovery) cocok? **Ya!**
4.  **Eksekusi & Terminasi**: Sistem menjalankan metode `apply()` dari Rule P24, mengubah state siswa, dan **berhenti mencari**. Jika tidak ada aturan yang cocok, sistem kembali ke aksi default (`H05 - Standard Promotion`).

---

## 7. Struktur State Siswa (StudentState)

Data adaptivitas disimpan dalam model `StudentState` pada kolom JSON:

*   **performance_metrics**: Menyimpan statistik jawaban (total benar, salah, streak salah, penggunaan hint).
*   **learning_profile**: Menyimpan skor kecenderungan gaya belajar (visual vs tekstual) dan sertifikasi yang dimiliki.
*   **adaptive_state**: Menyimpan variabel kontrol mesin seperti `target_difficulty` dan `fast_track_active`.

---
*Dokumen ini merupakan referensi teknis lengkap untuk Logical Rules mesin adaptif Oopedia.*
