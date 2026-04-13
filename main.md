# Dokumentasi Proyek OOPedia

## Daftar Isi

1. [Pendahuluan](#pendahuluan)
2. [Struktur Proyek](#struktur-proyek)
3. [Arsitektur Sistem](#arsitektur-sistem)
4. [Fitur Utama](#fitur-utama)
5. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
6. [Sistem Adaptive Quiz (Forward Chaining)](#sistem-adaptive-quiz-forward-chaining)
7. [Model Data](#model-data)
8. [Layanan (Services)](#layanan-services)
9. [Panduan Instalasi](#panduan-instalasi)
10. [Panduan Penggunaan](#panduan-penggunaan)
11. [Aturan Adaptive (Rules)](#aturan-adaptive-rules)
12. [Kesimpulan](#kesimpulan)

---

## Pendahuluan

OOPedia adalah platform e-learning modern yang dikembangkan untuk kebutuhan skripsi. Platform ini dirancang untuk memberikan pengalaman belajar yang interaktif dan personal, khususnya dalam materi Pemrograman Berorientasi Objek (PBO) menggunakan bahasa pemrograman PHP.

Fitur utama dari proyek ini adalah **Adaptive Learning Engine** yang menggunakan metode **Forward Chaining** dan **Rule-Based System** untuk menyesuaikan kesulitan soal dan rekomendasi materi berdasarkan kemampuan pengguna secara real-time.

---

## Struktur Proyek

Proyek ini menggunakan arsitektur Laravel MVC dengan frontend Svelte dan Inertia.js.

```
oopedia/
├── app/
│   ├── Contracts/           # Interface untuk services dan repositories
│   ├── Http/
│   │   └── Controllers/    # Controller untuk Admin dan Mahasiswa
│   ├── Models/            # Model Eloquent
│   ├── Providers/         # Service Provider Laravel
│   ├── Repositories/      # Pattern Repository
│   ├── Rules/
│   │   └── Adaptive/      # Sistem aturan adaptive (Forward Chaining)
│   ├── Services/         # Business logic
│   └── Traits/           # Laravel Traits
├── database/
│   ├── migrations/        # Schema database
│   └── seeders/           # Data dummy
├── resources/
│   └── js/
│       ├── components/    # Komponen Svelte (UI, Quiz, Navigation)
│       ├── layouts/       # Layout aplikasi
│       └── pages/        # Halaman berdasarkan role (Admin, Mahasiswa)
├── routes/                # Definisi route
├── tests/                 # Unit testing
├── vite.config.ts        # Konfigurasi Vite
└── docker-compose.yml    # Konfigurasi Docker
```

---

## Arsitektur Sistem

### 1. Backend (Laravel 12)

Backend dibangun dengan Laravel 12 menggunakan pola-pola berikut:

- **Repository Pattern**: Untuk akses data yang terorganisir
- **Service Pattern**: Untuk business logic yang terpisah dari controller
- **Rule-Based System**: Untuk logika adaptive learning

### 2. Frontend (Svelte 5 + Inertia.js)

Frontend menggunakan Svelte 5 dengan Inertia.js untuk menghasilkan pengalaman Single Page Application (SPA) tanpa perlu membuat API terpisah.

- **Components**: UI components yang reusable (Button, Card, Modal, dll)
- **Quiz Components**: Komponen khusus untuk berbagai tipe soal
- **Pages**: Halaman untuk Admin dan Mahasiswa

### 3. Database

Database menggunakan MySQL dengan tabel-tabel utama:

- `users` - Data pengguna (admin dan mahasiswa)
- `roles` - Peran pengguna
- `materials` - Materi pembelajaran
- `sub_materials` - Sub materi
- `questions` - Bank soal
- `answers` - Jawaban user
- `progress` - Progres belajar
- `quiz_attempts` - Riwayat attempt quiz
- `student_states` - State mahasiswa untuk adaptive
- `ueq_surveys` - User Experience Questionnaire

---

## Fitur Utama

### 1. Adaptive Learning Engine

Sistem cerdas yang menyesuaikan:

- Tingkat kesulitan soal (Beginner, Medium, Hard, Final)
- Rekomendasi materi (Review atau Lanjut)
- Jenis intervensi (Visual atau Textual)

### 2. Sistem Kuis Interaktif

Mendukung berbagai tipe soal:

- Multiple Choice
- Fill in the Blank
- Drag and Drop

### 3. Gamification

Sistem penghargaan:

- **Streak**: Bonus hari berturut-turut belajar
- **Leveling**: Sistem level berdasarkan XP
- **Leaderboard**: Papan peringkat mahasiswa
- **Certificates**: Sertifikat (Gold, Silver, Bronze)

### 4. Dashboard

#### Admin Dashboard

- Statistik keseluruhan
- Kelola pengguna
- Kelola materi dan soal
- Import data mahasiswa
- Lihat hasil survey UEQ

#### Mahasiswa Dashboard

- Materi yang sedang dipelajari
- Progres penyelesaian
- Riwayat kuis
- Leaderboard

### 5. UEQ (User Experience Questionnaire)

Survey untuk mengukur pengalaman pengguna terhadap sistem adaptive learning.

---

## Teknologi yang Digunakan

| Komponen   | Teknologi           |
| ---------- | ------------------- |
| Backend    | Laravel 12.x        |
| Frontend   | Svelte 5            |
| Styling    | Tailwind CSS 4      |
| Database   | MySQL               |
| Server     | RoadRunner (Octane) |
| Build Tool | Vite                |

### Dependencies PHP

```json
"php": "^8.2",
"laravel/framework": "^12.0",
"inertiajs/inertia-laravel": "^2.0",
"laravel/sanctum": "^4.0",
"predis/predis": "^3.4",
"laravel/octane": "^2.14"
```

### Dependencies Node.js

```json
"svelte": "^5.53.6",
"@inertiajs/svelte": "^2.3.17",
"tailwindcss": "^4.2.1",
"vite": "^7.3.1",
"apexcharts": "^5.8.1",
"quill": "^2.0.3"
```

---

## Sistem Adaptive Quiz (Forward Chaining)

Ini adalah inti dari proyek skripsi ini. Sistem menggunakan **Forward Chaining** (penalaran maju) untuk menentukan aksi adaptive.

### 1. Alur Kerja Sistem

```
1. User menjawab soal
   ↓
2. FactGatheringService收集 fakta (G01-G26)
   ↓
3. AdaptiveEngineService evaluasi fakta dengan forward chaining
   ↓
4. Rule dengan priority tertinggi yang terpencils执行
   ↓
5. Apply action ke student state
   ↓
6. Return next question / review material
```

### 2. Facts (Fakta - Kode G)

Fakta adalah kondisi-kondisi yang dikumpulkan dari state mahasiswa dan konteks soal.

| Kode | Nama                  | Deskripsi                  |
| ---- | --------------------- | -------------------------- |
| G01  | Score Critical        | Skor < 40                  |
| G02  | Score Remedial        | Skor 40-69                 |
| G03  | Score Standard        | Skor 70-89                 |
| G04  | Score Mastery         | Skor 90-100                |
| G05  | Time Fast             | Waktu < 50% alokasi        |
| G06  | Time Normal           | Waktu >= 50% alokasi       |
| G07  | Style Visual          | Gaya belajar visual        |
| G08  | Style Textual         | Gaya belajar tekstual      |
| G09  | Error Syntax          | Kesalahan sintaksis        |
| G10  | Error Logic           | Kesalahan logika           |
| G11  | Hint None             | Tanpa menggunakan hint     |
| G12  | Hint Used             | Menggunakan hint           |
| G13  | Module Foundation     | Modul Dasar PBO            |
| G14  | Module Encapsulation  | Modul Enkapsulasi          |
| G15  | Diff Beginner         | Tingkat Easy               |
| G16  | Diff Medium           | Tingkat Medium             |
| G17  | Diff Hard             | Tingkat Advanced           |
| G18  | Final Project         | Soal proyek akhir          |
| G19  | Next Locked           | Materi berikutnya terkunci |
| G20  | Next Unlocked         | Materi berikutnya terbuka  |
| G21  | Prev Unlocked         | Materi sebelumnya terbuka  |
| G22  | Persistent Fail       | Gagal >= 3x berturut-turut |
| G26  | Satisfactory Progress | Progres >= 60%             |

### 3. Actions (Aksi - Kode H)

Aksi adalah respons yang diberikan sistem berdasarkan fakta yang terpenuhi.

| Kode | Nama                       | Deskripsi                          |
| ---- | -------------------------- | ---------------------------------- |
| H01  | Visual Crisis Intervention | Intervensi krisis mode visual      |
| H02  | Textual Remediation        | Remediasi mode teks                |
| H03  | Syntax Recovery            | Pemulihan materi sintaks           |
| H04  | Logic Recovery             | Pemulihan materi logika            |
| H05  | Standard Promotion         | Promo standar ke soal berikutnya   |
| H06  | Accelerated Jump           | Loncatan akselerasi (lewati level) |
| H07  | Critical Backtracking      | Mundur ke materi dasar             |
| H08  | Module Graduation          | Lulus modul                        |
| H09  | Gold Certificate           | Sertifikat emas                    |
| H10  | Silver Certificate         | Sertifikat perak                   |
| H11  | Bronze Certificate         | Sertifikat perunggu                |
| H12  | Visual Project Revision    | Revisi proyek mode visual          |
| H13  | Textual Project Revision   | Revisi proyek mode teks            |
| H14  | Persistent Visual Net      | Safety net visual (gagal berulang) |
| H15  | Persistent Textual Net     | Safety net teks (gagal berulang)   |

### 4. Implementasi Forward Chaining

#### FactGatheringService

Service ini bertanggung jawab mengumpulkan fakta dari:

- State mahasiswa (`StudentState`)
- Hasil jawaban soal
- Konfigurasi soal (difficult, type)
- Riwayat progress

```php
// Contoh pengumpulan fakta
public function gatherFacts(
    StudentState $studentState,
    bool $isCorrect,
    bool $usedHint,
    int $score,
    int $timeSpent,
    string $difficulty,
    int $questionId,
    int $materialId,
    ?int $moduleId = null,
): array {
    $facts = [];

    // Score facts (G01-G04)
    $facts = array_merge($facts, $this->getScoreFacts($score, $isCorrect));

    // Time facts (G05-G06)
    $facts = array_merge($facts, $this->getTimeFacts($timeSpent, $difficulty));

    // Learning style facts (G07-G08)
    $facts = array_merge($facts, $this->getLearningStyleFacts($studentState));

    // ... dst
}
```

#### AdaptiveEngineService

Service utama yang menjalankan forward chaining:

```php
public function evaluate(
    array $facts,
    array $currentState,
    array $context,
): array {
    $triggeredRule = null;
    $newState = $currentState;

    // Forward chaining: cari rule pertama yang terpenuhi
    foreach ($this->ruleRegistry->getAllRules() as $rule) {
        if ($rule->evaluate($facts)) {
            $triggeredRule = $rule;
            $newState = $rule->apply($newState, $context);
            break; // First match wins (priority-based)
        }
    }

    return [
        'triggered_rule' => $triggeredRule,
        'new_state' => $newState,
        'facts' => $facts,
    ];
}
```

### 5. Contoh Aturan Adaptive

#### Rule 01: Visual Crisis Intervention

```php
// IF (G01 AND G07 AND G15 AND NOT G22) THEN H01
class VisualCrisisIntervention extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_01';
    protected string $actionCode = 'H01';
    protected int $priority = 10;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            'G01', // Score Critical
            'G07', // Style Visual
            'G15', // Diff Beginner
        ]) && $this->notHasFact($facts, 'G22'); // Not Persistent Fail
    }

    public function apply(array $state, array $context): array
    {
        return [
            'recommendation' => 'Ulas Materi',
            'next_action' => 'STUDY_VISUAL',
            'message' => 'Performa Anda menurun. Mari ulas kembali materi...',
        ];
    }
}
```

#### Rule 05: Standard Promotion

```php
// IF (G03 AND G11 AND (G15 OR G16)) THEN H05
class StandardPromotion extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_05';
    protected string $actionCode = 'H05';
    protected int $priority = 50;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            'G03', // Score Standard
            'G11', // Hint None
        ]) && $this->hasAnyFact($facts, ['G15', 'G16']); // Beginner or Medium
    }
}
```

---

## Model Data

### User Model

```php
class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function studentState()
    {
        return $this->hasOne(StudentState::class);
    }
}
```

### StudentState Model

Menyimpan state mahasiswa untuk sistem adaptive:

```php
class StudentState extends Model
{
    protected $fillable = [
        'user_id',
        'learning_style',      // 'visual' atau 'textual'
        'current_level',       // Level saat ini
        'xp',                  // Total XP
        'streak_days',         // Hari streak
        'unlocked_modules',    // Array module yang sudah dibuka
        'certificates',        // Array sertifikat yang diperoleh
    ];
}
```

### Question Model

```php
class Question extends Model
{
    protected $fillable = [
        'material_id',
        'type',           // 'teori', 'sintaks', 'praktik'
        'difficulty',     // 'beginner', 'medium', 'hard', 'final'
        'question',
        'options',        // JSON untuk pilihan ganda
        'correct_answer',
        'explanation',
    ];
}
```

---

## Layanan (Services)

### Adaptive Services

| Service                     | Deskripsi                             |
| --------------------------- | ------------------------------------- |
| `AdaptiveEngineService`     | Engine utama untuk evaluasi rules     |
| `FactGatheringService`      | Mengumpulkan fakta dari student state |
| `NextActionResolverService` | Menentukan aksi selanjutnya           |
| `AdaptiveQuizFlowService`   | Mengatur alur kuis adaptive           |

### Gamification Services

| Service             | Deskripsi                      |
| ------------------- | ------------------------------ |
| `StreakService`     | Mengelola streak harian        |
| `LevelingService`   | Mengelola level berdasarkan XP |
| `QuizRewardService` | Menghitung reward dari kuis    |

### Analytics Services

| Service              | Deskripsi                   |
| -------------------- | --------------------------- |
| `UeqSurveyService`   | Mengelola survey UEQ        |
| `PerformanceService` | Analisis performa mahasiswa |

---

## Panduan Instalasi

### Prerequisites

- PHP >= 8.2
- Node.js >= 18
- Composer
- MySQL

### Langkah Instalasi

1. **Clone Repository**

```bash
git clone https://github.com/rey-workbench/oopedia.git
cd oopedia
```

2. **Instal Dependencies PHP**

```bash
composer install
```

3. **Instal Dependencies Frontend**

```bash
pnpm install
```

4. **Konfigurasi Environment**

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oopedia
DB_USERNAME=root
DB_PASSWORD=
```

5. **Migrasi dan Seed Database**

```bash
php artisan migrate --seed
```

6. **Jalankan Aplikasi**

Buka dua terminal:

```bash
# Terminal 1: Backend
php artisan serve

# Terminal 2: Frontend
pnpm run dev
```

### Menggunakan Docker

```bash
docker-compose up -d
```

---

## Panduan Penggunaan

### Untuk Admin

1. Login sebagai admin
2. Kelola pengguna (Create, Edit, Import)
3. Kelola materi pembelajaran
4. Kelola bank soal
5. Lihat statistik dashboard
6. Lihat hasil survey UEQ

### Untuk Mahasiswa

1. Login sebagai mahasiswa
2. Lihat dashboard materi
3. Mulai belajar materi
4. Jawab soal kuis adaptive
5. Dapatkan feedback dan rekomendasi
6. Kumpulkan XP dan naik level
7. Puncaki leaderboard

---

## Aturan Adaptive (Rules)

### Kategori Rules

#### 1. Crisis Rules (Priority 10-15)

Aturan untuk menangani kondisi krisis mahasiswa:

- `VisualCrisisIntervention` - Intervensi krisis mode visual
- `TextualRemediation` - Remediasi mode teks
- `VisualProjectRevision` - Revisi proyek visual
- `TextualProjectRevision` - Revisi proyek teks
- `PersistentVisualSafetyNet` - Safety net visual
- `PersistentTextualSafetyNet` - Safety net teks

#### 2. Recovery Rules (Priority 20)

Aturan untuk pemulihan:

- `SyntaxRecovery` - Pemulihan sintaks
- `LogicRecovery` - Pemulihan logika

#### 3. Achievement Rules (Priority 20-30)

Aturan untuk pencapaian:

- `ModuleGraduation` - Kelulusan modul
- `GoldCertificate` - Sertifikat emas
- `SilverCertificate` - Sertifikat perak
- `BronzeCertificate` - Sertifikat perunggu

#### 4. Progression Rules (Priority 25-50)

Aturan untuk progressions:

- `StandardPromotion` - Promo standar
- `AcceleratedJump` - Loncatan akselerasi
- `CriticalBacktracking` - Mundur kritis

---

## Kesimpulan

OOPedia adalah platform e-learning yang mengimplementasikan sistem adaptive learning menggunakan metode Forward Chaining dan Rule-Based System. Sistem ini mampu:

1. **Menyesuaikan Tingkat Kesulitan**: Berdasarkan performa mahasiswa
2. **Memberikan Rekomendasi**: Materi review atau lanjut
3. **Mendeteksi Krisis**: Intervensi ketika performa menurun drastis
4. **Memberikan Reward**: Sistem gamification untuk motivasi

Proyek ini dikembangkan sebagai skripsi dengan fokus pada implementasi ITS (Intelligent Tutoring System) dalam konteks pembelajaran Pemrograman Berorientasi Objek.
