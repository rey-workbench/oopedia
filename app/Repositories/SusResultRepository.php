<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\SusResultRepositoryInterface;
use App\Models\SusResult;
use Illuminate\Database\Eloquent\Collection;

final class SusResultRepository implements SusResultRepositoryInterface
{
    public function create(array $data): SusResult
    {
        return SusResult::create($data);
    }

    /** @return Collection<int, SusResult> */
    public function getAllWithUser(?string $assessmentType = null): Collection
    {
        $builder = SusResult::with('user');

        if ($assessmentType) {
            $builder->where('assessment_type', '=', $assessmentType);
        }

        return $builder->get();
    }

    public function findWithRelations(string $id): SusResult
    {
        return SusResult::with(['user', 'answers.question'])->findOrFail($id);
    }
}
