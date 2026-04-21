<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Safety;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class SafetyTextualCrisisRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_06';
    }

    public function getRuleName(): string
    {
        return 'Textual Crisis Intervention';
    }

    public function getPriority(): int
    {
        return 10;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_TEXTUAL_CRISIS_INTERVENTION;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasCriticalScore($facts)
            && $this->isBeginnerDifficulty($facts)
            && $this->isTextualLearner($facts)
            && ! $this->hasPersistentFailure($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyTextualCrisis(
            $state,
            'Sepertinya materi teks fundamental belum cukup dikuasai. Mari baca kembali penjelasannya.',
        );
    }
}
