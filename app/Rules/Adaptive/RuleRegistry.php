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
            Rule_PersistentVisualSafetyNet::class,
            Rule_PersistentTextualSafetyNet::class,
            Rule_VisualCrisisIntervention::class,
            Rule_TextualCrisisIntervention::class,
            Rule_VisualProjectRevision::class,
            Rule_TextualProjectRevision::class,
            Rule_FinalProjectVisualPersistentFail::class,
            Rule_FinalProjectTextualPersistentFail::class,
            Rule_SyntaxRecovery::class,
            Rule_LogicRecovery::class,
            Rule_RemedialIndependent::class,
            Rule_GoldCertificate::class,
            Rule_SilverCertificate::class,
            Rule_BronzeCertificate::class,
            Rule_ModuleGraduation::class,
            Rule_CriticalBacktracking::class,
            Rule_MasteryMedium::class,
            Rule_AcceleratedMaterialPromotion::class,
            Rule_AcceleratedJump::class,
            Rule_StandardPromotion::class,
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
