<?php

namespace App\Contracts\Repositories;

use App\Models\UeqSurvey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for UeqSurvey data access.
 */
interface UeqSurveyRepositoryInterface
{
    /** @return Collection<int, UeqSurvey> */
    public function all(): Collection;

    public function find(int $id): ?UeqSurvey;

    public function create(array $data): UeqSurvey;

    public function update(int $id, array $data): ?UeqSurvey;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function countAll(): int;

    /** @return Collection<int, UeqSurvey> */
    public function getAllWithUser(?string $class = null): Collection;

    /** @return array<string> */
    public function getDistinctClasses(): array;

    public function findByUserId(int $userId): ?UeqSurvey;

    public function findSurveyByUser(int $userId): ?UeqSurvey;
}
