<?php

namespace App\Rules\Adaptive\Contracts;

interface AdaptiveRuleInterface
{
    public function getRuleId(): string;

    public function getRuleName(): string;

    public function getActionCode(): string;

    public function getPriority(): int;

    public function evaluate(array $facts): bool;

    public function apply(array $state, array $context): array;
}
