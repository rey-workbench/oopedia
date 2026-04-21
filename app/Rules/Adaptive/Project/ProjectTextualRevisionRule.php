<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Project;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class ProjectTextualRevisionRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_08';
    }

    public function getRuleName(): string
    {
        return 'Textual Project Revision';
    }

    public function getPriority(): int
    {
        return 15;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_TEXTUAL_PROJECT_REVISION;
    }

    public function evaluate(array $facts): bool
    {
        return $this->isFinalProject($facts)
            && $this->hasFailingScore($facts)
            && $this->isTextualLearner($facts)
            && ! $this->hasPersistentFailure($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyTextualProjectRevision($state, $context['facts'] ?? []);
    }
}
