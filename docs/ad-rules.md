# Adaptive Rules - Forward Chaining Engine

Dokumen ini menjelaskan implementasi forward chaining adaptive rules pada proyek Oopedia.

---

## 1. Arsitektur Rule Engine

```
┌─────────────────────────────────────────────────────────────┐
│            AdaptiveEngineService                        │
│  - Mengambil semua rule dari RuleRegistry            │
│  - Mengevaluasi rule satu-satu sesuai prioritas      │
│  - First-match-wins (hanya rule pertama yang cocok)  │
│  - Anti-loop guards untuk mencegah infinite loop    │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                 RuleRegistry                          │
│  - registered 25 concrete rules                       │
│  - Urutkan ascending berdasarkan priority            │
│  - Jika priority sama, urutkan berdasarkan ruleId    │
└─────────────────────────────────────────────────────────────┘
                          │
         ┌────────────────┼────────────────┐
         ▼                ▼                ▼
  ┌────────────┐  ┌────────────┐  ┌────────────┐
  │RuleGoldCert│  │RuleSilverC │  │RuleStandardP│
  │ .evaluate()│  │ .evaluate()│  │ .evaluate()│
  │ .apply()  │  │ .apply()  │  │ .apply()  │
  └────────────┘  └────────────┘  └────────────┘
```

---

## 2. Struktur Setiap Rule

Setiap rule mengimplementasi `AdaptiveRuleInterface`:

```php
abstract class BaseAdaptiveRule implements AdaptiveRuleInterface
{
    // Facts & Actions helpers
    use HasScoreCondition;      // hasMasteryScore(), hasFailingScore(), dll
    use HasDifficultyLevel;     // isBeginnerDifficulty(), isMediumDifficulty()
    use HasLearningStyle;        // isVisualLearner(), isTextualLearner()
    use HasErrorType;            // hasSyntaxError(), hasLogicError()

    // Action appliers
    use AppliesProgression;     // applyStandardPromotion(), applyAcceleratedJump()
    use AppliesAchievement;      // applyGoldCertificate(), dll
    use AppliesCrisisIntervention;
    use AppliesRecovery;

    protected string $ruleId;      // 'RULE_01', 'RULE_02', dll
    protected string $ruleName;   // 'Gold Certificate', dll
    protected string $actionCode;// 'H09', 'H10', dll
    protected int $priority;     // 1-100 (lower = higher precedence)

    // Side-effect free - hanya cek kondisi
    abstract public function evaluate(array $facts): bool;

    // Mutate state - hanya di sini state berubah
    abstract public function apply(array $state, array $context): array;
}
```

### Pola Implementasi Rule

```php
class RuleStandardPromotion extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $ruleId = 'RULE_20';
    protected string $ruleName = 'Standard Promotion';
    protected string $actionCode = AdaptiveConstants::ACTION_STANDARD_PROMOTION;
    protected int $priority = 50;

    public function evaluate(array $facts): bool
    {
        return $this->hasPassingScore($facts)                    // G03 atau G04
            && ($this->isBeginnerDifficulty($facts)              // G13
                || $this->isMediumDifficulty($facts)              // G14
                || $this->isHardDifficulty($facts))                // G15
            && !$this->isFinalProject($facts)                    // bukan G16
            && !$this->isPractice($facts);                       // bukan G17
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyStandardPromotion($state, $context['is_correct'] ?? false);
    }
}
```

---

## 3. Facts (G-Codes) - Input ke Engine

Facts dikumpulkan oleh `FactGatheringService` dari kondisi siswa saat submit jawaban:

