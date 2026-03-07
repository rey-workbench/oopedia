# Analisis & Perbaikan Rule-Based Adaptive Quiz System — OOPedia

> **Tanggal**: 7 Maret 2026  
> **Branch**: `v2`  
> **Update Terakhir**: Sesi 2 — Analisis Sinkronisasi Lintas Modul

---

## 1. Arsitektur & Alur Kerja

### 1.1 Komponen Utama

| Layer        | Komponen                         | Tanggung Jawab                                     |
|--------------|----------------------------------|-----------------------------------------------------|
| Repository   | `ProgressRepository`             | Akses data quiz_attempts, student_states             |
| Repository   | `QuizAttemptRepository`          | CRUD quiz_attempts                                   |
| Repository   | `QuestionRepository`             | Akses data questions                                 |
| Service      | `PerformanceService`             | Personalisasi: learning style, score, fatigue        |
| Service      | `GamificationService`            | XP, streak, leveling (unified)                       |
| Service      | `FactGatheringService`           | Mengumpulkan fakta G01-G27                           |
| Service      | `AdaptiveEngineService`          | Forward chaining evaluation                          |
| Service      | `AdaptiveQuizFlowService`        | **Orchestrator utama** adaptive quiz                 |
| Service      | `NextActionResolverService`      | Menerjemahkan H-code → aksi konkret (URL/redirect)  |
| Rules        | `RuleRegistry`                   | Registrasi & prioritas semua adaptive rules          |
| Rules        | `BaseAdaptiveRule` + subclasses  | Evaluasi & apply individual rules                    |
| Controller   | `MaterialQuestionController`     | Handle HTTP request untuk quiz                       |
| View         | Inertia/Svelte pages             | UI quiz, feedback, navigation                        |

### 1.2 Alur Request (End-to-End)

```
[View/Inertia] → POST /mahasiswa/quiz/{material}/{question}/check
                    │
                    ▼
   MaterialQuestionController::checkAnswer()
                    │
      ┌─────────── isGuest? ───────────┐
      │ YES                            │ NO
      ▼                                ▼
   QuestionAnswerService        AdaptiveQuizFlowService
   (simple check)               ::processAdaptiveAttempt()
      │                                │
      ▼                         ┌──────┴──────────────────┐
   JSON response               ▼                         ▼
                    PerformanceService        QuestionAnswerService
                    (update student state)    (determine correctness)
                         │                         │
                         ▼                         ▼
                    GamificationService     FactGatheringService
                    (XP, streak, level)     (gather G01-G27 facts)
                         │                         │
                         ▼                         ▼
                    ProgressRepository      AdaptiveEngineService
                    (quiz_attempts,         (forward chaining rules)
                     student_states)               │
                                                   ▼
                                          NextActionResolverService
                                          (resolve H-code → URL)
                                                   │
                                                   ▼
                                            JSON Response → View
```

### 1.3 Flow Detail: `processAdaptiveAttempt()`

```
1.   Determine correctness               → QuestionAnswerService
1.5  Update student performance counters  → PerformanceService::updateStudentPerformance()
1.5  Update learning style (real-time)    → PerformanceService::updateLearningStyleFromInteraction()
2.   Calculate nuanced score (0-100)      → PerformanceService::calculateScore()
3.   Calculate XP rewards                 → GamificationService::calculateCorrectAnswerReward()
3.5  Apply gamification (XP+streak+level) → applyGamificationRewards() [atomic]
4.   Save quiz attempt to DB              → ProgressRepository::saveProgress()
4.5  Invalidate dashboard caches          → Cache::forget()
5.   Gather facts G01-G27                 → FactGatheringService::gatherFacts()
6.   Evaluate rules (forward chaining)    → AdaptiveEngineService::evaluate()
7.   Apply adaptive state changes         → StudentState::adaptive_state
8.   Resolve next action → URL            → NextActionResolverService::resolve()
9.   Return JSON response to View
```

### 1.4 Skor Kalkulasi (PerformanceService::calculateScore)

```
Base Score = 80

+ Difficulty Bonus:
  └─ hard   → +10
  └─ medium → +5
  └─ beginner → +0

+ Time Bonus (G05: jawab < 50% allocated time):
  └─ beginner: < 22.5s → +10
  └─ medium:   < 45s   → +10
  └─ hard:     < 75s   → +10

- Hint Penalty:
  └─ used_hint → -20

Final Score = max(0, min(100, score))

Contoh:
  benar + hard + cepat + no hint = 80+10+10 = 100
  benar + beginner + lambat + hint = 80+0+0-20 = 60
  salah = 0
```

### 1.5 Registered Rules (Priority Order)

