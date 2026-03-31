<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait HasDifficultyLevel
{
    protected function isBeginnerDifficulty(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_DIFF_BEGINNER);
    }

    protected function isMediumDifficulty(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_DIFF_MEDIUM);
    }

    protected function isHardDifficulty(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_DIFF_HARD);
    }

    protected function isFinalProject(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    protected function isPractice(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_IS_PRACTICE);
    }
}
