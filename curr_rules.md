# Oopedia Adaptive Rules

## Safety Domain

| ID | Priority | Name | Class | Variant | Action | Conditions |
|---|---|---|---|---|---|---|
| RULE_01 | 1 | Final Project Visual Persistent Failure | SafetyFinalVisualPersistentRule | intervention | H24 | `isFinalProject() && hasFailingScore() && isVisualLearner() && hasPersistentFailure()` |
| RULE_02 | 1 | Final Project Textual Persistent Failure | SafetyFinalTextualPersistentRule | intervention | H25 | `isFinalProject() && hasFailingScore() && isTextualLearner() && hasPersistentFailure()` |
| RULE_25 | 1 | Final Project Mixed Persistent Failure | SafetyFinalMixedPersistentRule | result | H20 | `isFinalProject() && hasFailingScore() && isMixedLearner() && hasPersistentFailure()` |
| RULE_03 | 3 | Persistent Visual Safety Net | SafetyVisualNetRule | intervention | H14 | `hasPersistentFailure() && hasFailingScore() && isVisualLearner() && ! isFinalProject()` |
| RULE_04 | 3 | Persistent Textual Safety Net | SafetyTextualNetRule | intervention | H15 | `hasPersistentFailure() && hasFailingScore() && isTextualLearner() && ! isFinalProject()` |
| RULE_27 | 3 | Persistent Mixed Safety Net | SafetyMixedNetRule | intervention | H18 | `hasPersistentFailure() && hasFailingScore() && isMixedLearner() && ! isFinalProject()` |
| RULE_05 | 10 | Visual Crisis Intervention | SafetyVisualCrisisRule | intervention | H01 | `hasCriticalScore() && isBeginnerDifficulty() && isVisualLearner() && ! hasPersistentFailure() && ! isFinalProject()` |
| RULE_06 | 10 | Textual Crisis Intervention | SafetyTextualCrisisRule | intervention | H02 | `hasCriticalScore() && isBeginnerDifficulty() && isTextualLearner() && ! hasPersistentFailure() && ! isFinalProject()` |
| RULE_26 | 10 | Mixed Style Crisis Intervention | SafetyMixedCrisisRule | intervention | H17 | `hasCriticalScore() && isBeginnerDifficulty() && isMixedLearner() && ! hasPersistentFailure() && ! isFinalProject()` |

## Project Domain

| ID | Priority | Name | Class | Variant | Action | Conditions |
|---|---|---|---|---|---|---|
| RULE_07 | 15 | Visual Project Revision | ProjectVisualRevisionRule | intervention | H12 | `isFinalProject() && hasFailingScore() && isVisualLearner() && ! hasPersistentFailure()` |
| RULE_08 | 15 | Textual Project Revision | ProjectTextualRevisionRule | intervention | H13 | `isFinalProject() && hasFailingScore() && isTextualLearner() && ! hasPersistentFailure()` |
| RULE_28 | 15 | Mixed Project Revision | ProjectMixedRevisionRule | intervention | H19 | `isFinalProject() && hasFailingScore() && isMixedLearner() && ! hasPersistentFailure()` |

## Achievement Domain

| ID | Priority | Name | Class | Variant | Action | Conditions |
|---|---|---|---|---|---|---|
| RULE_09 | 21 | Gold Certificate Award | AwardGoldCertRule | certificate | H09 | `isFinalProject() && hasMasteryScore() && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST) && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS) && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)` |
| RULE_10 | 22 | Silver Certificate Award | AwardSilverCertRule | certificate | H10 | `isFinalProject() && hasPassingScore() && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS) && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)` |
| RULE_11 | 23 | Bronze Certificate Award | AwardBronzeCertRule | certificate | H11 | `isFinalProject() && hasPassingScore() && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED) && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS)` |

## Recovery Domain

| ID | Priority | Name | Class | Variant | Action | Conditions |
|---|---|---|---|---|---|---|
| RULE_21 | 31 | Remedial Redirect At Beginner | RecoverRemedialBeginnerRule | intervention | H21 | `hasFailingScore() && isBeginnerDifficulty() && ! hasCriticalScore() && ! isFinalProject()` |
| RULE_18 | 32 | Syntax Error Recovery | RecoverSyntaxRule | intervention | H03 | `// Widened to include HARD difficulty hasFailingScore() && hasSyntaxError() && (isMediumDifficulty() || isHardDifficulty()) && ! isFinalProject()` |
| RULE_19 | 33 | Logic Error Recovery | RecoverLogicRule | intervention | H04 | `// Widened to include HARD difficulty hasFailingScore() && hasLogicError() && (isMediumDifficulty() || isHardDifficulty()) && ! isFinalProject()` |
| RULE_22 | 34 | Remedial Independent Study | RecoverRemedialIndepRule | intervention | H26 | `hasFailingScore() && $this->notHasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS) && ! isFinalProject()` |
| RULE_17 | 35 | Critical Performance Backtracking | RecoverBacktrackRule | backtrack | H07 | `hasCriticalScore() && (isMediumDifficulty() || isHardDifficulty()) && ! isFinalProject()` |
| RULE_23 | 36 | Review Previous Material | RecoverReviewPrevRule | intervention | H22 | `hasCriticalScore() && isBeginnerDifficulty() && ! isFinalProject()` |
| RULE_29 | 37 | Remedial Due To High Hint Usage | RecoverRemedialHintRule | intervention | H21 | `hasFailingScore() && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED) && isBeginnerDifficulty() && ! isFinalProject()` |
| RULE_20 | 38 | Careless Fast Wrong Recovery | RecoverFastWrongRule | intervention | H23 | `hasFailingScore() && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST) && ! isFinalProject()` |

## Progression Domain

| ID | Priority | Name | Class | Variant | Action | Conditions |
|---|---|---|---|---|---|---|
| RULE_13 | 40 | Accelerated Difficulty Jump | ProgressJumpRule | acceleration | H06 | `hasMasteryScore() && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST) && isBeginnerDifficulty() && ! isFinalProject()` |
| RULE_15 | 42 | Mastery At Medium Difficulty | ProgressMasteryMedRule | result | H27 | `hasMasteryScore() && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST) && isMediumDifficulty() && ! isFinalProject()` |
| RULE_14 | 45 | Accelerated Material Graduation | ProgressMaterialRule | result | H16 | `hasMasteryScore() && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS) && isHardDifficulty() && ! isFinalProject()` |
| RULE_16 | 48 | Standard Module Graduation | ProgressGraduationRule | certificate | H08 | `hasPassingScore() && isHardDifficulty() && ! isFinalProject()` |
| RULE_12 | 50 | Standard Level Promotion | ProgressStandardRule | result | H05 | `hasPassingScore() && ! isFinalProject()` |

## Interaction Domain

| ID | Priority | Name | Class | Variant | Action | Conditions |
|---|---|---|---|---|---|---|
| RULE_24 | 52 | Hint Used With Good Score | InteractHintSuccessRule | result | H05 | `hasPassingScore() && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED) && ! isFinalProject()` |

