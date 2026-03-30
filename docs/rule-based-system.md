# Rule-Based System - Oopedia Adaptive Learning

## Overview

Sistem rule-based di Oopedia adalah implementasi **Intelligent Tutoring System (ITS)** yang menggunakan forward chaining untuk menyesuaikan pengalaman belajar siswa berdasarkan performa, gaya belajar, dan konteks soal.

---

## Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        ADAPTIVE QUIZ FLOW                                │
├─────────────────────────────────────────────────────────────────────────┤
│  1. Student Submit Answer                                                │
│           ↓                                                               │
│  2. Update Performance (PerformanceService)                             │
│           ↓                                                               │
│  3. Gather Facts (FactGatheringService) ──────► Facts (G01-G26)         │
│           ↓                                                               │
│  4. Evaluate Rules (AdaptiveEngineService)                               │
│           ↓                                                               │
│  5. Apply Rule Action ─────────────────────────────────────────────────►│
│  6. Resolve Next Action (NextActionResolver)                             │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Komponen Inti

### 1. Interface & Base Class

**File:** `app/Rules/Adaptive/Contracts/AdaptiveRuleInterface.php`

```php
interface AdaptiveRuleInterface
{
    public function getRuleId(): string;        // Unique identifier (RULE_01)
    public function getRuleName(): string;     // Human-readable name
    public function getActionCode(): string;   // Action code (H01-H16)
    public function getPriority(): int;         // Lower = higher priority
    public function evaluate(array $facts): bool;  // Check if rule triggers
    public function apply(array $state, array $context): array;  // Apply action
}
```

**File:** `app/Rules/Adaptive/BaseAdaptiveRule.php`

Kelas abstrak yang menyediakan helper methods:

```php
// Check single fact exists
$this->hasFact($facts, 'G01');

// Check ALL facts exist (AND logic)
$this->hasAllFacts($facts, ['G01', 'G07']);

// Check ANY fact exists (OR logic)
$this->hasAnyFact($facts, ['G01', 'G02']);

// Check fact does NOT exist
$this->notHasFact($facts, 'G22');
```

---

## Facts (G Codes)

Facts adalah kondisi/fakta yang dikumpulkan dari state siswa dan konteks soal.

### Kategori Facts

| Code                               | Nama                       | Deskripsi                                    |
| ---------------------------------- | -------------------------- | -------------------------------------------- |
| **Performance / Score (G01-G04)**  |                            |                                              |
| G01                                | FACT_SCORE_CRITICAL        | Skor Kritis (<50)                            |
| G02                                | FACT_SCORE_REMEDIAL        | Skor Remedial (50-74)                        |
| G03                                | FACT_SCORE_STANDARD        | Skor Standar (75-89)                         |
| G04                                | FACT_SCORE_MASTERY         | Skor Mahir (90-100)                          |
| **Time (G05-G06)**                 |                            |                                              |
| G05                                | FACT_TIME_FAST             | Waktu Pengerjaan Cepat (<70% allocated time) |
| **Learning Styles (G07-G08, G27)** |                            |                                              |
| G07                                | FACT_STYLE_VISUAL          | Gaya Belajar Visual                          |
| G08                                | FACT_STYLE_TEXTUAL         | Gaya Belajar Tekstual                        |
| G27                                | FACT_STYLE_MIXED           | Gaya Belajar Campuran                        |
| **Error Types (G09-G10)**          |                            |                                              |
| G09                                | FACT_ERROR_SYNTAX          | Kesalahan Sintaksis                          |
| G10                                | FACT_ERROR_LOGIC           | Kesalahan Logika                             |
| **Hint (G12)**                     |                            |                                              |
| G12                                | FACT_HINT_USED             | Menggunakan Hint                             |
| **Difficulty (G15-G17)**           |                            |                                              |
| G15                                | FACT_DIFF_BEGINNER         | Tingkat Kesulitan: Easy                      |
| G16                                | FACT_DIFF_MEDIUM           | Tingkat Kesulitan: Medium                    |
| G17                                | FACT_DIFF_HARD             | Tingkat Kesulitan: Advanced                  |
| **Special (G18-G22, G26)**         |                            |                                              |
| G18                                | FACT_IS_FINAL_PROJECT      | Soal Proyek Akhir                            |
| G20                                | FACT_NEXT_UNLOCKED         | Materi Berikutnya Terbuka                    |
| G21                                | FACT_PREV_UNLOCKED         | Materi Sebelumnya Terbuka                    |
| G22                                | FACT_PERSISTENT_FAIL       | Gagal Berulang (≥2x gagal soal sama)         |
| G26                                | FACT_SATISFACTORY_PROGRESS | Progres Materi Memadai (≥50%)                |

