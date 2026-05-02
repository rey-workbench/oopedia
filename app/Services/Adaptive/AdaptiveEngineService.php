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

    private const int MAX_CHAINING_ITERATIONS = 10;

    private const string FALLBACK_RULE_ID        = 'R00';

    private const string CATEGORY_PRIMARY       = 'primary';

    /**
     * Core Engine: True Forward Chaining with Iterative Evaluation.
     */
    public function evaluate(StudentStateDTO $studentStateDTO): EngineResultDTO
    {
        try {
            $activeFacts  = $this->generateActiveFacts($studentStateDTO);
            $appliedRules = $this->runForwardChaining($activeFacts);

            if ($appliedRules === []) {
                return $this->handleFallback($activeFacts);
            }

            return EngineResultDTO::fromAppliedRules($appliedRules, $activeFacts, count($appliedRules));
        } catch (\Exception $exception) {
            return $this->handleEngineError($exception, $activeFacts ?? []);
        }
    }

    /**
     * Maps raw state values to Fact G-Codes based on DB logic.
     */
    private function generateActiveFacts(StudentStateDTO $studentStateDTO): array
    {
        $activeFacts = [];

        $factDefinitions = AdaptiveFact::where('category', self::CATEGORY_PRIMARY)->get();

        foreach ($factDefinitions as $factDefinition) {
            $formula = json_decode($factDefinition->logic ?? '', true);
            if (! $formula) {
                continue;
            }

            if (! isset($formula[AdaptiveConditionKeys::KEY])) {
                continue;
            }

            $currentValue = $studentStateDTO->getMetric($formula[AdaptiveConditionKeys::KEY]);

            if ($this->evaluateCondition($currentValue, $formula[AdaptiveConditionKeys::OP], $formula[AdaptiveConditionKeys::VAL])) {
                $activeFacts[] = $factDefinition->id;
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
        if ($required === []) {
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

    private function handleEngineError(\Exception $exception, array $activeFacts): EngineResultDTO
    {
        Log::error('Adaptive Engine Error: ' . $exception->getMessage(), [
            'trace' => $exception->getTraceAsString(),
        ]);

        if ($exception instanceof QueryException) {
            throw $exception;
        }

        return EngineResultDTO::fromFallback($activeFacts);
    }
}
