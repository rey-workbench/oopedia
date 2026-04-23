# Adaptive Engine Analysis - Oopedia

## Overview

Oopedia's adaptive learning engine is a forward-chaining ITS (Intelligent Tutoring System) that adjusts question difficulty and content based on student performance. The system uses prioritized rules to evaluate facts gathered from quiz attempts and emit actions that drive remediation, promotion, certification, and intervention.

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                   Quiz Attempt                             │
│           (is_correct, time_spent, hint_used)            │
└─────────────────────┬───────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│           FactGatheringService                        │
│      Transforms attempt → G-codes (facts)              │
└─────────────────────┬───────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│           AdaptiveEngineService                      │
│   Evaluates facts against rules (priority order)       │
│   First match wins → applies state changes            │
└─────────────────────┬───────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│           NextActionResolverService              │
│        Translates H-codes → UI actions         │
└───────────────────────────────────────────────┘
```

---

## Facts (G-Codes)

| Code | Constant | Description |
| :--- | :--- | :--- |
| G01 | FACT_SCORE_CRITICAL | Score < 50% |
| G02 | FACT_SCORE_REMEDIAL | Score 50-74% |
| G03 | FACT_SCORE_STANDARD | Score 75-89% |
| G04 | FACT_SCORE_MASTERY | Score >= 90% |
| G05 | FACT_TIME_FAST | Answered < 70% allocated time |
| G06 | FACT_STYLE_VISUAL | Learner prefers visual content |
| G07 | FACT_STYLE_TEXTUAL | Learner prefers textual content |
| G08 | FACT_ERROR_SYNTAX | Syntax error detected |
| G09 | FACT_ERROR_LOGIC | Logic error detected |
| G10 | FACT_NO_ERROR | No error (correct answer) |
| G11 | FACT_HINT_USED | Hint was used |
| G12 | FACT_IN_MODULE | Within a module |
| G13 | FACT_DIFF_BEGINNER | Current difficulty: beginner |
| G14 | FACT_DIFF_MEDIUM | Current difficulty: medium |
| G15 | FACT_DIFF_HARD | Current difficulty: hard |
| G16 | FACT_IS_FINAL_PROJECT | Final project question |
| G17 | FACT_IS_PRACTICE | Practice question |
| G18 | FACT_NEXT_UNLOCKED | Next question unlocked |
| G19 | FACT_PREV_UNLOCKED | Previous question unlocked |
| G20 | FACT_PERSISTENT_FAIL | 3+ consecutive failures |
| G21 | FACT_SATISFACTORY_PROGRESS | Progress is satisfactory |
| G22 | FACT_STYLE_MIXED | Mixed learning style |

---

## Actions (H-Codes)

| Code | Constant | Action |
| :--- | :--- | :--- |
| H01 | ACTION_VISUAL_CRISIS_INTERVENTION | Crisis干预 - visual learner |
| H02 | ACTION_TEXTUAL_CRISIS_INTERVENTION | Crisis干预 - textual learner |
| H03 | ACTION_SYNTAX_RECOVERY | 恢复语法 |
| H04 | ACTION_LOGIC_RECOVERY | 恢复逻辑 |
| H05 | ACTION_STANDARD_PROMOTION | 标准升级 - next question |
| H06 | ACTION_ACCELERATED_JUMP | 快速跳过 - bypass levels |
| H07 | ACTION_CRITICAL_BACKTRACKING | 后退到beginner |
| H08 | ACTION_MODULE_GRADUATION | 模块完成 |
| H09 | ACTION_GOLD_CERTIFICATE | 颁发金证书 |
| H10 | ACTION_SILVER_CERTIFICATE | 颁发银证书 |
| H11 | ACTION_BRONZE_CERTIFICATE | 颁发铜证书 |
| H12 | ACTION_VISUAL_PROJECT_REVISION | 项目修订 - visual |
| H13 | ACTION_TEXTUAL_PROJECT_REVISION | 项目修订 - textual |
| H14 | ACTION_PERSISTENT_VISUAL_NET | 安全网 - visual |
| H15 | ACTION_PERSISTENT_TEXTUAL_NET | 安全网 - textual |
| H16 | ACTION_ACCELERATED_MATERIAL | 跳转学习材料 |
| H17 | ACTION_MIXED_CRISIS_INTERVENTION | 混合危机干预 |
| H18 | ACTION_PERSISTENT_MIXED_NET | 混合安全网 |
| H21 | ACTION_REMEDIAL_AT_BEGINNER | 从头恢复 |
| H22 | ACTION_REVIEW_PREVIOUS | 复习 previous |
| H23 | ACTION_FAST_WRONG_RECOVERY | 快速错误恢复 |
| H26 | ACTION_REMEDIAL_INDEPENDENT | 独立复习 |
| H27 | ACTION_MASTERY_MEDIUM | 中级掌握 |

---

## Rule Categories & Execution Order

Rules are evaluated in priority order (lower = higher precedence).

### 1. Safety Domain (Priority 1-10)

**Purpose**: Crisis intervention and persistent failure safety nets.

| Rule | ID | Priority | Trigger |
| :--- | :--- | :--- | :--- |
| SafetyFinalVisualPersistentRule | RULE_01 | 1 | Final + persistent fail + visual |
| SafetyFinalTextualPersistentRule | RULE_02 | 2 | Final + persistent fail + textual |
| SafetyFinalMixedPersistentRule | RULE_03 | 3 | Final + persistent fail + mixed |
| SafetyVisualNetRule | RULE_04 | 4 | Persistent fail + visual |
| SafetyTextualNetRule | RULE_05 | 5 | Persistent fail + textual |
| SafetyMixedNetRule | RULE_07 | 7 | Persistent fail + mixed |
| SafetyVisualCrisisRule | RULE_08 | 8 | Critical score + beginner + visual |
| SafetyTextualCrisisRule | RULE_06 | 10 | Critical score + beginner + textual |
| SafetyMixedCrisisRule | RULE_09 | 9 | Critical score + beginner + mixed |

**Behavior**: If student fails persistently or hits critical threshold, trigger intervention instead of progression.

---

### 2. Project Domain (Priority 11-13)

| Rule | ID | Priority | Trigger |
| :--- | :--- | :--- | :--- |
| ProjectVisualRevisionRule | RULE_10 | 11 | Final project + visual learner |
| ProjectTextualRevisionRule | RULE_11 | 12 | Final project + textual learner |
| ProjectMixedRevisionRule | RULE_13 | 13 | Final project + mixed learner |

**Behavior**: Request project revision instead of allowing graduation.

---

### 3. Achievement Domain (Priority 20-22)

| Rule | ID | Priority | Trigger |
| :--- | :--- | :--- | :--- |
| AwardGoldCertRule | RULE_20 | 20 | Mastery + 5+ consecutive correct |
| AwardSilverCertRule | RULE_21 | 21 | Standard + 5+ consecutive correct |
| AwardBronzeCertRule | RULE_22 | 22 | Remedial + module completed |

**Behavior**: Issue certificates based on performance tier.

---

### 4. Recovery Domain (Priority 30-37)

| Rule | ID | Priority | Trigger |
| :--- | :--- | :--- | :--- |
| RecoverBacktrackRule | RULE_30 | 30 | Syntax/logic error + fast answer |
| RecoverSyntaxRule | RULE_31 | 31 | Syntax error detected |
| RecoverLogicRule | RULE_32 | 32 | Logic error detected |
| RecoverFastWrongRule | RULE_33 | 33 | Fast + wrong answer |
| RecoverRemedialBeginnerRule | RULE_34 | 34 | Multiple remedial scores |
| RecoverRemedialIndepRule | RULE_35 | 35 | Independent remedial |
| RecoverReviewPrevRule | RULE_36 | 36 | Review previous material |
| RecoverRemedialHintRule | RULE_37 | 37 | Hint used + wrong |

**Behavior**: Provide targeted remediation based on error type.

---

### 5. Progression Domain (Priority 50-55)

| Rule | ID | Priority | Trigger |
| :--- | :--- | :--- | :--- |
| ProgressStandardRule | RULE_12 | 50 | Passing score (75%+) |
| ProgressJumpRule | RULE_14 | 52 | Mastery + fast answer |
| ProgressMaterialRule | RULE_15 | 53 | Accelerated material |
| ProgressMasteryMedRule | RULE_16 | 54 | Mastery at medium |
| ProgressGraduationRule | RULE_17 | 55 | Module completion |

**Behavior**: Standard progression, fast-track jumps, and module graduation.

---

### 6. Interaction Domain (Priority 60)

| Rule | ID | Priority | Trigger |
| :--- | :--- | :--- | :--- |
| InteractHintSuccessRule | RULE_18 | 60 | Hint used + correct answer |

**Behavior**: Reward hint usage with positive feedback.

---

## State Management

The engine maintains `adaptive_state` in `student_states` table:

```json
{
  "current_difficulty": "beginner",
  "consecutive_correct": 0,
  "consecutive_recovery_count": 0,
  "last_rule": { "id": "RULE_12", "action": "H05" },
  "target_difficulty": null,
  "fast_track_active": false,
  "current_material_id": "m1"
}
```

---

## Key Services

### FactGatheringService

Transforms quiz attempt into G-codes:
- Score band → G01-G04
- Time ratio → G05
- Learning style → G06/G07/G22
- Error detection → G08/G09/G10
- Difficulty → G13-G15

### AdaptiveEngineService

- Loops through `RuleRegistry` in priority order
- Calls `rule.evaluate(facts)` - side-effect free
- First matching rule wins (priority-based)
- Calls `rule.apply(state, context)` to get state changes
- Merges outputs with "First-Priority Wins" strategy

### NextActionResolverService

Maps H-codes to UI actions:
- H01/H02 → `next_action: REVIEW_MATERIAL`
- H05 → `next_action: NEXT_QUESTION`
- H06 → `next_action: ACCELERATED_JUMP`
- H08 → `next_action: FINISH_MATERIAL`
- H09/H10/H11 → `next_action: ISSUE_CERTIFICATE`

---

## Evaluation Flow

```
1. Student submits answer
2. AdaptiveQuizFlowService validates & calculates score
3. FactGatheringService builds fact array
4. AdaptiveEngineService.evaluate(facts, state, context)
   └─ For each rule in registry (priority order):
       ├─ shouldSkipRule()? → continue
       ├─ evaluate(facts) → false? → continue
       └─ apply(state, context) → merge into finalState