| Priority | Rule ID  | Nama                       | Kondisi                                      | Aksi (H-code)   |
|----------|----------|----------------------------|----------------------------------------------|------------------|
| 5        | RULE_14  | PersistentVisualSafetyNet  | G22 AND G07 AND G01                          | H14              |
| 5        | RULE_15  | PersistentTextualSafetyNet | G22 AND G08 AND G01                          | H15              |
| 10       | RULE_01  | VisualCrisisIntervention   | G01 AND G07 AND G15                          | H01              |
| 10       | RULE_02  | TextualRemediation         | G01 AND G08 AND G15                          | H02              |
| 15       | RULE_12  | VisualProjectRevision      | G01 AND G07 AND G18                          | H12              |
| 15       | RULE_13  | TextualProjectRevision     | G01 AND G08 AND G18                          | H13              |
| 21       | RULE_09  | GoldCertificate            | G04 AND G17 AND G26                          | H09              |
| 22       | RULE_10  | SilverCertificate          | G03 AND G17 AND G26                          | H10              |
| 23       | RULE_11  | BronzeCertificate          | G02 AND G17 AND G26                          | H11              |
| 24       | RULE_03  | SyntaxRecovery             | G02 AND G09 AND G16 AND G12                 | H03              |
| 25       | RULE_04  | LogicRecovery              | G02 AND G10 AND G16 AND G12                 | H04              |
| 27       | RULE_07  | CriticalBacktracking       | G01 AND (G15 OR G16 OR G17)                 | H07              |
| 30       | RULE_08  | ModuleGraduation           | G04 AND G26 AND (G13-G25 module fact)        | H08              |
| 35       | RULE_16  | MasteryMedium              | G04 AND G16 AND G05                          | H05 (promote)    |
| 40       | RULE_06  | AcceleratedJump            | G04 AND G05 AND G11 AND G26                 | H06              |
| 48       | RULE_17  | RemedialIndependent        | G02 AND G11                                  | H04 (study mixed)|
| 50       | RULE_05  | StandardPromotion          | (G03 OR G04) AND (G15 OR G16 OR G17)        | H05              |

### 1.6 Data Flow: StudentState (Single Source of Truth)

`StudentState` model menyimpan seluruh state mahasiswa dalam 4 JSON column:

```
student_states
├── gamification_data    → { global_xp, current_level, current_streak, max_streak, badges }
├── learning_profile     → { learning_style, mastery_levels, unlocked_modules }
├── performance_metrics  → { total_questions_answered, correct_count, wrong_count,
│                            wrong_streak, hints_used_count, hints_available }
└── adaptive_state       → { last_rule, last_action, fast_track_active,
                              target_difficulty, time_metrics }
```

Accessor pattern pada `StudentState` model memastikan akses transparan:
- `$state->learning_style` → reads from `learning_profile['learning_style']`
- `$state->unlocked_modules` → reads from `learning_profile['unlocked_modules']`
- `$state->global_xp` → reads from `gamification_data['global_xp']`
- `$state->current_streak` → reads from `gamification_data['current_streak']`
- dll.

---

## 2. Bug yang Ditemukan & Diperbaiki

### ✅ BUG-01 [CRITICAL]: `ProgressRepository::getUserProgressStats()` — Filter `is_correct` Salah

**File**: `app/Repositories/ProgressRepository.php`

**Masalah**:
```php
// SEBELUM (SALAH)
->where('quiz_attempts.is_correct', true)  // ← Filter HANYA jawaban benar
// → answered_questions SELALU = correct_answers (inflated 100%)
```

Query memfilter `is_correct = true` sebelum menghitung `answered_questions`. Akibatnya:
- `answered_questions` hanya menghitung soal yang pernah dijawab **benar**, bukan total soal yang dicoba
- `correct_answers` jadi redundan (selalu = answered_questions) karena sudah difilter
- **Dashboard menampilkan progress yang inflated** — mahasiswa terlihat selalu 100% akurat

**Perbaikan**:
```php
// SESUDAH (BENAR)
->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as answered_questions')
->selectRaw('COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1
             THEN quiz_attempts.question_id END) as correct_answers')
->where('quiz_attempts.user_id', $userId)
// Filter is_correct DIHAPUS — hitung semua attempt
->groupBy('questions.material_id')
```

**Dampak perbaikan**: Dashboard, material progress, dan `QuestionListingService::getMaterialsListWithStudentCount()` kini menampilkan data akurat.

---

### ✅ BUG-02 [HIGH]: `ProgressRepository::saveProgress()` — Score Nuanced Tidak Tersimpan

**File**: `app/Repositories/ProgressRepository.php`

**Masalah**:
```php
// SEBELUM (SALAH)
'score' => $data['score'] ?? ($data['is_correct'] ? 100 : 0),  // ← Selalu 100 atau 0
'time_spent' => 0,  // ← Hardcoded 0, lalu di-update terpisah
```