| Kode | Constant                   | Predikat                      |
| ---- | -------------------------- | ----------------------------- |
| G01  | FACT_SCORE_CRITICAL        | Skor < 50%                    |
| G02  | FACT_SCORE_REMEDIAL        | Skor 50-74%                   |
| G03  | FACT_SCORE_STANDARD        | Skor 75-89%                   |
| G04  | FACT_SCORE_MASTERY         | Skor >= 90%                   |
| G05  | FACT_TIME_FAST             | Waktu < 70% alokasi           |
| G06  | FACT_STYLE_VISUAL          | Gaya belajar visual           |
| G07  | FACT_STYLE_TEXTUAL         | Gaya belajar tekstual         |
| G08  | FACT_ERROR_SYNTAX          | Error syntax                  |
| G09  | FACT_ERROR_LOGIC           | Error logic                   |
| G10  | FACT_NO_ERROR              | Tidak ada error               |
| G11  | FACT_HINT_USED             | menggunakan hint              |
| G12  | FACT_IN_MODULE             | Sedang di modul               |
| G13  | FACT_DIFF_BEGINNER         | Difficulty beginner           |
| G14  | FACT_DIFF_MEDIUM           | Difficulty medium             |
| G15  | FACT_DIFF_HARD             | Difficulty hard               |
| G16  | FACT_IS_FINAL_PROJECT      | Final project                 |
| G17  | FACT_IS_PRACTICE           | Mode latihan                  |
| G18  | FACT_NEXT_UNLOCKED         | Materi berikutnya unlock      |
| G19  | FACT_PREV_UNLOCKED         | Materi sebelumnya unlock      |
| G20  | FACT_PERSISTENT_FAIL       | Gagal >= 2x beruntun          |
| G21  | FACT_SATISFACTORY_PROGRESS | Progress >= 50% di difficulty |
| G22  | FACT_STYLE_MIXED           | Gaya campuran                 |

### Helper Predikat di BaseAdaptiveRule

```php
// Dari trait HasScoreCondition
protected function hasFact(array $facts, string $fact): bool;
protected function hasAnyFact(array $facts, array $requiredFacts): bool;
protected function hasCriticalScore(array $facts): bool;      // G01
protected function hasRemedialScore(array $facts): bool;       // G02
protected function hasStandardScore(array $facts): bool;           // G03
protected function hasMasteryScore(array $facts): bool;      // G04
protected function hasPassingScore(array $facts): bool;       // G03 || G04
protected function hasFailingScore(array $facts): bool;       // G01 || G02
```

---

## 4. Actions (H-Codes) - Output dari Engine

| Kode | Constant                              | Aksi                   |
| ---- | ------------------------------------- | ---------------------- |
| H01  | ACTION_VISUAL_CRISIS_INTERVENTION     | Intervensi visual      |
| H02  | ACTION_TEXTUAL_CRISIS_INTERVENTION    | Intervensi textual     |
| H03  | ACTION_SYNTAX_RECOVERY                | Recovery syntax        |
| H04  | ACTION_LOGIC_RECOVERY                 | Recovery logic         |
| H05  | ACTION_STANDARD_PROMOTION             | Next question reguler  |
| H06  | ACTION_ACCELERATED_JUMP               | Loncat difficulty      |
| H07  | ACTION_CRITICAL_BACKTRACKING          | Turunkan difficulty    |
| H08  | ACTION_MODULE_GRADUATION              | Lulus modul            |
| H09  | ACTION_GOLD_CERTIFICATE               | Klaim gold             |
| H10  | ACTION_SILVER_CERTIFICATE             | Klaim silver           |
| H11  | ACTION_BRONZE_CERTIFICATE             | Klaim bronze           |
| H12  | ACTION_VISUAL_PROJECT_REVISION        | Revisi project visual  |
| H13  | ACTION_TEXTUAL_PROJECT_REVISION       | Revisi project textual |
| H14  | ACTION_PERSISTENT_VISUAL_NET          | Safety net visual      |
| H15  | ACTION_PERSISTENT_TEXTUAL_NET         | Safety net textual     |
| H16  | ACTION_ACCELERATED_MATERIAL_PROMOTION | Loncat materi          |

### Operational Actions (Non-H)

Setelah H-code ditentukan oleh rule, `NextActionResolverService` mengkonversi ke aksi nyata:

| Action              | makna                     |
| ------------------- | ------------------------- |
| NEXT_QUESTION       | Lanjut soal berikutnya    |
| NEXT_MATERIAL       | Lanjut materi berikutnya  |
| FINISH_MATERIAL     | Selesai materi            |
| ISSUE_CERTIFICATE   | Klaim sertifikat          |
| REDUCE_DIFFICULTY   | Turunkan level            |
| INCREASE_DIFFICULTY | Naikkan level             |
| STUDY_VISUAL        | Tampilkan materi visual   |
| STUDY_TEXTUAL       | Tampilkan materi tekstual |

