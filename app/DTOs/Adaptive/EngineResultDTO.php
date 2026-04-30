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
        public readonly string $recommendation,
        public readonly array $recommendations,
        public readonly array $activeFacts,
        public readonly array $deducedFacts,
        public readonly int $iterations,
        public readonly array $ruleChain,
        public readonly int $priority,
        public readonly string $timestamp,
        public readonly string $version = '4.0.0-forward-chaining',
    ) {}

    public static function fromFallback(array $activeFacts): self
    {
        return new self(
            ruleId: 'ERR-FALLBACK',
            diagnosis: 'Normal Learning',
            recommendation: 'Tetap konsisten dalam belajar!',
            recommendations: [['id' => 'FEEDBACK', 'metadata' => []]],
            activeFacts: $activeFacts,
            deducedFacts: [],
            iterations: 0,
            ruleChain: [],
            priority: 0,
            timestamp: now()->toIso8601String(),
        );
    }

    public static function fromAppliedRules(array $appliedRules, array $activeFacts, int $iterations): self
    {
        $finalRule = end($appliedRules);

        $recommendations = [];
        foreach ($appliedRules as $appliedRule) {
            if (empty($appliedRule->actions)) {
                continue;
            }

            foreach ($appliedRule->actions as $action) {
                if (is_string($action)) {
                    $recommendations[] = [
                        'id'       => $action,
                        'metadata' => [],
                    ];
                } else {
                    $recommendations[] = [
                        'id'       => $action['id'],
                        'metadata' => $action['metadata'] ?? [],
                    ];
                }
            }
        }

        return new self(
            ruleId: $finalRule->id,
            diagnosis: $finalRule->name,
            recommendation: $finalRule->recommendation,
            recommendations: $recommendations,
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
            'recommendations' => $this->recommendations,
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
