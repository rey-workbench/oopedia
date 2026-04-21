# Adaptive Rules Catalog

## Quick Reference

| Priority | Rule ID | Rule Name | Category | Trigger Facts | Action |
|-----------|---------|-----------|----------|---------------|--------|
| 5 | RULE_01 | FinalProjectVisualPersistentFail | Crisis | G16+G20+G06 | H12 |
| 5 | RULE_02 | FinalProjectTextualPersistentFail | Crisis | G16+G20+G07 | H13 |
| 8 | RULE_03 | PersistentVisualSafetyNet | Crisis | G01/G02+G20+G06 | H14 |
| 8 | RULE_04 | PersistentTextualSafetyNet | Crisis | G01/G02+G20+G07 | H15 |
| 10 | RULE_05 | VisualCrisisIntervention | Crisis | G01+G06+G13 | H01 |
| 10 | RULE_06 | TextualCrisisIntervention | Crisis | G01+G07+G13 | H02 |
| 15 | RULE_07 | VisualProjectRevision | Project | G16+G01/G02 | H12 |
| 15 | RULE_08 | TextualProjectRevision | Project | G16+G01/G02 | H13 |
| 21 | RULE_09 | GoldCertificate | Certificate | G04+G05+G16+G21 | H09 |
| 22 | RULE_10 | SilverCertificate | Certificate | G03+G16+G21 | H10 |
| 23 | RULE_11 | BronzeCertificate | Certificate | G03/G04+G16+G21 | H11 |
| 25 | RULE_12 | SyntaxRecovery | Recovery | G02+G08+G14+G11 | H03 |
| 26 | RULE_13 | LogicRecovery | Recovery | G02+G09+G14+G11 | H04 |
| 27 | RULE_23 | ReviewPreviousMaterial | Backtrack | G02+G19 | H04 |
| 28 | RULE_14 | CriticalBacktracking | Backtrack | G01+(G13/G14/G15) | H07 |
| 29 | RULE_25 | FastWrong | Edge | G05+(G01/G02) | H04 |
| 30 | RULE_15 | ModuleGraduation | Promotion | G04+G05+G15+G21 | H08 |
| 35 | RULE_16 | MasteryMedium | Promotion | G04+G05+G14 | H05 |
| 36 | RULE_17 | AcceleratedMaterialPromotion | Promotion | G04+G05+G18+G21 | H16 |
| 40 | RULE_18 | AcceleratedJump | Promotion | G04+G05+G13 | H06 |
| 45 | RULE_21 | RemedialAtBeginner | Remediation | G02+G13 | H04 |
| 48 | RULE_19 | RemedialIndependent | Remediation | G02 | H04 |
| 50 | RULE_20 | StandardPromotion | Remediation | G03/G04+(G13/G14/G15) | H05 |
| 52 | RULE_24 | HintUsedWithGoodScore | Edge | G03/G04+G11 | H05 |
| 55 | RULE_22 | MasteryAtHard | Edge | G04+G05+G15+G21 | H16 |

## Priority Ranges

| Range | Category | Description |
|-------|---------|------------|
| 1-10 | Crisis/Safety | Urgent intervention for failing students |
| 11-20 | Project | Final project handling |
| 21-30 | Certificate | Achievement rewards |
| 31-40 | Promotion | Advancing difficulty/material |
| 41-50 | Remediation | Recovery and catch-up |
| 51-60 | Edge Cases | Special scenarios |

## Facts Reference

| Code | Fact | Description |
|------|------|-------------|
| G01 | FACT_SCORE_CRITICAL | Score < 50% |
| G02 | FACT_SCORE_REMEDIAL | Score 50-74% |
| G03 | FACT_SCORE_STANDARD | Score 75-89% |
| G04 | FACT_SCORE_MASTERY | Score >= 90% |
| G05 | FACT_TIME_FAST | Time < 70% allocated |
| G06 | FACT_STYLE_VISUAL | Visual learner |
| G07 | FACT_STYLE_TEXTUAL | Textual learner |
| G08 | FACT_ERROR_SYNTAX | Syntax error detected |
| G09 | FACT_ERROR_LOGIC | Logic error detected |
| G10 | FACT_NO_ERROR | No error (correct answer) |
| G11 | FACT_HINT_USED | Hint was used |
| G12 | FACT_IN_MODULE | In a module |
| G13 | FACT_DIFF_BEGINNER | Beginner difficulty |
| G14 | FACT_DIFF_MEDIUM | Medium difficulty |
| G15 | FACT_DIFF_HARD | Hard difficulty |
| G16 | FACT_IS_FINAL_PROJECT | Final project question |
| G17 | FACT_IS_PRACTICE | Practice mode |
| G18 | FACT_NEXT_UNLOCKED | Next material unlocked |
| G19 | FACT_PREV_UNLOCKED | Previous material unlocked |
| G20 | FACT_PERSISTENT_FAIL | Failed 2x consecutively |
| G21 | FACT_SATISFACTORY_PROGRESS | Progress >= 50% |
| G22 | FACT_STYLE_MIXED | Mixed learning style |

## Actions Reference

| Code | Action | Description |
|------|--------|-------------|
| H01 | VISUAL_CRISIS_INTERVENTION | Visual crisis intervention |
| H02 | TEXTUAL_CRISIS_INTERVENTION | Textual crisis intervention |
| H03 | SYNTAX_RECOVERY | Syntax recovery mode |
| H04 | LOGIC_RECOVERY | Logic recovery mode |
| H05 | STANDARD_PROMOTION | Standard next question |
| H06 | ACCELERATED_JUMP | Jump difficulty level |
| H07 | CRITICAL_BACKTRACKING | Reduce difficulty |
| H08 | MODULE_GRADUATION | Graduate module |
| H09 | GOLD_CERTIFICATE | Issue gold certificate |
| H10 | SILVER_CERTIFICATE | Issue silver certificate |
| H11 | BRONZE_CERTIFICATE | Issue bronze certificate |
| H12 | VISUAL_PROJECT_REVISION | Visual project revision |
| H13 | TEXTUAL_PROJECT_REVISION | Textual project revision |
| H14 | PERSISTENT_VISUAL_NET | Visual safety net |
| H15 | PERSISTENT_TEXTUAL_NET | Textual safety net |
| H16 | ACCELERATED_MATERIAL_PROMOTION | Jump to next material |

## Rule Categories

### Crisis (Priority 1-10)
Handle urgent situations where student is failing significantly.

### Project (Priority 11-20)
Handle final project questions and revisions.

### Certificate (Priority 21-30)
Award certificates based on performance.

### Promotion (Priority 31-40)
Advance student to higher difficulty or material.

### Remediation (Priority 41-50)
Help struggling students recover.

### Edge Cases (Priority 51-60)
Special scenarios like hint usage, rushing, or max difficulty.