<?php

namespace App\Repositories;

use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Models\UeqSurvey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UeqSurveyRepository implements UeqSurveyRepositoryInterface
{
    /** @return Collection<string, UeqSurvey> */
    public function all(): Collection
    {
        return UeqSurvey::all();
    }

    public function find(string $id): ?UeqSurvey
    {
        return UeqSurvey::query()->find($id, ['*']);
    }

    public function create(array $data): UeqSurvey
    {
        return UeqSurvey::query()->create($data);
    }

    public function update(string $id, array $data): ?UeqSurvey
    {
        $ueq = UeqSurvey::query()->find($id, ['*']);

        if ($ueq) {
            $ueq->update($data);

            return $ueq;
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $ueq = UeqSurvey::query()->find($id, ['*']);

        if ($ueq) {
            return (bool)$ueq->delete();
        }

        return false;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return UeqSurvey::query()->paginate($perPage, ['*'], 'page', null);
    }

    public function countAll(): int
    {
        return UeqSurvey::query()->count('*');
    }

    /** @return Collection<int, UeqSurvey> */
    public function getAllWithUser(?string $class = null): Collection
    {
        $query = UeqSurvey::query()->with('user');

        if ($class) {
            $query->where('class', '=', $class);
        }

        return $query->get();
    }

    /** @return array<string> */
    public function getDistinctClasses(): array
    {
        return UeqSurvey::distinct()->pluck('class')->filter()->values()->all();
    }

    public function findByUserId(string $userId): ?UeqSurvey
    {
        return UeqSurvey::query()->where('user_id', '=', $userId)->firstOrFail();
    }

    public function findSurveyByUser(string $userId): ?UeqSurvey
    {
        return UeqSurvey::query()->where('user_id', '=', $userId)->first();
    }
}