---

## Actions (H Codes)

Actions adalah respons/tindakan yang dihasilkan ketika sebuah rule terpicu.

| Code | Nama                                  | Deskripsi                        |
| ---- | ------------------------------------- | -------------------------------- |
| H01  | ACTION_VISUAL_CRISIS_INTERVENTION     | Intervensi Krisis Visual         |
| H02  | ACTION_TEXTUAL_CRISIS_INTERVENTION    | Intervensi Krisis Tekstual       |
| H03  | ACTION_SYNTAX_RECOVERY                | Pemulihan Sintaksis              |
| H04  | ACTION_LOGIC_RECOVERY                 | Pemulihan Logika                 |
| H05  | ACTION_STANDARD_PROMOTION             | Promosi Standar (next question)  |
| H06  | ACTION_ACCELERATED_JUMP               | Loncatan Akselerasi (Fast Track) |
| H07  | ACTION_CRITICAL_BACKTRACKING          | Mundur Kritis (Backtracking)     |
| H08  | ACTION_MODULE_GRADUATION              | Kelulusan Modul                  |
| H09  | ACTION_GOLD_CERTIFICATE               | Sertifikat Emas                  |
| H10  | ACTION_SILVER_CERTIFICATE             | Sertifikat Perak                 |
| H11  | ACTION_BRONZE_CERTIFICATE             | Sertifikat Perunggu              |
| H12  | ACTION_VISUAL_PROJECT_REVISION        | Revisi Proyek Visual             |
| H13  | ACTION_TEXTUAL_PROJECT_REVISION       | Revisi Proyek Tekstual           |
| H14  | ACTION_PERSISTENT_VISUAL_NET          | Safety Net Visual                |
| H15  | ACTION_PERSISTENT_TEXTUAL_NET         | Safety Net Tekstual              |
| H16  | ACTION_ACCELERATED_MATERIAL_PROMOTION | Loncatan Akseleratif Modul       |

---

## Kategori Rules

### 1. Crisis Rules (Priority 5-15)

Prioritas tertinggi - menangani situasi krisis siswa.

| Rule ID | Rule Name                         | Priority | IF Condition        | THEN Action                    |
| ------- | --------------------------------- | -------- | ------------------- | ------------------------------ |
| RULE_14 | PersistentVisualSafetyNet         | 5        | G22 AND G07         | H14 - Safety Net Visual        |
| RULE_15 | PersistentTextualSafetyNet        | 6        | G22 AND G08         | H15 - Safety Net Tekstual      |
| RULE_01 | VisualCrisisIntervention          | 10       | G01 AND G07 AND G15 | H01 - Study Visual Material    |
| RULE_02 | TextualCrisisIntervention         | 11       | G01 AND G08 AND G15 | H02 - Study Textual Material   |
| RULE_12 | VisualProjectRevision             | 12       | G01 AND G07 AND G18 | H12 - Project Revision Visual  |
| RULE_13 | TextualProjectRevision            | 13       | G01 AND G08 AND G18 | H13 - Project Revision Textual |
| RULE_07 | FinalProjectVisualPersistentFail  | 14       | G22 AND G07 AND G18 | H12 - Force Revision Visual    |
| RULE_08 | FinalProjectTextualPersistentFail | 15       | G22 AND G08 AND G18 | H13 - Force Revision Textual   |

### 2. Recovery Rules (Priority 24-48)

Membantu siswa pulih dari kesalahan spesifik.

| Rule ID | Rule Name           | Priority | IF Condition                | THEN Action                  |
| ------- | ------------------- | -------- | --------------------------- | ---------------------------- |
| RULE_03 | SyntaxRecovery      | 24       | G02 AND G09 AND G16 AND G12 | H03 - Practice Syntax        |
| RULE_04 | LogicRecovery       | 26       | G02 AND G10 AND G16 AND G12 | H04 - Practice Logic         |
| RULE_17 | RemedialIndependent | 48       | G02 AND NOT G12             | Self-practice recommendation |

### 3. Achievement Rules (Priority 20-30)

Menangani sertifikasi dan kelulusan modul.

| Rule ID | Rule Name         | Priority | IF Condition                            | THEN Action                    |
| ------- | ----------------- | -------- | --------------------------------------- | ------------------------------ |
| RULE_09 | GoldCertificate   | 21       | G04 AND G05 AND NOT G12 AND G18 AND G26 | H09 - Issue Gold Certificate   |
| RULE_10 | SilverCertificate | 23       | G03 AND G18 AND G26                     | H10 - Issue Silver Certificate |
| RULE_11 | BronzeCertificate | 25       | G02 AND G18 AND G26                     | H11 - Issue Bronze Certificate |
| RULE_06 | ModuleGraduation  | 30       | G26 AND G18 (or all diffs passed)       | H08 - Graduate Module          |

