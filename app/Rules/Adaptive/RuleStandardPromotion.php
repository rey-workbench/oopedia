<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesProgression;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleStandardPromotion extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $ruleId = 'RULE_20';

    protected string $ruleName = 'Standard Promotion';

    protected string $actionCode = AdaptiveConstants::ACTION_STANDARD_PROMOTION;

    protected int $priority = 50;

    public function evaluate(array $facts): bool
    {
        return $this->hasPassingScore($facts)
            && ($this->isBeginnerDifficulty($facts)
                || $this->isMediumDifficulty($facts)
                || $this->isHardDifficulty($facts))
            && ! $this->isFinalProject($facts)
            && ! $this->isPractice($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyStandardPromotion($state, $context['is_correct'] ?? false);
    }
}
