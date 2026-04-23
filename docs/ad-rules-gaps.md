# Analisis Celah Adaptive Rules

Dokumen ini mengidentifikasi celah, inkonsistensi, dan area perbaikan dalam sistem adaptive rules forward chaining.

---

## 1. Facts Tidak Diproduksi (Dead Facts)

| Constant                   | Didefinisikan | Diproduksi            | Masalah                                |
| -------------------------- | ------------- | --------------------- | -------------------------------------- |
| `FACT_NO_ERROR` (G10)      | ✅            | ❌ Dead code          | Helper ada tapi tidak pernah digunakan |
| `FACT_IS_PRACTICE` (G17)   | ✅            | ❌ Dead code          | Tidak ada logika deteksi practice mode |
| `FACT_PREV_UNLOCKED` (G19) | ✅            | ⚠️ Tidak dipakai rule | Tidak ada rule yang menggunakan        |
| `FACT_STYLE_MIXED` (G22)   | ✅            | ⚠️ Tidak dipakai rule | Ada di facts, tapi tidak触发 rule      |

### Celah 1.1: G17 (Practice Mode)

```php
// Di FactGatheringService:68-70
// BUG: FACT_IS_PRACTICE (G17) tidak diproduksi
// Rule menggunakan isPractice() tapi fact tidak pernah di-set
```

**Impact**: Semua rule yang menggunakan `isPractice()` akan never trigger.

### Celah 1.2: G10 (No Error)

```php
// Di HasErrorType:19-22
protected function hasNoError(array $facts): bool {
    return $this->hasFact($facts, AdaptiveConstants::FACT_NO_ERROR);  // ← Never produced
}
```

**Impact**: Tidak ada rule yang menggunakan `hasNoError()` - dead code.

### Celah 1.3: G22 (Mixed Style)

```php
// Di FactGatheringService:119-124
if ($style === StudentStateSchema::STYLE_MIXED) {
    return [
        AdaptiveConstants::FACT_STYLE_VISUAL,  // ← Ada 2 facts sekaligus
        AdaptiveConstants::FACT_STYLE_TEXTUAL,
        AdaptiveConstants::FACT_STYLE_MIXED,
    ];
}
```

**Impact**: Mixed = visual + textual. Crisis intervention akan match BUALAN yang pertama ada (priority lebih tinggi), bukan sesuai desired behavior.

---

## 2. Inkonsistensi Priority

### Celah 2.1: MasteryMedium vs AcceleratedJump

```
Priority 35: RuleMasteryMedium (G04 + G05 + G14 + bukan G11 + bukan G16)
Priority 40: RuleAcceleratedJump  (G04 + G05 + G13 + bukan G11 + bukan G16)
```

**Masalah**: Di beginner难度 mastery + fast = bisa trigger accelerated jump SEBELUM mastery medium. Ini tidak logis.

**Contoh scenario**:

- Siswa di beginner
- Skor mastery (G04)
- Waktu fast (G05)
- Tidak pakai hint

→ RuleAcceleratedJump (priority 40) trigger SEBELUM siswa capai medium.

### Celah 2.2: Recovery Priority Collision

```
Priority 24: RuleSyntaxRecovery     → G02 + G08 + G14 + G11
Priority 25: RuleLogicRecovery    → G02 + G09 + G14 + G11
Priority 48: RuleRemedialIndependent → G02 + bukan G11
```

**Masalah**: Syntax/Logic recovery require G14 (medium), tapi master rule di medium = G04. Jika skor mastery, recovery tidak akan pernah aktif karena ruleMasteryMedium priority 35 akan trigger duluan.

---

## 3. Logic Gaps (Missing Scenarios)

### Celah 3.1: Tidak Ada Rule untuk Critical di Beginner

```
Priority 27: RuleCriticalBacktracking → G01 + (G14|G15) + bukan G20
```

**Masalah**: Jika siswa beginner + skor critical + bukan persistent fail = tidak ada rule yang cover.

**Impact**: Siswa beginner dengan skor kritis tapi bukan persistent fail tidak dapat intervensi.

### Celah 3.2: Remedial + Beginner TidakCOVER

```php
// Ketiadaan:
RuleRemedialAtBeginner
// Evaluate: G02 (remedial) + G13 (beginner) + bukan G11 (tanpa hint)
```

**Masalah**: Semua recovery rule ada di medium/higher difficulty. Beginner dengan skor remedial = langsung ke standard promotion.

### Celah 3.3: Error Detection Tanpa Remedial Score

```php
// RuleSyntaxRecovery: G02 + G08 + G14 + G11
// RuleLogicRecovery:    G02 + G09 + G14 + G11
```

**Masalah**: Jika siswa G03 (standard) tapi ada syntax error, recovery tidak aktif karena memerlukan G02.

### Celah 3.4: Hint Usage dengan Good Score

Tidak ada handling untuk:

