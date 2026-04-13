<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Models\UeqSurvey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class UeqSurveyRepository implements UeqSurveyRepositoryInterface
{
    /** @return Collection<string, UeqSurvey> */
    public function all(): Collection
    {
        return UeqSurvey::all();
    }

    public function find(string $id): ?UeqSurvey
    {
        return UeqSurvey::find($id);
    }

    public function create(array $data): UeqSurvey
    {
        return UeqSurvey::create($data);
    }

    public function update(string $id, array $data): ?UeqSurvey
    {
        $ueq = UeqSurvey::find($id);

        if (! $ueq) {
            return null;
        }

        $ueq->update($data);

        return $ueq;
    }

    public function delete(string $id): bool
    {
        $ueq = UeqSurvey::find($id);

        if (! $ueq) {
            return false;
        }

        return (bool) $ueq->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return UeqSurvey::paginate($perPage, ['*'], 'page', null);
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
        return UeqSurvey::where('user_id', '=', $userId)->firstOrFail();
    }

    public function findSurveyByUser(string $userId): ?UeqSurvey
    {
        return UeqSurvey::where('user_id', '=', $userId)->first();
    }
}
