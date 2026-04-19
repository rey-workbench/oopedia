<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\MslqRepositoryInterface;
use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class MslqRepository implements MslqRepositoryInterface
{
    public function getAll(?string $class = null, int $perPage = 10): LengthAwarePaginator
    {
        return MslqResult::with('user')
            ->when($class, fn ($query) => $query->where('class', $class))
            ->latest()
            ->paginate($perPage);
    }

    public function getAllForCalculation(?string $class = null): Collection
    {
        return MslqResult::query()
            ->when($class, fn ($query) => $query->where('class', $class))
            ->get();
    }

    public function getDistinctClasses(): Collection
    {
        return MslqResult::distinct()->pluck('class')->filter()->values();
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
