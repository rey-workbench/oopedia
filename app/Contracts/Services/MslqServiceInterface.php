<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MslqServiceInterface
{
    /**
     * Get list of results for admin.
     */
    public function getAdminResults(?string $class = null): LengthAwarePaginator;

    /**
     * Get distinct classes for filtering.
     */
    public function getDistinctClasses(): Collection;

    /**
     * Calculate global metrics for a class.
     */
    public function calculateGlobalMetrics(?string $class = null): array;

    /**
     * Get single result detail.
     */
    public function getResultDetail(string $id): MslqResult;

    /**
     * Process and store survey submission.
     */
    public function storeSubmission(array $data, int|string $userId, string $nim, string $class): MslqResult;

    /**
     * Get all results for export.
     */
    public function getResultsForExport(?string $class = null): Collection;
}
