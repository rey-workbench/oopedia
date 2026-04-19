<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Models\UeqSurvey;
use Illuminate\Database\Eloquent\Collection;

final class UeqSurveyRepository implements UeqSurveyRepositoryInterface
{
    public function create(array $data): UeqSurvey
    {
        return UeqSurvey::create($data);
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
