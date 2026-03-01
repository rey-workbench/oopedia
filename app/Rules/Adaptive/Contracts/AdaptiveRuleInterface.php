<?php

namespace App\Rules\Adaptive\Contracts;

interface AdaptiveRuleInterface
{
    /**
     * Get unique rule identifier.
     */
    public function getRuleId(): string;

    /**
     * Get human-readable rule name.
     */
    public function getRuleName(): string;

    /**
     * Get action code (H01-H11).
     */
    public function getActionCode(): string;

    /**
     * Get rule priority (lower = higher priority).
     */
    public function getPriority(): int;

    /**
     * Evaluate if this rule should be triggered based on facts.
     *
     * @param array $facts Array of fact codes (G01, G02, etc.)
     * @return bool True if rule conditions are met
     */
    public function evaluate(array $facts): bool;

    /**
     * Apply the rule's action to the student state.
     *
     * @param array $state Current student state
     * @param array $context Additional context (is_correct, question_id, etc.)
     * @return array Modified state with action applied
     */
    public function apply(array $state, array $context): array;
}
