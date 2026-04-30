<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\UeqSurvey;
use Illuminate\Database\Eloquent\Collection;

interface UeqSurveyServiceInterface
{
    public function getAllSurveys(?string $class = null): Collection;

    public function getDistinctClasses(): array;

    public function getStudentDetail(string $userId): ?UeqSurvey;

    public function hasUserSubmitted(string $userId): bool;

    public function createSurvey(array $data): UeqSurvey;

    public function calculateAverages(Collection $surveys): array;
}
