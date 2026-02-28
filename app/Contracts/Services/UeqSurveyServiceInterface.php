<?php

namespace App\Contracts\Services;

use App\Models\UeqSurvey;
use Illuminate\Database\Eloquent\Collection;

interface UeqSurveyServiceInterface
{
    /** @return Collection<int, UeqSurvey> */
    public function getAllSurveys(?string $class = null): Collection;

    /** @return array<string> */
    public function getDistinctClasses(): array;

    public function getStudentDetail(int $userId): ?UeqSurvey;

    public function hasUserSubmitted(int $userId): bool;

    public function createSurvey(array $data): UeqSurvey;

    /** @return array<string, float> */
    public function calculateAverages(Collection $surveys): array;
}
