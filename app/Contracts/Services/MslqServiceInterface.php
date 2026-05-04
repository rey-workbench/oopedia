<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Enums\Lms\AssessmentType;
use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MslqServiceInterface
{
    /**
     * Get list of results for admin.
     */
    public function getAdminResults(?AssessmentType $type = null): LengthAwarePaginator;

    /**
     * Get distinct classes for filtering.
     */
    public function getDistinctAssessmentTypes(): Collection;

    /**
     * Calculate global metrics for a class.
     */
    public function calculateGlobalMetrics(?AssessmentType $type = null): array;

    /**
     * Get single result detail.
     */
    public function getResultDetail(string $id): array;

    /**
     * Process and store survey submission.
     */
    public function storeSubmission(array $data, int|string $userId, AssessmentType $type): MslqResult;

    /**
     * Get all results for export.
     */
    public function getResultsForExport(?AssessmentType $type = null): Collection;

    /**
     * Perform advanced statistical analysis (Reliability, Mann-Whitney).
     */
    public function calculateStatisticalAnalysis(?AssessmentType $type1 = null, ?AssessmentType $type2 = null): array;
}
