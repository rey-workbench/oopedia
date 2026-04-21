<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Safety;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class SafetyTextualNetRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_04';
    }

    public function getRuleName(): string
    {
        return 'Persistent Textual Safety Net';
    }

    public function getPriority(): int
    {
        return 3;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_PERSISTENT_TEXTUAL_NET;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasPersistentFailure($facts)
            && $this->hasFailingScore($facts)
            && $this->isTextualLearner($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyPersistentTextualSafety(
            $state,
            'Anda membutuhkan bantuan lebih lanjut. Mari baca kembali materi teori teks secara mendalam.',
        );
    }
}
