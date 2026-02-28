<?php

namespace App\Repositories;

use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Models\UeqSurvey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UeqSurveyRepository implements UeqSurveyRepositoryInterface
{
    /** @return Collection<int, UeqSurvey> */
    public function all(): Collection
    {
        return UeqSurvey::all();
    }

    public function find(int $id): ?UeqSurvey
    {
        return UeqSurvey::find($id);
    }

    public function create(array $data): UeqSurvey
    {
        return UeqSurvey::create($data);
    }

    public function update(int $id, array $data): ?UeqSurvey
    {
        $ueq = UeqSurvey::find($id);

        if ($ueq) {
            $ueq->update($data);

            return $ueq;
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $ueq = UeqSurvey::find($id);

        if ($ueq) {
            return (bool) $ueq->delete();
        }

        return false;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return UeqSurvey::paginate($perPage);
    }

    public function countAll(): int
    {
        return UeqSurvey::count();
    }

    /** @return Collection<int, UeqSurvey> */
    public function getAllWithUser(?string $class = null): Collection
    {
        $query = UeqSurvey::with('user');

        if ($class) {
            $query->where('class', $class);
        }

        return $query->get();
    }

    /** @return array<string> */
    public function getDistinctClasses(): array
    {
        return UeqSurvey::distinct()->pluck('class')->filter()->values()->all();
    }

    public function findByUserId(int $userId): ?UeqSurvey
    {
        return UeqSurvey::where('user_id', $userId)->firstOrFail();
    }

    public function findSurveyByUser(int $userId): ?UeqSurvey
    {
        return UeqSurvey::where('user_id', $userId)->first();
    }
}