### 4. Progression Rules (Priority 27-50)

Mengatur kemajuan siswa dalam materi.

| Rule ID | Rule Name                    | Priority | IF Condition                         | THEN Action                        |
| ------- | ---------------------------- | -------- | ------------------------------------ | ---------------------------------- |
| RULE_18 | CriticalBacktracking         | 27       | G01 AND G15 AND G21                  | H07 - Back to Previous Material    |
| RULE_16 | MasteryMedium                | 35       | G04 AND G16                          | Next Question (upgrade difficulty) |
| RULE_20 | AcceleratedMaterialPromotion | 40       | G04 AND G16 AND G20                  | H16 - Skip to Next Module          |
| RULE_19 | AcceleratedJump              | 45       | G04 AND G05 AND NOT G12              | H06 - Fast Track                   |
| RULE_05 | StandardPromotion            | 50       | (G03 OR G04) AND (G15 OR G16 OR G17) | H05 - Next Question                |

---

## Flow Chart Evaluasi Rules

```
                    ┌──────────────────┐
                    │ Gather Facts     │
                    │ (G01-G26)        │
                    └────────┬─────────┘
                             │
                             ▼
                ┌────────────────────────┐
                │ Get Rules by Priority  │
                │ (RuleRegistry)         │
                └────────────┬───────────┘
                             │
                             ▼
               ┌────────────────────────────┐
               │ FORWARD CHAINING LOOP      │
               │                            │
               │  For each rule (sorted):    │
               │    ├─► evaluate(facts)     │
               │    │    ├─ TRUE ───────────┤
               │    │    │                  │
               │    │    └─ FALSE ──► Next  │
               │    │                      │
               │    └─► apply(state, ctx)   │
               │         │                  │
               │         ▼                  │
               │    BREAK (first match)     │
               └────────────┬───────────────┘
                            │
              ┌─────────────┴──────────────┐
              │                            │
              ▼                            ▼
   ┌─────────────────────┐    ┌──────────────────────┐
   │ Rule Triggered      │    │ No Rule Matched      │
   │ - Return action     │    │ - Default: NEXT_Q     │
   │ - Update state      │    │ - Default message    │
   └─────────────────────┘    └──────────────────────┘
```

---

## Contoh Implementasi Rule

### Contoh 1: Visual Crisis Intervention

**File:** `app/Rules/Adaptive/Crisis/VisualCrisisIntervention.php`

```php
/**
 * Rule 1: Visual Crisis Intervention
 * IF (G01 AND G07 AND G15 AND NOT G22 AND NOT G18) THEN H01
 *
 * Triggers when student has critical score,
 * is a visual learner, on beginner difficulty,
 * and hasn't failed persistently yet.
 */
class VisualCrisisIntervention extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_01';
    protected string $ruleName = 'Visual Crisis Intervention';
    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_CRISIS_INTERVENTION;
    protected int $priority = 10;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_CRITICAL,
            AdaptiveConstants::FACT_STYLE_VISUAL,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Ulas Materi';
        $state['next_action'] = 'STUDY_VISUAL';
        $state['message'] = 'Performa Anda menurun. Mari ulas kembali materi.';
        $state['intervention_type'] = 'visual_crisis';

        return $state;
    }
}
```

### Contoh 2: Gold Certificate

**File:** `app/Rules/Adaptive/Achievement/GoldCertificate.php`

```php
/**
 * Rule 9: Gold Certificate
 * IF (G04 AND G05 AND NOT G12 AND G18 AND G26) THEN H09
 *
 * Requires mastery score, fast time, no hints,
 * final project, and satisfactory progress.
 */
class GoldCertificate extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_09';
    protected string $ruleName = 'Gold Certificate';
    protected string $actionCode = AdaptiveConstants::ACTION_GOLD_CERTIFICATE;
    protected int $priority = 21;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_IS_FINAL_PROJECT,
            AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'ISSUE_CERTIFICATE';
        $state['message'] = 'Luar Biasa! Anda layak mendapatkan Sertifikat EMAS.';
        $state['certification'] = 'gold';
        $state['achievement'] = 'gold_certificate';

        // Add badge
        $badges = $state['gamification_data']['badges'] ?? [];
        $badges[] = 'gold_architect';
        $state['gamification_data']['badges'] = $badges;

        return $state;
    }
}
```

---

## Services

### 1. FactGatheringService

**File:** `app/Services/Adaptive/FactGatheringService.php`

Mengumpulkan facts dari:

- **StudentState**: learning_style, performance_metrics, adaptive_state
- **Context**: is_correct, score, time_spent, difficulty, question_id, material_id, module_id

