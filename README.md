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

Fakta dibentuk setiap submit jawaban. Berikut daftar lengkap G-Code dari `AdaptiveConstants` menurut implementasi terbaru:

| Kode | Constant                   | Keterangan Singkat                                             |
| :--- | :------------------------- | :------------------------------------------------------------- |
| G01  | FACT_SCORE_CRITICAL        | Skor < 50%                                                     |
| G02  | FACT_SCORE_REMEDIAL        | Skor 50-74%                                                    |
| G03  | FACT_SCORE_STANDARD        | Skor 75-89%                                                    |
| G04  | FACT_SCORE_MASTERY         | Skor >= 90%                                                    |
| G05  | FACT_TIME_FAST             | Waktu berlalu sangat cepat (< 70% dari alokasi waktu)          |
| G06  | FACT_STYLE_VISUAL          | Gaya belajar visual                                            |
| G07  | FACT_STYLE_TEXTUAL         | Gaya belajar tekstual                                          |
| G08  | FACT_ERROR_SYNTAX          | Error tipe syntax                                              |
| G09  | FACT_ERROR_LOGIC           | Error tipe logic                                               |
| G10  | FACT_NO_ERROR              | Tidak ada error                                                |
| G11  | FACT_HINT_USED             | Menggunakan hint                                               |
| G12  | FACT_IN_MODULE             | Sedang berada di dalam modul                                   |
| G13  | FACT_DIFF_BEGINNER         | Difficulty `beginner`                                          |
| G14  | FACT_DIFF_MEDIUM           | Difficulty `medium`                                            |
| G15  | FACT_DIFF_HARD             | Difficulty `hard`                                              |
| G16  | FACT_IS_FINAL_PROJECT      | Soal adalah Final Project                                      |
| G17  | FACT_IS_PRACTICE           | Mode latihan                                                   |
| G18  | FACT_NEXT_UNLOCKED         | Modul/materi selanjutnya telah terbuka                         |
| G19  | FACT_PREV_UNLOCKED         | Modul/materi sebelumnya telah terbuka                          |
| G20  | FACT_PERSISTENT_FAIL       | Gagal berturut-turut (>= 2x salah beruntun)                    |
| G21  | FACT_SATISFACTORY_PROGRESS | Mencapai progress minim (>= 50% soal) pada difficulty tertentu |
| G22  | FACT_STYLE_MIXED           | Gaya belajar campuran                                          |

Daftar lengkap H-Code (Aksi) juga diperbarui:

| Kode | Constant                              | Keterangan Singkat                       |
| :--- | :------------------------------------ | :--------------------------------------- |
| H01  | ACTION_VISUAL_CRISIS_INTERVENTION     | Intervensi visual untuk skor kritis      |
| H02  | ACTION_TEXTUAL_CRISIS_INTERVENTION    | Intervensi textual untuk skor kritis     |
| H03  | ACTION_SYNTAX_RECOVERY                | Recovery jika terjadi error syntax terus |
| H04  | ACTION_LOGIC_RECOVERY                 | Recovery jika terjadi error logis terus  |
| H05  | ACTION_STANDARD_PROMOTION             | Promosi materi standar                   |
| H06  | ACTION_ACCELERATED_JUMP               | Percepatan (loncat difficulty internal)  |
| H07  | ACTION_CRITICAL_BACKTRACKING          | Mundur level (backtracking) otomatis     |
| H08  | ACTION_MODULE_GRADUATION              | Kelulusan dari sebuah modul penuh        |
| H09  | ACTION_GOLD_CERTIFICATE               | Reward sertifikat gold                   |
| H10  | ACTION_SILVER_CERTIFICATE             | Reward sertifikat silver                 |
| H11  | ACTION_BRONZE_CERTIFICATE             | Reward sertifikat bronze                 |
| H12  | ACTION_VISUAL_PROJECT_REVISION        | Mode revisi project visual               |
| H13  | ACTION_TEXTUAL_PROJECT_REVISION       | Mode revisi project textual              |
| H14  | ACTION_PERSISTENT_VISUAL_NET          | Jaring pengaman berulang visual          |
| H15  | ACTION_PERSISTENT_TEXTUAL_NET         | Jaring pengaman berulang textual         |
| H16  | ACTION_ACCELERATED_MATERIAL_PROMOTION | Promosi loncat ke materi berikutnya      |

Catatan: selain H-Code, engine juga memakai action operasional non-H seperti `NEXT_QUESTION`, `NEXT_MATERIAL`, `FINISH_MATERIAL`, `ISSUE_CERTIFICATE`, `REDUCE_DIFFICULTY`, `INCREASE_DIFFICULTY`, `STUDY_SYNTAX`, `STUDY_THEORY`, `STUDY_MIXED`, `STUDY_VISUAL`, dan `STUDY_TEXTUAL` pada tahap resolusi navigasi.

### 3. Aturan Evaluasi (Core Engine Behavior)

Mesin rule menggunakan prinsip berikut:

- Ada **20 Concrete Rules** yang aktif di-register dalam `RuleRegistry` yang mencakup kasus recovery, net-safety persisten, hingga kelulusan project.
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
- Urutan referensi: berdasarkan nama Class Rule.

_Jumlah total aktif: 20 rules._

#### 4.1 Urutan Eksekusi (Berdasarkan Priority)

| Priority | Rule Class                              | Detail Singkat & Output Aksi         |
| :------- | :-------------------------------------- | :----------------------------------- |
| 3        | `RuleFinalProjectVisualPersistentFail`  | H12 (Visual Project Revision)        |
| 3        | `RuleFinalProjectTextualPersistentFail` | H13 (Textual Project Revision)       |
| 5        | `RulePersistentVisualSafetyNet`         | H14 (Persistent Visual Net)          |
| 5        | `RulePersistentTextualSafetyNet`        | H15 (Persistent Textual Net)         |
| 10       | `RuleVisualCrisisIntervention`          | H01 (Visual Crisis Intervention)     |
| 10       | `RuleTextualCrisisIntervention`         | H02 (Textual Crisis Intervention)    |
| 15       | `RuleVisualProjectRevision`             | H12 (Visual Project Revision)        |
| 15       | `RuleTextualProjectRevision`            | H13 (Textual Project Revision)       |
| 21       | `RuleGoldCertificate`                   | H09 (Gold Certificate)               |
| 22       | `RuleSilverCertificate`                 | H10 (Silver Certificate)             |
| 23       | `RuleBronzeCertificate`                 | H11 (Bronze Certificate)             |
| 24       | `RuleSyntaxRecovery`                    | H03 (Syntax Recovery)                |
| 25       | `RuleLogicRecovery`                     | H04 (Logic Recovery)                 |
| 27       | `RuleCriticalBacktracking`              | H07 (Critical Backtracking)          |
| 30       | `RuleModuleGraduation`                  | H08 (Module Graduation)              |
| 35       | `RuleMasteryMedium`                     | H05 (Standard Promotion)             |
| 36       | `RuleAcceleratedMaterialPromotion`      | H16 (Accelerated Material Promotion) |
| 40       | `RuleAcceleratedJump`                   | H06 (Accelerated Jump)               |
| 48       | `RuleRemedialIndependent`               | H04 (Logic Recovery / Independent)   |
| 50       | `RuleStandardPromotion`                 | H05 (Standard Promotion)             |

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
