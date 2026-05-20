<?php

declare(strict_types=1);

namespace App\DTOs\Adaptive;

/**
 * Data Transfer Object for Adaptive Engine Evaluation Result.
 */
class EngineResultDTO
{
    public function __construct(
        public readonly string $ruleId,
        public readonly string $diagnosis,
        public readonly string $recommendation, // The message/text
        public readonly array $actions,         // Array of action IDs
        public readonly array $activeFacts,
        public readonly array $deducedFacts,
        public readonly int $iterations,
        public readonly array $ruleChain,
        public readonly int $priority,
        public readonly string $timestamp,
        public readonly string $version = '4.1.0-forward-chaining',
    ) {
    }

    public static function fromFallback(array $activeFacts): self
    {
        return new self(
            ruleId: 'R00',
            diagnosis: 'Progres Terjaga',
            recommendation: 'Teruslah melangkah! Setiap soal yang kamu kerjakan membawamu lebih dekat ke penguasaan materi.',
            actions: ['FEEDBACK'],
            activeFacts: $activeFacts,
            deducedFacts: [],
            iterations: 0,
            ruleChain: ['R00'],
            priority: 100,
            timestamp: now()->toIso8601String(),
        );
    }

    public static function fromAppliedRules(array $appliedRules, array $activeFacts, int $iterations): self
    {
        // Sort by priority (ascending, so P0 is first) and pick the most specific one as the final rule
        usort($appliedRules, fn ($a, $b): int => $a->priority <=> $b->priority);
        $finalRule = $appliedRules[0];

        $actions = [];
        foreach ($appliedRules as $appliedRule) {
            if (empty($appliedRule->actions)) {
                continue;
            }

            foreach ($appliedRule->actions as $actionId) {
                $actions[] = is_string($actionId) ? $actionId : $actionId['id'];
            }
        }

        return new self(
            ruleId: $finalRule->id,
            diagnosis: $finalRule->name,
            recommendation: $finalRule->recommendation,
            actions: array_values(array_unique($actions)),
            activeFacts: $activeFacts,
            deducedFacts: $finalRule->deduced_fact_ids ?? [],
            iterations: $iterations,
            ruleChain: array_column($appliedRules, 'id'),
            priority: (int) $finalRule->priority,
            timestamp: now()->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return [
            'id'              => $this->ruleId,
            'diagnosis'       => $this->diagnosis,
            'recommendation'  => $this->recommendation,
            'actions'         => $this->actions,
            'facts'           => $this->activeFacts,
            'deduced_facts'   => $this->deducedFacts,
            'timestamp'       => $this->timestamp,
            'engine_metadata' => [
                'engine_version' => $this->version,
                'iterations'     => $this->iterations,
                'rule_chain'     => $this->ruleChain,
                'priority'       => $this->priority,
            ],
        ];
    }
}
