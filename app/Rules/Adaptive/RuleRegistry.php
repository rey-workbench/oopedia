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
            RulePersistentVisualSafetyNet::class,
            RulePersistentTextualSafetyNet::class,
            RuleVisualCrisisIntervention::class,
            RuleTextualCrisisIntervention::class,
            RuleVisualProjectRevision::class,
            RuleTextualProjectRevision::class,
            RuleFinalProjectVisualPersistentFail::class,
            RuleFinalProjectTextualPersistentFail::class,
            RuleSyntaxRecovery::class,
            RuleLogicRecovery::class,
            RuleRemedialIndependent::class,
            RuleGoldCertificate::class,
            RuleSilverCertificate::class,
            RuleBronzeCertificate::class,
            RuleModuleGraduation::class,
            RuleCriticalBacktracking::class,
            RuleMasteryMedium::class,
            RuleAcceleratedMaterialPromotion::class,
            RuleAcceleratedJump::class,
            RuleStandardPromotion::class,
        ];

        foreach ($ruleClasses as $ruleClass) {
            $this->register(new $ruleClass);
        }

        usort($this->rules, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());
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

    public function getRulesByAction(string $actionCode): array
    {
        return array_filter(
            $this->rules,
            fn ($rule) => $rule->getActionCode() === $actionCode,
        );
    }
}