- G04 (mastery) + G11 (hint used)
- G03 (standard) + G11 (hint used)

**Masalah**: Hint yang digunakan dengan skor baik tidak mengubah behavior - tidak ada rule penalty/negative consequence.

---

## 4. Edge Cases Tidak Tertangani

### Celah 4.1: Hard + Mastery + Fast = Tidak Ada Promotion Path

```
RuleMasteryMedium: G04 + G05 + G14 + bukan G11 + bukan G16
```

**Masalah**: Di hard difficulty dengan mastery = tidak ada rule untuk naikkan ke tingkat selanjutnya (sudah max).

### Celah 4.2: Persistent Fail + Mastery (Contradiction)

```php
// Dua fakta ini seharusnya conflict tapi tidak ada guard
// G04 (mastery) + G20 (persistent fail)
```

**Masalah**: Tidak ada rule yang handle "mastery tapi tetap gagal". Ini indicatesomething wrong dengan scoring atau state.

### Celah 4.3: Time Fast tapi Wrong Answer

```php
// G05 (fast) tidak bergantung pada isCorrect
// Bisa: fast answer + wrong answer = G05 + G01/G02
```

**Masalah**: Fast tapi salah = mastery/fast flag conflict. Engine tidak handle ini.

### Celah 4.4: RepeatMaterial Tidak Ada

Tidak ada rule untuk:

- Jika semua materi sudah selesai = finish course
- Jika gagal berkali-kali di materi sama = repeat/review material

---

## 5. Missing Facts (Reserved tapi Tidak Ada)

| Constant                | Keterangan                 |
| ----------------------- | -------------------------- |
| `FACT_MODULE_STARTED`   | Reserved, tidak diproduksi |
| `FACT_COMPLETED_MODULE` | Reserved, tidak diproduksi |
| `FACT_COMPLETED_ALL`    | Reserved, tidak diproduksi |
| `FACT_HIGH_ENGAGEMENT`  | Reserved, tidak diproduksi |
| `FACT_TIME_SLOW`        | Reserved, tidak diproduksi |

**Impact**: Tidak bisa detect engagement patterns atau slow learners.

---

## 6. Rules dengan Masalah Implementasi

### Celah 6.1: RulePersistentVisualSafetyNet

```php
// Priority 5
public function evaluate(array $facts): bool {
    return $this->hasFailingScore($facts)  // G01 ATAU G02
        && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
        && $this->isVisualLearner($facts)
        && !$this->isFinalProject($facts);
}
```

**Masalah**: `hasFailingScore()` mencakup G01 dan G02. Jadi RulePersistentVisualSafetyNet akan trigger untuk skor remedial PULA, bukan hanya critical. Ini overlap dengan crisis intervention.

### Celah 6.2: RuleVisualCrisisIntervention

```php
// Priority 10
public function evaluate(array $facts): bool {
    return $this->hasCriticalScore($facts)  // G01 saja
        && $this->isVisualLearner($facts)
        && $this->isBeginnerDifficulty($facts)
        && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
        && !$this->isFinalProject($facts);
}
```

**Masalah**: BUTUH persistent fail dikecualikan, KECUALI persistent fail adalah scenario yang LEBIH PARAH. Seharusnya persistent fail JANGAN di-exclude - itu case yang perlu safety net lebih urgent.

### Celah 6.3: RuleBronzeCertificate vs RuleSilverCertificate

```php
// RuleBronzeCertificate evaluate:
return $this->hasPassingScore($facts)  // G03 ATAU G04
    && $this->isFinalProject($facts)
    && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS);

// RuleSilverCertificate evaluate:
return $this->hasStandardScore($facts)  // G03 saja - lebih strict
    && $this->isFinalProject($facts)
    && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS);
```

**Masalah**: Urutan priority:

- Priority 21: RuleGoldCertificate (G04 + G05 + G16 + G21 + bukan G11)
- Priority 22: RuleSilverCertificate (G03 + G16 + G21)
- Priority 23: RuleBronzeCertificate (G03/G04 + G16 + G21)

Bronze dan Silver TIDAK ADA bedanya waktu fast! G04 akan selalu dapat silver karena gold di priority 21 check G05 dulu.

---

## 7. Priority Conflicts (Rule yang Bersaing)

### Celah 7.1: Promotion Rules Race Condition

```
Priority 35: RuleMasteryMedium        → G04 + G05 + G14 + bukan G11
Priority 36: RuleAcceleratedMaterial → G04 + G05 + G18 + G21 + bukan G11
Priority 40: RuleAcceleratedJump     → G04 + G05 + G13 + bukan G11
Priority 50: RuleStandardPromotion   → (G03|G04) + (G13|G14|G15) + bukan G16 + bukan G17
```

**Masalah**: Semua promotion rules compete buat G04 (mastery) + G05 (fast). Urutan menentukan siapa menang, bukan logic yang paling sesuai.

### Celah 7.2: Final Project Rules