`AdaptiveQuizFlowService` mengirim score nuanced (60-100) di `$data['attributes']['score']`, bukan `$data['score']`. Sehingga `QuizAttempt` selalu menyimpan score binary 100/0. Score dari `calculateScore()` (mis. 70, 85, 90) **tidak pernah tersimpan** ke database.

Time_spent juga hardcoded 0, lalu di-update dalam query terpisah yang tidak perlu.

**Perbaikan**:
```php
// SESUDAH (BENAR)
'score'      => $data['attributes']['score'] ?? $data['score'] ?? ($data['is_correct'] ? 100 : 0),
'time_spent' => $data['attributes']['time_spent'] ?? $data['time_spent'] ?? 0,
```

Juga ditambahkan `DB::transaction()` + `lockForUpdate()` pada `attempt_number` untuk mencegah race condition.

---

### ✅ BUG-03 [MEDIUM]: `QuizAttemptRepository::create()` — Race Condition pada `attempt_number`

**File**: `app/Repositories/QuizAttemptRepository.php`

**Masalah**:
`countAttempts()` dan `create()` tidak dibungkus transaction. Dua request bersamaan bisa mendapat `attempt_number` yang sama.

**Perbaikan**:
```php
public function create(array $data): QuizAttempt
{
    return DB::transaction(function () use ($data) {
        if (! isset($data['attempt_number'])) {
            $data['attempt_number'] = QuizAttempt::where('user_id', $data['user_id'])
                ->where('question_id', $data['question_id'])
                ->lockForUpdate()
                ->count() + 1;
        }
        return QuizAttempt::create($data);
    });
}
```

---

### ✅ BUG-04 [MEDIUM]: `PerformanceService::updateLearningStyleFromInteraction()` — Division by Zero

**File**: `app/Services/User/PerformanceService.php`

**Masalah**:
```php
if ($totalTime === 0) {  // ← Strict comparison: 0.0 (float) !== 0 (int) → DIVISION BY ZERO
```

JSON decode bisa mengembalikan `float`. Jika `$totalTime = 0.0`, strict comparison `=== 0` false, lalu `abs(...) / $totalTime` → **Division by Zero**.

**Perbaikan**:
```php
if ($totalTime == 0) {  // Loose comparison: 0.0 == 0 → true → aman
```

---

### ✅ BUG-05 [HIGH]: `PerformanceService::markMaterialCompleted()` — Material ID vs Module ID

**File**: `app/Services/User/PerformanceService.php`

**Masalah**:
`markMaterialCompleted()` menyimpan `$materialId` ke `unlocked_modules[]`, tapi `FactGatheringService::getUnlockStatusFacts()` memeriksa `$nextMaterial->module_id` di array yang sama. Material ID ≠ Module ID!

```php
// SEBELUM (SALAH)
$completed[] = $materialId;  // Menyimpan material_id, bukan module_id
```

**Perbaikan**:
```php
// SESUDAH (BENAR)
$material = Material::find($materialId);
$moduleId = $material?->module_id ?? $materialId;
$completed[] = $moduleId;  // Sekarang konsisten dengan getUnlockStatusFacts()
```

**Dampak perbaikan**: Facts G19-G21 (unlock status) kini akurat, sehingga Achievement rules (ModuleGraduation, Certificate) bisa ter-trigger dengan benar.

---

### ✅ BUG-06 [MEDIUM]: Cache Tidak Di-invalidate Setelah Quiz Attempt

**File**: `app/Services/Adaptive/AdaptiveQuizFlowService.php`

**Masalah**:
Dashboard data di-cache 5 menit (`DashboardService`) tapi tidak ada cache invalidation setelah quiz attempt. Progress baru tidak muncul di dashboard.

**Perbaikan**: Ditambahkan cache invalidation setelah `saveProgress()`:
```php
Cache::forget("dashboard_index_{$userId}_false");
Cache::forget("dashboard_index_{$userId}_true");
Cache::forget("dashboard_inprogress_{$userId}_false");
Cache::forget("dashboard_inprogress_{$userId}_true");
Cache::forget("dashboard_completed_{$userId}_false");
Cache::forget("dashboard_completed_{$userId}_true");
```

---

### ✅ BUG-07 [LOW]: Docblock Tidak Sesuai Implementasi pada Rule Files

**Files**:
- `app/Rules/Adaptive/Recovery/LogicRecovery.php`
- `app/Rules/Adaptive/Recovery/SyntaxRecovery.php`
- `app/Rules/Adaptive/Progression/StandardPromotion.php`

**Masalah**: Docblock menyebut formula yang berbeda dari kode aktual.

