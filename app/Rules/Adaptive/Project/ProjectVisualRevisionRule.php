<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Project;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class ProjectVisualRevisionRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_07';
    }

    public function getRuleName(): string
    {
        return 'Visual Project Revision';
    }

    public function getPriority(): int
    {
        return 15;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_VISUAL_PROJECT_REVISION;
    }

    public function evaluate(array $facts): bool
    {
        return $this->isFinalProject($facts)
            && $this->hasFailingScore($facts)
            && $this->isVisualLearner($facts)
            && ! $this->hasPersistentFailure($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyVisualProjectRevision($state, $context['facts'] ?? []);
    }
}
