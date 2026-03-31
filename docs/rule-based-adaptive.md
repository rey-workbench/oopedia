# Rule-Based Adaptive Learning System - Forward Chaining

## 1. Overview

Sistem Adaptive Learning menggunakan **Forward Chaining** untuk menentukan tindakan yang tepat berdasarkan fakta-fakta yang dikumpulkan dari performa siswa. Forward Chaining berarti sistem memulai dari **fakta-fakta** yang diketahui, lalu **mencari rule yang cocok** secara berurutan berdasarkan prioritas.

## 2. Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         Adaptive Quiz Flow                                │
│                                                                          │
│  ┌─────────────┐    ┌─────────────────┐    ┌───────────────────────┐ │
│  │   Student   │───▶│  Fact Gathering │───▶│  Adaptive Engine       │ │
│  │   Answer    │    │    Service      │    │  (Forward Chaining)   │ │
│  └─────────────┘    └─────────────────┘    └───────────────────────┘ │
│                                                        │               │
│                                                        ▼               │
│  ┌───────────────────────────────────────────────────────────────────┐ │
│  │                         Rule Registry                              │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐           │ │
│  │  │ Crisis   │ │Recovery  │ │Progression│ │Achievement│           │ │
│  │  │ P: 3-15 │ │ P: 24-48│ │ P: 27-40 │ │ P: 21-30 │           │ │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘           │ │
│  └───────────────────────────────────────────────────────────────────┘ │
│                                                        │               │
│                                                        ▼               │
│  ┌─────────────┐    ┌─────────────────┐    ┌───────────────────────┐ │
│  │   Student   │◀───│   Next Action   │◀───│    Rule Applied       │ │
│  │    State    │    │   Resolver      │    │   (State Updated)     │ │
│  └─────────────┘    └─────────────────┘    └───────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────┘
```

## 3. Komponen Utama

### 3.1 StudentState Model

Menyimpan seluruh state learner dalam JSON fields:

| Field                 | Tipe | Deskripsi                                                      |
| --------------------- | ---- | -------------------------------------------------------------- |
| `learning_profile`    | JSON | Gaya belajar, mastery levels, unlocked modules, certifications |
| `adaptive_state`      | JSON | Fast track, target difficulty, module progress, time metrics   |
| `performance_metrics` | JSON | Total questions, correct/wrong count, wrong streak, hints      |
| `gamification_data`   | JSON | XP, level, badges, streaks                                     |

**Lokasi**: `app/Models/StudentState.php`

### 3.2 FactGatheringService

Mengumpulkan fakta (G-codes) berdasarkan kondisi siswa saat menjawab soal.

**Lokasi**: `app/Services/Adaptive/FactGatheringService.php`

### 3.3 AdaptiveEngineService

Orchestrator utama yang menjalankan forward chaining.

**Lokasi**: `app/Services/Adaptive/AdaptiveEngineService.php`

### 3.4 AdaptiveQuizFlowService

Koordinator utama yang orchestrate seluruh alur adaptive learning.

**Lokasi**: `app/Services/Adaptive/AdaptiveQuizFlowService.php`

### 3.5 NextActionResolverService

Meresolve action command menjadi URL dan metadata untuk frontend.

**Lokasi**: `app/Services/Adaptive/NextActionResolverService.php`

### 3.6 RuleRegistry

Register dan urutkan semua rules berdasarkan prioritas.

**Lokasi**: `app/Rules/Adaptive/RuleRegistry.php`

### 3.7 Adaptive Rules

21 concrete rules yang terbagi dalam 4 kategori:

| Kategori    | Jumlah Rules | Priority Range | Lokasi                            |
| ----------- | ------------ | -------------- | --------------------------------- |
| Crisis      | 8            | 3-15           | `app/Rules/Adaptive/Crisis/`      |
| Achievement | 4            | 21-30          | `app/Rules/Adaptive/Achievement/` |
| Recovery    | 3            | 24-48          | `app/Rules/Adaptive/Recovery/`    |
| Progression | 5            | 27-40          | `app/Rules/Adaptive/Progression/` |

## 4. Struktur Fakta (G-Codes)

Fakta dikumpulkan dari performa siswa dan digunakan sebagai kondisi/rules.

### 4.1 Fakta Performa (G01-G04)

| Kode | Nama           | Kondisi     |
| ---- | -------------- | ----------- |
| G01  | Score Critical | Skor < 50%  |
| G02  | Score Remedial | Skor 50-74% |
| G03  | Score Standard | Skor 75-89% |
| G04  | Score Mastery  | Skor >= 90% |

### 4.2 Fakta Waktu (G05)

| Kode | Nama      | Kondisi                                   |
| ---- | --------- | ----------------------------------------- |
| G05  | Time Fast | Waktu pengerjaan < 70% dari alokasi waktu |

### 4.3 Fakta Gaya Belajar (G07-G08, G27)

| Kode | Nama          | Kondisi               |
| ---- | ------------- | --------------------- |
| G07  | Visual Style  | Gaya belajar visual   |
| G08  | Textual Style | Gaya belajar tekstual |
| G27  | Mixed Style   | Gaya belajar campuran |

### 4.4 Fakta Error Type (G09-G10)

| Kode | Nama         | Kondisi                          |
| ---- | ------------ | -------------------------------- |
| G09  | Syntax Error | Kesalahan sintaksis pada jawaban |
| G10  | Logic Error  | Kesalahan logika pada jawaban    |

### 4.5 Fakta Hint (G12)

| Kode | Nama      | Kondisi                               |
| ---- | --------- | ------------------------------------- |
| G12  | Hint Used | Siswa menggunakan hint untuk menjawab |

### 4.6 Fakta Module (G13)

| Kode | Nama      | Kondisi                                |
| ---- | --------- | -------------------------------------- |
| G13  | In Module | Sedang berada dalam modul pembelajaran |

### 4.7 Fakta Difficulty (G15-G17)

| Kode | Nama     | Kondisi                    |
| ---- | -------- | -------------------------- |
| G15  | Beginner | Tingkat kesulitan easy     |
| G16  | Medium   | Tingkat kesulitan medium   |
| G17  | Hard     | Tingkat kesulitan advanced |

### 4.8 Fakta Special (G18-G22, G26)

| Kode | Nama                  | Kondisi                                   |
| ---- | --------------------- | ----------------------------------------- |
| G18  | Final Project         | Soal proyek akhir                         |
| G20  | Next Unlocked         | Materi berikutnya sudah terbuka           |
| G21  | Prev Unlocked         | Materi sebelumnya terbuka                 |
| G22  | Persistent Fail       | Gagal berulang >= 2x                      |
| G26  | Satisfactory Progress | Minimal 50% soal difficulty sudah dijawab |

## 5. Struktur Action (H-Codes)

Action yang dihasilkan oleh rules.

| Kode | Nama                           | Kategori    | Efek                             |
| ---- | ------------------------------ | ----------- | -------------------------------- |
| H01  | Visual Crisis Intervention     | Crisis      | Arahkan review materi visual     |
| H02  | Textual Crisis Intervention    | Crisis      | Arahkan review materi tekstual   |
| H03  | Syntax Recovery                | Recovery    | Pemulihan sintaksis              |
| H04  | Logic Recovery                 | Recovery    | Pemulihan logika                 |
| H05  | Standard Promotion             | Progression | Lanjut ke soal berikutnya        |
| H06  | Accelerated Jump               | Progression | Fast track untuk performa tinggi |
| H07  | Critical Backtracking          | Progression | Kembali ke materi dasar          |
| H08  | Module Graduation              | Achievement | Unlock modul berikutnya          |
| H09  | Gold Certificate               | Achievement | Sertifikat emas                  |
| H10  | Silver Certificate             | Achievement | Sertifikat perak                 |
| H11  | Bronze Certificate             | Achievement | Sertifikat perunggu              |
| H12  | Visual Project Revision        | Crisis      | Revisi proyek visual             |
| H13  | Textual Project Revision       | Crisis      | Revisi proyek tekstual           |
| H14  | Persistent Visual Net          | Crisis      | Safety net untuk visual learner  |
| H15  | Persistent Textual Net         | Crisis      | Safety net untuk textual learner |
| H16  | Accelerated Material Promotion | Progression | Loncatan akseleratif modul       |

## 6. Interface AdaptiveRule

Setiap rule mengimplementasi interface berikut:

```php
interface AdaptiveRuleInterface
{
    public function getRuleId(): string;        // ID unik rule
    public function getRuleName(): string;       // Nama human-readable
    public function getActionCode(): string;     // Kode action (H01-H16)
    public function getPriority(): int;         // Prioritas (lower = higher priority)

