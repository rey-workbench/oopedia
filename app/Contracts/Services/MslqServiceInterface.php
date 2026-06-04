<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Enums\Lms\AssessmentType;
use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MslqServiceInterface
{
    public function getAdminResults(?AssessmentType $type = null): LengthAwarePaginator;

    public function getDistinctAssessmentTypes(): Collection;

    public function calculateGlobalMetrics(?AssessmentType $type = null): array;

    public function getResultDetail(string $id): array;

    public function storeSubmission(array $data, int|string $userId, AssessmentType $type): MslqResult;

    public function getResultsForExport(?AssessmentType $type = null): Collection;

    public function calculateStatisticalAnalysis(?AssessmentType $type1 = null, ?AssessmentType $type2 = null): array;

    public function hasExistingResult(string $userId, string $assessmentType): bool;

    public function getOrderedQuestions(): Collection;

    public function getMslqResultForUser(string $userId): ?MslqResult;
}
