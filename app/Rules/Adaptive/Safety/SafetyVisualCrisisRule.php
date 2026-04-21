<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Safety;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class SafetyVisualCrisisRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_05';
    }

    public function getRuleName(): string
    {
        return 'Visual Crisis Intervention';
    }

    public function getPriority(): int
    {
        return 10;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_VISUAL_CRISIS_INTERVENTION;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasCriticalScore($facts)
            && $this->isBeginnerDifficulty($facts)
            && $this->isVisualLearner($facts)
            && ! $this->hasPersistentFailure($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyVisualCrisis(
            $state,
            'Sepertinya Anda butuh penguatan konsep. Mari ulas kembali materi visual fundamental.',
        );
    }
}
