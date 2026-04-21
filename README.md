# Oopedia.com - Platform Pembelajaran Interaktif

Oopedia platform e-learning modern. Pengalaman interaktif + personal. Stack modern, performa optimal.

## 🚀 Tech Stack
- **Backend:** Laravel 12.x
- **Frontend:** Svelte 5 + Inertia.js
- **Styling:** Tailwind CSS 4
- **Database:** MySQL
- **Build Tool:** Vite

## ✨ Fitur Utama
- **Adaptive Learning Engine:** Sesuaikan materi/soal berdasarkan kemampuan user.
- **Inertia.js Integration:** SPA tanpa API terpisah.
- **Svelte Components:** Interface ringan, cepat, reaktif.
- **Dashboards:** Admin + Student.

## Alur Adaptive Engine
Mesin adaptif forward chaining bereaksi pada kondisi belajar.

### 1. Alur Jawab Soal
1. Entrypoint: `AdaptiveQuizFlowService`.
2. Validasi ID, hitung: Benar (`is_correct`), Waktu (`time_spent`), Hint (`used_hint`), Skor.
3. Update gamifikasi, simpan attempt (DB/cookie).
4. `FactGatheringService` bentuk fakta (`Gxx`).
5. `AdaptiveEngineService` evaluasi rule (prioritas).
6. First match policy: Rule cocok eksekusi, stop evaluasi.
7. `NextActionResolverService` ubah rule jadi aksi (Lanjut, Review, Jump, Sertifikat).

### 2. Fakta (G-Code)
GCode dari `AdaptiveConstants`.
| Kode | Constant | Keterangan |
| :--- | :--- | :--- |
| G01 | SCORE_CRITICAL | < 50% |
| G02 | SCORE_REMEDIAL | 50-74% |
| G03 | SCORE_STANDARD | 75-89% |
| G04 | SCORE_MASTERY | >= 90% |
| G05 | TIME_FAST | < 70% alokasi |
... (Keep full G-codes table) ...
| G26 | SATISFACTORY | Progres OK |

### 3. Aksi (H-Code)
| Kode | Constant | Aksi |
| :--- | :--- | :--- |
| H01 | VISUAL_CRISIS | Intervensi visual |
| H05 | STANDARD_PROMOTION | Lanjut soal |
| H06 | ACCELERATED_JUMP | Lompati level |
| H08 | MODULE_GRADUATION | Selesai modul |
... (Keep full H-codes table) ...

## 🏗️ Aturan Adaptif Aktif

### Kategori Aturan:
1. **Progression (Hampir Selalu):** StandardPromo, Jump, Graduation, MasteryMed.
2. **Project:** Textual/Visual/Mixed Revision.
3. **Safety Net:** Fail safety (Net), Crisis Intervention.
4. **Recovery:** Syntax/Logic Recovery.
5. **Achievement:** Certificates (Gold/Silver/Bronze).

## 🛠️ Instalasi
```bash
composer install
pnpm install
cp .env.example .env
php artisan key:generate
# Configure DB in .env
php artisan migrate --seed
pnpm run dev # or pnpm run build
php artisan serve
```

## 📊 UEQ (User Experience Questionnaire)
Survey 26 item untuk ukur pengalaman user.
Analisis dashboard Admin: Daya Tarik, Kejelasan, Efisiensi, Ketepatan, Stimulasi, Kebaruan.
