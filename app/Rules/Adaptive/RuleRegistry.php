<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Achievement\ModuleGraduation;
use App\Rules\Adaptive\Achievement\GoldCertificate;
use App\Rules\Adaptive\Achievement\SilverCertificate;
use App\Rules\Adaptive\Achievement\BronzeCertificate;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;
use App\Rules\Adaptive\Crisis\VisualCrisisIntervention;
use App\Rules\Adaptive\Crisis\TextualRemediation;
use App\Rules\Adaptive\Crisis\VisualProjectRevision;
use App\Rules\Adaptive\Crisis\TextualProjectRevision;
use App\Rules\Adaptive\Crisis\PersistentVisualSafetyNet;
use App\Rules\Adaptive\Crisis\PersistentTextualSafetyNet;
use App\Rules\Adaptive\Progression\StandardPromotion;
use App\Rules\Adaptive\Progression\AcceleratedJump;
use App\Rules\Adaptive\Progression\CriticalBacktracking;
use App\Rules\Adaptive\Progression\MasteryMedium;
use App\Rules\Adaptive\Recovery\SyntaxRecovery;
use App\Rules\Adaptive\Recovery\LogicRecovery;
use App\Rules\Adaptive\Recovery\RemedialIndependent;

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
        $this->register(new PersistentVisualSafetyNet);
        $this->register(new PersistentTextualSafetyNet);
        $this->register(new VisualCrisisIntervention);
        $this->register(new TextualRemediation);
        $this->register(new VisualProjectRevision);
        $this->register(new TextualProjectRevision);

        // Recovery rules (priority 24-48)
        $this->register(new SyntaxRecovery);
        $this->register(new LogicRecovery);
        $this->register(new RemedialIndependent);

        // Achievement rules (priority 20-30)
        $this->register(new GoldCertificate);
        $this->register(new SilverCertificate);
        $this->register(new BronzeCertificate);
        $this->register(new ModuleGraduation);

        // Progression rules (priority 27-50)
        $this->register(new CriticalBacktracking);
        $this->register(new MasteryMedium);
        $this->register(new AcceleratedJump);
        $this->register(new StandardPromotion);

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
