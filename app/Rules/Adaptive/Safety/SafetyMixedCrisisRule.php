<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Safety;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class SafetyMixedCrisisRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_26';
    }

    public function getRuleName(): string
    {
        return 'Mixed Style Crisis Intervention';
    }

    public function getPriority(): int
    {
        return 10;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_MIXED_CRISIS_INTERVENTION;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasCriticalScore($facts)
            && $this->isBeginnerDifficulty($facts)
            && $this->isMixedLearner($facts)
            && ! $this->hasPersistentFailure($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyMixedCrisis(
            $state,
            'Sepertinya Anda butuh pendekatan materi yang lebih bervariasi. Mari ulas kembali konsep dasar dengan kombinasi teks dan visual.',
        );
    }
}