**Thresholds yang digunakan:**

- Score Critical: < 50
- Score Remedial: 50-74
- Score Standard: 75-89
- Score Mastery: ≥ 90
- Time Fast: < 70% allocated time
- Persistent Fail: ≥ 2 consecutive failures
- Satisfactory Progress: ≥ 50% questions answered

### 2. AdaptiveEngineService

**File:** `app/Services/Adaptive/AdaptiveEngineService.php`

Menggunakan forward chaining:

1. Iterate rules berdasarkan priority (ascending)
2. Call `evaluate(facts)` pada setiap rule
3. Jika true, call `apply(state, context)` dan break
4. Return triggered rule info dan new state

### 3. AdaptiveQuizFlowService

**File:** `app/Services/Adaptive/AdaptiveQuizFlowService.php`

Orchestrator utama yang memproses setiap jawaban:

```php
public function processAdaptiveAttempt(...): array
{
    // 1. Update student performance
    $studentState = $this->performanceService->updateStudentPerformance(...);

    // 2. Calculate score
    $score = $this->performanceService->calculateScore(...);

    // 3. Calculate rewards
    $rewardResult = $this->gamificationService->calculateCorrectAnswerReward(...);

    // 4. Save progress
    $savedProgress = $this->progressRepo->saveProgress(...);

    // 5. Gather facts
    $facts = $this->factGathering->gatherFacts(...);

    // 6. Evaluate adaptive rules
    $adaptiveResult = $this->adaptiveEngine->evaluate($facts, ...);

    // 7. Apply adaptive state changes
    $adaptiveState['last_rule'] = $adaptiveResult['triggered_rule'];

    // 8. Resolve next action
    $nextActionData = $this->nextActionResolver->resolve(...);

    return [...];
}
```

---

## File Structure

```
app/
├── Rules/
│   └── Adaptive/
│       ├── Contracts/
│       │   └── AdaptiveRuleInterface.php
│       ├── BaseAdaptiveRule.php
│       ├── RuleRegistry.php
│       ├── Constants/
│       │   └── AdaptiveConstants.php
│       ├── Crisis/
│       │   ├── VisualCrisisIntervention.php
│       │   ├── TextualCrisisIntervention.php
│       │   ├── VisualProjectRevision.php
│       │   ├── TextualProjectRevision.php
│       │   ├── PersistentVisualSafetyNet.php
│       │   ├── PersistentTextualSafetyNet.php
│       │   ├── FinalProjectVisualPersistentFail.php
│       │   └── FinalProjectTextualPersistentFail.php
│       ├── Recovery/
│       │   ├── SyntaxRecovery.php
│       │   ├── LogicRecovery.php
│       │   └── RemedialIndependent.php
│       ├── Achievement/
│       │   ├── GoldCertificate.php
│       │   ├── SilverCertificate.php
│       │   ├── BronzeCertificate.php
│       │   └── ModuleGraduation.php
│       └── Progression/
│           ├── StandardPromotion.php
│           ├── AcceleratedJump.php
│           ├── AcceleratedMaterialPromotion.php
│           ├── CriticalBacktracking.php
│           └── MasteryMedium.php
└── Services/
    └── Adaptive/
        ├── AdaptiveQuizFlowService.php
        ├── AdaptiveEngineService.php
        └── FactGatheringService.php
```

---

## Konfigurasi Konstanta

**File:** `app/Rules/Adaptive/Constants/AdaptiveConstants.php`

```php
// Allocated time per difficulty
public const ALLOCATED_TIME = [
    'beginner' => 45,  // seconds
    'medium'   => 90,
    'hard'     => 150,
    'final'    => 300,
];

// Time fast threshold (percentage)
public const TIME_FAST_THRESHOLD = 70;  // < 70% = fast
```

---

## Catatan Penting

1. **Priority System**: Rules dengan angka lebih kecil memiliki prioritas lebih tinggi dan akan dievaluasi terlebih dahulu.

2. **Forward Chaining**: Sistem menggunakan pendekatan forward chaining - rules dievaluasi berdasarkan facts yang ada, bukan backward chaining.

3. **First Match Wins**: Ketika sebuah rule cocok, sistem langsung mengeksekusi aksi dan tidak melanjutkan ke rule berikutnya.

4. **Fallback**: Jika tidak ada rule yang cocok, sistem menggunakan default action (NEXT_QUESTION).

5. **Learning Style Detection**: Sistem mendeteksi gaya belajar dari interaksi siswa (waktu menjawab, tipe soal yang dipilih).

6. **Persistent Fail Detection**: Sistem melacak kegagalan berurutan pada soal yang sama untuk memicu intervensi khusus.
