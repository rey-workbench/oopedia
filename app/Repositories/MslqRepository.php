<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\MslqRepositoryInterface;
use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class MslqRepository implements MslqRepositoryInterface
{
    public function getAll(?string $assessmentType = null, int $perPage = 10): LengthAwarePaginator
    {
        return MslqResult::with('user')
            ->when($assessmentType, fn ($query) => $query->where('assessment_type', $assessmentType))
            ->latest()
            ->paginate($perPage);
    }

    public function getAllForCalculation(?string $assessmentType = null): Collection
    {
        return MslqResult::when($assessmentType, fn ($query) => $query->where('assessment_type', $assessmentType))
            ->get();
    }

    public function getDistinctAssessmentTypes(): Collection
    {
        return MslqResult::distinct()->pluck('assessment_type')->filter()->values();
    }

    public function findWithRelations(string $id): MslqResult
    {
        return MslqResult::with(['user', 'answers.question'])->findOrFail($id);
    }

    public function create(array $data): MslqResult
    {
        return MslqResult::create($data);
    }
}