5. First matching rule → new_state with action/message
6. NextActionResolverService resolves UI action
7. Return response to frontend
```

---

## Threshold Configuration

From `AdaptiveConstants`:

| Difficulty | Allocated Time (seconds) |
| :--- | :--- |
| beginner | 45 |
| medium | 90 |
| hard | 150 |
| final | 300 |

| Score Band | Threshold |
| :--- | :--- |
| Critical (G01) | < 50% |
| Remedial (G02) | 50-74% |
| Standard (G03) | 75-89% |
| Mastery (G04) | >= 90% |

| Time | Threshold |
| :--- | :--- |
| Fast (G05) | < 70% of allocated |

---

## Sample State Transitions

### Scenario 1: Standard Progression

```
Initial: { adaptive_state: { consecutive_correct: 2, current_difficulty: "beginner" } }
Attempt: Score 85% (G03), time 30s
Facts: [G03, G05, G13]
Matched: ProgressStandardRule (RULE_12)
Action: H05 (STANDARD_PROMOTION)
New State: { consecutive_correct: 3, target_difficulty: "medium" }
```

### Scenario 2: Critical Failure Recovery

```
Initial: { adaptive_state: { consecutive_correct: 0, current_difficulty: "medium" } }
Attempt: Score 40% (G01), logic error
Facts: [G01, G09, G14]
Matched: RecoverLogicRule (RULE_32)
Action: H04 (LOGIC_RECOVERY)
New State: { consecutive_recovery_count: 1, next_action: STUDY_LOGIC }
```

### Scenario 3: Certificate Award

```
Initial: { adaptive_state: { consecutive_correct: 4 } }
Attempt: Score 92% (G04), fast
Facts: [G04, G05]
Matched: AwardGoldCertRule (RULE_20)
Action: H09 (GOLD_CERTIFICATE)
New State: { certification: "gold", badges: ["gold_architect"] }
```

---

## Risk Assessment

### Potential Issues

1. **Rule Conflict**: Multiple rules may evaluate true but only first in priority wins - may miss desired outcomes
2. **State Drift**: accumulated state may become inconsistent if DB/cookie sync fails
3. **Recovery Loop**: Without prevention, same recovery rule could fire repeatedly (mitigated by `consecutive_recovery_count >= 2` check)
4. **Hint Gaming**: Students could exploit hints then answer correctly for positive reinforcement

### Mitigation Strategies

- Recovery loop prevention in `shouldSkipRule()`
- Max 2 consecutive same-type recovery actions
- Accelerated jump disabled at hard difficulty
- Achievement-based certificates (not just score thresholds)

---

## File Structure

```
app/
├── Rules/Adaptive/
│   ├── BaseAdaptiveRule.php
│   ├── RuleRegistry.php
│   ├── Constants/
│   │   ├── AdaptiveConstants.php
│   │   └── RulePriorities.php
│   ├── Contracts/
│   │   └── AdaptiveRuleInterface.php
│   ├── Concerns/
│   │   ├── AppliesCrisisIntervention.php
│   │   ├── AppliesProgression.php
│   │   ├── HasDifficultyLevel.php
│   │   ├── HasErrorType.php
│   │   ├── HasLearningStyle.php
│   │   └── HasScoreCondition.php
│   ├── Safety/          # 9 rules
│   ├── Project/        # 3 rules
│   ├── Achievement/    # 3 rules
│   ├── Recovery/      # 8 rules
│   ├── Progression/   # 5 rules
│   └── Interaction/   # 1 rule
└── Services/Adaptive/
    ├── AdaptiveEngineService.php
    ├── AdaptiveQuizFlowService.php
    ├── FactGatheringService.php
    └── NextActionResolverService.php
```

**Total Rules**: 29

---

## Extension Points

To add new adaptive behavior:

1. Add constants to `AdaptiveConstants.php` (new G/H codes if needed)
2. Create rule class extending `BaseAdaptiveRule`
3. Implement `evaluate(facts)` - return bool
4. Implement `apply(state, context)` - return state array
5. Register in `RuleRegistry::registerRules()`
6. Set priority relative to existing rules

Example:
```php
final class MyNewRule extends BaseAdaptiveRule
{
    public function getRuleId(): string { return 'RULE_99'; }
    public function getRuleName(): string { return 'My New Rule'; }
    public function getPriority(): int { return 25; } // Between achievement & recovery
    public function getActionCode(): string { return AdaptiveConstants::ACTION_NEW_ACTION; }
    
    public function evaluate(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_SCORE_MASTERY)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST);
    }
    
    public function apply(array $state, array $context): array
    {
        return ['next_action' => 'SPECIAL_REWARD', 'bonus_points' => 50];
    }
}
```