    public function evaluate(array $facts): bool;  // Kondisi rule
    public function apply(array $state, array $context): array;  // Aksi rule
}
```

## 7. BaseAdaptiveRule Helpers

Class dasar menyediakan helper methods untuk evaluasi kondisi:

```php
protected function hasFact(array $facts, string $fact): bool
protected function hasAllFacts(array $facts, array $requiredFacts): bool  // AND
protected function hasAnyFact(array $facts, array $requiredFacts): bool    // OR
protected function notHasFact(array $facts, string $fact): bool            // NOT
```

## 8. Konfigurasi Quiz

Konfigurasi waktu dan threshold terdapat di `AdaptiveConstants`:

```php
// Waktu alokasi per tingkat kesulitan (detik)
public const ALLOCATED_TIME = [
    'beginner' => 45,
    'medium' => 90,
    'hard' => 150,
    'final' => 300,
];

// Threshold cepat (<70% = G05)
public const TIME_FAST_THRESHOLD = 70;
```

## 9. Semua Concrete Rules

### 9.1 Crisis Rules (Priority 3-15)

#### RULE 18: Final Project Visual Persistent Fail

```
IF (G22 AND G07 AND G18) THEN H12
```

**Priority: 3** (Tertinggi)

Triggers ketika visual learner gagal >=2x pada Final Project.

```php
class FinalProjectVisualPersistentFail extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_18';
    protected int $priority = 3;

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, ['G01', 'G02'])
            && $this->hasFact($facts, 'G22')  // Persistent fail
            && $this->hasFact($facts, 'G07')  // Visual style
            && $this->hasFact($facts, 'G18'); // Final project
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'STUDY_VISUAL';
        $state['force_material_review'] = true;
        return $state;
    }
}
```

#### RULE 19: Final Project Textual Persistent Fail

```
IF (G22 AND G08 AND G18) THEN H13
```

**Priority: 3**

Sama dengan Visual, tapi untuk textual learner.

#### RULE 14: Persistent Visual Safety Net

```
IF ((G01 OR G02) AND G22 AND G07) THEN H14
```

**Priority: 5**

Safety net untuk visual learner yang gagal berulang.

#### RULE 15: Persistent Textual Safety Net

```
IF ((G01 OR G02) AND G22 AND G08) THEN H15
```

**Priority: 5**

Safety net untuk textual learner yang gagal berulang.

#### RULE 01: Visual Crisis Intervention

```
IF (G01 AND G07 AND G15 AND NOT G22 AND NOT G18) THEN H01
```

**Priority: 10**

Triggers ketika visual learner dengan skor kritis pada level beginner.

#### RULE 02: Textual Crisis Intervention

```
IF (G01 AND G08 AND G15 AND NOT G22 AND NOT G18) THEN H02
```

**Priority: 10**

Sama dengan Visual, tapi untuk textual learner.

#### RULE 12: Visual Project Revision

```
IF ((G01 OR G02) AND G07 AND G18) THEN H12
```

**Priority: 15**

Triggers ketika visual learner gagal pada Final Project.

#### RULE 13: Textual Project Revision

```
IF ((G01 OR G02) AND G08 AND G18) THEN H13
```

**Priority: 15**

Sama dengan Visual, tapi untuk textual learner.

---

### 9.2 Achievement Rules (Priority 21-30)

#### RULE 09: Gold Certificate

```
IF (G04 AND G05 AND NOT G12 AND G18 AND G26) THEN H09
```

**Priority: 21**

| Kondisi | Keterangan                 |
| ------- | -------------------------- |
| G04     | Skor Mastery (>=90%)       |
| G05     | Waktu cepat (<70% alokasi) |
| NOT G12 | Tidak pakai hint           |
| G18     | Final Project              |
| G26     | Progress memuaskan (>=50%) |

**apply()**: Memberikan Sertifikat Emas, badge 'gold_architect', module_progress = 100%

#### RULE 10: Silver Certificate

```
IF ((G03 OR G04) AND NOT G12 AND G18 AND G26) THEN H10
```

**Priority: 22**

| Kondisi | Keterangan                 |
| ------- | -------------------------- |
| G03/G04 | Skor Standard atau Mastery |
| NOT G12 | Tidak pakai hint           |
| G18     | Final Project              |
| G26     | Progress memuaskan         |

**apply()**: Memberikan Sertifikat Perak, badge 'silver_developer'

#### RULE 11: Bronze Certificate

```
IF ((G03 OR G04) AND G12 AND G18 AND G26) THEN H11
```

**Priority: 23**

| Kondisi | Keterangan                 |
| ------- | -------------------------- |
| G03/G04 | Skor Standard atau Mastery |
| G12     | Pakai hint                 |
| G18     | Final Project              |
| G26     | Progress memuaskan         |

**apply()**: Memberikan Sertifikat Perunggu, badge 'bronze_junior'

#### RULE 08: Module Graduation

```
IF (G04 AND G05 AND NOT G12 AND G17 AND G26 AND G13) THEN H08
```

**Priority: 30**

| Kondisi | Keterangan         |
| ------- | ------------------ |
| G04     | Skor Mastery       |
| G05     | Waktu cepat        |
| NOT G12 | Tidak pakai hint   |
| G17     | Level Hard         |
| G26     | Progress memuaskan |
| G13     | Dalam modul        |

**apply()**: `next_action = 'FINISH_MATERIAL'`, module_progress = 100%

---

### 9.3 Recovery Rules (Priority 24-48)

#### RULE 03: Syntax Recovery

```
IF (G02 AND G09 AND G16 AND G12) THEN H03
```

**Priority: 24**

| Kondisi | Keterangan             |
| ------- | ---------------------- |
| G02     | Skor Remedial (50-74%) |
| G09     | Error Sintaksis        |
| G16     | Level Medium           |
| G12     | Pakai hint             |

**apply()**: `next_action = 'STUDY_SYNTAX'`

#### RULE 04: Logic Recovery

```
IF (G02 AND G10 AND G16 AND G12) THEN H04
```

**Priority: 25**

| Kondisi | Keterangan    |
| ------- | ------------- |
| G02     | Skor Remedial |
| G10     | Error Logika  |
| G16     | Level Medium  |
| G12     | Pakai hint    |

**apply()**: `next_action = 'STUDY_THEORY'`

#### RULE 17: Remedial Independent

```
IF (G02 AND NOT G12 AND NOT G18) THEN STUDY_MIXED
```

**Priority: 48**

Triggers ketika siswa mendapat skor remedial tanpa hint (tidak bisa tentukan error type).

**apply()**: `next_action = 'STUDY_MIXED'`

---

### 9.4 Progression Rules (Priority 27-40)

#### RULE 07: Critical Backtracking

```
IF (G01 AND (G16 OR G17) AND NOT G22 AND NOT G18) THEN H07
```

**Priority: 27**

| Kondisi | Keterangan             |
| ------- | ---------------------- |
| G01     | Skor Kritis (<50%)     |
| G16/G17 | Level Medium atau Hard |
| NOT G22 | Belum gagal berulang   |
| NOT G18 | Bukan Final Project    |

**apply()**: `target_difficulty = 'beginner'`, `next_action = 'REDUCE_DIFFICULTY'`

#### RULE 16: Mastery Medium

```
IF (G04 AND G05 AND G16 AND NOT G12 AND NOT G18) THEN H05
```

**Priority: 35**

| Kondisi | Keterangan          |
| ------- | ------------------- |
| G04     | Skor Mastery        |
| G05     | Waktu cepat         |
| G16     | Level Medium        |
| NOT G12 | Tidak pakai hint    |
| NOT G18 | Bukan Final Project |

**apply()**: `fast_track_active = true`, `target_difficulty = 'hard'`

#### RULE 20: Accelerated Material Promotion

```
IF (G04 AND G05 AND G20 AND NOT G12 AND NOT G18) THEN H16
```

**Priority: 35**

| Kondisi | Keterangan                |
| ------- | ------------------------- |
| G04     | Skor Mastery              |
| G05     | Waktu cepat               |
| G20     | Materi berikutnya terbuka |
| NOT G12 | Tidak pakai hint          |
| NOT G18 | Bukan Final Project       |

**apply()**: `fast_track_active = true`, `next_action = 'NEXT_MATERIAL'`

#### RULE 06: Accelerated Jump

```
IF (G04 AND G05 AND G15 AND NOT G12 AND NOT G20 AND NOT G18) THEN H06
```

**Priority: 40**

| Kondisi | Keterangan                      |
| ------- | ------------------------------- |
| G04     | Skor Mastery                    |
| G05     | Waktu cepat                     |
| G15     | Level Beginner                  |
| NOT G12 | Tidak pakai hint                |
| NOT G20 | Materi berikutnya belum terbuka |
| NOT G18 | Bukan Final Project             |

**apply()**: `fast_track_active = true`, `target_difficulty = 'medium'`

#### RULE 05: Standard Promotion

```
IF ((G03 OR G04) AND (G15 OR G16 OR G17) AND NOT G18) THEN H05
```

**Priority: 50**

Catch-all rule untuk promosi normal.

**apply()**: `next_action = 'NEXT_QUESTION'`

## 10. Aturan Prioritas Lengkap

| Priority | Rule ID | Nama Rule                             | Kategori    |
| -------- | ------- | ------------------------------------- | ----------- |
| 3        | RULE_18 | Final Project Visual Persistent Fail  | Crisis      |
| 3        | RULE_19 | Final Project Textual Persistent Fail | Crisis      |
| 5        | RULE_14 | Persistent Visual Safety Net          | Crisis      |
| 5        | RULE_15 | Persistent Textual Safety Net         | Crisis      |
| 10       | RULE_01 | Visual Crisis Intervention            | Crisis      |
| 10       | RULE_02 | Textual Crisis Intervention           | Crisis      |
| 15       | RULE_12 | Visual Project Revision               | Crisis      |
| 15       | RULE_13 | Textual Project Revision              | Crisis      |
| 21       | RULE_09 | Gold Certificate                      | Achievement |
| 22       | RULE_10 | Silver Certificate                    | Achievement |
| 23       | RULE_11 | Bronze Certificate                    | Achievement |
| 24       | RULE_03 | Syntax Recovery                       | Recovery    |
| 25       | RULE_04 | Logic Recovery                        | Recovery    |
| 27       | RULE_07 | Critical Backtracking                 | Progression |
| 30       | RULE_08 | Module Graduation                     | Achievement |
| 35       | RULE_16 | Mastery Medium                        | Progression |
| 35       | RULE_20 | Accelerated Material Promotion        | Progression |
| 40       | RULE_06 | Accelerated Jump                      | Progression |
| 48       | RULE_17 | Remedial Independent                  | Recovery    |
| 50       | RULE_05 | Standard Promotion                    | Progression |

## 11. Proses Forward Chaining

```
┌─────────────────────────────────────────────────────────────────┐
│                    AdaptiveEngineService::evaluate()            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. INITIALIZATION                                               │
│     ├─ Initialize RuleRegistry                                   │
│     └─ Get all rules sorted by priority                         │
│                                                                  │
│  2. FORWARD CHAINING LOOP                                        │
│     ┌────────────────────────────────────────────────────────┐  │
│     │ For each rule in priority order:                       │  │
│     │   ├─ Check: rule->evaluate(facts)                     │  │
│     │   │                                                        │  │
│     │   ├─ IF TRUE:                                             │  │
│     │   │   ├─ rule->apply(state, context)                     │  │
│     │   │   ├─ Store triggered_rule                             │  │
│     │   │   └─ BREAK (first match wins)                        │  │
│     │   │                                                        │  │
│     │   └─ IF FALSE: Continue to next rule                     │  │
│     └────────────────────────────────────────────────────────┘  │
│                                                                  │
│  3. FALLBACK (no rule matched)                                  │
│     └─ Set next_action = 'NEXT_QUESTION'                        │
│                                                                  │
│  4. RETURN                                                       │
│     └─ [triggered_rule, new_state, facts]                       │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Kode Sumber Forward Chaining

