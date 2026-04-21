<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait HasErrorType
{
    protected function hasSyntaxError(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_ERROR_SYNTAX);
    }

    protected function hasLogicError(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_ERROR_LOGIC);
    }

    protected function hasNoError(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_NO_ERROR);
    }

    protected function hasNoErrorInContext(array $facts, bool $isCorrect): bool
    {
        return $isCorrect || $this->hasFact($facts, AdaptiveConstants::FACT_NO_ERROR);
    }

    protected function hasAnyError(array $facts): bool
    {
        return $this->hasAnyFact($facts, [
            AdaptiveConstants::FACT_ERROR_SYNTAX,
            AdaptiveConstants::FACT_ERROR_LOGIC,
        ]);
    }
}
