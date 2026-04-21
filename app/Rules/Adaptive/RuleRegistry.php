<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;

class RuleRegistry
{
    protected array $rules = [];

    public function __construct()
    {
        $this->registerRules();
    }

    protected function registerRules(): void
    {
        $ruleClasses = [
            // Safety Domain
            Safety\SafetyFinalVisualPersistentRule::class,
            Safety\SafetyFinalTextualPersistentRule::class,
            Safety\SafetyFinalMixedPersistentRule::class,
            Safety\SafetyVisualNetRule::class,
            Safety\SafetyTextualNetRule::class,
            Safety\SafetyMixedNetRule::class,
            Safety\SafetyVisualCrisisRule::class,
            Safety\SafetyTextualCrisisRule::class,
            Safety\SafetyMixedCrisisRule::class,

            // Project Domain
            Project\ProjectVisualRevisionRule::class,
            Project\ProjectTextualRevisionRule::class,
            Project\ProjectMixedRevisionRule::class,

            // Achievement Domain
            Achievement\AwardGoldCertRule::class,
            Achievement\AwardSilverCertRule::class,
            Achievement\AwardBronzeCertRule::class,

            // Recovery Domain
            Recovery\RecoverBacktrackRule::class,
            Recovery\RecoverSyntaxRule::class,
            Recovery\RecoverLogicRule::class,
            Recovery\RecoverFastWrongRule::class,
            Recovery\RecoverRemedialBeginnerRule::class,
            Recovery\RecoverRemedialIndepRule::class,
            Recovery\RecoverReviewPrevRule::class,
            Recovery\RecoverRemedialHintRule::class,

            // Progression Domain
            Progression\ProgressStandardRule::class,
            Progression\ProgressJumpRule::class,
            Progression\ProgressMaterialRule::class,
            Progression\ProgressMasteryMedRule::class,
            Progression\ProgressGraduationRule::class,

            // Interaction Domain
            Interaction\InteractHintSuccessRule::class,
        ];

        foreach ($ruleClasses as $ruleClass) {
            $this->register(new $ruleClass);
        }

        usort($this->rules, function ($a, $b): int {
            $priorityComparison = $a->getPriority() <=> $b->getPriority();

            if ($priorityComparison !== 0) {
                return $priorityComparison;
            }

            return strcmp($a->getRuleId(), $b->getRuleId());
        });
    }

    protected function register(AdaptiveRuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }

    public function getAllRules(): array
    {
        return $this->rules;
    }

    public function getRuleById(string $ruleId): ?AdaptiveRuleInterface
    {
        foreach ($this->rules as $rule) {
            if ($rule->getRuleId() === $ruleId) {
                return $rule;
            }
        }

        return null;
    }
}
