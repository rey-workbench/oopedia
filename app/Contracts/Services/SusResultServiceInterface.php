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
    public function getAllResults(?string $class = null): Collection;

    /**
     * @return array<string>
     */
    public function getDistinctClasses(): array;

    public function getStudentDetail(string $userId): ?SusResult;

    public function hasUserSubmitted(string $userId): bool;

    /**
     * @param array<string, mixed> $data
     */
    public function submitResult(array $data): SusResult;

    /**
     * @param Collection<int, SusResult> $results
     * @return array<string, mixed>
     */
    public function calculateGlobalMetrics(Collection $results): array;

    /**
     * @return array<string, int>
     */
    public function calculateItemScores(SusResult|array $result): array;
}
