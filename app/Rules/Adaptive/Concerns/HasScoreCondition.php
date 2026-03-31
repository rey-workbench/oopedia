<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait HasScoreCondition
{
    protected function hasCriticalScore(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_SCORE_CRITICAL);
    }

    protected function hasRemedialScore(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_SCORE_REMEDIAL);
    }

    protected function hasStandardScore(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_SCORE_STANDARD);
    }

    protected function hasMasteryScore(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_SCORE_MASTERY);
    }

    protected function hasPassingScore(array $facts): bool
    {
        return $this->hasAnyFact($facts, [
            AdaptiveConstants::FACT_SCORE_STANDARD,
            AdaptiveConstants::FACT_SCORE_MASTERY,
        ]);
    }

    protected function hasFailingScore(array $facts): bool
    {
        return $this->hasAnyFact($facts, [
            AdaptiveConstants::FACT_SCORE_CRITICAL,
            AdaptiveConstants::FACT_SCORE_REMEDIAL,
        ]);
    }
}
