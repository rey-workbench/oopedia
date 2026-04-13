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
            // Priority 3
            RuleFinalProjectVisualPersistentFail::class,
            RuleFinalProjectTextualPersistentFail::class,

            // Priority 5
            RulePersistentVisualSafetyNet::class,
            RulePersistentTextualSafetyNet::class,

            // Priority 10
            RuleVisualCrisisIntervention::class,
            RuleTextualCrisisIntervention::class,

            // Priority 15
            RuleVisualProjectRevision::class,
            RuleTextualProjectRevision::class,

            // Priority 21 - 23 (Certificates)
            RuleGoldCertificate::class,
            RuleSilverCertificate::class,
            RuleBronzeCertificate::class,

            // Priority 24 - 25 (Recovery)
            RuleSyntaxRecovery::class,
            RuleLogicRecovery::class,

            // Priority 27
            RuleCriticalBacktracking::class,

            // Priority 30
            RuleModuleGraduation::class,

            // Priority 35
            RuleMasteryMedium::class,

            // Priority 36
            RuleAcceleratedMaterialPromotion::class,

            // Priority 40
            RuleAcceleratedJump::class,

            // Priority 48
            RuleRemedialIndependent::class,

            // Priority 50
            RuleStandardPromotion::class,
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
