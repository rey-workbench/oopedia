<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesProgression;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class ProgressStandardRule extends BaseAdaptiveRule
{
    use AppliesProgression;

    public function getRuleId(): string
    {
        return 'RULE_12';
    }

    public function getRuleName(): string
    {
        return 'Standard Level Promotion';
    }

    public function getPriority(): int
    {
        return 50;
    }

    protected string $variant = 'result';

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_STANDARD_PROMOTION;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasPassingScore($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyStandardPromotion($state, (bool) ($context['is_correct'] ?? true));
    }
}
