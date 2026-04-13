<?php

namespace App\Contracts\Repositories;

use App\Models\UeqSurvey;
use Illuminate\Database\Eloquent\Collection;

interface UeqSurveyRepositoryInterface
{
    public function create(array $data): UeqSurvey;

    public function getAllWithUser(?string $class = null): Collection;

    public function getDistinctClasses(): array;

    public function findByUserId(string $userId): ?UeqSurvey;

    public function findSurveyByUser(string $userId): ?UeqSurvey;
}