```
Priority 3:  RuleFinalProjectVisualPersistentFail  → G16 + G20 + G06
Priority 3:  RuleFinalProjectTextualPersistentFail → G16 + G20 + G07
Priority 15: RuleVisualProjectRevision         → G16 + (G01|G02)
Priority 15: RuleTextualProjectRevision        → G16 + (G01|G02)
Priority 21: RuleGoldCertificate            → G04 + G05 + G16 + G21 + bukan G11
Priority 22: RuleSilverCertificate        → G03 + G16 + G21
Priority 23: RuleBronzeCertificate        → (G03|G04) + G16 + G21
```

**Masalah**: Certificate di优先级 lebih tinggi DARI pada revision. Jadi student dapat GOLD certificate bahkan jika tidak layak (semua faktor terpenuhi) TAPI project masih harus direvisi?

---

## 8. Missing Anti-Loops

### Celah 8.1: Repeated Promotion Attempts

```php
// Di AdaptiveEngineService.shouldSkipRule()
// Guard 1: Accelerated Jump - sudah ada
// Guard 2: Accelerated Material - sudah ada
```

**Yang TIDAK ADA**:

- Guard untuk multiple graduation attempts
- Guard untuk certificate claims
- Guard untuk backtracking loops

### Celah 8.2: Infinite Recovery Loop

```
Recovery (H03/H04) → Study Material → NEXT → Recovery (H03/H04)
```

Tidak ada guard untuk prevent recovery loop jika student repeatedly gets same error type.

---

## 9. State Management Gaps

### Celah 9.1: Consecutive Correct Counter Tidak ada di Facts

Tidak ada fact untuk consecutive correct streak yang digunakan decision engine.

### Celah 9.2: Last Action Tracking Tidak Konsisten

```php
// Di AppliesProgression:78-91
$state['next_action'] = $nextAction;

// Di AdaptiveEngineService.shouldSkipRule()
// Cek last action hanya untuk material promotion
```

Yang TIDAK ada tracking:

- Last difficulty
- Last action timestamp untuk timing analysis
- Recovery count per error type

---

## 10. Rekomendasi Perbaikan

### Priority 1 (Critical)

1. **Perbaiki Fact Production**:
    - Implementasikan G17 (Practice mode)
    - Hapus atau gunakan G10 (No Error)
    - Fix G22 (Mixed) behavior

2. **Tambah Missing Rules**:
    - RuleRemedialAtBeginner
    - RuleCriticalAtBeginner
    - RuleMasteryAtHard (for max level students)

3. **Fix Priority Conflicts**:
    - Pindahkan recovery rules ke priority lebih tinggi
    - Review certificate vs revision priority

### Priority 2 (High)

4. **Tambah Facts**:
    - FACT_CONSECUTIVE_CORRECT
    - FACT_TIME_SLOW
    - FACT_HIGH_ENGAGEMENT

5. **Tambah Edge Case Handling**:
    - Mastery + hint used
    - Fast + wrong
    - Hard + mastery (max level)

### Priority 3 (Medium)

6. **Implementasikan Anti-Loops**:
    - Certificate claim guard
    - Recovery loop guard
    - Multiple graduation guard

7. **State Improvements**:
    - Consistent last action tracking
    - Consecutive counters as facts

---

## 11. Quick Fixes (Immediate)

```php
// Fix 1: FactGatheringService - tambahkan practice mode detection
if ($context['is_practice_mode'] ?? false) {
    $facts[] = AdaptiveConstants::FACT_IS_PRACTICE;
}

// Fix 2: RuleCriticalBacktracking - tambahkan beginner support
public function evaluate(array $facts): bool {
    return $this->hasCriticalScore($facts)
        && ($this->isBeginnerDifficulty($facts)   // ← TAMBAH
            || $this->isMediumDifficulty($facts)
            || $this->isHardDifficulty($facts))
        && !$this->isFinalProject($facts);
}

// Fix 3: Hapus G20 exclusion dari crisis intervention
// karena persistent fail更需要 intervention
```

---

## 12. New Rules yang。建议

```php
// RuleMasteryAtHard - untuk max difficulty students
class RuleMasteryAtHard extends BaseAdaptiveRule {
    protected int $priority = 55;  // setelah standard promotion

    public function evaluate(array $facts): bool {
        return $this->hasMasteryScore($facts)
            && $this->isHardDifficulty($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST)
            && $this->hasFact($facts, AdaptiveConstants::FACT_NEXT_UNLOCKED);
    }
    // action: lanjut ke materi berikutnya atau selesai
}

// RuleHintUsedWithMastery - hint dengan skor baik = downgrade
class RuleHintUsedWithMastery extends BaseAdaptiveRule {
    protected int $priority = 45;

    public function evaluate(array $facts): bool {
        return $this->hasMasteryScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED);
    }
    // action: message bahwa hint mengurangi mastery
}
```

---

End of analysis.
