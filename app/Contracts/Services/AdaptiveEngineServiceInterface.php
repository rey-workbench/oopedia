<?php

namespace App\Contracts\Services;

interface AdaptiveEngineServiceInterface
{
    public function evaluate(array $facts, array $currentState, array $context): array;

    public function getAllRules(): array;

    public function getRuleById(string $ruleId): mixed;
}
