<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\UeqSurvey;
use Illuminate\Database\Eloquent\Collection;

interface UeqSurveyRepositoryInterface
{
    public function create(array $data): UeqSurvey;

    public function getAllWithUser(?string $type = null): Collection;

    public function findByUserId(string $userId): ?UeqSurvey;

    public function findWithRelations(string $id): UeqSurvey;

    public function findSurveyByUser(string $userId, ?string $type = null): ?UeqSurvey;
}
