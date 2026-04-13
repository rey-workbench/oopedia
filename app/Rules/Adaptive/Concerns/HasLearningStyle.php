<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait HasLearningStyle
{
    protected function isVisualLearner(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_VISUAL);
    }

    protected function isTextualLearner(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_TEXTUAL);
    }

    protected function isMixedLearner(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_MIXED);
    }

    protected function getLearningStyle(array $facts): ?string
    {
        if ($this->isVisualLearner($facts)) {
            return 'visual';
        }
        if ($this->isTextualLearner($facts)) {
            return 'textual';
        }
        if ($this->isMixedLearner($facts)) {
            return 'mixed';
        }

        return null;
    }
}
