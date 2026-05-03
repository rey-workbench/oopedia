<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\SusResult;
use Illuminate\Database\Eloquent\Collection;

interface SusResultServiceInterface
{
    /**
     * @return Collection<int, SusResult>
     */
    public function getAllResults(?string $class = null): \Illuminate\Support\Collection;

    /**
     * @return array<string>
     */
    public function getDistinctClasses(): array;

    public function getStudentDetail(string $userId): ?array;

    public function hasUserSubmitted(string $userId): bool;

    /**
     * @param array<string, mixed> $data
     */
    public function submitResult(array $data): SusResult;

    /**
     * @return array<string, mixed>
     */
    public function calculateGlobalMetrics(\Illuminate\Support\Collection $results): array;

    /**
     * @return array<string, int>
     */
    public function calculateItemScores(SusResult|array $result): array;

    /**
     * Perform advanced statistical analysis (Reliability, Mann-Whitney).
     */
    public function calculateStatisticalAnalysis(?string $class1 = null, ?string $class2 = null): array;
}
