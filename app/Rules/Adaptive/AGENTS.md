# Adaptive Rules Directory

**Generated:** 2026-04-02
**Part of:** Adaptive E-Learning Platform

## OVERVIEW

Forward-chaining rule-based system for adaptive learning. 20+ rules evaluate student facts and trigger appropriate actions (H01-H11).

## ARCHITECTURE

```
Rules/Adaptive/
├── BaseAdaptiveRule.php           # Abstract base with fact helpers
├── Contracts/AdaptiveRuleInterface.php  # Rule contract
├── Constants/AdaptiveConstants.php  # Fact/action codes
├── Concerns/                      # Composable rule traits
│   ├── HasScoreCondition.php
│   ├── HasErrorType.php
│   ├── HasLearningStyle.php
│   ├── HasDifficultyLevel.php
│   ├── AppliesProgression.php
│   ├── AppliesAchievement.php
│   ├── AppliesRecovery.php
│   └── AppliesCrisisIntervention.php
├── RuleRegistry.php               # Rule registration
└── Rule*.php                      # 20+ concrete rules
```

## FACT CODES (G01-G25)

| Code | Meaning                 |
| ---- | ----------------------- |
| G01  | First attempt           |
| G04  | Low difficulty question |
| G07  | Correct answer          |
| G10  | Medium difficulty       |
| G13  | Visual learning style   |
| G20  | Failed attempt          |

## ACTION CODES (H01-H11)

| Code | Action       |
| ---- | ------------ |
| H01  | Remediation  |
| H04  | Promotion    |
| H07  | Certificate  |
| H10  | Intervention |

## BASE RULE HELPERS

```php
protected function hasFact(array $facts, string $fact): bool
protected function hasAllFacts(array $facts, array $requiredFacts): bool  // AND
protected function hasAnyFact(array $facts, array $requiredFacts): bool   // OR
protected function notHasFact(array $facts, string $fact): bool
protected function hasScoreCondition(string $operator, int $value): bool
```

## CREATING NEW RULES

```php
// 1. Create rule class extending BaseAdaptiveRule
class RuleMyNewRule extends BaseAdaptiveRule
{
    protected string $ruleId = 'RXXX';
    protected string $ruleName = 'My New Rule';
    protected string $actionCode = 'H05';
    protected int $priority = 25;  // Lower = higher priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G07', 'G10']);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'PROMOTE';
        return $state;
    }
}

// 2. Register in RuleRegistry.php
```

## RULE CATEGORIES

| Category         | Examples                                                             |
| ---------------- | -------------------------------------------------------------------- |
| **Certificate**  | RuleBronzeCertificate, RuleSilverCertificate, RuleGoldCertificate    |
| **Promotion**    | RuleStandardPromotion, RuleAcceleratedJump, RuleModuleGraduation     |
| **Recovery**     | RuleSyntaxRecovery, RuleLogicRecovery, RuleTextualCrisisIntervention |
| **Intervention** | RuleVisualCrisisIntervention, RuleCriticalBacktracking               |
| **Safety Net**   | RulePersistentVisualSafetyNet, RulePersistentTextualSafetyNet        |