---

## 5. Priority & Execution Order

RuleRegistry mengurutkan rules ascending berdasar priority (angka kecil = execute lebih dulu):

| Priority | Rule Id | Rule Name                         | Action |
| -------- | ------- | --------------------------------- | ------ |
| 3        | RULE_01 | FinalProjectVisualPersistentFail  | H12    |
| 3        | RULE_02 | FinalProjectTextualPersistentFail | H13    |
| 5        | RULE_03 | PersistentVisualSafetyNet         | H14    |
| 5        | RULE_04 | PersistentTextualSafetyNet        | H15    |
| 10       | RULE_05 | VisualCrisisIntervention          | H01    |
| 10       | RULE_06 | TextualCrisisIntervention         | H02    |
| 15       | RULE_07 | VisualProjectRevision             | H12    |
| 15       | RULE_08 | TextualProjectRevision            | H13    |
| 21       | RULE_09 | GoldCertificate                   | H09    |
| 22       | RULE_10 | SilverCertificate                 | H10    |
| 23       | RULE_11 | BronzeCertificate                 | H11    |
| 24       | RULE_12 | SyntaxRecovery                    | H03    |
| 25       | RULE_13 | LogicRecovery                     | H04    |
| 27       | RULE_14 | CriticalBacktracking              | H07    |
| 30       | RULE_15 | ModuleGraduation                  | H08    |
| 35       | RULE_16 | MasteryMedium                     | H05    |
| 36       | RULE_17 | AcceleratedMaterialPromotion      | H16    |
| 40       | RULE_18 | AcceleratedJump                   | H06    |
| 48       | RULE_19 | RemedialIndependent               | H04    |
| 50       | RULE_20 | StandardPromotion                 | H05    |

---

## 6. 20 Concrete Rules - Detail

### 6.1 Crisis Intervention (Priority 10)

```php
// RuleVisualCrisisIntervention
evaluate: G01 (critical) + G06 (visual) + G13 (beginner) + bukan G20
apply: inter学生 masuk mode review visual
```

```php
// RuleTextualCrisisIntervention
evaluate: G01 (critical) + G07 (textual) + G13 (beginner) + bukan G20
apply:学生 masuk mode review textual
```

### 6.2 Safety Net (Priority 5)

```php
// RulePersistentVisualSafetyNet
evaluate: G20 (persistent fail) + G06 (visual)
apply: jaring pengaman berulangvisual
```

```php
// RulePersistentTextualSafetyNet
evaluate: G20 (persistent fail) + G07 (textual)
apply: jaring pengaman berulanguarial
```

### 6.3 Promotion (Priority 35-50)

```php
// RuleStandardPromotion (priority 50 - fallback)
evaluate: G03/G04 (passing) + (G13|G14|G15) + bukan G16 + bukan G17
apply: next question reguler
```

```php
// RuleAcceleratedJump (priority 40)
evaluate: G04 (mastery) + G05 (fast) + G13 (beginner) + bukan G11 + bukan G16
apply: loncat ke medium
```

```php
// RuleMasteryMedium (priority 35)
evaluate: G04 (mastery) + G05 (fast) + G14 (medium) + bukan G11 + bukan G16
apply: loncat ke hard
```

```php
// RuleAcceleratedMaterialPromotion (priority 36)
evaluate: G04 + G05 + G18 (next unlocked)
apply: loncat ke materi berikutnya
```

### 6.4 Recovery (Priority 24-25, 48)

```php
// RuleSyntaxRecovery
evaluate: G08 (syntax error) + G02 (remedial)
apply: STUDY_SYNTAX
```

```php
// RuleLogicRecovery
evaluate: G09 (logic error) + G02 (remedial)
apply: STUDY_THEORY / STUDY_MIXED
```

```php
// RuleRemedialIndependent
evaluate: G02 (remedial) + bukan G11 (tanpa hint)
apply:recovery mandiri
```

### 6.5 Certificates (Priority 21-23)

