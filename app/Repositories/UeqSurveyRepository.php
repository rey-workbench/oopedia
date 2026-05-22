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
    public function getAllWithUser(?string $type = null): Collection
    {
        $builder = UeqSurvey::with('user');

        if ($type) {
            $builder->where('assessment_type', '=', $type);
        }

        return $builder->get();
    }

    public function findByUserId(string $userId): ?UeqSurvey
    {
        return UeqSurvey::where('user_id', '=', $userId)->firstOrFail();
    }

    public function findWithRelations(string $id): UeqSurvey
    {
        return UeqSurvey::with('user')->findOrFail($id);
    }

    public function findSurveyByUser(string $userId, ?string $type = null): ?UeqSurvey
    {
        $query = UeqSurvey::where('user_id', '=', $userId);

        if ($type) {
            $query->where('assessment_type', '=', $type);
        }

        return $query->first();
    }
}
