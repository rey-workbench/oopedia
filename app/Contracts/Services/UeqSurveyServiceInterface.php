<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Enums\Lms\AssessmentType;
use App\Models\UeqSurvey;
use Illuminate\Support\Collection;

interface UeqSurveyServiceInterface
{
    public function getAllSurveys(?AssessmentType $type = null): Collection;

    public function getDistinctAssessmentTypes(): Collection;

    public function getSurveyDetail(string $id): array;

    public function hasUserSubmitted(string $userId, ?AssessmentType $type = null): bool;

    /**
     * @param array<string, mixed> $data
     */
    public function createSurvey(array $data): UeqSurvey;

    public function calculateAverages(Collection $surveys): array;

    public function calculateStatisticalAnalysis(?AssessmentType $type1 = null, ?AssessmentType $type2 = null): array;
}
