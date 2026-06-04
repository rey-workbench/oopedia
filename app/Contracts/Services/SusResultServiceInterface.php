<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Enums\Lms\AssessmentType;
use App\Models\SusQuestion;
use App\Models\SusResult;
use Illuminate\Database\Eloquent\Collection;

interface SusResultServiceInterface
{
    /**
     * @return Collection<int, SusResult>
     */
    public function getAllResults(?AssessmentType $type = null): \Illuminate\Support\Collection;

    /**
     * @return array<string>
     */
    public function getDistinctAssessmentTypes(): array;

    public function getResultDetail(string $id): array;

    public function hasUserSubmitted(string $userId, ?AssessmentType $type = null): bool;

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
    public function calculateStatisticalAnalysis(?AssessmentType $type1 = null, ?AssessmentType $type2 = null): array;

    /** @return Collection<int, SusQuestion> */
    public function getOrderedQuestions(): Collection;
}