```php
public function evaluate(array $facts, array $currentState, array $context): array
{
    $triggeredRule = null;
    $newState = $currentState;

    // Forward Chaining: Find first matching rule
    foreach ($this->ruleRegistry->getAllRules() as $rule) {
        if ($rule->evaluate($facts)) {
            $triggeredRule = $rule;
            $context['facts'] = $facts;
            $newState = $rule->apply($newState, $context);
            break; // First match wins
        }
    }

    // Fallback if no rule matched
    if (!$triggeredRule) {
        $newState['next_action'] = 'NEXT_QUESTION';
        $newState['message'] = $context['is_correct']
            ? 'Jawaban benar! Silakan lanjut ke soal berikutnya.'
            : 'Jawaban kurang tepat. Mari coba lagi.';
    }

    return [
        'triggered_rule' => $triggeredRule ? [
            'id' => $triggeredRule->getRuleId(),
            'name' => $triggeredRule->getRuleName(),
            'action' => $triggeredRule->getActionCode(),
            'priority' => $triggeredRule->getPriority(),
        ] : null,
        'new_state' => $newState,
        'facts' => $facts,
    ];
}
```

## 12. Alur Lengkap Sistem

```
┌──────────────────────────────────────────────────────────────────────────┐
│                           USER INTERACTION                               │
│                                                                          │
│  1. Student Answers Question                                             │
│     └─ POST /api/materials/{id}/questions/{id}/check                    │
│         → MaterialQuestionController::checkAnswer()                      │
│                                                                          │
│  2. AdaptiveQuizFlowService::processAdaptiveAttempt()                  │
│     ┌──────────────────────────────────────────────────────────────┐   │
│     │ a. Update Student Performance                                │   │
│     │    └─ PerformanceService::updateStudentPerformance()        │   │
│     │                                                               │   │
│     │ b. Update Learning Style (Real-time)                          │   │
│     │    └─ PerformanceService::updateLearningStyleFromInteraction()│   │
│     │                                                               │   │
│     │ c. Calculate Score                                            │   │
│     │    └─ PerformanceService::calculateScore()                   │   │
│     │                                                               │   │
│     │ d. Calculate Gamification Rewards                             │   │
│     │    └─ GamificationService::calculateCorrectAnswerReward()   │   │
│     │                                                               │   │
│     │ e. Apply Gamification Rewards (XP, streak, level)           │   │
│     │    └─ applyGamificationRewards()                             │   │
│     │                                                               │   │
│     │ f. Save Progress Log                                           │   │
│     │    └─ ProgressRepository::saveProgress()                     │   │
│     │                                                               │   │
│     │ g. Gather Facts (FactGatheringService)                        │   │
│     │    └─ Generate array of G-codes based on conditions         │   │
│     │                                                               │   │
│     │ h. Evaluate Rules (AdaptiveEngineService)                       │   │
│     │    └─ Forward chaining: find first matching rule             │   │
│     │                                                               │   │
│     │ i. Apply State Changes                                        │   │
│     │    └─ Update adaptive_state with rule's output               │   │
│     │                                                               │   │
│     │ j. Resolve Next Action                                        │   │
│     │    └─ NextActionResolverService::resolve()                  │   │
│     │                                                               │   │
│     │ k. Return Response to Frontend                                │   │
│     └──────────────────────────────────────────────────────────────┘   │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

## 13. Controller Entry Point

**Lokasi**: `app/Http/Controllers/Mahasiswa/MaterialQuestionController.php`

```php
public function checkAnswer(int|string $materialId, int|string $questionId, Request $request): JsonResponse
{
    $material = $this->materialService->getMaterialById((int) $materialId);
    $question = $this->questionService->getQuestionById((int) $questionId);

    $userId = $this->getUserId();
    $isGuest = $this->isGuestUser();

    if (!$isGuest) {
        // Use Adaptive Flow Service for authenticated users
        $result = $this->adaptiveQuizFlowService->processAdaptiveAttempt(
            $material,
            $question,
            $userId,
            $request->all()
        );
        return response()->json($result);
    }

    // Guest users use simple question answer service
    $result = $this->questionAnswerService->checkAnswer(...);
    return response()->json($result);
}
```

### Material Change Detection

Ketika siswa membuka material berbeda, adaptive state di-reset untuk mencegah bleed-over:

```php
$lastMaterialId = $adaptiveState['current_material_id'] ?? null;
if ($lastMaterialId !== null && (int) $lastMaterialId !== (int) $materialId) {
    $adaptiveState['target_difficulty'] = null;
    $adaptiveState['fast_track_active'] = false;
    $adaptiveState['last_rule'] = null;

    // Reset wrong_streak to prevent crisis rules firing
    $metrics['wrong_streak'] = 0;
    $studentState->save();
}
```

## 14. Next Action Commands

| Command               | Label               | URL                               | Tipe        |
| --------------------- | ------------------- | --------------------------------- | ----------- |
| `STUDY_MATERIAL`      | Ulas Materi         | /materials/{id}                   | material    |
| `STUDY_SYNTAX`        | Pelajari Sintaks    | /materials/{id}/submaterials/{id} | material    |
| `STUDY_THEORY`        | Pahami Konsep       | /materials/{id}/submaterials/{id} | material    |
| `STUDY_MIXED`         | Materi Komprehensif | /materials/{id}/submaterials/{id} | material    |
| `STUDY_VISUAL`        | Materi Visual       | /materials/{id}/submaterials/{id} | material    |
| `STUDY_TEXTUAL`       | Materi Tekstual     | /materials/{id}/submaterials/{id} | material    |
| `REDUCE_DIFFICULTY`   | Soal Berikutnya     | /materials/{id}/questions         | question    |
| `INCREASE_DIFFICULTY` | Soal Berikutnya     | /materials/{id}/questions         | question    |
| `NEXT_QUESTION`       | Soal Berikutnya     | /materials/{id}/questions         | question    |
| `NEXT_MATERIAL`       | Lanjut ke Materi    | /materials/{next_id}              | material    |
| `FINISH_MATERIAL`     | Selesaikan Modul    | /dashboard                        | navigation  |
| `ISSUE_CERTIFICATE`   | Klaim Sertifikat    | /dashboard                        | certificate |

## 15. Contoh Skenario

### Skenario 1: Visual Learner with Critical Score

```
Student: Visual learner (G07)
Answer: 35% score (G01 - Critical)
Difficulty: Beginner (G15)
Previous fails: 1 (NOT G22)
Question: Regular (NOT G18)

