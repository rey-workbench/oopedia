<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MslqRepositoryInterface
{
    public function getAll(?string $class = null, int $perPage = 10): LengthAwarePaginator;

    public function getAllForCalculation(?string $class = null): Collection;

    public function getDistinctClasses(): Collection;

    public function findWithRelations(string $id): MslqResult;

    public function create(array $data): MslqResult;

    public function hasUserSubmitted(int|string $userId): bool;
}
