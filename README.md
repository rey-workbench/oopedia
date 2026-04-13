# Oopedia.com - Platform Pembelajaran Interaktif

Oopedia adalah platform e-learning modern yang dirancang untuk memberikan pengalaman belajar yang interaktif dan personal. Versi terbaru ini menggunakan stack teknologi terkini untuk performa yang optimal dan user experience yang premium.

## 🚀 Tech Stack

- **Backend:** [Laravel 12.x](https://laravel.com)
- **Frontend:** [Svelte 5](https://svelte.dev) dengan [Inertia.js](https://inertiajs.com)
- **Styling:** [Tailwind CSS 4](https://tailwindcss.com)
- **Database:** MySQL
- **Build Tool:** [Vite](https://vitejs.dev)

## ✨ Fitur Utama

- **Adaptive Learning Engine:** Sistem cerdas yang menyesuaikan materi dan soal berdasarkan kemampuan pengguna.
- **Inertia.js Integration:** Pengalaman Single Page Application (SPA) tanpa kerumitan framework API terpisah.
- **Svelte Components:** Interface yang ringan, cepat, dan reaktif.
- **Admin & Student Dashboard:** Manajemen konten dan pemantauan progres yang komprehensif.

## Cara Kerja Rule-Based Adaptive Engine (Menyeluruh)

Bagian ini menjelaskan alur lengkap mesin adaptif Oopedia berbasis rule (forward chaining), termasuk bagaimana sistem bereaksi pada berbagai kondisi belajar.

### 1. Alur End-to-End Saat Siswa Menjawab Soal

1. Jawaban diproses melalui entrypoint terpusat `AdaptiveQuizFlowService::processAdaptiveAttemptByIds()`.
2. Service memvalidasi `materialId` dan `questionId`, lalu mendelegasikan eksekusi ke `processAdaptiveAttempt()`.
3. Sistem menghitung:
     - Kebenaran jawaban (`is_correct`)
     - Waktu pengerjaan (`time_spent`)
     - Penggunaan hint (`used_hint`)
     - Skor akhir
4. Sistem memperbarui performa/gamifikasi, lalu menyimpan attempt (DB untuk user login, cookie untuk guest).
5. `FactGatheringService` membentuk fakta (`Gxx`) dari kondisi aktual siswa.
6. `AdaptiveEngineService` menjalankan evaluasi rule sesuai urutan prioritas.
7. Rule pertama yang cocok akan dijalankan (`first match policy`) dan evaluasi berhenti.
8. Hasil rule diubah menjadi aksi navigasi konkret oleh `NextActionResolverService` (misalnya lanjut soal, review materi, loncat materi, atau klaim sertifikat).

### 2. Fakta (Input Ke Mesin Rule)

Fakta dibentuk setiap submit jawaban. Berikut daftar lengkap G-Code dari `AdaptiveConstants` beserta status implementasi saat ini:

| Kode | Constant | Status Saat Ini | Keterangan Singkat |
| :--- | :--- | :--- | :--- |
| G01 | FACT_SCORE_CRITICAL | Aktif | Skor kritis |
| G02 | FACT_SCORE_REMEDIAL | Aktif | Skor remedial |
| G03 | FACT_SCORE_STANDARD | Aktif | Skor standar |
| G04 | FACT_SCORE_MASTERY | Aktif | Skor mastery |
| G05 | FACT_TIME_FAST | Aktif | Waktu cepat |
| G06 | FACT_TIME_SLOW | Reserved | Konstanta ada, belum diproduksi FactGathering |
| G07 | FACT_STYLE_VISUAL | Aktif | Gaya visual |
| G08 | FACT_STYLE_TEXTUAL | Aktif | Gaya textual |
| G09 | FACT_ERROR_SYNTAX | Aktif | Error syntax |
| G10 | FACT_ERROR_LOGIC | Aktif | Error logic |
| G11 | FACT_NO_ERROR | Reserved | Konstanta ada, belum diproduksi FactGathering |
| G12 | FACT_HINT_USED | Aktif | Hint digunakan |
| G13 | FACT_IN_MODULE | Aktif | Sedang dalam modul |
| G14 | FACT_MODULE_STARTED | Reserved | Konstanta ada, belum diproduksi FactGathering |
| G15 | FACT_DIFF_BEGINNER | Aktif | Difficulty beginner |
| G16 | FACT_DIFF_MEDIUM | Aktif | Difficulty medium |
| G17 | FACT_DIFF_HARD | Aktif | Difficulty hard |
| G18 | FACT_IS_FINAL_PROJECT | Aktif | Final project |
| G19 | FACT_IS_PRACTICE | Reserved | Konstanta ada, belum diproduksi FactGathering |
| G20 | FACT_NEXT_UNLOCKED | Aktif | Materi berikutnya terbuka |
| G21 | FACT_PREV_UNLOCKED | Diproduksi, belum dipakai rule | Sudah dibentuk, belum ada rule yang baca |
| G22 | FACT_PERSISTENT_FAIL | Aktif | Gagal berulang |
| G23 | FACT_COMPLETED_MODULE | Reserved | Konstanta ada, belum diproduksi FactGathering |
| G24 | FACT_COMPLETED_ALL_MODULES | Reserved | Konstanta ada, belum diproduksi FactGathering |
| G25 | FACT_HIGH_ENGAGEMENT | Reserved | Konstanta ada, belum diproduksi FactGathering |
| G26 | FACT_SATISFACTORY_PROGRESS | Aktif | Progress memadai (>= 61%) |
| G27 | FACT_STYLE_MIXED | Diproduksi, belum dipakai rule | Sudah dibentuk, belum ada rule yang baca |

Daftar lengkap H-Code (Aksi) juga sebagai referensi:

| Kode | Constant | Status Saat Ini | Keterangan Singkat |
| :--- | :--- | :--- | :--- |
| H01 | ACTION_VISUAL_CRISIS_INTERVENTION | Aktif | Intervensi krisis visual |
| H02 | ACTION_TEXTUAL_CRISIS_INTERVENTION | Aktif | Intervensi krisis textual |
| H03 | ACTION_SYNTAX_RECOVERY | Aktif | Recovery syntax |
| H04 | ACTION_LOGIC_RECOVERY | Aktif | Recovery logic |
| H05 | ACTION_STANDARD_PROMOTION | Aktif | Promosi standar |
| H06 | ACTION_ACCELERATED_JUMP | Aktif | Percepatan level soal |
| H07 | ACTION_CRITICAL_BACKTRACKING | Aktif | Turunkan difficulty |
| H08 | ACTION_MODULE_GRADUATION | Aktif | Lulus modul |
| H09 | ACTION_GOLD_CERTIFICATE | Aktif | Sertifikat emas |
| H10 | ACTION_SILVER_CERTIFICATE | Aktif | Sertifikat perak |
| H11 | ACTION_BRONZE_CERTIFICATE | Aktif | Sertifikat perunggu |
| H12 | ACTION_VISUAL_PROJECT_REVISION | Aktif | Revisi proyek visual |
| H13 | ACTION_TEXTUAL_PROJECT_REVISION | Aktif | Revisi proyek textual |
| H14 | ACTION_PERSISTENT_VISUAL_NET | Aktif | Safety net visual |
| H15 | ACTION_PERSISTENT_TEXTUAL_NET | Aktif | Safety net textual |
| H16 | ACTION_ACCELERATED_MATERIAL_PROMOTION | Aktif | Percepatan antar materi |

Catatan: selain H-Code, engine juga memakai action operasional non-H seperti `NEXT_QUESTION`, `NEXT_MATERIAL`, `FINISH_MATERIAL`, `ISSUE_CERTIFICATE`, `REDUCE_DIFFICULTY`, `INCREASE_DIFFICULTY`, `STUDY_SYNTAX`, `STUDY_THEORY`, `STUDY_MIXED`, `STUDY_VISUAL`, dan `STUDY_TEXTUAL` pada tahap resolusi navigasi.

### 3. Aturan Evaluasi (Core Engine Behavior)

Mesin rule menggunakan prinsip berikut:

- Rule diambil dari `RuleRegistry` dan diurutkan menaik berdasarkan `priority` (angka kecil dieksekusi lebih dulu).
- Jika ada `priority` yang sama, urutan ditentukan lagi berdasarkan `Rule ID` agar hasil evaluasi tetap deterministik.
- Evaluasi bersifat `first match`: hanya rule pertama yang cocok yang dijalankan.
- Jika tidak ada rule cocok, fallback ke aksi default: `NEXT_QUESTION`.
- Ada guard anti-loop percepatan dalam materi yang sama:
    - Rule percepatan level (`ACTION_ACCELERATED_JUMP`) di-skip jika target difficulty sudah tercapai untuk materi aktif.
    - Rule percepatan antar materi (`ACTION_ACCELERATED_MATERIAL_PROMOTION`) di-skip jika aksi terakhir pada materi aktif juga percepatan materi.

### 4. Daftar Rule Aktif

Ada dua cara membaca urutan rule:

- Urutan eksekusi engine: berdasarkan `priority` (angka lebih kecil dieksekusi lebih dahulu).
- Urutan referensi: berdasarkan `Rule ID` (RULE_01 sampai RULE_20).

#### 4.1 Urutan Eksekusi (Berdasarkan Priority)

| Priority | Rule ID  | Nama Rule                                | Action Code |
| :------- | :------- | :--------------------------------------- | :---------- |
| 3        | RULE_18  | Final Project Visual Persistent Fail     | ACTION_VISUAL_PROJECT_REVISION |
| 3        | RULE_19  | Final Project Textual Persistent Fail    | ACTION_TEXTUAL_PROJECT_REVISION |
| 5        | RULE_14  | Persistent Visual Safety Net             | ACTION_PERSISTENT_VISUAL_NET |
| 5        | RULE_15  | Persistent Textual Safety Net            | ACTION_PERSISTENT_TEXTUAL_NET |
| 10       | RULE_01  | Visual Crisis Intervention               | ACTION_VISUAL_CRISIS_INTERVENTION |
| 10       | RULE_02  | Textual Crisis Intervention              | ACTION_TEXTUAL_CRISIS_INTERVENTION |
| 15       | RULE_12  | Visual Project Revision                  | ACTION_VISUAL_PROJECT_REVISION |
| 15       | RULE_13  | Textual Project Revision                 | ACTION_TEXTUAL_PROJECT_REVISION |
| 21       | RULE_09  | Gold Certificate                         | ACTION_GOLD_CERTIFICATE |
| 22       | RULE_10  | Silver Certificate                       | ACTION_SILVER_CERTIFICATE |
| 23       | RULE_11  | Bronze Certificate                       | ACTION_BRONZE_CERTIFICATE |
| 24       | RULE_03  | Syntax Recovery                          | ACTION_SYNTAX_RECOVERY |
| 25       | RULE_04  | Logic Recovery                           | ACTION_LOGIC_RECOVERY |
| 27       | RULE_07  | Critical Backtracking                    | ACTION_CRITICAL_BACKTRACKING |
| 30       | RULE_08  | Module Graduation                        | ACTION_MODULE_GRADUATION |
| 35       | RULE_16  | Mastery Medium                           | ACTION_STANDARD_PROMOTION |
| 36       | RULE_20  | Accelerated Material Promotion           | ACTION_ACCELERATED_MATERIAL_PROMOTION |
| 40       | RULE_06  | Accelerated Jump                         | ACTION_ACCELERATED_JUMP |
| 48       | RULE_17  | Remedial Independent                     | ACTION_LOGIC_RECOVERY |
| 50       | RULE_05  | Standard Promotion                       | ACTION_STANDARD_PROMOTION |

#### 4.2 Urutan Referensi (Berdasarkan Rule ID)

| Rule ID  | Priority | Nama Rule                                | Action Code |
| :------- | :------- | :--------------------------------------- | :---------- |
| RULE_01  | 10       | Visual Crisis Intervention               | ACTION_VISUAL_CRISIS_INTERVENTION |
| RULE_02  | 10       | Textual Crisis Intervention              | ACTION_TEXTUAL_CRISIS_INTERVENTION |
| RULE_03  | 24       | Syntax Recovery                          | ACTION_SYNTAX_RECOVERY |
| RULE_04  | 25       | Logic Recovery                           | ACTION_LOGIC_RECOVERY |
| RULE_05  | 50       | Standard Promotion                       | ACTION_STANDARD_PROMOTION |
| RULE_06  | 40       | Accelerated Jump                         | ACTION_ACCELERATED_JUMP |
| RULE_07  | 27       | Critical Backtracking                    | ACTION_CRITICAL_BACKTRACKING |
| RULE_08  | 30       | Module Graduation                        | ACTION_MODULE_GRADUATION |
| RULE_09  | 21       | Gold Certificate                         | ACTION_GOLD_CERTIFICATE |
| RULE_10  | 22       | Silver Certificate                       | ACTION_SILVER_CERTIFICATE |
| RULE_11  | 23       | Bronze Certificate                       | ACTION_BRONZE_CERTIFICATE |
| RULE_12  | 15       | Visual Project Revision                  | ACTION_VISUAL_PROJECT_REVISION |
| RULE_13  | 15       | Textual Project Revision                 | ACTION_TEXTUAL_PROJECT_REVISION |
| RULE_14  | 5        | Persistent Visual Safety Net             | ACTION_PERSISTENT_VISUAL_NET |
| RULE_15  | 5        | Persistent Textual Safety Net            | ACTION_PERSISTENT_TEXTUAL_NET |
| RULE_16  | 35       | Mastery Medium                           | ACTION_STANDARD_PROMOTION |
| RULE_17  | 48       | Remedial Independent                     | ACTION_LOGIC_RECOVERY |
| RULE_18  | 3        | Final Project Visual Persistent Fail     | ACTION_VISUAL_PROJECT_REVISION |
| RULE_19  | 3        | Final Project Textual Persistent Fail    | ACTION_TEXTUAL_PROJECT_REVISION |
| RULE_20  | 36       | Accelerated Material Promotion           | ACTION_ACCELERATED_MATERIAL_PROMOTION |

### 5. Bagaimana Sistem Bertindak Dalam Berbagai Kondisi

Berikut ringkasan perilaku sistem pada kondisi-kondisi penting:

1. Kondisi krisis awal (Beginner + skor kritis)
     - Jika gaya belajar visual, sistem cenderung masuk intervensi visual.
     - Jika gaya belajar textual, sistem cenderung masuk intervensi textual.

2. Kondisi gagal berulang (persistent fail)
     - Rule safety net mendapat prioritas tinggi agar siswa diarahkan ke review materi lebih komprehensif.
     - Pada final project, ada jalur khusus persistent fail berbasis gaya belajar.

3. Kondisi remedial dengan hint pada medium
     - Error syntax memicu recovery syntax.
     - Error logic memicu recovery logic.

4. Kondisi remedial tanpa hint
     - Sistem mengarahkan ke remedial mandiri (jalur mixed review) melalui rule remedial independent.

5. Kondisi performa sangat baik di medium
     - Jika mastery + fast + tanpa hint, sistem mendorong progres ke level lebih menantang.

6. Kondisi performa sangat baik di beginner
     - Jika mastery + fast + tanpa hint, rule accelerated jump dapat aktif.
     - Guard anti-loop mencegah percepatan level dipicu berulang pada materi yang sama setelah target difficulty tercapai.

7. Kondisi performa sangat baik + materi berikutnya sudah terbuka
     - Rule accelerated material promotion dapat loncat ke materi berikutnya.
     - Guard anti-loop mencegah loncat materi berulang pada materi yang sama.

8. Kondisi final project
     - Jalur sertifikat ditentukan oleh kualitas performa, hint usage, dan progress material (`G26`).
     - Jalur revisi proyek aktif jika performa belum memadai.

9. Kondisi tidak ada rule cocok
     - Sistem fallback ke `NEXT_QUESTION` agar flow pembelajaran tetap berjalan.

### 6. Perbedaan User Login dan Guest

- User login:
    - State disimpan di database (`student_states`, `quiz_attempts`).
    - Riwayat dan adaptasi bersifat persisten lintas sesi.
- Guest:
    - Progress dan state disimpan pada cookie.
    - Tetap menggunakan pipeline fakta dan evaluasi rule yang sama, tetapi persistence terbatas pada cookie browser.

### 7. Catatan Implementasi Penting

- Mesin rule saat ini fully registered: semua file rule yang ada sudah masuk `RuleRegistry`.
- Ada fakta yang sudah diproduksi tetapi belum dipakai rule secara langsung (`FACT_PREV_UNLOCKED`, `FACT_STYLE_MIXED`).
    - Ini bukan bug kritis, namun bisa menjadi kandidat pengembangan rule berikutnya.
- Rule order sangat menentukan hasil karena engine memakai `first match`.
    - Saat menambah rule baru, selalu pertimbangkan efek prioritas terhadap rule lama.
    - Update terbaru memisahkan prioritas `RULE_20` ke 36 (sebelumnya setara dengan `RULE_16`) agar konflik evaluasi antar rule percepatan berkurang.

Untuk referensi teknis lebih detail (narasi panjang per kategori), lihat dokumen: `docs/rule-based-adaptive-lengkap.md`.

## 🛠️ Instalasi

Pastikan Anda memiliki PHP >= 8.2, Node.js, dan Composer yang terinstal.

1. **Clone Repository**

    ```bash
    git clone https://github.com/rey-workbench/oopedia.git
    ```

2. **Instal Dependensi PHP**

    ```bash
    composer install
    ```

3. **Instal Dependensi Frontend**

    ```bash
    pnpm install
    ```

4. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Migrasi & Seed Database**

    ```bash
    php artisan migrate --seed
    ```

6. **Jalankan Aplikasi**
   Buka dua terminal terpisah:

    ```bash
    # Terminal 1: Backend
    php artisan serve

    # Terminal 2: Frontend
    pnpm run dev
    ```

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan akademik (Skripsi) dan berlisensi MIT.
