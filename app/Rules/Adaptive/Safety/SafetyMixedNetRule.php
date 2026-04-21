<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Safety;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class SafetyMixedNetRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_27';
    }

    public function getRuleName(): string
    {
        return 'Persistent Mixed Safety Net';
    }

    public function getPriority(): int
    {
        return 3;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_PERSISTENT_MIXED_NET;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasPersistentFailure($facts)
            && $this->hasFailingScore($facts)
            && $this->isMixedLearner($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyPersistentMixedSafety(
            $state,
            'Kami mendeteksi kendala belajar yang cukup berat. Mari kita tinjau kembali materi menggunakan metode belajar campuran yang lebih komprehensif.',
        );
    }
}
