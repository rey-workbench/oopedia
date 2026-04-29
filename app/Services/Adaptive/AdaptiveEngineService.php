<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\DTOs\Adaptive\EngineResultDTO;
use App\DTOs\Adaptive\StudentStateDTO;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConditionKeys;
use App\Traits\Adaptive\EvaluatesAdaptiveConditions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    use EvaluatesAdaptiveConditions;

    private const MAX_CHAINING_ITERATIONS = 10;

    private const FALLBACK_RULE_ID        = 'R14';

    private const CATEGORY_PRIMARY       = 'primary';

    private const CATEGORY_VIRTUAL       = 'virtual';

    /**
     * Core Engine: True Forward Chaining with Iterative Evaluation.
     */
    public function evaluate(StudentStateDTO $state): EngineResultDTO
    {
        try {
            $activeFacts  = $this->generateActiveFacts($state);
            $appliedRules = $this->runForwardChaining($activeFacts);

            if (empty($appliedRules)) {
                return $this->handleFallback($activeFacts);
            }

            return EngineResultDTO::fromAppliedRules($appliedRules, $activeFacts, count($appliedRules));
        } catch (\Exception $e) {
            return $this->handleEngineError($e, $activeFacts ?? []);
        }
    }

    /**
     * Maps raw state values to Fact G-Codes based on DB logic.
     */
    private function generateActiveFacts(StudentStateDTO $state): array
    {
        $activeFacts = [];

        $factDefinitions = AdaptiveFact::where('category', self::CATEGORY_PRIMARY)->get();

        foreach ($factDefinitions as $fact) {
            $formula = json_decode($fact->logic ?? '', true);

            if (! $formula || ! isset($formula[AdaptiveConditionKeys::KEY])) {
                continue;
            }

            $currentValue = $state->getMetric($formula[AdaptiveConditionKeys::KEY]);

            if ($this->evaluateCondition($currentValue, $formula[AdaptiveConditionKeys::OP], $formula[AdaptiveConditionKeys::VAL])) {
                $activeFacts[] = $fact->id;
            }
        }

        return array_unique($activeFacts);
    }

    /**
     * Performs the forward chaining loop until stabilization or max iterations.
     *
     * @return array<int, AdaptiveRule>
     */
    private function runForwardChaining(array &$activeFacts): array
    {
        $appliedRules   = [];
        $appliedRuleIds = [];
        $iteration      = 0;

        /** @var Collection<int, AdaptiveRule> $rules */
        $rules = AdaptiveRule::where('is_active', true)->ordered()->get();

        while ($iteration < self::MAX_CHAINING_ITERATIONS) {
            $iteration++;
            $newFactAdded = false;

            foreach ($rules as $rule) {
                if ($this->shouldSkipRule($rule, $appliedRuleIds)) {
                    continue;
                }

                if ($this->isRuleSatisfied($rule->required_fact_ids ?? [], $activeFacts)) {
                    $appliedRules[]   = $rule;
                    $appliedRuleIds[] = $rule->id;

                    if ($this->injectDeducedFacts($rule, $activeFacts)) {
                        $newFactAdded = true;
                    }
                }
            }

            if (! $newFactAdded) {
                break;
            }
        }

        return $appliedRules;
    }

    private function shouldSkipRule(mixed $rule, array $appliedRuleIds): bool
    {
        /** @var AdaptiveRule $rule */
        if (in_array($rule->id, $appliedRuleIds)) {
            return true;
        }

        return empty($rule->required_fact_ids);
    }

    private function injectDeducedFacts(mixed $rule, array &$activeFacts): bool
    {
        /** @var AdaptiveRule $rule */
        $newFactAdded = false;
        if (empty($rule->deduced_fact_ids)) {
            return false;
        }

        foreach ($rule->deduced_fact_ids as $factId) {
            if (! in_array($factId, $activeFacts)) {
                $activeFacts[] = $factId;
                $newFactAdded  = true;
            }
        }

        return $newFactAdded;
    }

    private function isRuleSatisfied(array $required, array $active): bool
    {
        if (empty($required)) {
            return false;
        }

        return count(array_intersect($required, $active)) === count($required);
    }

    private function handleFallback(array $activeFacts): EngineResultDTO
    {
        $fallback = AdaptiveRule::where('id', self::FALLBACK_RULE_ID)->first();

        if ($fallback instanceof AdaptiveRule) {
            return EngineResultDTO::fromAppliedRules([$fallback], $activeFacts, 1);
        }

        return EngineResultDTO::fromFallback($activeFacts);
    }

    private function handleEngineError(\Exception $e, array $activeFacts): EngineResultDTO
    {
        Log::error('Adaptive Engine Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        if ($e instanceof QueryException) {
            throw $e;
        }

        return EngineResultDTO::fromFallback($activeFacts);
    }
}
