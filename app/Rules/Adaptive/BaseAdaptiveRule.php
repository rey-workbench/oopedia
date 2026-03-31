<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\HasDifficultyLevel;
use App\Rules\Adaptive\Concerns\HasErrorType;
use App\Rules\Adaptive\Concerns\HasLearningStyle;
use App\Rules\Adaptive\Concerns\HasScoreCondition;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;

abstract class BaseAdaptiveRule implements AdaptiveRuleInterface
{
    use HasDifficultyLevel;
    use HasErrorType;
    use HasLearningStyle;
    use HasScoreCondition;

    protected string $ruleId;

    protected string $ruleName;

    protected string $actionCode;

    protected int $priority = 100; // Lower = higher priority

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function getRuleName(): string
    {
        return $this->ruleName;
    }

    public function getActionCode(): string
    {
        return $this->actionCode;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Helper: Check if a single fact exists.
     */
    protected function hasFact(array $facts, string $fact): bool
    {
        return in_array($fact, $facts, true);
    }

    /**
     * Helper: Check if all required facts exist (AND logic).
     */
    protected function hasAllFacts(array $facts, array $requiredFacts): bool
    {
        foreach ($requiredFacts as $required) {
            if (! $this->hasFact($facts, $required)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Helper: Check if any of the required facts exist (OR logic).
     */
    protected function hasAnyFact(array $facts, array $requiredFacts): bool
    {
        foreach ($requiredFacts as $required) {
            if ($this->hasFact($facts, $required)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Helper: Check if fact does NOT exist.
     */
    protected function notHasFact(array $facts, string $fact): bool
    {
        return ! $this->hasFact($facts, $fact);
    }

    /**
     * Abstract method: Must be implemented by concrete rules.
     */
    abstract public function evaluate(array $facts): bool;

    /**
     * Abstract method: Must be implemented by concrete rules.
     */
    abstract public function apply(array $state, array $context): array;
}
