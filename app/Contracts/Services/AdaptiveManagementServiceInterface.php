<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Adaptive\AdaptiveActionDTO;
use App\DTOs\Adaptive\AdaptiveRuleDTO;

interface AdaptiveManagementServiceInterface
{
    // --- Rule Management ---
    public function createRule(AdaptiveRuleDTO $adaptiveRuleDTO): array;

    public function updateRule(string $id, AdaptiveRuleDTO $adaptiveRuleDTO): array;

    public function deleteRule(string $id): void;

    // --- Action Management ---
    public function createAction(AdaptiveActionDTO $adaptiveActionDTO): array;

    public function updateAction(string $id, AdaptiveActionDTO $adaptiveActionDTO): array;

    public function deleteAction(string $id): void;

    // --- Fact Syncing ---
    public function syncFacts(array $facts, array $deducedFacts = []): void;
}