| Rule              | Docblock (lama)                      | Implementasi (benar)                       |
|-------------------|---------------------------------------|--------------------------------------------|
| SyntaxRecovery    | IF (G02 AND G09 AND G14)             | IF (G02 AND G09 AND **G16** AND **G12**)   |
| LogicRecovery     | IF (G02 AND G10 AND G14)             | IF (G02 AND G10 AND **G16** AND **G12**)   |
| StandardPromotion | IF (G03 AND G11 AND (G15\|G16\|G17)) | IF ((**G03 OR G04**) AND (G15\|G16\|G17))  |

**Catatan**: G14 = `FACT_MODULE_ENCAPSULATION`, G16 = `FACT_DIFF_MEDIUM`. Implementasi benar menggunakan G16 (medium difficulty), bukan G14 (module encapsulation).

**Perbaikan**: Docblock di-update agar sesuai dengan implementasi aktual.

---

## 3. Hal yang Diklarifikasi (Bukan Bug)

### ❌ False Positive: Getter/Setter `learning_style` Inkonsisten

**Analisis sebelumnya** menyebut `setUserLearningStyle()` menulis ke `learning_profile['learning_style']` (JSON) tapi `getUserLearningStyle()` membaca dari `$state->learning_style` (kolom DB).

**Realitas**: `$state->learning_style` adalah **Laravel accessor** yang membaca dari `learning_profile['learning_style']`:
```php
// StudentState model
public function getLearningStyleAttribute()
{
    return $this->learning_profile['learning_style'] ?? 'visual';
}
```

Artinya getter dan setter **konsisten** — keduanya menggunakan `learning_profile['learning_style']` sebagai single source of truth. Accessor hanya menyediakan shortcut transparan.

### ❌ False Positive: Getter/Setter `unlocked_modules` Inkonsisten

Sama dengan di atas — `$state->unlocked_modules` adalah accessor ke `learning_profile['unlocked_modules']`. Konsisten.

### ❌ False Positive: Duplikasi Service Files

Tidak ditemukan file duplikat. `MaterialViewService` dan `QuestionListingService` masing-masing hanya ada satu versi di `app/Services/Lms/`.

### ❌ False Positive: `QuizRewardService` Duplikat

File `app/Services/Gamification/QuizRewardService.php` **tidak ada**. Sudah dikonsolidasi ke `GamificationService`.

---

## 4. Ringkasan Perbaikan

| # | Severity   | Komponen               | Masalah                                          | Status     |
|---|------------|------------------------|---------------------------------------------------|------------|
| 1 | 🔴 Critical | ProgressRepository     | `getUserProgressStats` filter inflated progress   | ✅ Fixed   |
| 2 | 🟠 High     | ProgressRepository     | `saveProgress` score nuanced tidak tersimpan      | ✅ Fixed   |
| 3 | 🟡 Medium   | QuizAttemptRepository  | Race condition pada `attempt_number`              | ✅ Fixed   |
| 4 | 🟡 Medium   | PerformanceService     | Division by zero pada strict comparison           | ✅ Fixed   |
| 5 | 🟠 High     | PerformanceService     | `markMaterialCompleted` material_id vs module_id  | ✅ Fixed   |
| 6 | 🟡 Medium   | AdaptiveQuizFlowService| Cache tidak di-invalidate setelah quiz attempt    | ✅ Fixed   |
| 7 | 🟢 Low      | Rule Files (3 files)   | Docblock tidak sesuai implementasi                | ✅ Fixed   |

---

## 5. Files yang Dimodifikasi

```
app/Repositories/ProgressRepository.php
  - getUserProgressStats(): hapus filter is_correct yang salah
  - saveProgress(): gunakan score & time_spent dari attributes
  - saveProgress(): tambah DB::transaction + lockForUpdate untuk attempt_number

app/Repositories/QuizAttemptRepository.php
  - create(): bungkus dalam DB::transaction + lockForUpdate

app/Services/User/PerformanceService.php
  - updateLearningStyleFromInteraction(): === 0 → == 0
  - markMaterialCompleted(): resolve module_id dari Material, bukan pakai materialId langsung

app/Services/Adaptive/AdaptiveQuizFlowService.php
  - tambah Cache import
  - tambah cache invalidation setelah saveProgress()
  - hapus redundant time_spent second save (sudah disimpan langsung via attributes)

app/Rules/Adaptive/Recovery/LogicRecovery.php
  - update docblock: IF (G02 AND G10 AND G16 AND G12) THEN H04

app/Rules/Adaptive/Recovery/SyntaxRecovery.php
  - update docblock: IF (G02 AND G09 AND G16 AND G12) THEN H03

app/Rules/Adaptive/Progression/StandardPromotion.php
  - update docblock: IF ((G03 OR G04) AND (G15 OR G16 OR G17)) THEN H05
```
