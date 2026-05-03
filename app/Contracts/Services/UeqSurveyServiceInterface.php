<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\UeqSurvey;
use Illuminate\Support\Collection;

interface UeqSurveyServiceInterface
{
    public function getAllSurveys(?string $class = null): Collection;

    public function getDistinctClasses(): array;

    public function getStudentDetail(string $userId): ?array;

    public function hasUserSubmitted(string $userId): bool;

    public function createSurvey(array $data): UeqSurvey;

    public function calculateAverages(Collection $surveys): array;

    public function calculateStatisticalAnalysis(?string $class1 = null, ?string $class2 = null): array;
}