Facts gathered: [G01, G07, G15]

Rule Evaluation:
├─ FinalProjectVisualPersistentFail (P:3) → FALSE (G01 not G22)
├─ FinalProjectTextualPersistentFail (P:3) → FALSE
├─ PersistentVisualSafetyNet (P:5) → FALSE (not G22)
├─ PersistentTextualSafetyNet (P:5) → FALSE
├─ VisualCrisisIntervention (P:10) → TRUE ✓
└─ STOP (first match wins)

Result:
├─ next_action: "STUDY_VISUAL"
├─ message: "Performa Anda menurun. Mari ulas kembali materi..."
├─ intervention_type: "visual_crisis"
└─ recommendation: "Ulas Materi"
```

### Skenario 2: Persistent Fail on Final Project

```
Student: Visual learner (G07)
Answer: 45% score (G02 - Remedial)
Difficulty: Final (G18)
Previous fails: 3 (G22 - Persistent Fail)

Facts gathered: [G02, G07, G18, G22]

Rule Evaluation:
├─ FinalProjectVisualPersistentFail (P:3) → TRUE ✓
└─ STOP (highest priority)

Result:
├─ next_action: "STUDY_VISUAL"
├─ message: "Anda mengalami kesulitan berulang di Proyek Akhir..."
├─ intervention_type: "final_project_visual_persistent"
└─ force_material_review: true
```

### Skenario 3: Gold Certificate Achievement

```
Student: Textual learner (G08)
Answer: 95% score (G04 - Mastery)
Difficulty: Final (G18)
Time: 180s on 300s allocation (G05 - Fast)
Progress: 60% answered (G26 - Satisfactory)
Hint: No (NOT G12)

