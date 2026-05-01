<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use Illuminate\Support\Collection;

interface AdaptiveAnalyticsServiceInterface
{
    /** @return array<string, mixed> */
    public function getDashboardStats(): array;

    /** @return array<int, mixed> */
    public function getRecentTriggers(int $limit = 10): array;

    /** @return array<int, mixed> */
    public function getRuleTriggerStats(): array;

    /** @return array<int, mixed> */
    public function getAdaptiveStateDistribution(): array;

    /** @return array<string, mixed> */
    public function getDecisionTree(): array;

    /** @return array<int, mixed> */
    public function getRulesByDiagnosis(): array;

    public function getAllFacts(): Collection;

    public function getAllActions(): Collection;
}
