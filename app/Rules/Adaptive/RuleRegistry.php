<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Achievement\Rule08_ModuleGraduation;
use App\Rules\Adaptive\Achievement\Rule09_GoldCertificate;
use App\Rules\Adaptive\Achievement\Rule10_SilverCertificate;
use App\Rules\Adaptive\Achievement\Rule11_BronzeCertificate;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;
use App\Rules\Adaptive\Crisis\Rule01_VisualCrisisIntervention;
use App\Rules\Adaptive\Crisis\Rule02_TextualRemediation;
use App\Rules\Adaptive\Crisis\Rule12_VisualProjectRevision;
use App\Rules\Adaptive\Crisis\Rule13_TextualProjectRevision;
use App\Rules\Adaptive\Crisis\Rule14_PersistentVisualSafetyNet;
use App\Rules\Adaptive\Crisis\Rule15_PersistentTextualSafetyNet;
use App\Rules\Adaptive\Progression\Rule05_StandardPromotion;
use App\Rules\Adaptive\Progression\Rule06_AcceleratedJump;
use App\Rules\Adaptive\Progression\Rule07_CriticalBacktracking;
use App\Rules\Adaptive\Recovery\Rule03_SyntaxRecovery;
use App\Rules\Adaptive\Recovery\Rule04_LogicRecovery;

/**
 * RuleRegistry
 *
 * Central registry for all adaptive rules.
 * Manages rule registration and provides access to rules by priority.
 */
class RuleRegistry
{
    protected array $rules = [];

    public function __construct()
    {
        $this->registerRules();
    }

    /**
     * Register all adaptive rules in priority order.
     */
    protected function registerRules(): void
    {
        // Crisis rules (highest priority 5-15)
        $this->register(new Rule14_PersistentVisualSafetyNet);
        $this->register(new Rule15_PersistentTextualSafetyNet);
        $this->register(new Rule01_VisualCrisisIntervention);
        $this->register(new Rule02_TextualRemediation);
        $this->register(new Rule12_VisualProjectRevision);
        $this->register(new Rule13_TextualProjectRevision);

        // Recovery rules (priority 20)
        $this->register(new Rule03_SyntaxRecovery);
        $this->register(new Rule04_LogicRecovery);

        // Achievement rules (priority 20-30)
        $this->register(new Rule09_GoldCertificate);
        $this->register(new Rule10_SilverCertificate);
        $this->register(new Rule11_BronzeCertificate);
        $this->register(new Rule08_ModuleGraduation);

        // Progression rules (priority 25-50)
        $this->register(new Rule07_CriticalBacktracking);
        $this->register(new Rule06_AcceleratedJump);
        $this->register(new Rule05_StandardPromotion);

        // Sort by priority (lower number = higher priority)
        usort($this->rules, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());
    }

    /**
     * Register a single rule.
     */
    protected function register(AdaptiveRuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * Get all registered rules in priority order.
     */
    public function getAllRules(): array
    {
        return $this->rules;
    }

    /**
     * Get a specific rule by its ID.
     */
    public function getRuleById(string $ruleId): ?AdaptiveRuleInterface
    {
        foreach ($this->rules as $rule) {
            if ($rule->getRuleId() === $ruleId) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Get rules by action code.
     */
    public function getRulesByAction(string $actionCode): array
    {
        return array_filter(
            $this->rules,
            fn ($rule) => $rule->getActionCode() === $actionCode,
        );
    }
}
