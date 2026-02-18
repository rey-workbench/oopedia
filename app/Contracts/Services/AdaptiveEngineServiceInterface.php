<?php

namespace App\Contracts\Services;

/**
 * Contract for the adaptive learning engine that evaluates learning conditions
 * and determines appropriate next actions for a student.
 */
interface AdaptiveEngineServiceInterface
{
    /**
     * Evaluate the current student state against all adaptive rules.
     *
     * @param array<string, mixed> $facts Gathered facts about the student's performance
     * @param array<string, mixed> $currentState The student's current learning state
     * @param array<string, mixed> $context Additional context (material, question, etc.)
     *
     * @return array<string, mixed> The evaluation result with triggered rules and recommended action
     */
    public function evaluate(array $facts, array $currentState, array $context): array;

    /**
     * Get all registered adaptive rules.
     *
     * @return array<string, mixed>
     */
    public function getAllRules(): array;

    /**
     * Get a specific adaptive rule by its ID.
     */
    public function getRuleById(string $ruleId): mixed;
}
