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

    protected int $priority = 100;

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

    protected function hasFact(array $facts, string $fact): bool
    {
        return in_array($fact, $facts, true);
    }

    protected function hasAnyFact(array $facts, array $requiredFacts): bool
    {
        foreach ($requiredFacts as $required) {
            if ($this->hasFact($facts, $required)) {
                return true;
            }
        }

        return false;
    }

    protected function notHasFact(array $facts, string $fact): bool
    {
        return ! $this->hasFact($facts, $fact);
    }

    abstract public function evaluate(array $facts): bool;

    abstract public function apply(array $state, array $context): array;
}
