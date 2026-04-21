<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Safety;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class SafetyVisualNetRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_03';
    }

    public function getRuleName(): string
    {
        return 'Persistent Visual Safety Net';
    }

    public function getPriority(): int
    {
        return 3;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_PERSISTENT_VISUAL_NET;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasPersistentFailure($facts)
            && $this->hasFailingScore($facts)
            && $this->isVisualLearner($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyPersistentVisualSafety(
            $state,
            'Kami mendeteksi kendala berkelanjutan. Mari ulas materi koding dengan bantuan visual interaktif.',
        );
    }
}