Facts gathered: [G04, G05, G08, G18, G26]

Rule Evaluation:
├─ Crisis rules → FALSE (no G01)
├─ GoldCertificate (P:21) → TRUE ✓
└─ STOP

Result:
├─ next_action: "ISSUE_CERTIFICATE"
├─ message: "Luar Biasa! Anda layak mendapatkan Sertifikat EMAS..."
├─ certification: "gold"
├─ achievement: "gold_certificate"
├─ badges: ["gold_architect"]
└─ module_progress: {module_id: 100}
```

### Skenario 4: Accelerated Jump

```
Student: Mixed learner (G27)
Answer: 92% score (G04 - Mastery)
Difficulty: Beginner (G15)
Time: 25s on 45s allocation (G05 - Fast)
Hint: No (NOT G12)
Next material: Locked (NOT G20)
Question: Regular (NOT G18)

Facts gathered: [G04, G05, G07, G08, G15, G27]

Rule Evaluation:
├─ Crisis rules → FALSE (no G01)
├─ Achievement rules → FALSE (not G18)
├─ SyntaxRecovery (P:24) → FALSE
├─ CriticalBacktracking (P:27) → FALSE
├─ MasteryMedium (P:35) → FALSE (G15 not G16)
├─ AcceleratedMaterialPromotion (P:35) → FALSE (not G20)
├─ AcceleratedJump (P:40) → TRUE ✓
└─ STOP