```php
// RuleGoldCertificate
evaluate: G04 (mastery) + G05 (fast) + G16 (final) + G21 (progress) + bukan G11
apply: ISSUE_CERTIFICATE + gold
```

```php
// RuleSilverCertificate
evaluate: G03 (standard) + G16 (final) + G21 (progress)
apply: ISSUE_CERTIFICATE + silver
```

```php
// RuleBronzeCertificate
evaluate: G03|G04 + G16 + G21
apply: bronze (lower rank)
```

### 6.6 Module Graduation (Priority 30)

```php
// RuleModuleGraduation
evaluate: semua soal di modul sudah dijawab dengan G21
apply: FINISH_MATERIAL + module_completed
```

### 6.7 Backtracking (Priority 27)

```php
// RuleCriticalBacktracking
evaluate: G01 (critical) + G14|G15 (medium|hard) + bukan G20
apply: REDUCE_DIFFICULTY + beginner
```

### 6.8 Final Project Revision (Priority 3, 15)

```php
// RuleFinalProjectVisualPersistentFail (priority 3)
evaluate: G16 + G20 + G06
apply: revisi project visual
```

```php
// RuleVisualProjectRevision (priority 15)
evaluate: G16 + G01|G02
apply: revisi project visual
```

---

## 7. Evaluasi Flow - Step by Step

```
1. Submit Jawaban
       │
       ▼
2. FactGatheringService.kumpulkan()
   - Dari response: score, time_spent, hint_used
   - Dari student_state: learning_style, difficulty
   - Dari DB/kuki: consecutive_fail, module_progress
   ─────────────────────
   Mengembalikan array facts: ['G01', 'G06', 'G13', ...]
       │
       ▼
3. AdaptiveEngineService.evaluate()
   foreach rule di RuleRegistry (sorted by priority):
       │
       ├── shouldSkipRule()? → continue jika:
       │   - RULE_18 (accelerated jump) + sudah capai target
       │   - RULE_17 (accelerated material) + last action sama
       │
       ├── rule.evaluate(facts) → true/false
       │   (side-effect free - hanya cek kondisi)
       │
       └── jika true:
           firstMatch = rule
           newState = rule.apply(state, context)
           break loop (first-match-wins)
       │
       ▼
4. Jika tidak ada rule cocok:
   newState = default NEXT_QUESTION
       │
       ▼
5. NextActionResolverService.resolve()
   Konversi action code ke operational action:
   - H01 → STUDY_VISUAL
   - H05 → NEXT_QUESTION
   - H09 → ISSUE_CERTIFICATE
   dll
       │
       ▼
6. Return final decision ke frontend
```

---

## 8. Anti-Loop Guards

Engine mencegah infinite loop pada percepatan:

```php
// Di AdaptiveEngineService.shouldSkipRule()

// Guard 1: Accelerated Jump - jangan memicu lagi jika target sudah tercapai
if ($actionCode === ACTION_ACCELERATED_JUMP) {
    $target = $adaptiveState['target_difficulty'] ?? null;
    if ($target === DIFFICULTY_MEDIUM || $target === DIFFICULTY_HARD) {
        return true; // skip rule ini
    }
}

// Guard 2: Accelerated Material - jangan double jump
if ($actionCode === ACTION_ACCELERATED_MATERIAL_PROMOTION) {
    $lastAction = $adaptiveState['last_rule']['action'] ?? null;
    if ($lastAction === ACTION_ACCELERATED_MATERIAL_PROMOTION) {
        return true; // skip rule ini
    }
}
```

---

## 9. State Structure

State disimpan di `student_states` table (user) atau cookie (guest):

```json
{
    "adaptive_state": {
        "current_material_id": "material_01",
        "current_difficulty": "beginner",
        "target_difficulty": "medium",
        "fast_track_active": false,
        "last_rule": {
            "id": "RULE_18",
            "name": "Accelerated Jump",
            "action": "H06"
        },
        "consecutive_correct": 3
    },
    "gamification_data": {
        "xp": 250,
        "streak": 5,
        "badges": ["bronze_junior"]
    },
    "learning_profile": {
        "preferred_style": "visual",
        "weaknesses": ["syntax"]
    },
    "performance_metrics": {
        "accuracy": 0.85,
        "avg_time": 35
    },
    "certifications": {
        "material_01": "gold"
    }
}
```

