<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Adaptive\AdaptiveActionDTO;
use App\DTOs\Adaptive\AdaptiveRuleDTO;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveRule;

interface AdaptiveManagementServiceInterface
{
    // --- Rule Management ---
    public function createRule(AdaptiveRuleDTO $adaptiveRuleDTO): AdaptiveRule;

    public function updateRule(string $id, AdaptiveRuleDTO $adaptiveRuleDTO): AdaptiveRule;

    public function deleteRule(string $id): void;

    // --- Action Management ---
    public function createAction(AdaptiveActionDTO $adaptiveActionDTO): AdaptiveAction;

    public function updateAction(string $id, AdaptiveActionDTO $adaptiveActionDTO): AdaptiveAction;

    public function deleteAction(string $id): void;

    // --- Fact Syncing ---
    public function syncFacts(array $facts, array $deducedFacts = []): void;
}