Result:
├─ fast_track_active: true
├─ target_difficulty: "medium"
├─ next_action: "NEXT_QUESTION"
└─ message: "Luar biasa! Penguasaan dan kecepatan Anda sangat baik..."
```

### Skenario 5: Syntax Recovery

```
Student: Textual learner (G08)
Answer: 65% score (G02 - Remedial)
Difficulty: Medium (G16)
Error Type: Syntax (G09)
Hint: Yes (G12)
Question: Regular (NOT G18)

Facts gathered: [G02, G08, G09, G12, G16]

Rule Evaluation:
├─ Crisis rules → FALSE (not G01)
├─ SyntaxRecovery (P:24) → TRUE ✓
└─ STOP

Result:
├─ next_action: "STUDY_SYNTAX"
├─ recommendation: "Latihan Sintaksis"
├─ recovery_type: "syntax"
└─ message: "Sepertinya Anda butuh penguatan sintaks..."
```

## 16. Lokasi File

```
app/
├── Models/
│   └── StudentState.php
├── Http/
│   └── Controllers/
│       └── Mahasiswa/
│           └── MaterialQuestionController.php
├── Services/
│   └── Adaptive/
│       ├── AdaptiveEngineService.php
│       ├── AdaptiveQuizFlowService.php
│       ├── FactGatheringService.php
│       └── NextActionResolverService.php
└── Rules/
    └── Adaptive/
        ├── BaseAdaptiveRule.php
        ├── RuleRegistry.php
        ├── Contracts/
        │   └── AdaptiveRuleInterface.php
        ├── Constants/
        │   └── AdaptiveConstants.php
        ├── Crisis/
        │   ├── FinalProjectTextualPersistentFail.php    (P:3)
        │   ├── FinalProjectVisualPersistentFail.php     (P:3)
        │   ├── PersistentTextualSafetyNet.php           (P:5)
        │   ├── PersistentVisualSafetyNet.php            (P:5)
        │   ├── TextualCrisisIntervention.php            (P:10)
        │   ├── VisualCrisisIntervention.php             (P:10)
        │   ├── TextualProjectRevision.php              (P:15)
        │   └── VisualProjectRevision.php              (P:15)
        ├── Recovery/
        │   ├── SyntaxRecovery.php                       (P:24)
        │   ├── LogicRecovery.php                        (P:25)
        │   └── RemedialIndependent.php                 (P:48)
        ├── Achievement/
        │   ├── GoldCertificate.php                     (P:21)
        │   ├── SilverCertificate.php                   (P:22)
        │   ├── BronzeCertificate.php                   (P:23)
        │   └── ModuleGraduation.php                    (P:30)
        └── Progression/
            ├── CriticalBacktracking.php                 (P:27)
            ├── MasteryMedium.php                       (P:35)
            ├── AcceleratedMaterialPromotion.php         (P:35)
            ├── AcceleratedJump.php                     (P:40)
            └── StandardPromotion.php                    (P:50)
```

## 17. Referensi Kode

| Komponen                | File                                                | Fungsi                   |
| ----------------------- | --------------------------------------------------- | ------------------------ |
| Main Entry              | `AdaptiveQuizFlowService::processAdaptiveAttempt()` | Orchestrate seluruh alur |
| Forward Chaining Engine | `AdaptiveEngineService::evaluate()`                 | Eksekusi rules           |
| Fact Gathering          | `FactGatheringService::gatherFacts()`               | Generate G-codes         |
| Rule Registration       | `RuleRegistry::registerRules()`                     | Register & sort rules    |
| Next Action Resolve     | `NextActionResolverService::resolve()`              | Convert command ke URL   |
| Controller              | `MaterialQuestionController::checkAnswer()`         | HTTP entry point         |
| Constants               | `AdaptiveConstants`                                 | Semua G/H codes          |
| Student State Model     | `StudentState`                                      | Data model               |
