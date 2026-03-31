<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesProgression;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class Rule_StandardPromotion extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $ruleId = 'RULE_05';

    protected string $ruleName = 'Standard Promotion';

    protected string $actionCode = AdaptiveConstants::ACTION_STANDARD_PROMOTION;

    protected int $priority = 50;

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [
            AdaptiveConstants::FACT_SCORE_STANDARD,
            AdaptiveConstants::FACT_SCORE_MASTERY,
        ]) && $this->hasAnyFact($facts, [
            AdaptiveConstants::FACT_DIFF_BEGINNER,
            AdaptiveConstants::FACT_DIFF_MEDIUM,
            AdaptiveConstants::FACT_DIFF_HARD,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT); // Final Project uses certificate rules
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyStandardPromotion($state, $context['is_correct'] ?? false);
    }
}