---

## 10. Menambah Rule Baru

### Step 1: Tambah constant jika perlu

```php
// di app/Rules/Adaptive/Constants/AdaptiveConstants.php
public const string FACT_NEW_FACT = 'G23';
public const string ACTION_NEW_ACTION = 'H17';
```

### Step 2: Implementasi rule class

```php
// app/Rules/Adaptive/RuleNewFeature.php
class RuleNewFeature extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_21';
    protected string $ruleName = 'New Feature';
    protected string $actionCode = AdaptiveConstants::ACTION_NEW_ACTION;
    protected int $priority = 45; // sesuai urutan yang diinginkan

    public function evaluate(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_NEW_FACT)
            && $this->isBeginnerDifficulty($facts);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = AdaptiveConstants::ACTION_NEW_ACTION;
        $state['message'] = 'New feature triggered!';
        return $state;
    }
}
```

### Step 3: Register di RuleRegistry

```php
// app/Rules/Adaptive/RuleRegistry.php
// Di method registerRules():
RuleNewFeature::class,  // tambahkan di posisi yang sesuai priority
```

---

## 11. Testing

```bash
# Run all adaptive tests
php artisan test --filter Adaptive

# Run specific rule test
php artisan test tests/Feature/Unit/Services/Adaptive/AdaptiveEngineServiceTest.php

# Run with verbose output
php artisan test --filter Adaptive --verbose
```

---

## 12. Files Reference

| File                                                     | Peran                            |
| -------------------------------------------------------- | -------------------------------- |
| `app/Rules/Adaptive/RuleRegistry.php`                    | Kumpulan semua rule              |
| `app/Rules/Adaptive/BaseAdaptiveRule.php`                | Abstract base class              |
| `app/Rules/Adaptive/Contracts/AdaptiveRuleInterface.php` | Interface contract               |
| `app/Rules/Adaptive/Constants/AdaptiveConstants.php`     | G-codes & H-codes                |
| `app/Rules/Adaptive/Concerns/*.php`                      | Traits untuk evaluate & apply    |
| `app/Rules/Adaptive/Rule*.php`                           | 20 concrete rule implementations |
| `app/Services/Adaptive/AdaptiveEngineService.php`        | Engine orchestrator              |
| `app/Services/Adaptive/FactGatheringService.php`         | Fact collector                   |
| `app/Services/Adaptive/NextActionResolverService.php`    | Action resolver                  |

---

## 13. Diagram Forward Chaining

```
                    ┌─────────────────┐
                    │      FACTS       │
                    │  G01, G04, G05   │
                    │  G06, G13, G20   │
                    └────────┬──────��─��
                             │
                             ▼
              ┌──────────────────────────────┐
              │    FORWARD CHAINING LOOP     │
              │  for each rule (priority):  │
              │    1. check skip guard      │
              │    2. evaluate(facts)     │
              │    3. if match → apply()   │
              │    4. break (first match)  │
              └────────────┬───────────────┘
                             │
              ┌──────────────┴──────────────┐
              ▼                            ▼
        ┌──────────────┐            ┌──────────────┐
        │ RULE_MATCHES │            │ NO_MATCH    │
        │ apply()   │            │ fallback   │
        └─────┬─────┘            └─────┬─────┘
              │                       │
              ▼                       ▼
        ┌──────────────┐            ┌──────────────┐
        │ NEW_STATE  │            │ NEXT_QUESTION│
        │ + ACTION  │            │ default     │
        └──────────┬┘            └─────────────┘
                   │
                   ▼
            ┌──────────────┐
            │ NEXT_ACTION  │
            │ RESOLVER     │
            └──────┬───────┘
                   │
           ┌───────┴───────┐
           ▼               ▼
    ┌───────────┐   ┌───────────┐
    │ NEXT_    │   │ STUDY_   │
    │ QUESTION │   │ VISUAL   │
    └───────────┘   └───────────┘
```

End of document.
